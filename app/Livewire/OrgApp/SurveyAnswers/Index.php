<?php

namespace App\Livewire\OrgApp\SurveyAnswers;


use App\Models\Employee;
use App\Models\Student;
use App\Models\SurveyAnswer;
use App\Models\TeacherStudentGroup;
use App\Concerns\AccessibleGroupsTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Concerns\SurveyAnswers\SurveyAnswersTrait;
use App\Reposotries\StudentGroupRepo;

class Index extends Component
{
    use WithPagination, AccessibleGroupsTrait, SurveyAnswersTrait;

    public $searchAccountId = '';
    public $searchSurveyNo = '';
    public $searchCreatedBy = '';
    public $searchCreatedAt = '';
    public $searchAccountName = '';

    public string $filterBatch = '';
    public string $filterGroup = '';

    public $sortField = 'created_at';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $readyToLoad = false;

    // Modal state
    public $showAnswersModal = false;
    public $selectedAccountId = null;
    public $selectedSurveyNo = null;

    // Inline edit state
    public $editingAnswerId = null;
    public $editingAnswerText = '';

    protected $queryString = [
        'searchAccountId' => ['except' => ''],
        'searchAccountName' => ['except' => ''],
        'searchSurveyNo' => ['except' => ''],
        'searchCreatedBy' => ['except' => ''],
        'searchCreatedAt' => ['except' => ''],
        'filterBatch' => ['except' => ''],
        'filterGroup' => ['except' => ''],
    ];


