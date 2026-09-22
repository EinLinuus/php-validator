---
title: Arrays and shapes
description: Validate array entries, named fields, nested input, and projected output.
---

# Arrays and shapes

PHP Validator provides two complementary array methods:

- `isArray()` checks an array and can apply one callback to every entry.
- `isArrayOfShape()` applies a different callback to each declared key.

Both methods write transformed child values back into the parent result.

## Validate every array entry

Pass a callback as the first argument to `isArray()`. The callback receives a child `Validator` and the current array key:

```php
$validator = new Validator([25, 12, 93]);

$ages = $validator
    ->isArray(
        fn (Validator $entry, int|string $key) => $entry
            ->isInt('Every age must be an integer', "ages.$key")
            ->isGreaterThanOrEqual(0, 'Ages cannot be negative', "ages.$key"),
        'Ages must be an array',
        'ages',
    )
    ->isUnique('Ages must not contain duplicates', 'ages')
    ->get();
```

If no entry validation is needed, pass `null` before the error arguments:

```php
$validator->isArray(null, 'Input must be an array', 'input');
```

The method signature reserves its first argument for the callback.

## Transform array entries

Changes made by each child validator are copied into the resulting array:

```php
$validator = new Validator([' php ', ' validation ']);

$topics = $validator
    ->isArray(fn (Validator $entry) => $entry
        ->isString('Topics must be strings')
        ->cleanString()
        ->transform(fn (string $value) => strtoupper($value)))
    ->get();

// ['PHP', 'VALIDATION']
```

## Validate an associative shape

Declare one callback per accepted key:

```php
$validator = new Validator([
    'username' => ' EinLinuus ',
    'contact' => [
        'email' => 'linus@example.com',
    ],
]);

$profile = $validator
    ->isArrayOfShape([
        'username' => fn (Validator $field) => $field
            ->isString('Username must be a string', 'username')
            ->cleanString()
            ->transform(fn (string $value) => strtolower($value))
            ->matches(
                '/^[a-z0-9]{3,16}$/',
                'Username must contain 3-16 lowercase letters or digits',
                'username',
            ),
        'contact' => fn (Validator $field) => $field
            ->isArrayOfShape([
                'email' => fn (Validator $email) => $email
                    ->isEmail('Enter a valid email address', 'contact.email'),
            ], 'Contact must be an array', 'contact'),
    ])
    ->get();
```

## Understand shape projection

`isArrayOfShape()` builds a new array from the schema:

- Keys declared in the schema are returned in schema order.
- Input keys that are not declared in the schema are removed.
- A missing schema key is passed to its callback as `null`.
- Each callback's transformed value is stored in the output.

Use `optional()` inside a field callback when a missing key is allowed:

```php
$result = (new Validator(['name' => 'Linus']))
    ->isArrayOfShape([
        'name' => fn (Validator $field) => $field->isString('Name is required'),
        'nickname' => fn (Validator $field) => $field
            ->optional('Anonymous')
            ->isString('Nickname must be a string'),
    ])
    ->get();

// ['name' => 'Linus', 'nickname' => 'Anonymous']
```

See [Optional values](optional-values.md) for the complete empty-value behavior.

## Validate nested paths

The library does not build error paths automatically. Pass the path as the rule's `$data` argument:

```php
'hobbies' => fn (Validator $field) => $field->isArray(
    fn (Validator $hobby, int|string $key) => $hobby
        ->isString('Hobby must be a string', "hobbies.$key")
        ->min(1, 'Hobby cannot be empty', "hobbies.$key"),
    'Hobbies must be an array',
    'hobbies',
),
```

When a rule fails, retrieve that path with `ValidatorException::getData()`.

## Check uniqueness and size

Use `isUnique()` for duplicate checks and `min()` or `max()` for entry counts:

```php
$validator
    ->isArray(fn (Validator $entry) => $entry->isInt('IDs must be integers'))
    ->isUnique('IDs must be unique')
    ->min(1, 'Choose at least one ID')
    ->max(5, 'Choose at most five IDs');
```

`isUnique()` uses PHP's default `array_unique()` comparison behavior. It is intended for scalar array values.

See the [array API reference](../reference/api.md#arrays) for exact signatures.
