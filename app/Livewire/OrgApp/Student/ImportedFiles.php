<?php

namespace App\Livewire\OrgApp\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

class ImportedFiles extends Component
{
    public function download($path)
    {
        if (Storage::exists($path)) {
            return Storage::download($path);
        }
        
        session()->flash('error', __('File not found.'));
    }

    public function render()
    {
        $files = Storage::files('student_imported_sheet_files');
        
        $fileDetails = collect($files)->map(function($path) {
            return [
                'path' => $path,
                'name' => basename($path),
                'size' => Storage::size($path),
                'last_modified' => Storage::lastModified($path),
            ];
        })->sortByDesc('last_modified');

        return view('livewire.org-app.student.imported-files', [
            'files' => $fileDetails
        ]);
    }
}
