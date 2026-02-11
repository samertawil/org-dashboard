<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseRequisition;
use App\Reposotries\StatusRepo;
use App\Reposotries\PartnersRepo;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    public $search_number = '';
    public $search_year = '';
    public $search_date = '';
    public $search_status_id = '';
    public $search_vendor_id = ''; // For suggested_vendor_ids

    public function updatingSearchNumber() { $this->resetPage(); }
    public function updatingSearchYear() { $this->resetPage(); }
    public function updatingSearchDate() { $this->resetPage(); }
    public function updatingSearchStatusId() { $this->resetPage(); }
    public function updatingSearchVendorId() { $this->resetPage(); }

    #[Computed]
    public function purchaseRequisitions()
    {
        return PurchaseRequisition::with(['status', 'creator'])
            ->when($this->search_number, fn($q) => $q->where('request_number', 'like', '%' . $this->search_number . '%'))
            // ->when($this->search_year, fn($q) => $q->whereYear('request_year', $this->search_year))
            ->when($this->search_date, fn($q) => $q->whereDate('request_date', $this->search_date))
            ->when($this->search_status_id, fn($q) => $q->where('status_id', $this->search_status_id))
            ->when($this->search_vendor_id, function($q) {
                 // MySQL helper for JSON search if it's a JSON column
                 // Or if it's casted, raw JSON search might be needed
                 // Assuming JSON column 'suggested_vendor_ids' stores IDs like [1, 2]
                 // JSON_CONTAINS(suggested_vendor_ids, '1')
                 $q->whereJsonContains('suggested_vendor_ids', $this->search_vendor_id);
                 // Note: Ensure the ID is passed as string or int depending on storage. JSON_CONTAINS expects string for the needle if purely searching text, but for JSON array it handles strict types.
                 // Usually casting to string is safer for JSON_CONTAINS on typical columns
            })
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function statuses()
    {
        return StatusRepo::statuses();
    }

    #[Computed]
    public function partners()
    {
        return PartnersRepo::partners();
    }

    public function delete($id)
    {
        $record = PurchaseRequisition::findOrFail($id);
        $record->delete();
        session()->flash('message', __('Purchase Requisition deleted successfully.'));
    }

    public function render()
    {
        return view('livewire.org-app.purchase-request.index');
    }
}
