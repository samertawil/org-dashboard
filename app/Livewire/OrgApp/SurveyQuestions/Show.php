<?php

namespace App\Livewire\OrgApp\SurveyQuestions;

use App\Models\SurveyQuestion;
use Livewire\Component;

class Show extends Component
{
    public function surveySections() {
           SurveyQuestion::select('survey_for_section')->get()->distinct();
    }

    public function render()
    {
        dd(  SurveyQuestion::select('survey_for_section')->get()->distinct() );
        return view('livewire.org-app.survey-questions.show');
    }
}
