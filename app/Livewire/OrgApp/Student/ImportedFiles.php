<?php

namespace App\Livewire\OrgApp\Student;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

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
        if(Gate::denies('student.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
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
