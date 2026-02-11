<?php

namespace App\Livewire\OrgApp\SubjectForLearn;

use App\Models\Status;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Reposotries\StatusRepo;
use App\Models\ActivityAttchment;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;
use App\Models\StudentSubjectForLearn;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Attachment logic properties
    public ?StudentSubjectForLearn $selectedSubject = null;
    public $attachments = [];
    public $newAttachments = [];
    
    // For modal
    public $selectedSubjectIdForShowModal = null;

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

    public function delete($id)
    {
        $subject = StudentSubjectForLearn::findOrFail($id);
        $subject->delete();
        session()->flash('message', __('Subject successfully deleted.'));
    }

    // Attachment methods
    public function selectSubject($subjectId)
    {
        $this->selectedSubject = StudentSubjectForLearn::with('subjectsAttchments.attachmentType')->find($subjectId);
        $this->attachments = $this->selectedSubject->subjectsAttchments->toArray();
        $this->newAttachments = [];
        $this->dispatch('open-modal', 'attachments-modal');
    }

    public function addAttachment()
    {
        
         $this->newAttachments[] = [
            'file' => null,
            'attchment_type' => '', 
            'notes' => '',
        ];
    }

    public function removeNewAttachment($index)
    {
        unset($this->newAttachments[$index]);
        $this->newAttachments = array_values($this->newAttachments);
    }

    public function deleteAttachment($id)
    {
        $attachment = ActivityAttchment::find($id);
        if ($attachment) {
            Storage::disk('public')->delete($attachment->attchment_path);
            $attachment->delete();
        }
        // Refresh
        $this->selectSubject($this->selectedSubject->id);
    }

    public function saveAttachments()
    {
        $this->validate([
            'newAttachments.*.file' => 'required|file|max:10240', // Increased max size slightly
            'newAttachments.*.attchment_type' => 'required',
        ]);

        foreach ($this->newAttachments as $item) {
            $path = $item['file']->store('subject-attachments', 'public');
            $this->selectedSubject->subjectsAttchments()->create([
                'attchment_path' => $path,
                'attchment_type' => $item['attchment_type'],
                'notes' => $item['notes'],
                'status_id' => 1, // Default status
            ]);
        }

        $this->newAttachments = [];
        $this->selectSubject($this->selectedSubject->id);
        session()->flash('attachment_message', __('Attachments uploaded successfully.'));
    }
    
    #[Computed]
    public function subjects()
    {
        return StudentSubjectForLearn::query()
            ->with(['type'])
            ->withCount('subjectsAttchments')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function allStatuses()
    {
        return StatusRepo::statuses();
    }

    public function render()
    {
        if(Gate::denies('curricula.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.org-app.subject-for-learn.index');
    }
}
