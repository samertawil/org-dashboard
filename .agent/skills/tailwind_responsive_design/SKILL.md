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

#### A. Livewire Loading (`wire:loading`)

Use `wire:loading` to toggle visibility or attributes based on network requests.

- **Button Loading**: Disable the button and show a spinner while the action is processing.

    ```html
    <flux:button wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Save</span>
        <span wire:loading class="flex items-center gap-2">
            <svg
                class="animate-spin h-5 w-5 text-current"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
            Saving...
        </span>
    </flux:button>
    ```

- **Targeted Loading**: Only show loading for specific methods or properties using `wire:target`.
    ```html
    <div wire:loading wire:target="delete({{ $id }})">Deleting...</div>
    ```

#### B. Alpine.js Loading (`x-show`)

For standard form submissions (non-Livewire), use Alpine.js to manage local state.

- **Form Submission**: prevent multiple submissions and show feedback.

    ```html
    <form
        x-data="{ loading: false }"
        @submit="loading = true"
        method="POST"
        action="..."
    >
        <!-- Form Inputs -->

        <div class="flex items-center justify-end">
            <flux:button type="submit" ::disabled="loading">
                <span x-show="!loading">Submit</span>
                <span
                    x-show="loading"
                    class="flex items-center gap-2"
                    style="display: none;"
                >
                    <svg
                        class="animate-spin h-5 w-5 text-current"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    Processing...
                </span>
            </flux:button>
        </div>
    </form>
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
```

### 4. Auto-Scroll on Success (Scroll to Top)

To provide a smooth user experience when a user saves a long form and the success message is at the top of the page, dispatch an event to scroll up automatically.

```php
// In Livewire Component
public function save()
{
    // ... validation and saving logic ...
   
    $this->dispatch('scroll-to-top');
}
```

```html
<!-- In Blade View (Main Container) -->
<div class="flex flex-col gap-6" x-data x-on:scroll-to-top.window="window.scrollTo({ top: 0, behavior: 'smooth' })">
    <!-- Form and Messages -->
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

### Responsive & Interactive Tables

When building data tables (e.g., Index views), design a dual-view responsive layout:
1. **Mobile Card View (`block md:hidden`)**: Stacks information inside vertical cards for small screens so users don't have to scroll horizontally.
2. **Desktop Sticky Table (`hidden md:block overflow-auto custom-scrollbar`)**: Renders a standard table with sticky headers (`sticky top-0 z-20`), sticky left-most column (usually the name/title: `sticky left-0 bg-white dark:bg-zinc-800 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]`), and sticky right-most column (actions: `sticky right-0 bg-white dark:bg-zinc-800 z-10 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)]`).

Additionally, standard tables should implement:
1. **Clear Filters Button**: Display a ghost button positioned to the right to reset applied search properties.
2. **Pagination Summary Block**: Display the "Showing X to Y of Z results" text block directly above the `<table/>`.
3. **Sortable Column Headers**: Add clickable `<th wire:click="sortBy('field')">` headers paired with dynamic Flux chevron icons.

#### Example Architecture

```html
<div class="mb-4">
    <!-- 1. Clear Filters Container (Shown conditionally if filters are active) -->
    @if ($search || $status_id)
    <div class="mt-4 flex items-center justify-end">
        <flux:button
            wire:click="$set('search', ''); $set('status_id', '');"
            variant="ghost"
            size="sm"
            icon="x-mark"
        >
            {{ __('Clear Filters') }}
        </flux:button>
    </div>
    @endif
</div>

<!-- Pagination Summary Block -->
<div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
    <div class="flex items-center justify-between">
        <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
            {{ __('Showing') }}
            <span class="font-medium text-zinc-900 dark:text-white">{{ $records->firstItem() }}</span>
            {{ __('to') }}
            <span class="font-medium text-zinc-900 dark:text-white">{{ $records->lastItem() }}</span>
            {{ __('of') }}
            <span class="font-medium text-zinc-900 dark:text-white">{{ $records->total() }}</span>
            {{ __('results') }}
        </p>
    </div>
</div>

<div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
    
    {{-- A. Mobile Card View --}}
    <div class="block md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
        @forelse($records as $record)
            <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                <div class="flex justify-between items-start">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $record->name }}</span>
                        <span class="text-xs text-zinc-500">{{ $record->secondary_attribute }}</span>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                        {{ $record->status_label }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-1">{{ __('Attribute 1') }}</span>
                        <div class="text-xs text-zinc-600 dark:text-zinc-300 leading-tight">
                            {{ $record->attribute_val_1 }}
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-1">{{ __('Attribute 2') }}</span>
                        <div class="text-xs text-zinc-600 dark:text-zinc-300">
                            {{ $record->attribute_val_2 }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800/50">
                    <div class="text-xs text-zinc-500">
                        <span class="font-medium text-zinc-700 dark:text-zinc-400">Extra:</span> 
                        {{ $record->extra_details }}
                    </div>
                    <div class="flex items-center gap-1">
                        <flux:button href="{{ route('record.show', $record) }}" wire:navigate variant="ghost" size="xs" icon="eye" />
                        <flux:button href="{{ route('record.edit', $record) }}" wire:navigate variant="ghost" size="xs" icon="pencil-square" />
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-sm text-zinc-500 italic">
                {{ __('No records found.') }}
            </div>
        @endforelse
    </div>

    {{-- B. Desktop Sticky Table View --}}
    <div class="hidden md:block overflow-auto custom-scrollbar" style="max-height: 70vh;">
        <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700 border-separate border-spacing-0">
            <thead class="bg-zinc-50 dark:bg-zinc-900 sticky top-0 z-20">
                <tr>
                    <th wire:click="sortBy('name')"
                        class="sticky left-0 bg-zinc-50 dark:bg-zinc-900 z-30 px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center gap-1">
                            {{ __('Name') }}
                            @if ($sortField === 'name')
                                <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3" />
                            @else
                                <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                        {{ __('Attribute 1') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                        {{ __('Attribute 2') }}
                    </th>
                    <th scope="col"
                        class="sticky right-0 bg-zinc-50 dark:bg-zinc-900 z-30 px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($records as $record)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                        <td class="sticky left-0 bg-white dark:bg-zinc-800 z-10 px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-700/50 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] dark:shadow-[2px_0_5px_-2px_rgba(0,0,0,0.3)]">
                            {{ $record->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            {{ $record->attribute_val_1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            {{ $record->attribute_val_2 }}
                        </td>
                        <td class="sticky right-0 bg-white dark:bg-zinc-800 z-10 px-6 py-4 whitespace-nowrap text-right text-sm font-medium border-b border-zinc-100 dark:border-zinc-700/50 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)] dark:shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.3)]">
                            <flux:button href="{{ route('record.show', $record) }}" wire:navigate variant="ghost" size="sm" icon="eye" />
                        </td>
                    </tr>
                @empty
                    <!-- Empty row ... -->
                @endforelse
            </tbody>
        </table>
    </div>
</div>

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

### Charts & Data Visualization

When using libraries like ApexCharts, ensure text colors adapt to the theme:

```javascript
init() {
    const isDark = document.documentElement.classList.contains('dark');
    let chart = new ApexCharts(this.$el, {
        // ...
        chart: {
            foreColor: isDark ? '#e4e4e7' : '#374151', // essential for axes/legends
            fontFamily: 'inherit'
        },
        tooltip: {
             theme: isDark ? 'dark' : 'light'
        }
    });
    chart.render();
}
```

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
