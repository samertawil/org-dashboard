<?php

namespace App\Livewire\OrgApp\Gallery;

use Livewire\Component;

class Index extends Component
{
    use \Livewire\WithPagination;

    public function render()
    {
        $activities = \App\Models\Activity::query()
            ->with(['attachments' => function($q) {
                // $q->latest()->take(5); // Removed take(5) to fix window function SQL error
                $q->latest();
            }, 'attachments.attachmentType', 'activityStatus', 'statusSpecificSector'])
            ->whereHas('attachments') // Only activities with files
            ->latest('start_date')
            ->paginate(12);

        return view('livewire.org-app.gallery.index', [
            'activities' => $activities,
        ]);
    }
}
