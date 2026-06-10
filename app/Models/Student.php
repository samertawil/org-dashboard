<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StudentGroup;
use App\Reposotries\StudentGroupRepo;
use App\Enums\GlobalSystemConstant;
use Carbon\Carbon;
use DateTime;


class Student extends Model
{
    protected $fillable = [
        'identity_number',
        'full_name',
        'birth_date',
        'student_groups_id',
        'gender',
        'activation',
        'status_id',
        'parent_phone',
        'living_parent_id',
        'notes',
        'added_type',
        'enrollment_type',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['student_age_when_join'];

    protected static ?string $latestActiveGroupStartDate = null;

    public static function getLatestActiveGroupStartDate(): ?string
    {
        if (self::$latestActiveGroupStartDate === null) {
            $group = StudentGroupRepo::studentGroups()
                ->where('activation', GlobalSystemConstant::ACTIVE)
                ->sortByDesc('batch_no')
                ->first();
            self::$latestActiveGroupStartDate = $group?->start_date ?? '';
        }

        return self::$latestActiveGroupStartDate !== '' ? self::$latestActiveGroupStartDate : null;
    }

    public static function maxBirthDate()
    {
        $startDate = self::getLatestActiveGroupStartDate();

        $from = $startDate ? new DateTime($startDate) : new DateTime();
        $y = $from->modify(config('malRules.allowDate1.fromDate'));
        $from = $y->modify(config('malRules.allowDate1.min_date'));
        $from =  $from->format('Y-m-d');  // birthdate not grater than $from date  "6 years old"

        return $from;
    }

    public static function minBirthDate()
    {
        $startDate = self::getLatestActiveGroupStartDate();

        $to = $startDate ? new DateTime($startDate) : new DateTime();
        $x =  $to->modify(config('malRules.allowDate1.toDate'));
        $to = $x->modify(config('malRules.allowDate1.plus_date'));
        $to =  $to->format('Y-m-d');   // birthdate not less than $from date  "6 years old"
        return $to;
    }


    public function surveyStudentanswers()
    {
        return $this->hasMany(SurveyAnswer::class, 'account_id', 'identity_number');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }



    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function livingParent()
    {
        return $this->belongsTo(Status::class, 'living_parent_id');
    }

    public function group()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }


    public function feedbacks()
    {
        return $this->hasMany(FeedBack::class);
    }

    public function dailyAttendances()
    {
        return $this->hasMany(StudentDailyAttendance::class);
    }

    public function getStudentAgeWhenJoinAttribute(): int
    {

        $birthDate = $this->birth_date;
        $joinDate = StudentGroup::where('id', $this->student_groups_id)->first()->start_date ?? null;

        $this->join_date;

        if (!$birthDate || !$joinDate) {
            return 0;
        }

        $birth = Carbon::parse($birthDate);
        $join = Carbon::parse($joinDate);
        $diff = $birth->diff($join); // DateInterval
        $years = $diff->y;
        $months = $diff->m;

        // إذا كان العمر أقل من 10 سنوات بشهرين أو أقل (9 سنوات و 10 أشهر فأكثر) يتم احتسابه 10 سنوات
        if ($years == 9 && $months >= 9) {
            $years = 10;
            $months = 0;
            return 10;
        }

        //   return "$years years and $months months";

        return $birth->diffInYears($join);
    }

    public function getStudentExactAgeWhenJoinAttribute(): mixed
    {

        $birthDate = $this->birth_date;
        $joinDate = StudentGroup::where('id', $this->student_groups_id)->first()->start_date ?? null;

        $this->join_date;

        if (!$birthDate || !$joinDate) {
            return 0;
        }

        $birth = Carbon::parse($birthDate);
        $join = Carbon::parse($joinDate);
        $diff = $birth->diff($join); // DateInterval
        $years = $diff->y;
        $months = $diff->m;


        return "$years years and $months months";
    }



    public function studentGroup()
    {
        return $this->belongsTo(StudentGroup::class, 'student_groups_id');
    }

