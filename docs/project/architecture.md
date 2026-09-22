---
title: Architecture
description: Understand PHP Validator's repository layout, validation pipeline, and implementation constraints.
---

# Architecture

PHP Validator is intentionally small. Its behavior is concentrated in a chainable validator, a mutable value container, and one exception type.

## Repository map

| Path | Purpose |
| --- | --- |
| `src/EinLinuus/PhpValidator/Validator.php` | Public validation, transformation, array, and optional APIs. |
| `src/EinLinuus/PhpValidator/ValidatorValue.php` | Mutable value and optional-lock state used by `Validator`. |
| `src/EinLinuus/PhpValidator/ValidatorException.php` | Failure type with optional contextual data. |
| `examples/` | Runnable examples for scalar values, arrays, shapes, optional fields, and transforms. |
| `docs/` | Website-ready user and contributor documentation. |
| `composer.json` | Package identity and PSR-4 autoload configuration. |

## Runtime model

Each `Validator` owns a `ValidatorValue`. Most public methods perform one of four operations:

1. Check the current value and throw on failure.
2. Replace the current value with a normalized or transformed result.
3. Lock the value so later methods are skipped and a default is returned.
4. Create child validators for array entries or schema fields.

```text
raw input
    |
    v
ValidatorValue
    |
    +--> validation rule fails --> ValidatorException(message, data)
    |
    +--> transform updates current value
    |
    +--> optional lock returns a default
    |
    v
Validator::get()
```

The library returns the first failure. It does not collect multiple validation errors.

## Chaining

Validation methods return the same `Validator` instance. This makes call order part of the contract:

```php
$result = (new Validator($input))
    ->isString('Expected a string')
    ->cleanString()
    ->transform(fn (string $value) => strtolower($value))
    ->matches('/^[a-z]+$/', 'Only letters are allowed')
    ->get();
```

Later methods see the value produced by earlier transformations.

## Array validation

`isArray()` creates a child validator for each input entry. After the callback runs, the child's value is copied back under the original key.

`isArrayOfShape()` creates a child validator for each schema key. Its output is a new array containing only schema keys, so it behaves as both validation and projection.

```text
input array + callback/schema
            |
            v
      child Validator
            |
      validate/transform
            |
            v
write child result into output array
```

Child exceptions are not wrapped. Their original message and context propagate to the caller.

## Optional locking

`optional()` and `optionalIf()` lock the shared value container. Lock-aware methods return immediately, and `get()` returns the stored default rather than the original value.

`isUnique()` currently does not perform this lock check and is the only public rule with that exception.

## Exceptions and context

Built-in failures call the private `Validator::error()` helper, which throws `ValidatorException`. The exception preserves:

- A message through inherited `Exception::getMessage()`.
- Arbitrary context through `ValidatorException::getData()`.

The library does not infer field paths. Array and shape callbacks should pass useful paths as context when callers need field-level error reporting.

## Autoloading and namespaces

Composer maps:

```text
EinLinuus\PhpValidator\ => src/
```

The source files also live under `src/EinLinuus/PhpValidator/` and declare that same suffix. As a result, the current public namespace is:

```text
EinLinuus\PhpValidator\EinLinuus\PhpValidator
```

Documentation and examples use this actual installed namespace.

## Known behavioral constraints

- `cleanString()` escapes HTML-sensitive characters; it does not remove HTML tags.
- String `min()` and `max()` use byte length through `strlen()`, not multibyte character length.
- Membership checks use non-strict `in_array()` comparisons.
- `isEqual()` and `isNotEqual()` use strict comparison between integers and floats.
- Date parsing follows `DateTime` constructor behavior and transforms the value into a mutable `DateTime`.
- Array shapes remove undeclared input keys.
- Validation stops at the first exception.
- No automated test suite or static-analysis configuration is currently included.

Treat these behaviors as compatibility constraints when changing the implementation.
