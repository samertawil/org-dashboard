<?php

namespace App\Livewire\OrgApp\Gallery;

use Livewire\Component;

class Index extends Component
{
    use \Livewire\WithPagination;

    public function render()
    {
        $query = \App\Models\ActivityAttchment::query()
            ->where('attchment_type', 48)
            ->whereNotNull('activity_id')
            ->with(['project' => function ($query) {
                $query->select('id', 'name', 'start_date', 'end_date', 'sector_id')
                      ->with(['statusSpecificSector:id,status_name']);
            }])
            ->latest();

        $images = $query->paginate(20);

        $slides = $images->getCollection()->map(fn($img) => [
            'id' => $img->id,
            'url' => asset('storage/' . $img->attchment_path),
            'activity_name' => $img->project->name ?? __('Unknown Activity'),
            'activity_id' => $img->project->id ?? '',
            'start_date' => $img->project->start_date ?? '-',
            'end_date' => $img->project->end_date ?? '-',
            'sector' => $img->project->statusSpecificSector->status_name ?? '-'
        ])->values();

        return view('livewire.org-app.gallery.index', [
            'images' => $images,
            'slides' => $slides
        ]);
    }
}
