<?php

namespace App\Livewire\OrgApp\DisplacementCamps;

use App\Models\DisplacementCamp;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Gallery extends Component
{
    use WithFileUploads;

    public DisplacementCamp $displacementCamp;
    public $search = '';
    public $uploadFiles = [];
    public $uploadNotes = '';
    public $existingAttachments = [];

    public function mount(DisplacementCamp $displacementCamp)
    {
        $this->displacementCamp = $displacementCamp;
        $this->loadAttachments();
    }

    public function loadAttachments()
    {
        // the property on the model is 'attchments'
        $this->existingAttachments = $this->displacementCamp->attchments ?? [];
    }

    public function saveUploadedFile()
    {
        if(Gate::denies('displacement.camps.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $this->validate([
            'uploadFiles.*' => 'required|file|max:10240', // 10MB max
        ]);

        $newAttachmentsData = [];

        foreach ($this->uploadFiles as $file) {
            // Image Optimization Logic
            if (str_starts_with($file->getMimeType(), 'image/') && $file->getSize() > 3145728) {
                try {
                    $manager = new \Intervention\Image\ImageManager(
                        new \Intervention\Image\Drivers\Gd\Driver()
                    );
                    
                    $image = $manager->read($file->getRealPath());
                    $image->scale(width: 1920);
                    $image->toJpeg(quality: 80)->save($file->getRealPath());
                    
                } catch (\Exception $e) {
                    // fall back
                }
            }

            $path = $file->store('displacement-camp-attachments', 'public');
            
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
            ];
        }

        if (!empty($newAttachmentsData)) {
            $finalAttachments = array_merge($this->existingAttachments, $newAttachmentsData);
            
            $this->displacementCamp->update([
                'attchments' => $finalAttachments // note: model casts attchments to array, db col is attchments
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
        if(Gate::denies('displacement.camps.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        if (isset($this->existingAttachments[$index])) {
            if (isset($this->existingAttachments[$index]['path'])) {
                Storage::disk('public')->delete($this->existingAttachments[$index]['path']);
            }

            unset($this->existingAttachments[$index]);
            $this->existingAttachments = array_values($this->existingAttachments);
            
            $this->displacementCamp->update([
                'attchments' => $this->existingAttachments
            ]);
            
            $this->dispatch('flux-toast', variant: 'success', title: __('Attachment deleted successfully.'));
        }
    }

    public $filterType = '';

    public function getAllStatusesProperty()
    {
        return \App\Reposotries\StatusRepo::statuses()->where('p_id_sub', config('appConstant.attchment_types', 47));
    }

    public function render()
    {
        if(Gate::denies('displacement.camps.index')) {
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

        return view('livewire.org-app.displacement-camps.gallery', [
            'attachments' => $attachments
        ]);
    }
}
