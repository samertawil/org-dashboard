<?php

namespace App\Concerns;

use App\Reposotries\StudentGroupRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;

trait AccessibleGroupsTrait
{
    /**
     * Get the student groups accessible by the current user.
     */
    #[Computed()]
    public function accessibleGroups()
    {
        return StudentGroupRepo::educationPoints();
    }

    /**
     * Get the student group IDs accessible by the current teacher.
     * Returns null for super admins (no restriction).
     */
    #[Computed()]
    public function accessibleGroupIds()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin() || Gate::allows('select.any.student')) {
            return null;
        }
        return $this->accessibleGroups->pluck('id')->toArray();
    }

    /**
     * Available batches filtered by teacher's accessible groups.
     */
    #[Computed()]
    public function availableBatches()
    {
        return $this->accessibleGroups->pluck('batch_no')->filter()->unique()->values()->toArray();
    }
}
