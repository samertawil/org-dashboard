<?php

namespace App\Livewire\OrgApp\Gallery;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityAttchment;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = ''; // '' = all, 'image', 'document', 'media'
    public $filterSource = ''; // '' = all, 'activity', 'purchase_request'

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterSource' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedFilterSource()
    {
        $this->resetPage();
    }

    public function getGenericAttachmentsProperty()
    {
        // 1. Fetch Activity & Subject Learning Attachments
        $genericAttachments = collect();
        if ($this->filterSource === '' || $this->filterSource === 'activity' || $this->filterSource === 'subject_learning') {
            $query = ActivityAttchment::with(['activity', 'subjectLearning'])
                ->latest();
            
            if ($this->filterSource === 'activity') {
                $query->whereNotNull('activity_id');
            } elseif ($this->filterSource === 'subject_learning') {
                $query->whereNotNull('subject_learning_id');
            }

            $genericAttachments = $query->get()->map(function ($item) {
                    $ext = strtolower(pathinfo($item->attchment_path, PATHINFO_EXTENSION));
                    $typeId = 49;
                     if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                        $typeId = 48;
                    } elseif (in_array($ext, ['mp4', 'avi', 'mov', 'wmv', 'mp3', 'wav', 'ogg'])) {
                        $typeId = 50;
                    }

                    // Determine Source and Title
                    $source = 'Unknown';
                    $sourceTitle = '-';
                    $sourceId = null;

                    if ($item->activity_id) {
                        $source = 'Activity';
                        $sourceId = $item->activity_id;
                        $sourceTitle = $item->activity->name ?? 'Activity #' . $item->activity_id;
                    } elseif ($item->subject_learning_id) {
                        $source = 'Subject Learning';
                        $sourceId = $item->subject_learning_id;
                        $sourceTitle = $item->subjectLearning->name ?? 'Subject #' . $item->subject_learning_id;
                    }

                    return [
                        'id' => 'gen-' . $item->id,
                        'path' => $item->attchment_path,
                        'name' => $item->notes ?? basename($item->attchment_path),
                        'extension' => $ext,
                        'type_id' => $item->attchment_type ?? $typeId, // Fallback
                        'source' => $source,
                        'source_id' => $sourceId,
                        'source_title' => $sourceTitle,
                        'date' => $item->created_at,
                        'uploaded_at' => $item->created_at,
                    ];
                });
        }

        // 2. Fetch Purchase Request Attachments (JSON)
        $prAttachments = collect();
        if ($this->filterSource === '' || $this->filterSource === 'purchase_request') {
            $prRequests = PurchaseRequisition::whereNotNull('attachments')
                ->latest()
                ->get();

            foreach ($prRequests as $pr) {
                if (is_array($pr->attachments)) {
                    foreach ($pr->attachments as $att) {
                        // Ensure required fields exist
                        if (!isset($att['path'])) continue;

                        $ext = strtolower($att['extension'] ?? pathinfo($att['path'], PATHINFO_EXTENSION));
                        // Logic to determine type_id if not present
                         $typeId = $att['type_id'] ?? 49;
                         if (!isset($att['type_id'])) {
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                                $typeId = 48;
                            } elseif (in_array($ext, ['mp4', 'avi', 'mov', 'wmv', 'mp3', 'wav', 'ogg'])) {
                                $typeId = 50;
                            }
                        }

                        $prAttachments->push([
                            'id' => 'pr-' . $pr->id . '-' . md5($att['path']), // unique fake ID
                            'path' => $att['path'],
                            'name' => $att['name'] ?? basename($att['path']),
                            'extension' => $ext,
                            'type_id' => $typeId,
                            'source' => 'Purchase Request',
                            'source_id' => $pr->id,
                            'source_title' => 'PR #' . $pr->request_number,
                            'date' => $att['uploaded_at'] ?? $pr->created_at,
                            'uploaded_at' => $att['uploaded_at'] ?? $pr->created_at,
                        ]);
                    }
                }
            }
        }

        // 3. Merge and Sort
        $all = $genericAttachments->merge($prAttachments)->sortByDesc('uploaded_at');

        // 4. Apply Filters (Search & Type)
        // Search
        if ($this->search) {
            $all = $all->filter(function ($item) {
                return stripos($item['name'], $this->search) !== false || 
                       stripos($item['source_title'], $this->search) !== false;
            });
        }

        // Filter Type (Image=48, File=49, Media=50)
        if ($this->filterType) {
            $all = $all->filter(function ($item) {
                return $item['type_id'] == $this->filterType;
            });
        }

        return $all;
    }

    public function render()
    {
        // Custom Pagination for merged collection
        $items = $this->genericAttachments;
        $perPage = 24;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedItems = new LengthAwarePaginator($currentItems, count($items), $perPage);
        $paginatedItems->setPath(request()->url());

        // Get Statuses for filter sidebar
        $statuses = \App\Reposotries\StatusRepo::statuses()->where('p_id_sub', config('appConstant.attchment_types', 47));

        return view('livewire.org-app.gallery.index', [
            'attachments' => $paginatedItems,
            'allStatuses' => $statuses
        ]);
    }
}
