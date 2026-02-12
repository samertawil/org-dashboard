<?php

namespace App\Reposotries;

 
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Cache;
 

class PurchaseRequisitionRepo
{
    public static function purchases()
    {
        return Cache::rememberForever('PurchaseRequisition-all', function () {
            return PurchaseRequisition::get();
        });
    }

    
}
