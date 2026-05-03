<?php

namespace App\Livewire\OrgApp\Activity;

use App\Exports\ActivityBeneficiaryNamesExport;
use App\Models\Activity;
use App\Models\ActivityComments;
use App\Models\PurchaseQuotationResponse;
use App\Models\PurchaseRequisition;
use App\Reposotries\CityRepo;
use App\Reposotries\employeeRepo;
use App\Reposotries\RegionRepo;
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Feed extends Component
{
    use WithPagination;

    public $search = '';
    public $status_id = '';
    public $region_id = '';
    public $city_id = '';
    public $newCommentText = [];

    /** Populated in mount() — passed to Alpine for @mention */
    public array $mentionableUsers = [];

    public function mount(): void
    {
        $this->mentionableUsers = \App\Models\Employee::query()
            ->whereNotNull('user_id')
            ->where('activation', \App\Enums\GlobalSystemConstant::ACTIVE->value)
            ->where('user_id', '!=', auth()->id())
            ->with('user:id,name')
            ->get()
            ->filter(fn($e) => $e->user !== null)
            ->map(fn($e) => [
                'id'   => $e->user->id,
                'name' => $e->full_name ?? $e->user->name,
            ])
            ->values()
            ->toArray();
    }

    public ?PurchaseRequisition $selectedPr = null;
    public ?\App\Models\PurchaseQuotationResponse $selectedQuotation = null;

    public function showDetails($id)
    {
        $this->selectedPr = PurchaseRequisition::with(['status', 'creator', 'items.unit'])->findOrFail($id);
        $this->dispatch('modal-show', name: 'show-pr-modal');
    }

    public function showQuotationDetails($id)
    {
        $this->selectedQuotation = \App\Models\PurchaseQuotationResponse::with(['vendor', 'purchaseRequisition', 'prices.requisitionItem.unit', 'currency'])->findOrFail($id);
        $this->dispatch('modal-show', name: 'show-quotation-modal');
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
    public function feedItems()
    {
        $activityQuery = \App\Models\Activity::select('id', \Illuminate\Support\Facades\DB::raw("'activity' as feed_type"), 'created_at')
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
            ->when($this->city_id, fn($q) => $q->where('city', $this->city_id));

        $prQuery = PurchaseRequisition::select('id', \Illuminate\Support\Facades\DB::raw("'pr' as feed_type"), 'created_at')
            ->when($this->search, fn($q) => $q->where('request_number', 'like', '%' . $this->search . '%')
                                               ->orWhere('description', 'like', '%' . $this->search . '%'))
            ->when($this->status_id || $this->region_id || $this->city_id, fn($q) => $q->whereRaw('1=0'));

        $quotationQuery = PurchaseQuotationResponse::select('id', \Illuminate\Support\Facades\DB::raw("'quotation' as feed_type"), 'created_at')
            ->when($this->search, function($q) {
                $q->whereHas('vendor', fn($vq) => $vq->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('purchaseRequisition', fn($pq) => $pq->where('request_number', 'like', '%' . $this->search . '%'));
            })
            ->when($this->status_id || $this->region_id || $this->city_id, fn($q) => $q->whereRaw('1=0'));

        $combined = $activityQuery->union($prQuery)->union($quotationQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $grouped = $combined->getCollection()->groupBy('feed_type');
        $activities = collect();
        $allPrs = collect();
        $quotations = collect();

        // 1. Fetch Activities
        if (isset($grouped['activity'])) {
            $activities = \App\Models\Activity::with([
                'regions', 'cities', 'activityStatus', 'statusSpecificSector', 'attachments', 'creator',
                'beneficiaries.beneficiaryType', 'parcels.parcelType', 'parcels.unit',
                'workTeams.employeeRel.user', 'workTeams.missionTitle', 'comments.creator'
            ])->withCount(['attachments', 'beneficiaryNames'])
            ->withAvg('feedbacks', 'rating')
            ->whereIn('id', $grouped['activity']->pluck('id'))->get()->keyBy('id');
        }

        // 2. Fetch Quotations
        if (isset($grouped['quotation'])) {
            $quotations = PurchaseQuotationResponse::with(['vendor', 'purchaseRequisition', 'status', 'currency'])
            ->whereIn('id', $grouped['quotation']->pluck('id'))->get()->keyBy('id');
        }

        // 3. Batch Fetch ALL needed Purchase Requisitions (Standalone + Related to Activities)
        $standAlonePrIds = isset($grouped['pr']) ? $grouped['pr']->pluck('id') : collect();
        $relatedPrIds = $activities->flatMap(fn($a) => $a->parcels->pluck('purchase_requisition_id'))->filter()->unique();
        $allPrIds = $standAlonePrIds->concat($relatedPrIds)->unique();

        if ($allPrIds->isNotEmpty()) {
            $allPrs = PurchaseRequisition::with(['status', 'creator', 'items.unit', 'quotations.vendor'])
            ->whereIn('id', $allPrIds)->get()->keyBy('id');

            // Manually link PRs to Activity Parcels to avoid extra queries
            foreach ($activities as $activity) {
                foreach ($activity->parcels as $parcel) {
                    if ($parcel->purchase_requisition_id && $allPrs->has($parcel->purchase_requisition_id)) {
                        $parcel->setRelation('purchaseRequisition', $allPrs->get($parcel->purchase_requisition_id));
                    }
                }
            }
        }

        $items = $combined->getCollection()->map(function ($item) use ($activities, $allPrs, $quotations) {
            if ($item->feed_type === 'activity') {
                return $activities->get($item->id);
            } elseif ($item->feed_type === 'pr') {
                return $allPrs->get($item->id);
            } else {
                return $quotations->get($item->id);
            }
        })->filter();

        $combined->setCollection($items);
        return $combined;
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

        $activity = Activity::findOrFail($activityId);

        $comment = ActivityComments::create([
            'activity_id' => $activityId,
            'comment' => $this->newCommentText[$activityId],
            'created_by' => auth()->id(),
        ]);

        $this->processMentions($activity, $comment);

        $this->newCommentText[$activityId] = '';
    }

    private function processMentions(Activity $activity, ActivityComments $comment): void
    {
        $text = $comment->comment;
        $mentionableUsers = employeeRepo::mentionEmp();
        $notifiedUserIds = [];

        // Sort by length descending to match full names before partial names
        $employees = collect($mentionableUsers)->sortByDesc(fn($e) => mb_strlen($e['name']));

        foreach ($employees as $employee) {
            $name = $employee['name'];
            $mention = '@' . $name;
            
            if (mb_strpos($text, $mention) !== false || mb_strpos($text, $name) !== false) {
                $userId = $employee['id'];
                
                if ($userId && $userId != auth()->id() && !in_array($userId, $notifiedUserIds)) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->notify(new \App\Notifications\MentionInCommentNotification($activity, $comment, auth()->user()));
                        $notifiedUserIds[] = $userId;
                    }
                }
            }
        }
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
