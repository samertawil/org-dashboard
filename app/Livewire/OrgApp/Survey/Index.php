<?php

namespace App\Livewire\OrgApp\Survey;

use App\Models\SurveyTable;
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $survey_id = null;
    public $survey_name;
    public $survey_target;
    public $survey_for_section;
    public $semester = 1;
    public $is_active = true;
    public $conditions = '';
    public $notes = '';

    protected $rules = [
        'survey_name' => 'required|string|max:255',
        'survey_target' => 'required',
        'survey_for_section' => 'required',
        'semester' => 'nullable|integer',
    ];

    public function openModal($id = null)
    {
        $this->resetErrorBag();
        $this->survey_id = $id;

        if ($id) {
            $survey = SurveyTable::findOrFail($id);
            $this->survey_name = $survey->survey_name;
            $this->survey_for_section = $survey->survey_for_section;
            $this->survey_target = $survey->survey_target;
            $this->semester = $survey->semester;
            $this->is_active = (bool) $survey->is_active;
            $this->conditions = $survey->conditions;
            $this->notes = $survey->notes;
        } else {
            $this->survey_name = '';
            $this->survey_for_section = '';
            $this->survey_target = '';
            $this->semester = 1;
            $this->is_active = true;
            $this->conditions = '';
            $this->notes = '';
        }

        $this->showModal = true;
    }

    public function toggleStatus($id)
    {
        $survey = SurveyTable::findOrFail($id);
        $survey->is_active = !$survey->is_active;
        $survey->save();
        
        session()->flash('message', __('Status updated successfully.'));
    }

    public function save()
    {
        $this->validate([
            'survey_name' => 'required|string|max:255',
            'survey_for_section' => 'nullable|integer',
            'survey_target' => 'nullable|integer',
            'semester' => 'nullable|integer',
            'is_active' => 'boolean',
            'conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        SurveyTable::updateOrCreate(
            ['id' => $this->survey_id],
            [
                'survey_name' => $this->survey_name,
                'survey_for_section' => $this->survey_for_section,
                'survey_target' => $this->survey_target,
                'semester' => $this->semester,
                'is_active' => $this->is_active,
                'conditions' => $this->conditions,
                'notes' => $this->notes,
            ]
        );

        $this->showModal = false;
        session()->flash('message', __('Saved successfully'));
    }

    public function delete($id)
    {
        $survey = SurveyTable::withCount(['questions', 'answers'])->findOrFail($id);

        if ($survey->questions_count > 0) {
            session()->flash('error', __('Cannot delete survey because it has associated questions. Please remove questions first.'));
            return;
        }

        if ($survey->answers_count > 0) {
            session()->flash('error', __('Cannot delete survey because it has recorded responses.'));
            return;
        }

        $survey->delete();
        
        session()->flash('message', __('Survey deleted successfully.'));
    }

    public function render()
    {
        
        if (Gate::denies('outer.servey.list')) {
            abort(403, 'You do not have the necessary permissions');
        }

        
        $surveys = SurveyTable::with(['targetRel', 'sectionRel'])->orderBy('survey_for_section','desc')
        ->paginate(10);
        $targets = StatusRepo::statuses()->where('p_id_sub', config('appConstant.survey_target', 0)); // Assuming this constant exists
        $sections = StatusRepo::statuses()->where('p_id_sub', config('appConstant.survey_for', 0));

        return view('livewire.org-app.survey.index', [
            'surveys' => $surveys,
            'targets' => $targets,
            'sections' => $sections,
        ])->layout('layouts.app');
    }
}
