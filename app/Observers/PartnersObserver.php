<?php

namespace App\Observers;

use App\Models\PartnerInstitution;
use Illuminate\Support\Facades\Cache;

class PartnersObserver
{
    /**
     * Handle the PartnerInstitution "created" event.
     */
    public function created(PartnerInstitution $partnerInstitution): void
    {
       Cache::forget('partners-all');
    }

    /**
     * Handle the PartnerInstitution "updated" event.
     */
    public function updated(PartnerInstitution $partnerInstitution): void
    {
        Cache::forget('partners-all');
    }

    /**
     * Handle the PartnerInstitution "deleted" event.
     */
    public function deleted(PartnerInstitution $partnerInstitution): void
    {
        Cache::forget('partners-all');
    }

    /**
     * Handle the PartnerInstitution "restored" event.
     */
    public function restored(PartnerInstitution $partnerInstitution): void
    {
        //
    }

    /**
     * Handle the PartnerInstitution "force deleted" event.
     */
    public function forceDeleted(PartnerInstitution $partnerInstitution): void
    {
        //
    }
}
