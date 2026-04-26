<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityComments;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseQuotationResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status_id = $request->query('status_id', '');
        $region_id = $request->query('region_id', '');
        $city_id = $request->query('city_id', '');

        $activityQuery = Activity::select('id', DB::raw("'activity' as feed_type"), 'created_at')
            ->when($search, function($q) use ($search) {
                $q->where(function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%')
                       ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($status_id, function ($q) use ($status_id) {
                $today = now()->toDateString();
                $q->where(function ($query) use ($today, $status_id) {
                    $query->where('status', $status_id);
                    $query->orWhere(function ($subQuery) use ($today, $status_id) {
                        $subQuery->whereNull('status');
                        match ((int) $status_id) {
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
            ->when($region_id, fn($q) => $q->where('region', $region_id))
            ->when($city_id, fn($q) => $q->where('city', $city_id));

        $prQuery = PurchaseRequisition::select('id', DB::raw("'pr' as feed_type"), 'created_at')
            ->when($search, function($q) use ($search) {
                $q->where(function($sq) use ($search) {
                    $sq->where('request_number', 'like', '%' . $search . '%')
                       ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($status_id, fn($q) => $q->where('status_id', $status_id))
            // PRs don't have region/city usually, so we don't filter them by these unless needed
            ;

        $quotationQuery = PurchaseQuotationResponse::select('id', DB::raw("'quotation' as feed_type"), 'created_at')
            ->when($search, function($q) use ($search) {
                $q->whereHas('vendor', fn($vq) => $vq->where('name', 'like', '%' . $search . '%'))
                  ->orWhereHas('purchaseRequisition', fn($pq) => $pq->where('request_number', 'like', '%' . $search . '%'));
            })
            // Quotations don't have these filters directly
            ;

        $combined = DB::table(function ($query) use ($activityQuery, $prQuery, $quotationQuery) {
            $query->from($activityQuery->union($prQuery)->union($quotationQuery), 'combined_feed');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        $grouped = $combined->getCollection()->groupBy('feed_type');
        $activities = collect();
        $allPrs = collect();
        $quotations = collect();

        // 1. Fetch Activities
        if (isset($grouped['activity'])) {
            $activities = Activity::with([
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

        // 3. Batch Fetch ALL needed Purchase Requisitions
        $standAlonePrIds = isset($grouped['pr']) ? $grouped['pr']->pluck('id') : collect();
        $relatedPrIds = $activities->flatMap(fn($a) => $a->parcels->pluck('purchase_requisition_id'))->filter()->unique();
        $allPrIds = $standAlonePrIds->concat($relatedPrIds)->unique();

        if ($allPrIds->isNotEmpty()) {
            $allPrs = PurchaseRequisition::with(['status', 'creator', 'items.unit', 'quotations.vendor'])
            ->whereIn('id', $allPrIds)->get()->keyBy('id');
        }

        $items = $combined->getCollection()->map(function ($item) use ($activities, $allPrs, $quotations) {
            $data = null;
            if ($item->feed_type === 'activity') {
                $data = $activities->get($item->id);
            } elseif ($item->feed_type === 'pr') {
                $data = $allPrs->get($item->id);
            } else {
                $data = $quotations->get($item->id);
            }
            
            if ($data) {
                return [
                    'feed_type' => $item->feed_type,
                    'created_at' => $item->created_at,
                    'data' => $data
                ];
            }
            return null;
        })->filter()->values();

        return response()->json([
            'items' => $items,
            'meta' => [
                'current_page' => $combined->currentPage(),
                'last_page' => $combined->lastPage(),
                'total' => $combined->total(),
            ]
        ]);
    }

    public function addComment(Request $request, $activityId)
    {
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        $comment = ActivityComments::create([
            'activity_id' => $activityId,
            'comment' => $request->comment,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($comment->load('creator'));
    }
}
