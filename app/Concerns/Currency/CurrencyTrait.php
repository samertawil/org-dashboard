<?php

namespace App\Concerns\Currency;

use Livewire\Attributes\Validate;

trait CurrencyTrait
{
    #[Validate('required|date')]
    public $exchange_date = '';

    #[Validate('required|numeric')]
    public $currency_value = '';
}
