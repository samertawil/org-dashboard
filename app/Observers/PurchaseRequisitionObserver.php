<?php

namespace App\Observers;

use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Cache;

class PurchaseRequisitionObserver
{
    /**
     * Handle the PurchaseRequisition "created" event.
     */
    public function created(PurchaseRequisition $purchaseRequisition): void
    {
      Cache::forget('PurchaseRequisition-all');
    }

    /**
     * Handle the PurchaseRequisition "updated" event.
     */
    public function updated(PurchaseRequisition $purchaseRequisition): void
    {
        Cache::forget('PurchaseRequisition-all');
    }

    /**
     * Handle the PurchaseRequisition "deleted" event.
     */
    public function deleted(PurchaseRequisition $purchaseRequisition): void
    {
        Cache::forget('PurchaseRequisition-all');
    }

    /**
     * Handle the PurchaseRequisition "restored" event.
     */
    public function restored(PurchaseRequisition $purchaseRequisition): void
    {
        //
    }

    /**
     * Handle the PurchaseRequisition "force deleted" event.
     */
    public function forceDeleted(PurchaseRequisition $purchaseRequisition): void
    {
        //
    }
}
