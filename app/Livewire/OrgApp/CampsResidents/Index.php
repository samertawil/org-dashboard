<?php

namespace App\Livewire\OrgApp\CampsResidents;

use App\Imports\CampsResidentsImport;
use App\Models\displacementCampResident;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    
    public $excelFile;

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
    public function residents()
    {
        return displacementCampResident::query()
            ->with(['displacementCamp', 'status'])
            ->where('full_name', 'like', '%' . $this->search . '%')
            ->orWhere('identity_number', 'like', '%' . $this->search . '%')
            ->orWhereHas('displacementCamp', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('status', function ($q) {
                $q->where('status_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function delete($id)
    {
        if(Gate::denies('displacement.camps.create')) { // assuming same gate for now
            abort(403, 'You do not have the necessary permissions.');
        }
        $resident = displacementCampResident::findOrFail($id);
        $resident->delete();
        session()->flash('message', __('Resident successfully deleted.'));
    }

    public function import()
    {
        if(Gate::denies('displacement.camps.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);
        
        try {
             Excel::import(new CampsResidentsImport, $this->excelFile);

             // Archive the file
             $date = now()->format('Y-m-d');
             $userName = \Illuminate\Support\Str::slug(auth()->user()->name);
             $filename = "Camp_Residents_{$date}_{$userName}.xlsx";
             
             // Ensure directory exists
             if (!\Illuminate\Support\Facades\Storage::exists('resident_imported_sheet_files')) {
                 \Illuminate\Support\Facades\Storage::makeDirectory('resident_imported_sheet_files');
             }

             $this->excelFile->storeAs('resident_imported_sheet_files', $filename);
             
             $this->reset('excelFile');
             session()->flash('message', __('Residents imported successfully.'));
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
        if(Gate::denies('displacement.camps.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.camps-residents.index', [
            'residents' => $this->residents
        ]);
    }
}
