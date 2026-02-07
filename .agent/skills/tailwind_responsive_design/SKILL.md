---
name: Tailwind CSS & Livewire UI/UX
description: A guide for building responsive, modern dashboards using Tailwind CSS and Laravel Livewire, emphasizing mobile-first design, interactive feedback, and consistent component usage.
---

# Tailwind CSS & Livewire UI/UX

This skill provides best practices for creating responsive, polished, and user-friendly interfaces in a Laravel Livewire application using Tailwind CSS. It focuses on the "dashboard" experience.

## Responsive Design Checklist

- [ ] **Mobile-First**: Design for mobile (`base` styles) first, then add breakpoints (`md:`, `lg:`) for larger screens.
- [ ] **Grid Layouts**: Do your grids collapse to 1 column on mobile? (`grid-cols-1 md:grid-cols-2 lg:grid-cols-3`)
- [ ] **Tables**: Are tables scrollable on mobile (`overflow-x-auto`) or do they stack as cards?
- [ ] **Navigation**: Does the sidebar toggle or slide over on mobile?
- [ ] **Touch Targets**: Are buttons and inputs large enough for touch (min 44px)?
- [ ] **Spacing**: Is whitespace consistent (using `gap-` and `p-` classes)?

## Livewire UI/UX Patterns

### 1. Loading States

Users must know when the app is working.

- **Buttons**: Disable and show a spinner.
    ```html
    <flux:button wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Save</span>
        <span wire:loading>Saving...</span>
    </flux:button>
    ```
- **Targeted Loading**: Only show loading for the specific action.
    ```html
    <div wire:loading wire:target="delete({{ $id }})">Deleting...</div>
    ```

### 2. "Dirty" States (Unsaved Changes)

Warn users if they try to leave or just show that a field is modified.

```html
<input type="text" wire:model="title" class="border-gray-300" />
<span wire:dirty wire:target="title" class="text-yellow-600 text-sm"
    >Unsaved...</span
>
```

### 3. Flash Messages / Toasts

Provide immediate feedback after actions.

```php
// In Component
session()->flash('success', 'Post created successfully.');
// Or use a dispatch Event for a JS toast library
$this->dispatch('notify', message: 'Saved!', type: 'success');
```

```html
<!-- In Layout (Alpine.js example) -->
<div
    x-data="{ show: false, message: '' }"
    x-on:notify.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
>
    <div
        x-show="show"
        class="fixed bottom-4 right-4 bg-green-500 text-white p-4 rounded shadow-lg"
    >
        <span x-text="message"></span>
    </div>
</div>
```

## Responsive Layouts (Tailwind)

### Sidebar Layout (Collapsible)

```html
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white hidden md:block border-r">
        <!-- Links -->
    </aside>

    <!-- Mobile Header -->
    <div
        class="md:hidden p-4 bg-white border-b flex justify-between items-center"
    >
        <span>Logo</span>
        <button @click="sidebarOpen = !sidebarOpen">Menu</button>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-4 md:p-8">{{ $slot }}</main>
</div>
```

### Responsive Tables with Overflow

Wrap tables in a container to allow horizontal scrolling on small screens without breaking the layout.

```html
<div class="overflow-x-auto border rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <!-- ... -->
    </table>
</div>
```

### Stacked Grid for Forms

Use `grid` with `gap` for form layouts that naturally reflow.

```html
<form class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="col-span-1">
        <label>First Name</label>
        <input type="text" class="..." />
    </div>
    <div class="col-span-1">
        <label>Last Name</label>
        <input type="text" class="..." />
    </div>
    <!-- Full width on all screens -->
    <div class="col-span-1 md:col-span-2">
        <label>Bio</label>
        <textarea class="..."></textarea>
    </div>
</form>
```

## Dark Mode Strategy (Flux/Tailwind)

If using Flux or manual dark mode:

- Use `dark:` prefix consistently: `bg-white dark:bg-zinc-900`.
- Use specialized text colors for readability: `text-zinc-500 dark:text-zinc-400`.
- Test by toggling the class `dark` on the `html` tag.

## Component Reusability

Avoid repeating long class strings. Extract to Blade components.

- **Bad**: `<button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">...`
- **Good**: `<x-button primary>Save</x-button>`

If you are using **Flux UI**, strictly adhere to its components (`<flux:button>`, `<flux:input>`, etc.) as they have built-in responsiveness and accessibility.

## Visual Feedback (Micro-interactions)

- **Destructive Actions**: Always use `hover:text-red-600` (or `hover:text-red-700` in dark mode context) for delete or remove buttons to clearly indicate danger.
    ```html
    <flux:button icon="trash" class="text-red-500 hover:text-red-600" />
    ```