    public function mount()
    {

        if (!$this->isManager) {
            $this->searchCreatedBy = (string) (auth()->user()->employee?->id ?? '');
        }
        $this->filterBatch = StudentGroupRepo::activeToday()->sortByDesc('batch_no')->pluck('batch_no')->first() ?? '';
        $groupIds = $this->accessibleGroupIds;
        $this->filterGroup = is_array($groupIds) && count($groupIds) === 1 ? (string) $groupIds[0] : '';
    }



    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }



    public function updating($property)
    {
        if (in_array($property, ['searchAccountId', 'searchSurveyNo', 'searchCreatedBy', 'searchCreatedAt', 'searchAccountName', 'filterBatch', 'filterGroup'])) {
            $this->resetPage();
            $this->readyToLoad = false;
        }
    }

    public function updatingFilterBatch(): void
    {
        $this->filterGroup = '';
        $this->resetPage();
        $this->readyToLoad = false;
    }

    public function updatingFilterGroup(): void
    {
        $this->resetPage();
        $this->readyToLoad = false;
    }

    public function clearFilters()
    {
        $this->reset(['searchAccountId', 'searchSurveyNo', 'searchCreatedBy', 'searchCreatedAt', 'searchAccountName', 'filterBatch', 'filterGroup']);
        $this->readyToLoad = false;
        $this->resetPage();
    }

    public function searchData()
    {
        $this->readyToLoad = true;
        $this->resetPage();
    }

    #[Computed()]
    public function availableGroups()
    {
        $groups = $this->accessibleGroups;
        if (!empty($this->filterBatch)) {
            $groups = $groups->where('batch_no', $this->filterBatch);
        }
        return $groups->values();
    }

    #[Computed()]
    public function answers()
    {
        if ($this->readyToLoad && (
            $this->searchAccountId !== '' ||
            $this->searchAccountName !== '' ||
            $this->searchSurveyNo !== '' ||
            $this->searchCreatedBy !== '' ||
            $this->searchCreatedAt !== '' ||
            $this->filterBatch !== '' ||
            $this->filterGroup !== ''
        )) {
            $user = auth()->user();
            $employee = $user->employee;
            $teacherGroupIds = $this->accessibleGroupIds;

            return SurveyAnswer::query()
                ->select('account_id', 'survey_no', \DB::raw('MAX(created_at) as created_at'), \DB::raw('MAX(created_by) as created_by'))
                ->with(['creator', 'surveyfor', 'student'])
                // Permission-based filtering
                ->when($teacherGroupIds !== null, function ($q) use ($employee, $teacherGroupIds) {
                    $supervisorGroupIds = \App\Services\SupervisorService::getSupervisedGroupIds(auth()->user());

                    $q->whereHas('student', function ($sub) use ($teacherGroupIds) {
                        $sub->whereIn('student_groups_id', $teacherGroupIds);
                    })
                        ->where(function ($subQuery) use ($supervisorGroupIds, $employee) {
                            $subQuery->whereHas('student', function ($sub) use ($supervisorGroupIds) {
                                $sub->whereIn('student_groups_id', $supervisorGroupIds);
                            })
                                ->orWhere('survey_answers.created_by', $employee?->id);
                        });
                })
                // Filters
                ->when($this->searchAccountId !== '', fn($q) => $q->where('survey_answers.account_id', $this->searchAccountId))
                ->when($this->searchAccountName !== '', fn($q) => $q->where('survey_answers.account_id', $this->searchAccountName))
                ->when($this->searchSurveyNo !== '', fn($q) => $q->where('survey_answers.survey_no', $this->searchSurveyNo))
                ->when($this->searchCreatedBy !== '', fn($q) => $q->where('survey_answers.created_by', $this->searchCreatedBy))
                ->when($this->searchCreatedAt !== '', fn($q) => $q->whereDate('survey_answers.created_at', $this->searchCreatedAt))
                ->when($this->filterGroup !== '', function ($q) {
                    $q->whereHas('student', function ($sub) {
                        $sub->where('student_groups_id', $this->filterGroup);
                    });
                })
                ->when($this->filterBatch !== '', function ($q) {
                    $q->whereHas('student.studentGroup', function ($sub) {
                        $sub->where('batch_no', $this->filterBatch);
                    });
                })
                ->groupBy('account_id', 'survey_no')
                ->orderBy($this->sortField === 'id' ? 'created_at' : $this->sortField, $this->sortDirection)
                ->paginate($this->perPage);
        } else {
            return new LengthAwarePaginator(
                collect([]), // empty collection
                0, // total
                $this->perPage, // per page
                1 // current page
            );
        }
    }

    public function deleteSurveyGroup($accountId, $surveyNo)
    {
        if (Gate::denies('survey.create')) {
            abort(403, 'You do not have the necessary permissions');
        }

        SurveyAnswer::where('account_id', $accountId)
            ->where('survey_no', $surveyNo)
            ->delete();

        session()->flash('message', __('Survey answers deleted successfully.'));
    }

    public function openAnswersModal($accountId, $surveyNo)
    {
        $this->selectedAccountId = $accountId;
        $this->selectedSurveyNo = $surveyNo;
        $this->showAnswersModal = true;
        $this->cancelEdit();
    }

    #[Computed()]
    public function selectedSurveyAnswers()
    {
        if (!$this->selectedAccountId || !$this->selectedSurveyNo) {
            return collect([]);
        }

        $user = auth()->user();
        $employee = $user->employee;
        $teacherGroupIds = $this->accessibleGroupIds;

        return SurveyAnswer::query()
            ->where('account_id', $this->selectedAccountId)
            ->where('survey_no', $this->selectedSurveyNo)
            ->join('survey_questions', 'survey_answers.question_id', '=', 'survey_questions.id')
            ->select('survey_answers.*')
            // Permission-based filtering
            ->when($teacherGroupIds !== null, function ($q) use ($employee, $teacherGroupIds) {
                $supervisorGroupIds = \App\Services\SupervisorService::getSupervisedGroupIds(auth()->user());

                $q->whereHas('student', function ($sub) use ($teacherGroupIds) {
                    $sub->whereIn('student_groups_id', $teacherGroupIds);
                })
                    ->where(function ($subQuery) use ($supervisorGroupIds, $employee) {
                        $subQuery->whereHas('student', function ($sub) use ($supervisorGroupIds) {
                            $sub->whereIn('student_groups_id', $supervisorGroupIds);
                        })
                            ->orWhere('survey_answers.created_by', $employee?->id);
                    });
            })
            ->orderBy('survey_questions.survey_for_section')
            ->orderBy('survey_questions.question_order')
            ->with(['question', 'creator', 'surveyfor', 'student'])
            ->get();
    }

    public function startEditAnswer($id)
    {
        $answer = SurveyAnswer::findOrFail($id);
        $this->editingAnswerId = $id;
        $this->editingAnswerText = $answer->answer_ar_text;
    }

    public function cancelEdit()
    {
        $this->editingAnswerId = null;
        $this->editingAnswerText = '';
    }

    public function saveAnswer()
    {
        if (Gate::denies('survey.create')) {
            abort(403, 'You do not have the necessary permissions');
        }

        $this->validate([
            'editingAnswerText' => 'nullable|string',
        ]);

        $answer = SurveyAnswer::findOrFail($this->editingAnswerId);

        // Resolve answer_label if question has answer_options
        $answerLabel = null;
        $question = $answer->question;
        if ($question) {
            $options = $question->answer_options;
            if (!empty($options)) {
                if (is_string($options)) {
                    $options = json_decode($options, true);
                }
                if (is_array($options)) {
                    $decodedVal = json_decode($this->editingAnswerText, true);
                    $values = (json_last_error() === JSON_ERROR_NONE && is_array($decodedVal)) ? $decodedVal : [$this->editingAnswerText];

                    $labels = [];
                    foreach ($values as $val) {
                        $found = $val;
                        foreach ($options as $option) {
                            if (is_array($option) && isset($option['value']) && isset($option['label'])) {
                                if ((string) $option['value'] === (string) $val) {
                                    $found = $option['label'];
                                    break;
                                }
                            } elseif (is_string($option)) {
                                if ((string) $option === (string) $val) {
                                    $found = $option;
                                    break;
                                }
                            }
                        }
                        $labels[] = $found;
                    }
                    $answerLabel = implode('، ', $labels);
                }
            }
        }

        $answer->update([
            'answer_ar_text' => $this->editingAnswerText,
            'answer_label' => $answerLabel,
        ]);

        $this->editingAnswerId = null;
        $this->editingAnswerText = '';

        session()->flash('modal_message', __('Answer updated successfully.'));
    }

    public function deleteAnswer($id)
    {
        if (Gate::denies('survey.create')) {
            abort(403, 'You do not have the necessary permissions');
        }

        $answer = SurveyAnswer::findOrFail($id);
        $answer->delete();

        session()->flash('modal_message', __('Answer deleted successfully.'));
    }


    #[Computed]
    public function isManager(): bool
    {
        $user = auth()->user();
        if ($user && ($user->isSuperAdmin() || Gate::allows('select.any.student'))) {
            return true;
        }

        // Supervisors (job_title=167) can query all employees in their groups
        return \App\Services\SupervisorService::isSupervisor($user);
    }

    public function render()
    {
        // if (Gate::denies('survey.index')) {
        //     abort(403, 'You do not have the necessary permissions');
        // }

        $studentsQuery = Student::query();

        $teacherGroupIds = $this->accessibleGroupIds;
        if ($teacherGroupIds !== null) {
            $studentsQuery->whereIn('student_groups_id', $teacherGroupIds);
        }

        if ($this->filterGroup !== '') {
            $studentsQuery->where('student_groups_id', $this->filterGroup);
        } elseif ($this->filterBatch !== '') {
            $studentsQuery->whereHas('studentGroup', function ($q) {
                $q->where('batch_no', $this->filterBatch);
            });
        }

        $students = $studentsQuery->orderBy('full_name')->get();

        // For supervisors (job_title=167), show only employees in their groups
        $user = auth()->user();
        if ($user->isSuperAdmin() || Gate::allows('select.any.student')) {
            $employees = TeacherStudentGroup::employeesForEducationalTasks();
        } else {
            $supervisorGroupIds = \App\Services\SupervisorService::getSupervisedGroupIds($user);

            $employees = Employee::whereIn('user_id', function ($query) use ($supervisorGroupIds) {
                $query->select('teacher_id')
                    ->from('teacher_student_group')
                    ->whereIn('student_group_id', $supervisorGroupIds)
                    ->whereNotNull('teacher_id');
            })
                ->orderBy('full_name')
                ->get();
        }

        return view('livewire.org-app.survey-answers.index', [
            'employees' => $employees,
            'students' => $students,
            'answers' => $this->answers,
            'isManager' => $this->isManager,
        ]);
    }
}
