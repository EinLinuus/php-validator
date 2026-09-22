---
title: Optional values
description: Skip validation for empty or conditionally ignored values and return defaults.
---

# Optional values

Optional methods lock the validator. Most later validation and transformation calls then become no-ops, and `get()` returns the optional default.

## Skip empty values

`optional()` uses PHP's `empty()` semantics:

```php
$nickname = (new Validator(''))
    ->optional('Anonymous')
    ->isString('Nickname must be a string')
    ->min(3, 'Nickname must contain at least three characters')
    ->get();

// 'Anonymous'
```

Values treated as empty include:

- `null`
- `false`
- `0` and `0.0`
- `""` and `"0"`
- `[]`

If the input is not empty, the validator remains active and later rules run normally.

## Make a value conditionally optional

`optionalIf()` locks the validator when its condition evaluates to `true`, even if the current value is not empty:

```php
$input = [
    'public' => false,
    'username' => 'EinLinuus',
];

$username = (new Validator($input['username']))
    ->optionalIf(!$input['public'])
    ->isString('Username must be a string')
    ->min(3, 'Username is too short')
    ->get();

// null
```

Pass a second argument to choose the returned default:

```php
$value = (new Validator('ignored'))
    ->optionalIf(true, 'fallback')
    ->get();

// 'fallback'
```

## Use a callable condition

The condition can be a no-argument callable. It is evaluated immediately:

```php
$validator
    ->optionalIf(fn (): bool => !$currentUser->canEdit(), 'unchanged')
    ->isString('Value must be a string');
```

## Use optional fields in a shape

Missing shape keys are represented as `null`, so `optional()` can supply defaults:

```php
$settings = (new Validator(['theme' => 'dark']))
    ->isArrayOfShape([
        'theme' => fn (Validator $field) => $field
            ->isOneOf(['light', 'dark'], 'Unknown theme', 'theme'),
        'locale' => fn (Validator $field) => $field
            ->optional('en')
            ->isString('Locale must be a string', 'locale'),
    ])
    ->get();

// ['theme' => 'dark', 'locale' => 'en']
```

## Ordering rules

Call `optional()` or `optionalIf()` before rules that should be skipped:

```php
// Correct: an empty value is locked before isEmail() runs.
$validator->optional()->isEmail('Enter a valid email');
```

`isUnique()` is the exception in the current implementation: it does not check the optional lock before requiring an array. Do not call `isUnique()` after an optional method unless the value is known to be an array.

See the [optional API reference](../reference/api.md#optional-values) for exact signatures.
