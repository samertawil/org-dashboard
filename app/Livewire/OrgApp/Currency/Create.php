<?php

namespace App\Livewire\OrgApp\Currency;

use Livewire\Component;
use App\Models\CurrancyValue;
use Illuminate\Validation\Rule;
use App\Concerns\Currency\CurrencyTrait;

class Create extends Component
{

    public $exchange_date = '';
    public $currency_value = '';

    public function rules() 
    {
        return [
            'exchange_date' => [
                'required',
                'date',
                Rule::unique('currancy_values')->where(function ($query) {
                    return $query->where('currency_value', $this->currency_value);
                }),
            ],
            'currency_value' => ['required', 'numeric'],
        ];
    }

    public function save()
    {
        $this->validate();

        CurrancyValue::create([
            'exchange_date' => $this->exchange_date,
            'currency_value' => $this->currency_value,
        ]);

        session()->flash('message', __('Currency Value successfully created.'));

        return $this->redirect(route('currency.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.org-app.currency.create', [
            'heading' => __('Create Currency Value'),
            'type' => 'save',
        ]);
    }
}
