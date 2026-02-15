<?php

namespace App\Livewire\OrgApp\Activity;


use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;
use App\Reposotries\CityRepo;
use Livewire\WithFileUploads;
use App\Reposotries\RegionRepo;
use App\Reposotries\StatusRepo;
use App\Models\ActivityAttchment;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $start_date = '';
    public $status_id = '';
    public $region_id = '';
    public $city_id = '';

    protected $listeners = [
        'refresh-data' => '$refresh',
       
    ];

    public $selectedactivityIdForShowModal;

    public function openShowModal($projectId)
    {
        $this->selectedactivityIdForShowModal = $projectId;
        $this->dispatch('open-modal', name: 'activity-show-' . $projectId);
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
        if(Gate::denies('activity.create')){
            return abort(403,'You do not have the necessary permissions');
        }
        $activity = Activity::findOrFail($id);
        $activity->delete();
        session()->flash('message', __('Activity successfully deleted.'));
    }

    public function selectActivity($activityId)
    {
        $this->selectedactivity = Activity::with('attachments.attachmentType')->find($activityId);
        $this->attachments = $this->selectedactivity->attachments->toArray();
        $this->newAttachments = [];
    }

    public function addAttachment()
    {
        if(Gate::denies('activity.create')){
            return abort(403,'You do not have the necessary permissions');
        }
        $this->newAttachments[] = [
            'file' => null,
            'attchment_type' => '',
            'notes' => '',
        ];
    }

    public function removeNewAttachment($index)
    {
        if(Gate::denies('activity.create')){
            return abort(403,'You do not have the necessary permissions');
        }
        unset($this->newAttachments[$index]);
        $this->newAttachments = array_values($this->newAttachments);
    }

    public function deleteAttachment($id)
    {
        if(Gate::denies('activity.create')){
            return abort(403,'You do not have the necessary permissions');
        }
        $attachment = ActivityAttchment::find($id);
        if ($attachment) {

            \Storage::disk('public')->delete($attachment->attchment_path);
            $attachment->delete();
        }
        $this->selectActivity($this->selectedactivity->id);
    }

    public function saveAttachments()
    {
        if(Gate::denies('activity.create')){
            return abort(403,'You do not have the necessary permissions');
        }
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
        return Activity::with(['regions', 'cities', 'activityStatus', 'statusSpecificSector', 'attachments'])
            ->withCount('attachments')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->start_date, fn($q) => $q->where('start_date', $this->start_date))
            ->when($this->status_id, function ($q) {
                $today = now()->toDateString();

                // Merged logic: Filter for rows with this status ID OR rows with NULL status that logically match it
                $q->where(function ($query) use ($today) {
                    $query->where('status', $this->status_id);

                    $query->orWhere(function ($subQuery) use ($today) {
                        $subQuery->whereNull('status');
                        match ((int) $this->status_id) {
                            27 => $subQuery->whereHas('attachments'), // Completed
                            25 => $subQuery->whereDoesntHave('attachments')->where('start_date', '>', $today), // Planned
                            26 => $subQuery->whereDoesntHave('attachments')->where('start_date', '=', $today), // In Progress
                            28 => $subQuery->whereDoesntHave('attachments')->where('start_date', '<', $today), // On Hold / Undefined
                            default => $subQuery->whereRaw('1=0'), // No match for other IDs
                        };
                    });
                });
            })
            ->when($this->region_id, fn($q) => $q->where('region', $this->region_id))
            ->when($this->city_id, fn($q) => $q->where('city', $this->city_id))
            ->latest()
            ->paginate(10);
    }

    public function getNullstatus() {}

    #[Computed()]
    public function allStatuses()
    {
        return StatusRepo::statuses();
    }


    public function render()
    {
        if(Gate::denies('activity.index')){
            return abort(403,'You do not have the necessary permissions');
        }
        return view('livewire.org-app.activity.index', [
            'regions' => RegionRepo::regions(),
            'cities' => CityRepo::cities()->where('region_id', $this->region_id),
        ]);
    }
}
