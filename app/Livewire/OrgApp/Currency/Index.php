<?php

namespace App\Livewire\OrgApp\Currency;

use App\Models\CurrancyValue;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
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

    #[Computed()]
    public function currencies()
    {
        return CurrancyValue::query()
            ->where(function ($query) {
                $query->where('exchange_date', 'like', '%' . $this->search . '%')
                      ->orWhere('currency_value', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function delete($id)
    {
        $currency = CurrancyValue::findOrFail($id);
        $currency->delete();
        session()->flash('message', __('Currency Value successfully deleted.'));
    }

    public function render()
    {
        return view('livewire.org-app.currency.index');
    }
}
