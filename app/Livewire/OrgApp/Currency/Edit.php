<?php

namespace App\Livewire\OrgApp\Currency;

use Livewire\Component;
use App\Models\CurrancyValue;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use App\Concerns\Currency\CurrencyTrait;

class Edit extends Component
{
    public $exchange_date = '';
    public $currency_value = '';

    public CurrancyValue $currency;

    public function mount(CurrancyValue $currency)
    {
        $this->currency = $currency;
        $this->exchange_date = $currency->exchange_date;
        $this->currency_value = $currency->currency_value;
    }

    public function rules() 
    {
        return [
            'exchange_date' => [
                'required',
                'date',
                Rule::unique('currancy_values')->where(function ($query) {
                    return $query->where('currency_value', $this->currency_value);
                })->ignore($this->currency->id),
            ],
            'currency_value' => ['required', 'numeric'],
        ];
    }

    public function save()
    {
        $this->validate();

        $this->currency->update([
            'exchange_date' => $this->exchange_date,
            'currency_value' => $this->currency_value,
        ]);

        session()->flash('message', __('Currency Value successfully updated.'));

        return $this->redirect(route('currency.index'), navigate: true);
    }

    public function render()
    {
        
        if(Gate::denies('currency.create')){
            abort(403,'You do not have the necessary permissions.');
        }
        
        return view('livewire.org-app.currency.edit', [
            'heading' => __('Edit Currency Value'),
            'type' => 'save',
        ]);
    }
}
