<?php

use App\Models\Status;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyComparisonScale;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$accountId = '440819522';
$preId = 137;
$postId = 139;

echo "Analysis for Student Account ID: $accountId\n";
echo "----------------------------------------\n";

$domains = Status::where('p_id_sub', 145)->get();

$scales = SurveyComparisonScale::all();

foreach ($domains as $domain) {
    echo "Domain: {$domain->status_name} ({$domain->id})\n";
    
    $preScore = SurveyAnswer::where('account_id', $accountId)
        ->where('survey_no', $preId)
        ->whereHas('question', fn($q) => $q->where('domain_id', $domain->id))
        ->sum(DB::raw('CAST(answer_ar_text AS DECIMAL(10,2))'));

    $postScore = SurveyAnswer::where('account_id', $accountId)
        ->where('survey_no', $postId)
        ->whereHas('question', fn($q) => $q->where('domain_id', $domain->id))
        ->sum(DB::raw('CAST(answer_ar_text AS DECIMAL(10,2))'));

    $maxScore = SurveyQuestion::where('survey_for_section', $preId)
        ->where('domain_id', $domain->id)
        ->sum('max_score');

    if ($maxScore > 0) {
        $prePercent = ($preScore / $maxScore) * 100;
        $postPercent = ($postScore / $maxScore) * 100;
        $diff = $postPercent - $prePercent;
        
        $match = $scales->filter(function($s) use ($diff, $domain) {
            return $diff >= $s->from_percentage && $diff <= $s->to_percentage 
                && ($s->domain_id == $domain->id || is_null($s->domain_id));
        })->sortByDesc(fn($s) => !is_null($s->domain_id))->first();

        printf("  Pre:  %.2f (%.1f%%)\n", $preScore, $prePercent);
        printf("  Post: %.2f (%.1f%%)\n", $postScore, $postPercent);
        printf("  Diff: %+.1f%%\n", $diff);
        echo "  Status: " . ($match->evaluation ?? 'N/A') . "\n";
    } else {
        echo "  No questions found for this domain.\n";
    }
    echo "\n";
}
