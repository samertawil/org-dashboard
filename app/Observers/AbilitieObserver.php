<?php

namespace App\Observers;

use App\Models\Ability;
use Illuminate\Support\Facades\Cache;

class AbilitieObserver
{
    /**
     * Handle the Ability "created" event.
     */
    public function created(Ability $ability): void
    {
       Cache::forget('Abilities-all');
    }

    /**
     * Handle the Ability "updated" event.
     */
    public function updated(Ability $ability): void
    {
        Cache::forget('Abilities-all');
    }

    /**
     * Handle the Ability "deleted" event.
     */
    public function deleted(Ability $ability): void
    {
        Cache::forget('Abilities-all');
    }

    /**
     * Handle the Ability "restored" event.
     */
    public function restored(Ability $ability): void
    {
        //
    }

    /**
     * Handle the Ability "force deleted" event.
     */
    public function forceDeleted(Ability $ability): void
    {
        //
    }
}
