<?php

namespace App\Livewire\OrgApp\Activity;

 
use Livewire\Component;
use App\Models\Activity;
use Livewire\Attributes\Lazy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class Show extends Component
{
    public Activity $activity;

    public function mount(Activity $activity)
    {
        $this->activity = $activity->load(['regions', 'cities', 'activityNeighbourhood', 'activityLocation', 'activityStatus', 'statusSpecificSector', 'creator','parcels.purchaseRequisition', 'beneficiaries', 'workTeams', 'summary']);
    }

    public function generateSummary()
    {
        Log::info('ActivityShow: generateSummary called for activity ID: ' . $this->activity->id);
        
        if(Gate::denies('activity.index')){
            return abort(403,'You do not have the necessary permissions');
        }

        try {
            $action = app(\App\Actions\SummarizeActivityAction::class);
            $summary = $action->execute($this->activity);
            
            if ($summary) {
                $this->activity->load('summary');
                $this->dispatch('summary-generated');
                session()->flash('message', __('Summary generated successfully by AI.'));
            } else {
                Log::warning('ActivityShow: Summary generation returned null.');
                $this->dispatch('summary-error');
                session()->flash('error', __('Failed to generate summary. AI returned empty content.'));
            }
        } catch (\App\Exceptions\AIException $e) {
            Log::error('ActivityShow: AI error in generateSummary: ' . $e->getMessage());
            $this->dispatch('summary-error');
            
            $errorMessage = match($e->getCode()) {
                429 => __('AI Quota exceeded. Please try again later or check your API limit.'),
                503 => __('Unable to connect to AI service. Please check your internet connection.'),
                default => __('AI Service error: ') . $e->getMessage(),
            };
            
            session()->flash('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('ActivityShow: Unexpected error in generateSummary: ' . $e->getMessage());
            $this->dispatch('summary-error');
            session()->flash('error', __('An unexpected error occurred: ') . $e->getMessage());
        }
    }

    public function downloadPdf()
    {
        if(Gate::denies('activity.index')){
            return abort(403,'You do not have the necessary permissions');
        }

        $pdf = Pdf::loadView('livewire.org-app.activity.pdf', ['activity' => $this->activity]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'activity-report-' . $this->activity->id . '.pdf');
    }

    public function render()
    {
        if(Gate::denies('activity.index')){
            return abort(403,'You do not have the necessary permissions');
        }
        return view('livewire.org-app.activity.show');
    }
}
