---
title: Transforms and custom rules
description: Normalize values, convert dates, and add application-specific validation callbacks.
---

# Transforms and custom rules

A validation chain can both reject invalid input and produce a normalized value. Transformations run in call order, so later rules see the transformed result.

## Clean strings

`cleanString()` requires a string, then:

1. Trims leading and trailing whitespace.
2. Escapes HTML-sensitive characters with `htmlspecialchars()`.
3. Replaces each run of whitespace with one space.

```php
$title = (new Validator('  PHP   & validation  '))
    ->cleanString('Title must be a string')
    ->max(80, 'Title is too long')
    ->get();

// 'PHP &amp; validation'
```

Because cleaning changes the value before `max()` runs, the length check applies to the escaped, normalized string.

## Apply a custom transform

`transform()` receives the current value and stores its return value:

```php
$username = (new Validator('EinLinuus'))
    ->isString('Username must be a string')
    ->transform(fn (string $value): string => strtolower($value))
    ->matches('/^[a-z0-9]+$/', 'Username contains invalid characters')
    ->get();

// 'einlinuus'
```

A transform can change the value's type. Add the appropriate validation methods before and after the transform when type guarantees matter.

## Resolve identifiers

Transforms can replace validated identifiers with application objects:

```php
$posts = [
    10 => (object) ['id' => 10, 'status' => 'published'],
    20 => (object) ['id' => 20, 'status' => 'draft'],
];

$post = (new Validator(10))
    ->isInt('Post ID must be an integer')
    ->isOneOf(array_keys($posts), 'Post does not exist')
    ->transform(fn (int $id) => $posts[$id])
    ->validate(function (object $post): void {
        if ($post->status !== 'published') {
            throw new ValidatorException('Post must be published');
        }
    })
    ->get();
```

## Add a custom rule

`validate()` receives the current value. Return normally when it is valid, or throw `ValidatorException` when it is not:

```php
$validator->validate(function (string $value): void {
    if (reservedUsername($value)) {
        throw new ValidatorException('Username is reserved', 'username');
    }
});
```

The callback's return value is ignored. Use `transform()` when a callback should replace the current value.

## Parse and compare dates

`isDate()` is both a rule and a transform. It requires a string accepted by PHP's `DateTime` constructor and replaces that string with a `DateTime` object:

```php
use DateTime;

$publishedAt = (new Validator('2026-09-22 12:00:00'))
    ->isDate('Publication date is invalid')
    ->isAfterDate(new DateTime('2026-01-01'))
    ->isBeforeDate(new DateTime('2027-01-01'))
    ->get();

// $publishedAt is a DateTime instance.
```

Call `isDate()` before `isBeforeDate()`, `isAfterDate()`, `isBetweenDates()`, or `isNotBetweenDates()`.

## Transform nested values

Array callbacks propagate their transformed child values:

```php
$ids = (new Validator(['1', '2', '3']))
    ->isArray(fn (Validator $entry) => $entry
        ->isNumeric('ID must be numeric')
        ->transform(fn (string $id): int => (int) $id))
    ->get();

// [1, 2, 3]
```

See the [API reference](../reference/api.md#custom-validation-and-transformation) for callback signatures.
