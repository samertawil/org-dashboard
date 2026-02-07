---
name: Testing with Pest & Livewire
description: A comprehensive guide to writing automated tests for Laravel applications using Pest PHP and Livewire, covering component testing, form validation, and authorization.
---

# Testing with Pest & Livewire

This skill focuses on ensuring application stability and preventing regressions by writing expressive, readable tests using Pest and Livewire's testing utilities.

## 1. Getting Started with Pest

Pest is a testing framework with a focus on simplicity.

- **Run all tests**: `php artisan test`
- **Run specific test file**: `php artisan test tests/Feature/MyTest.php`
- **Create a new test**: `php artisan pest:test UserTest --feature`

### Basic Syntax

```php
test('basic sum', function () {
    $result = 1 + 1;
    expect($result)->toBe(2);
});

it('has a home page', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});
```

## 2. Livewire Component Testing

Livewire provides a simple API to test components without a browser.

### Basic Component Test

```php
use App\Livewire\CreatePost;
use Livewire\Livewire;

it('renders the create post component', function () {
    Livewire::test(CreatePost::class)
        ->assertStatus(200);
});
```

### interacting with State and Actions

You can set properties and call methods directly.

```php
it('can set title', function () {
    Livewire::test(CreatePost::class)
        ->set('title', 'My First Post')
        ->assertSet('title', 'My First Post');
});

it('can call save action', function () {
    Livewire::test(CreatePost::class)
        ->call('save')
        ->assertDispatched('post-created'); // Check for events
});
```

## 3. Testing Forms & Validation

Testing that your rules prevent invalid data is crucial.

### Testing Validation Errors

```php
it('requires a title', function () {
    Livewire::test(CreatePost::class)
        ->set('title', '') // Empty title
        ->call('save')
        ->assertHasErrors(['title' => 'required']);
});
```

### Testing Successful Submission

```php
use App\Models\Post;

it('creates a post', function () {
    Livewire::test(CreatePost::class)
        ->set('title', 'New Post')
        ->set('content', 'Content here')
        ->call('save')
        ->assertHasNoErrors();

    // Verify database
    $this->assertDatabaseHas('posts', [
        'title' => 'New Post',
    ]);
});
```

## 4. Authorization & Authentication

Test that only the right users can perform actions.

```php
use App\Models\User;

it('redirects guests', function () {
    Livewire::test(CreatePost::class)
        ->call('save')
        ->assertForbidden(); // or assertRedirect('/login') depending on your logic
});

it('allows authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(CreatePost::class)
        ->call('save')
        ->assertHasNoErrors();
});
```

## 5. Debugging Tests

If a test fails and you don't know why:

- `->dump()`: Dumps the rendered HTML of the component.
- `dd($var)`: Standard dump and die within the test.

```php
Livewire::test(CreatePost::class)
    ->call('save')
    ->dump(); // Check the output to see error messages
```

## Checklist for New Features

- [ ] **Happy Path**: Does it work with valid data?
- [ ] **Validation**: Does it fail gracefully with invalid data?
- [ ] **Authorization**: Can unauthorized users access it?
- [ ] **State**: does the component reset or feedback correctly after success?
