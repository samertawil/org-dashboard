<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Gallery extends Component
{
    use WithFileUploads;

    public PurchaseRequisition $purchaseRequisition;
    public $search = '';
    public $uploadFiles = [];
    public $uploadNotes = '';
    public $existingAttachments = [];

    public function mount(PurchaseRequisition $purchaseRequisition)
    {
        $this->purchaseRequisition = $purchaseRequisition;
        $this->loadAttachments();
    }

    public function loadAttachments()
    {
        $this->existingAttachments = $this->purchaseRequisition->attachments ?? [];
    }

    public function saveUploadedFile()
    {
        if(Gate::denies('purchase_request.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $this->validate([
            'uploadFiles.*' => 'required|file|max:10240', // 10MB max
        ]);

        $newAttachmentsData = [];

        foreach ($this->uploadFiles as $file) {
            // Image Optimization Logic
            // Check if file is image and larger than 3MB (3 * 1024 * 1024)
            if (str_starts_with($file->getMimeType(), 'image/') && $file->getSize() > 3145728) {
                try {
                    // Create manager instance with desired driver (gd or imagick)
                    $manager = new \Intervention\Image\ImageManager(
                        new \Intervention\Image\Drivers\Gd\Driver()
                    );
                    
                    $image = $manager->read($file->getRealPath());

                    // Resize to max 1920px width or height, maintaining aspect ratio
                    $image->scale(width: 1920);

                    // Encode to Jpeg with 80% quality and save back to temp file
                    $image->toJpeg(quality: 80)->save($file->getRealPath());
                    
                } catch (\Exception $e) {
                    // Log error or continue with original file if optimization fails
                    // \Log::error('Image optimization failed: ' . $e->getMessage());
                }
            }

            $path = $file->store('purchase-request-attachments', 'public');
            
            // Use the provided note/name for all files if single note, or fallback to filename
            $name = $this->uploadNotes ? $this->uploadNotes : $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize(); // Get new size after optimization if it happened

            // Determine type ID (same logic as Activity)
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
                'type_id' => $typeId, // Store the calculated type ID
            ];
        }

        if (!empty($newAttachmentsData)) {
            $finalAttachments = array_merge($this->existingAttachments, $newAttachmentsData);
            
            $this->purchaseRequisition->update([
                'attachments' => $finalAttachments
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
        if(Gate::denies('purchase_request.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        if (isset($this->existingAttachments[$index])) {
            // Delete file from storage
            if (isset($this->existingAttachments[$index]['path'])) {
                Storage::disk('public')->delete($this->existingAttachments[$index]['path']);
            }

            unset($this->existingAttachments[$index]);
            $this->existingAttachments = array_values($this->existingAttachments);
            
            $this->purchaseRequisition->update([
                'attachments' => $this->existingAttachments
            ]);
            
            $this->dispatch('flux-toast', variant: 'success', title: __('Attachment deleted successfully.'));
        }
    }

    public $filterType = '';

    public function getAllStatusesProperty()
    {
        // Assuming config is available, otherwise hardcode or fetch parent ID 47 (usually)
        // Activity\Gallery uses config('appConstant.attchment_types')
        return \App\Reposotries\StatusRepo::statuses()->where('p_id_sub', config('appConstant.attchment_types', 47));
    }

    public function render()
    {
        if(Gate::denies('purchase_request.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $attachments = collect($this->existingAttachments)->map(function ($item) {
            // Use stored type_id if available, otherwise calculate it (backward compatibility)
            if (!isset($item['type_id'])) {
                $ext = strtolower($item['extension'] ?? pathinfo($item['path'], PATHINFO_EXTENSION));
                $typeId = 49; // Default File
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

        return view('livewire.org-app.purchase-request.gallery', [
            'attachments' => $attachments
        ]);
    }
}
