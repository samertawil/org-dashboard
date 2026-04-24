<?php

namespace App\Livewire\OrgApp\Activity;

use Livewire\Component;
use App\Models\Activity;
use Livewire\WithFileUploads;
use App\Models\ActivityAttchment; // Keeping the typo as per codebase
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use App\Services\UploadingFilesServices;

class Gallery extends Component
{
    use WithFileUploads;

    public Activity $activity;
    public $search = '';
    public $filterType = ''; // '' = all, 'image', 'document', etc.
    
    // Upload Props
    public $newAttachments = []; // Array to hold multiple files
    public $isUploading = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
    ];

    public function mount(Activity $activity)
    {
        $this->activity = $activity;
        // Verify permissions or ownership if needed
        if(Gate::denies('activity.index')){
             abort(403,'You do not have the necessary permissions');
        }
    }

    public function getAttachmentsProperty()
    {
        return $this->activity->attachments()
            ->with('attachmentType')
            ->when($this->search, function($q) {
                // Search by notes or filename (if we stored filename, but path is usually hashed)
                // Assuming 'notes' is the description/name user sees
                $q->where('notes', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterType, function($q) {
                $q->where('attchment_type', $this->filterType);
            })
            ->latest()
            ->get()
            ->map(function($attachment) {
                // Add helper properties
                $ext = strtolower(pathinfo($attachment->attchment_path, PATHINFO_EXTENSION));
                $attachment->is_image = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                $attachment->extension = $ext;
                return $attachment;
            });
    }

    public function getAllStatusesProperty()
    {
        return StatusRepo::statuses()->where('p_id_sub', config('appConstant.attchment_types'));
    }

    // --- Actions ---

    public function deleteAttachment($id)
    {
         if(Gate::denies('activity.create')){
            return abort(403,'You do not have the necessary permissions');
        }

        $attachment = ActivityAttchment::find($id);

        if ($attachment && $attachment->activity_id === $this->activity->id) {
            Storage::disk('public')->delete($attachment->attchment_path);
            $attachment->delete();
            $this->dispatch('attachment-deleted'); // For notifications
        }
    }

    public function downloadAttachment($id)
    {
        $attachment = ActivityAttchment::find($id);
        if ($attachment && $attachment->activity_id === $this->activity->id) {
             return Storage::disk('public')->download($attachment->attchment_path);
        }
    }
    

    public $uploadFiles = []; // Changed to array for multiple files
    // public $uploadType; // Removed as it is now auto-detected
    public $uploadNotes;

    public function saveUploadedFile()
    {
         if(Gate::denies('activity.create')){
            return abort(403,'You do not have the necessary permissions');
        }
 
        $this->validate([
            'uploadFiles.*' => 'required|file|max:10240', // Validate each file, increased max size for media
            // 'uploadType' => 'required', // Removed validation
        ]);

        foreach ($this->uploadFiles as $file) {
            $mimeType = $file->getMimeType();
            
            // Use service for optimized upload
            if (str_starts_with($mimeType, 'image/')) {
                $path = UploadingFilesServices::uploadAndCompress($file, 'activity-attachments', 'public', 1);
            } else {
                $path = UploadingFilesServices::uploadSingleFile($file, 'activity-attachments', 'public');
            }
            $extension = strtolower($file->getClientOriginalExtension());
            
            // Determine type ID
            $typeId = 49; // Default to File
            
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $typeId = 48; // Image
            } elseif (in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'mp3', 'wav', 'ogg'])) {
                $typeId = 50; // Media
            }

            $this->activity->attachments()->create([
                'attchment_path' => $path,
                'attchment_type' => $typeId,
                'notes' => $this->uploadNotes ?? $file->getClientOriginalName(),
                'status_id' => 1,
            ]);
        }

        // Reset
        $this->reset(['uploadFiles', 'uploadNotes']);
        $this->dispatch('upload-finished'); 
        session()->flash('message', 'Files uploaded successfully.');
    }

    public function render()
    {
        return view('livewire.org-app.activity.gallery');
    }
}
