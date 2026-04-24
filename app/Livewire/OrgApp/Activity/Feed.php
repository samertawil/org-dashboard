<?php

namespace App\Livewire\OrgApp\Activity;

use App\Models\Activity;
use App\Models\ActivityComments;
use App\Models\PurchaseRequisition;
use App\Exports\ActivityBeneficiaryNamesExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Reposotries\CityRepo;
use App\Reposotries\RegionRepo;
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Feed extends Component
{
    use WithPagination;

    public $search = '';
    public $status_id = '';
    public $region_id = '';
    public $city_id = '';
    public $newCommentText = [];

    public ?PurchaseRequisition $selectedPr = null;

    public function showDetails($id)
    {
        $this->selectedPr = PurchaseRequisition::with(['status', 'creator', 'items.unit'])->findOrFail($id);
        $this->dispatch('modal-show', name: 'show-pr-modal');
    }

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
        'status_id' => ['except' => ''],
        'region_id' => ['except' => ''],
        'city_id' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function activities()
    {
        return Activity::with([
            'regions', 
            'cities', 
            'activityStatus', 
            'statusSpecificSector', 
            'attachments', 
            'creator',
            'beneficiaries.beneficiaryType',
            'parcels.parcelType',
            'parcels.unit',
            'workTeams.employeeRel.user',
            'workTeams.missionTitle',
            'comments.creator',

            
        ])
            ->withCount(['attachments', 'beneficiaryNames'])
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
                                              ->orWhere('description', 'like', '%' . $this->search . '%'))
            ->when($this->status_id, function ($q) {
                $today = now()->toDateString();
                $q->where(function ($query) use ($today) {
                    $query->where('status', $this->status_id);
                    $query->orWhere(function ($subQuery) use ($today) {
                        $subQuery->whereNull('status');
                        match ((int) $this->status_id) {
                            27 => $subQuery->whereHas('attachments'),
                            25 => $subQuery->whereDoesntHave('attachments')->where('start_date', '>', $today),
                            26 => $subQuery->whereDoesntHave('attachments')->where(function($q) use ($today) {
                                    $q->where('start_date', $today)->orWhere(function($sq) use ($today) {
                                          $sq->where('start_date', '<', $today)->where('end_date', '>', $today);
                                      });
                                }),
                            28 => $subQuery->whereDoesntHave('attachments')->where(function($q) use ($today) {
                                    $q->where(function($sq) use ($today) {
                                        $sq->where('start_date', '<', $today)->where(function($esq) use ($today) {
                                              $esq->where('end_date', '<=', $today)->orWhereNull('end_date');
                                          });
                                    })->orWhereNull('start_date');
                                }),
                            default => $subQuery->whereRaw('1=0'),
                        };
                    });
                });
            })
            ->when($this->region_id, fn($q) => $q->where('region', $this->region_id))
            ->when($this->city_id, fn($q) => $q->where('city', $this->city_id))
            ->orderBy('created_at', 'desc')
            ->paginate(30);
    }

    #[Computed()]
    public function allStatuses()
    {
        return StatusRepo::statuses();
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

    public function addComment($activityId)
    {
        $this->validate([
            'newCommentText.' . $activityId => 'required|string|max:500',
        ], [
            'newCommentText.' . $activityId . '.required' => __('The comment cannot be empty.'),
        ]);

        ActivityComments::create([
            'activity_id' => $activityId,
            'comment' => $this->newCommentText[$activityId],
            'created_by' => auth()->id(),
        ]);

        $this->newCommentText[$activityId] = '';
    }

    public function deleteComment($commentId)
    {
        $comment = ActivityComments::findOrFail($commentId);
        
        if ($comment->created_by !== auth()->id() && Gate::denies('activity.create')) {
            return abort(403);
        }

        $comment->delete();
    }


    #[Layout('layouts.app.land')]
    #[Title('Activity Timeline')]
    public function render()
    {
        if(Gate::denies('activity.index')){
            return abort(403,'You do not have the necessary permissions');
        }

        return view('livewire.org-app.activity.feed', [
            'regions' => RegionRepo::regions(),
            'cities' => CityRepo::cities()->where('region_id', $this->region_id),
        ])->layoutData(['mode' => 'light']);
    }
}
