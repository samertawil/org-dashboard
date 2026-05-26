<?php

namespace App\Livewire\OrgApp\EducationalActivityDetail;

use App\Models\EducationalActivityDetail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\UploadingFilesServices;

class Gallery extends Component
{
    use WithFileUploads;

    public EducationalActivityDetail $detail;
    public $search = '';
    public $uploadFiles = [];
    public $uploadNotes = '';
    public $existingAttachments = [];
    public $filterType = '';
    public $isModal = false;

    public function mount(EducationalActivityDetail $detail, $isModal = false)
    {
        $user = auth()->user();
        if (!($user->isSuperAdmin() || Gate::allows('select.any.educational-activity-detail'))) {
            $hasDetail = \App\Reposotries\EducationalActivityDetailRepo::getTeacherDetailsQuery()
                ->where('id', $detail->id)
                ->exists();
            if (!$hasDetail) {
                abort(403, 'You do not have permission to view/edit this record.');
            }
        }

        $this->detail = $detail;
        $this->isModal = $isModal;
        $this->loadAttachments();
    }

    public function loadAttachments()
    {
        $this->existingAttachments = $this->detail->attchments ?? [];
    }

    public function saveUploadedFile()
    {
        if (Gate::denies('educational-activity-detail.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        
        $this->validate([
            'uploadFiles.*' => 'required|file|max:10240', // 10MB max
        ]);

        $newAttachmentsData = [];

        foreach ($this->uploadFiles as $file) {
            $mimeType = $file->getMimeType();
            
            if (str_starts_with($mimeType, 'image/')) {
                $path = UploadingFilesServices::uploadAndCompress($file, 'educational-activity-detail-attachments', 'public', 1);
            } else {
                $path = UploadingFilesServices::uploadSingleFile($file, 'educational-activity-detail-attachments', 'public');
            }
            
            $name = $this->uploadNotes ? $this->uploadNotes : $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();

            $typeId = 49; // Default File
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $typeId = 48; // Image
            } elseif (in_array($ext, ['mp4', 'avi', 'mov', 'wmv', 'mp3', 'wav', 'ogg'])) {
                $typeId = 50; // Media
            }

            $newAttachmentsData[] = [
                'path' => $path,
                'name' => $name,
                'uploaded_at' => now()->toDateTimeString(),
                'extension' => $ext,
                'size' => $size,
                'type_id' => $typeId,
                // Additional metadata for UI display
                'group_name' => $this->detail->educationalActivity?->group?->name ?? '',
                'period_start' => $this->detail->educationalActivity?->period_start ? $this->detail->educationalActivity->period_start->format('Y-m-d') : '',
            ];
        }

        if (!empty($newAttachmentsData)) {
            $finalAttachments = array_merge($this->existingAttachments, $newAttachmentsData);
            
            $this->detail->update([
                'attchments' => $finalAttachments
            ]);
            
            $this->existingAttachments = $finalAttachments;
            $this->uploadFiles = [];
            $this->uploadNotes = '';
            
            $this->dispatch('modal-close', name: 'upload-modal');
            $this->dispatch('flux-toast', variant: 'success', title: __('Attachments uploaded successfully.'));
        }
    }

    public function deleteAttachment($index)
    {
        if (Gate::denies('educational-activity-detail.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        if (isset($this->existingAttachments[$index])) {
            if (isset($this->existingAttachments[$index]['path'])) {
                Storage::disk('public')->delete($this->existingAttachments[$index]['path']);
            }

            unset($this->existingAttachments[$index]);
            $this->existingAttachments = array_values($this->existingAttachments);
            
            $this->detail->update([
                'attchments' => $this->existingAttachments
            ]);
            
            $this->dispatch('flux-toast', variant: 'success', title: __('Attachment deleted successfully.'));
        }
    }

    public function getAllStatusesProperty()
    {
        return \App\Reposotries\StatusRepo::statuses()->where('p_id_sub', config('appConstant.attchment_types', 47));
    }

    public function render()
    {
        if (Gate::denies('view', $this->detail)) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $attachments = collect($this->existingAttachments)->map(function ($item) {
            if (!isset($item['type_id'])) {
                $ext = strtolower($item['extension'] ?? pathinfo($item['path'], PATHINFO_EXTENSION));
                $typeId = 49;
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                    $typeId = 48;
                } elseif (in_array($ext, ['mp4', 'avi', 'mov', 'wmv', 'mp3', 'wav', 'ogg'])) {
                    $typeId = 50;
                }
                $item['type_id'] = $typeId;
            }
            return $item;
        });

        if ($this->search) {
            $attachments = $attachments->filter(function ($item) {
                return stripos($item['name'], $this->search) !== false;
            });
        }

        if ($this->filterType) {
            $attachments = $attachments->filter(function ($item) {
                return $item['type_id'] == $this->filterType;
            });
        }

        return view('livewire.org-app.educational-activity-detail.gallery', [
            'attachments' => $attachments
        ]);
    }
}
