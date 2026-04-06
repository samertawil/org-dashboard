<?php

namespace App\Livewire\OrgApp\SurveyQuestions;

use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Show extends Component
{
    public function surveySections() {
           SurveyQuestion::select('survey_for_section')->get()->distinct();
    }

    public function render()
    {
        if (Gate::denies('survey.manage')) 
        { 
            abort(403, __('You do not have the necessary permissions.'));
        }
        return view('livewire.org-app.survey-questions.show');
    }
}
