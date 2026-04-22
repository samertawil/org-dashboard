<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use App\Models\PurchaseRequisition;
use App\Reposotries\PartnersRepo;
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

use App\Exports\PurchaseRequestExport;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;

    public $search_number = '';
    public $search_year = '';
    public $search_date = '';
    public $search_status_id = '';
    public $search_vendor_id = ''; // For suggested_vendor_ids
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // For Modal
    public ?PurchaseRequisition $selectedPr = null;

    public function showDetails($id)
    {
        $this->selectedPr = PurchaseRequisition::with(['status', 'creator', 'items.unit'])->findOrFail($id);
        $this->dispatch('modal-show', name: 'show-pr-modal');
    }

    public function export()
    {
        $query = PurchaseRequisition::query()
            ->when($this->search_number, fn($q) => $q->where('request_number', 'like', '%' . $this->search_number . '%'))
            ->when($this->search_date, fn($q) => $q->whereDate('request_date', $this->search_date))
            ->when($this->search_status_id, fn($q) => $q->where('status_id', $this->search_status_id))
            ->when($this->search_vendor_id, function($q) {
                $q->where(function ($query) {
                    $query->whereJsonContains('suggested_vendor_ids', (string) $this->search_vendor_id)
                          ->orWhereJsonContains('suggested_vendor_ids', (int) $this->search_vendor_id);
                });
            });

        return Excel::download(new PurchaseRequestExport($query), 'purchase-requisitions.xlsx');
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

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
                $q->where(function ($query) {
                    $query->whereJsonContains('suggested_vendor_ids', (string) $this->search_vendor_id)
                          ->orWhereJsonContains('suggested_vendor_ids', (int) $this->search_vendor_id);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
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
        if(Gate::denies('purchase_request.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $record = PurchaseRequisition::findOrFail($id);
        $record->delete();
        session()->flash('message', __('Purchase Requisition deleted successfully.'));
    }

    public function render()
    {
        return view('livewire.org-app.purchase-request.index');
    }
}
