<?php

namespace App\Reposotries;


use App\Models\ActivityBeneficiary;
use Illuminate\Support\Facades\Cache;

class ActivityBeneficiaryRepo
{
    public static function beneficiaries()
    {
        return Cache::rememberForever('Beneficiary-all', function () {
            return ActivityBeneficiary::get();
        });
    }
}
