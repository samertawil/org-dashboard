<?php

namespace App\Reposotries;

use App\Models\PartnerInstitution;
use Illuminate\Support\Facades\Cache;

class PartnersRepo
{

    public static function partners() {
     return   Cache::rememberForever('partners-all', function () {
        return PartnerInstitution::get();
        });
       
    }
}
