<?php

namespace App\Livewire\OrgApp\Student;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    
    public $excelFile; // For file upload

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function students()
    {
        return Student::query()
            ->with(['studentGroup', 'status', 'city', 'region'])
            ->where('full_name', 'like', '%' . $this->search . '%')
            ->orWhere('identity_number', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function delete($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        session()->flash('message', __('Student successfully deleted.'));
    }

    public function import()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);
        
        try {
             Excel::import(new StudentsImport, $this->excelFile);

             // Archive the file
             $date = now()->format('Y-m-d');
             $userName = \Illuminate\Support\Str::slug(auth()->user()->name);
             $filename = "Student_{$date}_{$userName}.xlsx";
             
             // Ensure directory exists
             if (!\Illuminate\Support\Facades\Storage::exists('student_imported_sheet_files')) {
                 \Illuminate\Support\Facades\Storage::makeDirectory('student_imported_sheet_files');
             }

             $this->excelFile->storeAs('student_imported_sheet_files', $filename);
             
             $this->reset('excelFile');
             session()->flash('message', __('Students imported successfully.'));
             $this->dispatch('close-modal', 'import-modal');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             
             $messages = [];
             foreach ($failures as $failure) {
                 $row = $failure->row();
                 $attribute = $failure->attribute();
                 $value = $failure->values()[$attribute] ?? 'N/A';
                 $errors = implode(', ', $failure->errors());
                 $messages[] = "Row {$row}: ({$value}) - {$errors}";
             }
             
             session()->flash('error', implode('<br>', $messages));
        }
    }

    public function render()
    {
        return view('livewire.org-app.student.index');
    }
}
