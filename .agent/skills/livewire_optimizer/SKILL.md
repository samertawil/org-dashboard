---
name: Livewire Component Optimizer
description: A comprehensive guide and skill for identifying performance bottlenecks and optimizing Laravel Livewire v4 components.
---

# Livewire Component Optimizer (v4)

This skill provides a systematic approach to analyzing and optimizing Livewire v4 components for maximum performance. It focuses on reducing server load, minimizing network payloads, and improving frontend responsiveness.

## Quick Optimization Checklist

- [ ] **Database**: Are you eager loading relationships? (No N+1 queries)
- [ ] **Payload**: Are you storing large Collections/Models in `public` properties? (Use `#[Computed]` instead)
- [ ] **Rendering**: Are you running expensive logic in `render()`? (Move to `mount()` or `#[Computed]`)
- [ ] **Loading**: Can this component be lazy loaded? (Use `#[Lazy]`)
- [ ] **Navigation**: Are you using `wire:navigate` for links?
- [ ] **Events**: Are you over-fetching data on every update?
- [ ] **Filtering**: Are heavy lists/schedules loaded by default? (Use compulsory filters to prevent massive initial queries)

## Deep Dive Strategies

### 1. Optimize Data Fetching & Storage

**The Golden Rule**: _Never store large data sets (Collections/Models) in public properties unless absolutely necessary._

- **Problem**: Public properties are dehydrated (serialized) and sent to the frontend, often resulting in massive payloads.
- **Solution**: Use **Computed Properties**. They are cached for the duration of the request and are not sent to the frontend.

**Example:**

```php
// ❌ Bad: Redundant data transfer
public $posts;

public function mount() {
    $this->posts = Post::all();
}

// ✅ Good: Data stays on server, fetched only when needed
use Livewire\Attributes\Computed;

#[Computed]
public function posts()
{
    // Always use pagination for lists
    return Post::with('author') // Eager load relationships
        ->paginate(10);
}
```

### 2. Database Efficiency

Livewire re-renders the component on every interaction. Efficient queries are critical.

- **Eager Loading**: Always use `with()` to prevent N+1 query issues in loops.
    ```php
    // In your Computed property or render method
    User::with(['posts', 'profile'])->get();
    ```
- **Select Columns**: Only select the columns you strictly need to reduce memory and hydration cost.
    ```php
    User::select('id', 'name', 'email')->get();
    ```
- **Caching**: For expensive queries that don't change often (e.g., settings, categories), use Laravel's Cache.
    ```php
    #[Computed(persist: true)] // If purely static for user session, or standard Cache::remember
    public function categories() {
        return Cache::remember('categories', 3600, fn() => Category::all());
    }
    ```

### 3. Frontend Interactions & Responsiveness

- **`wire:model.blur` / `wire:model.change`**:
    - Default `wire:model` (in v3/v4) defers requests.
    - Use `.blur` to only update when the user leaves the field.
    - Avoid `.live` unless real-time feedback (like search) is essential. If using `.live`, **always** add debounce: `wire:model.live.debounce.300ms="search"`.

- **`wire:navigate`**:
  Use `wire:navigate` on your links to enable SPA-like navigation. This avoids full page reloads and significantly speeds up "perceived" performance.

    ```html
    <a href="/posts" wire:navigate>All Posts</a>
    ```

- **`wire:ignore`**:
  Use `wire:ignore` for parts of the DOM controlled by third-party JS libraries (Charts, Maps, Select2) to prevent Livewire from re-rendering them unnecessarily.

### 4. Lazy Loading

For "heavy" components that are not critical for the initial paint (e.g., stats widgets, comments section below the fold), use Lazy Loading.

```php
use Livewire\Attributes\Lazy;

#[Lazy]
class RevenueChart extends Component
{
    public function render()
    {
        return view('livewire.revenue-chart');
    }
}
```

- **Placeholder**: Always provide a placeholder view for better UX while loading.
    ```php
    public function placeholder()
    {
        return view('livewire.placeholders.skeleton');
    }
    ```

### 5. Component Isolation

- **Refactor**: Break large "God Components" into smaller child components.
- **Benefits**: When a child component updates, it doesn't forcing the parent (and all other siblings) to re-render.
- **Key**: Use `#[Reactive]` for props passed from parent to child if the child needs to update when the parent changes.

### 6. Validation Optimization

