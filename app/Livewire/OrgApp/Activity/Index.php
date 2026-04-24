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
use App\Models\PurchaseRequisition;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;
use App\Services\UploadingFilesServices;
use App\Exports\ActivityBeneficiaryNamesExport;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $start_date = '';
    public $status_id = '';
    public $region_id = '';
    public $city_id = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

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

    public ?PurchaseRequisition $selectedPr = null;

    public function showDetails($id)
    {
        $this->selectedPr = PurchaseRequisition::with(['status', 'creator', 'items.unit'])->findOrFail($id);
        $this->dispatch('modal-show', name: 'show-pr-modal');
    }

    public ?Activity $selectedactivity = null;
    public $attachments = [];
    public $newAttachments = [];

    public ?Activity $selectedActivityForBeneficiaries = null;
    public $beneficiarySearch = '';

    public function showBeneficiaries($id)
    {
        $this->beneficiarySearch = '';
        $this->selectedActivityForBeneficiaries = Activity::findOrFail($id);
        $this->dispatch('modal-show', name: 'beneficiaries-modal');
    }

    #[Computed()]
    public function selectedActivityBeneficiaries()
    {
        if (!$this->selectedActivityForBeneficiaries) return collect();

        return $this->selectedActivityForBeneficiaries->beneficiaryNames()
            ->with('status')
            ->when($this->beneficiarySearch, function($query) {
                $query->where('full_name', 'like', '%' . $this->beneficiarySearch . '%');
            })
            ->get();
    }

    public function exportBeneficiaries($id)
    {
        $activity = Activity::findOrFail($id);
        return Excel::download(new ActivityBeneficiaryNamesExport($id), 'beneficiaries-' . $activity->name . '.xlsx');
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'start_date' => ['except' => ''],
        'status_id' => ['except' => ''],
        'region_id' => ['except' => ''],
        'city_id' => ['except' => ''],
    ];

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

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
            $file = $item['file'];
            $mimeType = $file->getMimeType();
            
            // Use compression for images, standard upload for other files
            if (str_starts_with($mimeType, 'image/')) {
                $path = UploadingFilesServices::uploadAndCompress($file, 'activity-attachments', 'public', 1);
            } else {
                $path = UploadingFilesServices::uploadSingleFile($file, 'activity-attachments', 'public');
            }

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
        return Activity::with(['regions', 'cities', 'activityStatus', 'statusSpecificSector', 'attachments', 'parcels.purchaseRequisition'])
            ->withCount(['attachments', 'beneficiaryNames'])
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
                            26 => $subQuery->whereDoesntHave('attachments')
                                ->where(function($q) use ($today) {
                                    $q->where('start_date', $today)
                                      ->orWhere(function($sq) use ($today) {
                                          $sq->where('start_date', '<', $today)
                                            ->where('end_date', '>', $today);
                                      });
                                }), // In Progress
                            28 => $subQuery->whereDoesntHave('attachments')
                                ->where(function($q) use ($today) {
                                    $q->where(function($sq) use ($today) {
                                        $sq->where('start_date', '<', $today)
                                          ->where(function($esq) use ($today) {
                                              $esq->where('end_date', '<=', $today)
                                                ->orWhereNull('end_date');
                                          });
                                    })->orWhereNull('start_date');
                                }), // Need Procedure / On Hold
                            default => $subQuery->whereRaw('1=0'), // No match for other IDs
                        };
                    });
                });
            })
            ->when($this->region_id, fn($q) => $q->where('region', $this->region_id))
            ->when($this->city_id, fn($q) => $q->where('city', $this->city_id))
            ->orderBy($this->sortField, $this->sortDirection)
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
