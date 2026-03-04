<?php

namespace App\Livewire\AppSetting\Setting;

use App\Models\Setting;
use App\Traits\SortTrait;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;


class SettingIndex extends Component
{
    use SortTrait;
    use WithPagination;

    public string $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public mixed $editSettingId;
    
    #[Rule('required')]
    public mixed $value;
    public string $key;
    public string|null $description='';
    public string|null $notes='';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
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



    public function destroyValueArray(int $id, int $key): void
    {
        if (Gate::denies('setting.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $setting = Setting::findOrFail($id);
        $valueArray = $setting->value_array;

        if (isset($valueArray[$key])) {
            unset($valueArray[$key]);
            $valueArray = array_values($valueArray);
            $setting->value_array = $valueArray;
            $setting->save();
        }
    }

    public function destroy(int $id): void
    {
        if (Gate::denies('setting.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        Setting::destroy($id);
        session()->flash('message', __('Setting Deleted successfully'));
    }

    public function render(): View
    {
        if (Gate::denies('setting.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $pageTitle = __('setting');

        $settings = Setting::query()
            ->when($this->search, function ($query) {
                $query->where('key', 'like', '%' . $this->search . '%')
                    ->orWhere('value', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.app-setting.setting.setting-index', compact('settings'))
            ->layoutData(['pageTitle' => $pageTitle, 'title' => $pageTitle]);
    }
}
