<?php

namespace App\Observers;

use App\Models\EventAssignee;
use Illuminate\Support\Facades\Cache;

class EventAssigneeObserver
{
    /**
     * Handle the EventAssignee "saved" event (created or updated).
     */
    public function saved(EventAssignee $eventAssignee): void
    {
        $this->clearCache($eventAssignee);
    }

    /**
     * Handle the EventAssignee "deleted" event.
     */
    public function deleted(EventAssignee $eventAssignee): void
    {
        $this->clearCache($eventAssignee);
    }

    /**
     * Clear the cache for the assigned employee's user.
     */
    protected function clearCache(EventAssignee $eventAssignee): void
    {
        // Load the employee to access their user_id
        $eventAssignee->loadMissing('employee');

        if ($eventAssignee->employee && $eventAssignee->employee->user_id) {
            $userId = $eventAssignee->employee->user_id;
            // Clear the specific user's cache
            // Note: We use a wildcard-like approach or just clear the main list key
            // Ideally we'd tag it, but for now we clear the list key "assignees.user_{$userId}.{$id}"
            // Since the Repo uses "assignees.user_{$userId}.{$id}" (where id is null for the list)
            // We clear the list view:
            Cache::forget("assignees.user_{$userId}.");
        }
    }
}
