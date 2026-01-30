<?php

namespace App\Livewire\OrgApp\Activity;

use App\Models\City;
use App\Models\Status;
use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;
use App\Reposotries\CityRepo;
use Livewire\WithFileUploads;
use App\Reposotries\RegionRepo;
use App\Reposotries\StatusRepo;
use App\Models\ActivityAttchment;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $start_date = '';
    public $status_id = '';
    public $region_id = '';
    public $city_id = '';

    public $selectedactivityIdForShowModal;

    public function openShowModal($projectId)
    {
        $this->selectedactivityIdForShowModal = $projectId;
        $this->dispatch('open-modal', name: 'project-show-' . $projectId);
    }

    public function closeShowModal()
    {
        $this->selectedactivityIdForShowModal = null;
    }

    public ?Activity $selectedactivity = null;
    public $attachments = [];
    public $newAttachments = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'start_date' => ['except' => ''],
        'status_id' => ['except' => ''],
        'region_id' => ['except' => ''],
        'city_id' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();
        session()->flash('message', __('Activity successfully deleted.'));
    }

    public function selectActivity($activityId)
    {
        $this->selectedactivity = Activity::with('attachments')->find($activityId);
        $this->attachments = $this->selectedactivity->attachments->toArray();
        $this->newAttachments = [];
    }

    public function addAttachment()
    {
        $this->newAttachments[] = [
            'file' => null,
            'attchment_type' => '',
            'notes' => '',
        ];
    }

    public function removeNewAttachment($index)
    {
        unset($this->newAttachments[$index]);
        $this->newAttachments = array_values($this->newAttachments);
    }

    public function deleteAttachment($id)
    {
        $attachment = ActivityAttchment::find($id);
        if ($attachment) {
          
            \Storage::disk('public')->delete($attachment->attchment_path);
            $attachment->delete();
        }
        $this->selectActivity($this->selectedactivity->id);
    }

    public function saveAttachments()
    {
        $this->validate([
            'newAttachments.*.file' => 'required|file|max:1024',
            'newAttachments.*.attchment_type' => 'required',
        ]);

        foreach ($this->newAttachments as $item) {
            $path = $item['file']->store('activity-attachments', 'public');
            $this->selectedactivity->attachments()->create([
                'attchment_path' => $path,
                'attchment_type' => $item['attchment_type'],
                'notes' => $item['notes'],
                'status_id' => 1,
            ]);
        }

        $this->newAttachments = [];
        $this->selectActivity($this->selectedactivity->id);
        session()->flash('message', __('Attachments uploaded successfully.'));
    }
    #[Computed()]
    public function activities()
    {
        return Activity::with(['regions', 'cities', 'activityStatus', 'statusSpecificSector'])
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->start_date, fn($q) => $q->where('start_date', $this->start_date))
            ->when($this->status_id, fn($q) => $q->where('status', $this->status_id))
            ->when($this->region_id, fn($q) => $q->where('region', $this->region_id))
            ->when($this->city_id, fn($q) => $q->where('city', $this->city_id))
            ->latest()
            ->paginate(10);
    }



    #[Computed()]
    public function allStatuses()
    {
        return StatusRepo::statuses();
    }


    public function render()
    {
        return view('livewire.org-app.activity.index', [
            'regions' => RegionRepo::regions(),
            'cities' => CityRepo::cities()->where('region_id', $this->region_id),
        ]) ;
    }
}
