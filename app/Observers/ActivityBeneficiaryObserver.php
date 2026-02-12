<?php

namespace App\Observers;

use App\Models\ActivityBeneficiary;
use Illuminate\Support\Facades\Cache;

class ActivityBeneficiaryObserver
{
    /**
     * Handle the ActivityBeneficiary "created" event.
     */
    public function created(ActivityBeneficiary $activityBeneficiary): void
    {
        Cache::forget('Beneficiary-all');
    }

    /**
     * Handle the ActivityBeneficiary "updated" event.
     */
    public function updated(ActivityBeneficiary $activityBeneficiary): void
    {
        Cache::forget('Beneficiary-all');
    }

    /**
     * Handle the ActivityBeneficiary "deleted" event.
     */
    public function deleted(ActivityBeneficiary $activityBeneficiary): void
    {
        Cache::forget('Beneficiary-all');
    }

    /**
     * Handle the ActivityBeneficiary "restored" event.
     */
    public function restored(ActivityBeneficiary $activityBeneficiary): void
    {
        //
    }

    /**
     * Handle the ActivityBeneficiary "force deleted" event.
     */
    public function forceDeleted(ActivityBeneficiary $activityBeneficiary): void
    {
        //
    }
}
