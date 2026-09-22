---
title: Getting started
description: Install PHP Validator and build your first validation chain.
---

# Getting started

## Install the package

From an existing Composer project, run:

```bash
composer require einlinuus/php-validator
```

If you are starting in an empty directory, initialize Composer first:

```bash
composer init
composer require einlinuus/php-validator
```

Load Composer's autoloader in your application:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

## Validate a value

Create a `Validator` with the raw value, then chain the rules that the value must satisfy:

```php
<?php

use EinLinuus\PhpValidator\EinLinuus\PhpValidator\Validator;
use EinLinuus\PhpValidator\EinLinuus\PhpValidator\ValidatorException;

require_once __DIR__ . '/vendor/autoload.php';

$validator = new Validator('hello world');

try {
    $validator
        ->isString('Input must be a string')
        ->isLowercase('Input must be lowercase')
        ->min(3, 'Input must be at least 3 characters long')
        ->max(12, 'Input must be at most 12 characters long');

    $validated = $validator->get();
} catch (ValidatorException $exception) {
    echo 'Invalid: ' . $exception->getMessage();
}
```

Each rule returns the same validator, so calls can be chained. Validation stops at the first failing rule.

## Read the validated value

Call `get()` only after the validation chain succeeds:

```php
$validated = $validator->get();
```

When a chain only checks rules, `get()` returns the original value. Methods such as `cleanString()`, `isDate()`, `transform()`, and nested array validators can change the returned value.

## Add error context

Most rules accept an error message followed by optional context data. The message is available through `getMessage()`, and the context is available through `getData()`:

```php
$validator = new Validator($input['email'] ?? null);

try {
    $email = $validator
        ->isEmail('Enter a valid email address', 'email')
        ->get();
} catch (ValidatorException $exception) {
    echo $exception->getData() . ': ' . $exception->getMessage();
}
```

The context can be any PHP value. Field names and nested paths are common choices.

## Validate structured input

Use `isArrayOfShape()` to validate named fields:

```php
$validator = new Validator([
    'name' => ' Linus ',
    'age' => 19,
]);

$profile = $validator
    ->isArrayOfShape([
        'name' => fn (Validator $field) => $field
            ->isString('Name must be a string', 'name')
            ->cleanString(),
        'age' => fn (Validator $field) => $field
            ->isInt('Age must be an integer', 'age')
            ->isGreaterThanOrEqual(13, 'You must be at least 13', 'age'),
    ])
    ->get();
```

The result contains the keys declared by the schema. Read [Arrays and shapes](guides/arrays-and-shapes.md) before using schemas with optional or extra fields.

## Troubleshooting

### Class not found

Confirm that `vendor/autoload.php` is loaded and that the imports include the package's full current namespace:

```php
use EinLinuus\PhpValidator\EinLinuus\PhpValidator\Validator;
use EinLinuus\PhpValidator\EinLinuus\PhpValidator\ValidatorException;
```

### Empty exception messages

Rule error messages default to an empty string. Pass an explicit message to each rule that can fail.

### A numeric string fails `isInt()`

`isInt()` only accepts PHP integers. For numeric strings such as `"42"`, use `isNumeric()` or transform the value before applying integer rules.

## Next steps

- Validate nested data with [Arrays and shapes](guides/arrays-and-shapes.md).
- Handle missing values with [Optional values](guides/optional-values.md).
- Normalize values with [Transforms and custom rules](guides/transforms-and-custom-rules.md).
- Look up every method in the [API reference](reference/api.md).
