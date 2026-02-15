---
name: Attachment Implementation Patterns
description: Standard patterns for implementing attachment galleries with upload, delete, and filtering capabilities in Livewire.
---

# Attachment Implementation Guide

This skill documents the standard pattern for implementing attachment galleries in the application, based on the `Activity` and `PurchaseRequest` modules.

## 1. Component Structure

### Properties

- `public $model`: The parent model (e.g., `Activity`, `PurchaseRequisition`).
- `public $search = '';`: For searching by name.
- `public $filterType = '';`: For filtering by file type (Image, Document, etc.).
- `public $uploadFiles = [];`: For multiple file uploads (TemporaryUploadedFile).
- `public $uploadNotes = '';`: For optional display names.
- `public $existingAttachments = [];`: Array or Collection of current attachments.

### Key Methods

- `mount($model)`: Initialize component.
- `saveUploadedFile()`:
    - Validate files.
    - Loop through `uploadFiles`.
    - Store file (`Storage::disk('public')->putFile`).
    - Determine file type (Image vs File).
    - Save to DB (either related model or JSON array).
    - Dispatch success event.
- `deleteAttachment($index/id)`:
    - **Crucial**: Delete physical file from storage using `Storage::disk('public')->delete($path)`.
    - Remove record from DB.
- `render()`:
    - Filter attachments property based on `$search` and `$filterType`.
    - Return view.

## 2. View Structure (Blade)

### Header

- Breadcrumbs.
- Search Input (`flux:input`).
- Filter Dropdown (optional sidebar or dropdown).
- Upload Button triggering Modal.

### Gallery Grid

- Use responsive grid: `grid-cols-2 md:grid-cols-4 lg:grid-cols-6`.
- Cards with:
    - Preview area (Image thumbnail or Icon for docs).
    - **Footer**: Filename, Date, and **Delete Button**.
    - **Click Action**: Open file in new tab.

### Upload Modal

- `flux:modal`.
- `flux:input type="file" multiple`.
- `flux:input` for optional display name.
- Save button with loading state.

## 3. Storage Handling

- Always use `Storage::disk('public')`.
- Store paths relative to public disk.
- Use `Storage::url($path)` for display.
- **Cleanup**: Ensure files are deleted from disk when attachment is deleted.

## 4. Comparisons

| Feature        | Activity Module               | Purchase Request Module                            |
| :------------- | :---------------------------- | :------------------------------------------------- |
| **Storage**    | `ActivityAttchment` (HasMany) | `attachments` (JSON Column)                        |
| **Type Logic** | DB Column `attchment_type`    | Calculated on upload & stored as `type_id` in JSON |
| **Filtering**  | DB Query Scope                | Collection Filter using `type_id`                  |

## 5. JSON Attachment Structure (Purchase Request)

When using a JSON column, store the following structure:

```json
[
    {
        "path": "path/to/file.ext",
        "name": "Display Name",
        "uploaded_at": "Y-m-d H:i:s",
        "extension": "ext",
        "size": 1234,
        "type_id": 49 // 48=Image, 49=File, 50=Media
    }
    }
]
```

## 6. Image Optimization Strategy

To optimize storage and performance, implement an image resizing strategy for large uploads.

### Logic

1.  **Check Size**: specific `max_size` parameter (e.g., 3MB).
2.  **Check Type**: Only apply to image MIME types (jpg, png, webp).
3.  **Resize**: If file > `max_size`, resize/compress to target size/dimensions.
4.  **Library**: Use `Intervention Image` or native PHP `GD`/`Imagick`.

### Example Implementation (Conceptual)

```php
use Intervention\Image\Facades\Image; // Requires intervention/image package

public function optimizedUpload($file, $maxSizeMB = 3, $targetSizeMB = 1)
{
    if ($file->getSize() > ($maxSizeMB * 1024 * 1024) && str_starts_with($file->getMimeType(), 'image/')) {
        $image = Image::make($file);

        // Resize logic (e.g., max width 1920px, keep aspect ratio)
        $image->resize(1920, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Encode/Compress to target quality (approximate)
        $image->encode($file->getClientOriginalExtension(), 80);

        // Save optimized file to temp path or overwrite
        $image->save($file->getRealPath());
    }

    return $file->store('attachments', 'public');
}
```
