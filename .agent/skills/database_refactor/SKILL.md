---
name: Database Query Refactor
description: A guide for identifying and refactoring inefficient database queries in Laravel, focusing on N+1 problems, proper indexing, and efficient data retrieval.
---

# Database Query Refactor

This skill provides a structured approach to identifying slow or inefficient database queries and refactoring them for better performance.

## Optimization Checklist

- [ ] **Eager Loading**: Are you using `with()` to prevent N+1 queries?
- [ ] **Select Specific Columns**: Are you selecting `*` when you only need a few columns?
- [ ] **Chunking**: Are you processing thousands of records at once? (Use `chunk()` or `cursor()`)
- [ ] **Indexes**: Are your `where`, `orderBy`, and `join` columns indexed?
- [ ] **Count/Exists**: Are you fetching all records just to count them? (Use `count()` or `exists()`)
- [ ] **Raw SQL**: Are you using complex Eloquent logic that could be a single raw SQL query?

## Common Refactoring Patterns

### 1. The N+1 Query Problem

**Problem**: Iterating through a collection and accessing a relationship that wasn't eager loaded.
**Detection**: `Debugbar` showing duplicate queries (e.g., `select * from users where id = ?`).

```php
// ❌ Bad
$books = Book::all();
foreach ($books as $book) {
    echo $book->author->name; // Triggers a query for EACH book
}

// ✅ Good
$books = Book::with('author')->get();
foreach ($books as $book) {
    echo $book->author->name; // Zero extra queries
}
```

### 2. Memory Optimization (Select Columns)

**Problem**: Selecting all columns (`select *`) when you only need a specific field, especially with large `text`/`blob` columns.

```php
// ❌ Bad
$users = User::all(); // Selects bio, profile_pic_base64, etc.

// ✅ Good
$users = User::select('id', 'name', 'email')->get();
```

### 3. Efficient Counting & Checks

**Problem**: Loading models just to check existence or count.

```php
// ❌ Bad
$users = User::where('active', 1)->get();
if ($users->count() > 0) { ... }

// ✅ Good
if (User::where('active', 1)->exists()) { ... }
// Or for counting:
$count = User::where('active', 1)->count();
```

### 4. Processing Large Datasets

**Problem**: Loading 50,000 records into memory at once causes OOM (Out of Memory) errors.

```php
// ❌ Bad
$users = User::all();
foreach ($users as $user) { ... }

// ✅ Good (Chunking)
User::chunk(1000, function ($users) {
    foreach ($users as $user) { ... }
});

// ✅ Good (Cursor - faster, less memory)
foreach (User::cursor() as $user) { ... }
```

### 5. Complex conditional logic in PHP

**Problem**: Filtering collection in PHP instead of Database.

```php
// ❌ Bad
$users = User::all()->filter(function($user) {
    return $user->created_at > now()->subDays(30);
});

// ✅ Good
$users = User::where('created_at', '>', now()->subDays(30))->get();
```

## Advanced Techniques

### Subqueries

Sometimes you need a value from a related table without loading the whole model.

```php
// Get users with their last login date
$users = User::addSelect(['last_login_at' => Login::select('created_at')
    ->whereColumn('user_id', 'users.id')
    ->latest()
    ->take(1)
])->get();
```

### Indexes with Migrations

Ensure your query columns are indexed.

```php
// In migration
Schema::table('posts', function (Blueprint $table) {
    $table->index(['user_id', 'status']); // Compound index for where('user_id', ...)->where('status', ...)
});
```

## Tools

- **Laravel Debugbar**: Visualizes all queries on a page.
- **Laravel Telescope**: Logs queries in development.
- **`DB::listen`**: Log queries to `laravel.log` in production (use with caution).