    /**
     * Get comparison results between Pre and Post surveys for this student.
     */
    public function getSurveyComparisonResults()
    {
        $mappings = [
            137 => 139,
            138 => 140,
            141 => 143,
            142 => 144,
        ];

        // 1. Fetch scales
        $scales = \App\Models\SurveyComparisonScale::all();

        // 2. Fetch all domains
        $domains = \App\Models\Status::where('p_id_sub', config('appConstant.domains_of_assessment', 145))->get();

        $results = [];

        foreach ($mappings as $preId => $postId) {
            // Check if student has answers for either survey
            $hasData = \App\Models\SurveyAnswer::where('account_id', $this->identity_number)
                ->whereIn('survey_no', [$preId, $postId])
                ->exists();

            if (!$hasData) continue;

            $pairResults = [
                'pre_id' => $preId,
                'post_id' => $postId,
                'pre_name' => \App\Models\Status::find($preId)?->status_name,
                'post_name' => \App\Models\Status::find($postId)?->status_name,
                'domains' => [],
                'total' => null
            ];

            // Aggregated scores for this pair
            $domainScores = \Illuminate\Support\Facades\DB::table('survey_answers as a')
                ->join('survey_questions as q', 'a.question_id', '=', 'q.id')
                ->where('a.account_id', $this->identity_number)
                ->whereIn('a.survey_no', [$preId, $postId])
                ->select(
                    'q.domain_id',
                    \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN a.survey_no = {$preId} THEN CAST(a.answer_ar_text AS DECIMAL(10,2)) ELSE 0 END) as pre_score"),
                    \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN a.survey_no = {$postId} THEN CAST(a.answer_ar_text AS DECIMAL(10,2)) ELSE 0 END) as post_score")
                )
                ->groupBy('q.domain_id')
                ->get();

            $maxScores = \App\Models\SurveyQuestion::whereIn('survey_for_section', [$preId, $postId])
                ->select('survey_for_section', 'domain_id', \Illuminate\Support\Facades\DB::raw('SUM(max_score) as total_max'))
                ->groupBy('survey_for_section', 'domain_id')
                ->get()
                ->groupBy('domain_id');

            $totalPre = 0;
            $totalPost = 0;
            $totalMax = 0;

            foreach ($domains as $domain) {
                $score = $domainScores->firstWhere('domain_id', $domain->id);
                $pre = $score?->pre_score ?? 0;
                $post = $score?->post_score ?? 0;
                $max = $maxScores->get($domain->id)?->where('survey_for_section', $preId)->first()?->total_max ?? 0;

                if ($max > 0) {
                    $hasPost = ($post > 0);
                    $diff = ($pre > 0 && $hasPost) ? ($post / $max * 100) - ($pre / $max * 100) : null;
                    $scale = $diff !== null ? $scales->filter(fn($s) => $diff >= $s->from_percentage && $diff <= $s->to_percentage && ($s->domain_id == $domain->id || is_null($s->domain_id)))->sortByDesc(fn($s) => !is_null($s->domain_id))->first() : null;

                    $pairResults['domains'][] = [
                        'name' => $domain->status_name,
                        'pre' => $pre,
                        'post' => $post,
                        'diff' => $diff !== null ? round($diff, 1) : null,
                        'evaluation' => $scale->evaluation ?? ($pre > 0 && !$hasPost ? __('Pending') : '-'),
                        'color' => $scale->color ?? '#9ca3af',
                    ];

                    $totalPre += $pre;
                    $totalPost += $post;
                    $totalMax += $max;
                }
            }

            if ($totalMax > 0) {
                $hasTotalPost = ($totalPost > 0);
                $totalDiff = ($totalPre > 0 && $hasTotalPost) ? (($totalPost / $totalMax) * 100) - (($totalPre / $totalMax) * 100) : null;
                $totalScale = $totalDiff !== null ? $scales->filter(fn($s) => $totalDiff >= $s->from_percentage && $totalDiff <= $s->to_percentage && is_null($s->domain_id))->first() : null;

                $pairResults['total'] = [
                    'pre' => $totalPre,
                    'post' => $totalPost,
                    'diff' => $totalDiff !== null ? round($totalDiff, 1) : null,
                    'evaluation' => $totalScale->evaluation ?? ($totalPre > 0 && !$hasTotalPost ? __('Pending Post-Survey') : '-'),
                    'color' => $totalScale->color ?? '#9ca3af',
                ];
            }

            $results[] = (object)$pairResults;
        }

        return $results;
    }

    public function scopeVisibleToTeacher($query, $user)
    {
        // ->when(!$user->isSuperAdmin() || \Illuminate\Support\Facades\DB::table('teacher_student_group')->where('teacher_id', $user->id)->exists(), function ($query) use ($user) {

        return $query->when(!$user->selectAnyStudent(), function ($q) use ($user) {
            $q->whereIn('student_groups_id', function ($subQuery) use ($user) {
                $subQuery->select('student_group_id')
                    ->from('teacher_student_group')
                    ->where('teacher_id', $user->id);
            });
        });
    }
}
