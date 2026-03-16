<?php

namespace App\Livewire\OrgApp\ActivityBeneficiaryName;

use App\Imports\ActivityBeneficiaryNameImport;
use App\Models\activityBeneficiaryName;
use App\Reposotries\ActivityRepo;
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
    public $activity = '';
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

    public function updatingActivity()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function beneficiaries()
    {
        return activityBeneficiaryName::query()
            ->with(['activity', 'displacementCamp', 'status'])
            ->when($this->activity, function ($query) {
                $query->where('activity_id', $this->activity);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('identity_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('displacementCamp', function ($sub) {
                            $sub->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function delete($id)
    {

        if (Gate::denies('activity.beneficiaries.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        
        $beneficiary = activityBeneficiaryName::findOrFail($id);
        $beneficiary->delete();
        session()->flash('message', __('Activity Beneficiary successfully deleted.'));
    }

    public function import()
    {
        if (Gate::denies('activity.beneficiaries.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new ActivityBeneficiaryNameImport, $this->excelFile);

            // Archive the file
            $date = now()->format('Y-m-d');
            $userName = \Illuminate\Support\Str::slug(auth()->user()->name);
            $filename = "Activity_Beneficiary_{$date}_{$userName}.xlsx";

            if (!\Illuminate\Support\Facades\Storage::exists('activity_beneficiary_imported_files')) {
                \Illuminate\Support\Facades\Storage::makeDirectory('activity_beneficiary_imported_files');
            }

            $this->excelFile->storeAs('activity_beneficiary_imported_files', $filename);

            $this->reset('excelFile');
            session()->flash('message', __('Activity Beneficiaries imported successfully.'));
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = collect($e->errors())->flatten()->implode('<br>');
            session()->flash('error', $errorMessages);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        if (Gate::denies('activity.beneficiaries.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.activity-beneficiary-name.index', [
            'activites' => ActivityRepo::activites(),
        ]);
    }
}
