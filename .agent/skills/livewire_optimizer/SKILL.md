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