- **Atomic Validation**: When validating a single field (e.g., on `updatedEmail`), use `$this->validateOnly('email')` instead of `$this->validate()` to avoid running rules for the entire form.

### 7. Loop Rendering & Component Optimization (Views Count Reduction)

- **Problem**: Rendering custom Blade components or UI library components (like `<flux:icon>`, `<flux:badge>`, and `<flux:button>`) inside nested loops (`foreach`) causes Laravel to boot and compile separate View templates for every single item, easily inflating the View render count to tens of thousands (e.g., 15,000+ views).
- **Solution**: Inside loop bodies (such as tables or tree lists), replace custom Blade components with standard HTML elements and inline SVGs:
  - Replace `<flux:icon>` with raw inline `<svg>` blocks.
  - Replace `<flux:badge>` with standard HTML `<span>` styled with Tailwind.
  - Replace `<flux:button>` with standard `<button>` or `<a>` styled with Tailwind.
- **Result**: Drastically reduces View rendering count, CPU execution time, and memory usage.

### 8. Authorization Check Optimization (Gate Caching in Loops)

- **Problem**: Running `@can` or `Gate::allows()` checks inside loops (e.g., per-row action buttons) triggers repetitive evaluation of policies and permission checks (e.g., 2,000+ Gate checks), adding massive CPU overhead.
- **Solution**:
  1. **Cache Class-Level Checks**: Cache static or class-level permissions once per request using Livewire computed properties (`#[Computed]`) and access them via `@if ($this->canEdit)` in the view.
     ```php
     #[Computed]
     public function canEdit(): bool
     {
         return auth()->user()->isSuperAdmin() || Gate::allows('posts.edit');
     }
     ```
  2. **In-Memory Row Checks**: For row-level instance checks (e.g. checking if the record belongs to the user), evaluate the logic in-memory directly in the Blade template or within a custom model method instead of invoking policies:
     ```html
     @if ($isSuperAdmin || $post->user_id === $currentUserId)
         <!-- Edit Action Button -->
     @endif
     ```

### 9. Compulsory (Mandatory) Filtering for Heavy Datasets

- **Problem**: When a page renders a large dataset (e.g., activity schedules, transactions, or log sheets) by default on initial load, the server executes heavy queries, dehydrates thousands of models, and compiles huge views. This leads to slow loading times, database bottlenecks, PHP timeout issues, or memory exhaustion.
- **Solution**: Force the user to select specific filters (e.g., a Batch and a Group) before executing any queries or compiling complex lists.
  1. **Early Return in Livewire**: In the query or computed property, check if the mandatory filters are empty. If so, return an empty paginated result immediately without hitting the database:
     ```php
     #[Computed]
     public function items()
     {
         if (empty($this->filterBatch) || empty($this->filterGroup)) {
             return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
         }

         return Item::where('batch_id', $this->filterBatch)
             ->where('group_id', $this->filterGroup)
             ->paginate(15);
     }
     ```
  2. **Clean View Prompts**: In the blade template, conditionalize the table/list rendering. If the mandatory filters are empty, display a clean, user-friendly instructions page or skeleton prompt instead of rendering an empty table:
     ```html
     @if (empty($filterBatch) || empty($filterGroup))
         <div class="flex flex-col items-center justify-center p-8 border border-dashed rounded-lg border-zinc-200 dark:border-zinc-800 text-zinc-500">
             <!-- Instruction Icon -->
             <svg class="w-12 h-12 mb-3 text-zinc-400" ...></svg>
             <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Filters Required</h3>
             <p class="mt-1 text-sm">Please select a Batch and a Group to display the schedules.</p>
         </div>
     @else
         <!-- Render the actual complex list/table layout -->
     @endif
     ```

## Diagnostic Tools

1.  **Laravel Debugbar**: Check the **Livewire** tab to see:
    - Time taken per component.
    - Memory usage.
    - Queries executed per component.
2.  **Browser Network Tab**:
    - Filter by `Fetch/XHR`.
    - Inspect `update` requests.
    - Look at the `payload` size (both Request and Response). If > 50KB, inspect your public properties.

## Common Pitfalls

- **Calling `save` explicitly in `render`**: Never change state or DB in `render()`. It is idempotent.
- **Loops in Blade**: `@foreach($users as $user) <livewire:user-row :$user /> @endforeach`
    - Make sure to pass a `:key="$user->id"` to the child component so Livewire can track DOM diffs efficiently.
    - `<livewire:user-row :$user :key="$user->id" />`
