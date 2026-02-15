---
name: Validation Skills
description: Guide for using GlobalSystemConstant validation across the application
---

# Validation Skills

This skill documents how to validate `GlobalSystemConstant` enum values using the custom `GlobalValidation` rule or the registered `global_validation` validator extension.

## Overview

The application uses a `GlobalSystemConstant` enum to manage various system status and types (e.g., Active/Inactive, Male/Female). To validate that an input value is a valid enum case of a specific type, use the `GlobalValidation` rule.

## Usage

### 1. Using string validation (Recommended for Attributes)

In Livewire components or Form Requests, you can use the `global_validation:{type}` rule.

```php
use Livewire\Attributes\Validate;

class CreateUser extends Component
{
    #[Validate('required|global_validation:status')]
    public $activation;

    #[Validate('required|global_validation:gender')]
    public $gender;
}
```

The `{type}` parameter corresponds to the return value of `GlobalSystemConstant::getType()`. Common types include:

- `status` (Active, Inactive)
- `gender` (Male, Female)

### 2. Using the Rule Class (Programmatic)

You can also use the rule class directly:

```php
use App\Rules\GlobalValidation;

$request->validate([
    'activation' => ['required', new GlobalValidation('status')],
    'gender'     => ['required', new GlobalValidation('gender')],
]);
```

## How it Works

The validation logic is centralize in `App\Providers\AppServiceProvider` (for string rule) and `App\Rules\GlobalValidation` (for object rule). Both check:

1. Is the value a valid integer backing value for `GlobalSystemConstant`?
2. Does the matching enum case have the correct `type`?
