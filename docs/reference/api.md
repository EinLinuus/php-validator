---
title: API reference
description: Complete reference for Validator and ValidatorException.
---

# API reference

## Imports

The package's public classes currently use these fully qualified names:

```php
use EinLinuus\PhpValidator\EinLinuus\PhpValidator\Validator;
use EinLinuus\PhpValidator\EinLinuus\PhpValidator\ValidatorException;
```

## Common rule behavior

Except where a signature says otherwise, validation rules follow this argument pattern:

```php
rule(/* rule arguments */, string $errorMessage = '', mixed $data = null): Validator
```

| Parameter | Description |
| --- | --- |
| `$errorMessage` | Message used when the rule throws `ValidatorException`. |
| `$data` | Optional context stored on the exception and returned by `getData()`. |

Rules return the same `Validator` instance for chaining. The first failed rule throws immediately.

## Creating and reading a validator

### `__construct()`

```php
new Validator(mixed $value)
```

Creates a validator around any PHP value.

### `get()`

```php
get(): mixed
```

Returns the current value. This can differ from the constructor input after a transformation, date conversion, array callback, shape projection, or optional default.

## Optional values

### `optional()`

```php
optional(mixed $default = null): Validator
```

Locks the validator when PHP considers the current value empty. Later lock-aware methods are skipped, and `get()` returns `$default`. Non-empty values remain active.

See [Optional values](../guides/optional-values.md#skip-empty-values) for the complete empty-value list.

### `optionalIf()`

```php
optionalIf(bool|callable $is_optional, mixed $default = null): Validator
```

Locks the validator when the boolean is `true` or when the no-argument callable returns a truthy value. Unlike `optional()`, this does not inspect the current value.

## Strings

All string-specific checks require the current value to be a PHP string.

### `isString()`

```php
isString(string $errorMessage = '', mixed $data = null): Validator
```

Requires a string.

### `cleanString()`

```php
cleanString(string $errorMessage = '', mixed $data = null): Validator
```

Requires a string, trims it, escapes HTML-sensitive characters with `htmlspecialchars()`, and collapses consecutive whitespace into one space. The normalized string replaces the current value.

### `isNumeric()`

```php
isNumeric(string $errorMessage = '', mixed $data = null): Validator
```

Requires a string for which PHP's `is_numeric()` returns `true`. Integers and floats do not pass because this method first requires a string.

### `isLowercase()`

```php
isLowercase(string $errorMessage = '', mixed $data = null): Validator
```

Requires the string to equal `strtolower($value)`.

### `isUppercase()`

```php
isUppercase(string $errorMessage = '', mixed $data = null): Validator
```

Requires the string to equal `strtoupper($value)`.

### `isEmail()`

```php
isEmail(string $errorMessage = '', mixed $data = null): Validator
```

Requires a string accepted by `FILTER_VALIDATE_EMAIL`.

### `isUrl()`

```php
isUrl(string $errorMessage = '', mixed $data = null): Validator
```

Requires a string accepted by `FILTER_VALIDATE_URL`.

### `matches()`

```php
matches(
    string $pattern,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires a string for which `preg_match($pattern, $value)` produces a match. Supply a complete PHP regular-expression pattern, including delimiters.

### `contains()`

```php
contains(
    string $needle,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires a string containing `$needle`, using `str_contains()`.

### `notContains()`

```php
notContains(
    string $needle,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires a string that does not contain `$needle`.

### `startsWith()`

```php
startsWith(
    string $needle,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires a string beginning with `$needle`, using `str_starts_with()`.

### `endsWith()`

```php
endsWith(
    string $needle,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires a string ending with `$needle`, using `str_ends_with()`.

## Numbers

Numeric comparison methods require the current value to be a PHP integer or float. Numeric strings are rejected.

### `isInt()`

```php
isInt(string $errorMessage = '', mixed $data = null): Validator
```

Requires a PHP integer.

### `isFloat()`

```php
isFloat(string $errorMessage = '', mixed $data = null): Validator
```

Requires a PHP float. Integers do not pass.

### `isGreaterThan()`

```php
isGreaterThan(
    int|float $value,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current number to be strictly greater than `$value`.

### `isGreaterThanOrEqual()`

```php
isGreaterThanOrEqual(
    int|float $value,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current number to be greater than or equal to `$value`.

### `isLessThan()`

```php
isLessThan(
    int|float $value,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current number to be strictly less than `$value`.

### `isLessThanOrEqual()`

```php
isLessThanOrEqual(
    int|float $value,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current number to be less than or equal to `$value`.

### `isEqual()`

```php
isEqual(
    int|float $value,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires strict numeric equality. For example, integer `1` is not strictly equal to float `1.0`.

### `isNotEqual()`

```php
isNotEqual(
    int|float $value,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current number not to be strictly equal to `$value`.

### `isBetween()`

```php
isBetween(
    int|float $min,
    int|float $max,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current number to be inside the inclusive range from `$min` to `$max`.

### `isNotBetween()`

```php
isNotBetween(
    int|float $min,
    int|float $max,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current number to be outside the inclusive range from `$min` to `$max`.

## Booleans

### `isBool()`

```php
isBool(string $errorMessage = '', mixed $data = null): Validator
```

Requires a PHP boolean.

### `isTrue()`

```php
isTrue(string $errorMessage = '', mixed $data = null): Validator
```

Requires the value to be exactly `true`.

### `isFalse()`

```php
isFalse(string $errorMessage = '', mixed $data = null): Validator
```

Requires the value to be exactly `false`.

## Dates

Date comparison methods operate on mutable PHP `DateTime` instances. Call `isDate()` first when starting with a string.

### `isDate()`

```php
isDate(string $errorMessage = '', mixed $data = null): Validator
```

Requires a string accepted by `new DateTime($value)`, then replaces the string with that `DateTime` instance.

### `isBetweenDates()`

```php
isBetweenDates(
    DateTime $min,
    DateTime $max,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current `DateTime` to be inside the inclusive range from `$min` to `$max`.

### `isNotBetweenDates()`

```php
isNotBetweenDates(
    DateTime $min,
    DateTime $max,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current `DateTime` to be outside the inclusive range from `$min` to `$max`.

### `isBeforeDate()`

```php
isBeforeDate(
    DateTime $date,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current `DateTime` to be strictly before `$date`.

### `isAfterDate()`

```php
isAfterDate(
    DateTime $date,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current `DateTime` to be strictly after `$date`.

## Arrays

### `isArray()`

```php
isArray(
    ?callable $shape = null,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires an array. When `$shape` is provided, it is called once per entry:

```php
function (Validator $entry, int|string $key): void
```

Each child validator's resulting value replaces that entry in the output array.

### `isArrayOfShape()`

```php
isArrayOfShape(
    array $schema = [],
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires an array and calls each schema callback with a child validator and the schema key:

```php
[
    'field' => function (Validator $field, string $key): void {
        $field->isString('Field must be a string', $key);
    },
]
```

Only schema keys are returned. Extra input keys are removed, missing keys are validated as `null`, and transformed child values are written into the output.

### `isUnique()`

```php
isUnique(string $errorMessage = '', mixed $data = null): Validator
```

Requires an array whose count is unchanged by PHP's `array_unique()` with its default comparison mode.

Unlike the other public rules, `isUnique()` does not currently honor an optional lock. Avoid placing it after `optional()` or `optionalIf()` unless the locked value is an array.

## Generic comparisons

### `isNull()`

```php
isNull(string $errorMessage = '', mixed $data = null): Validator
```

Requires `null`.

### `isNotNull()`

```php
isNotNull(string $errorMessage = '', mixed $data = null): Validator
```

Requires a value other than `null`.

### `isOneOf()`

```php
isOneOf(
    array $values = [],
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current value to occur in `$values`. The implementation uses `in_array()` without strict mode, so PHP type juggling applies.

### `isNotOneOf()`

```php
isNotOneOf(
    array $values = [],
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Requires the current value not to occur in `$values`. The implementation uses `in_array()` without strict mode.

### `min()`

```php
min(
    int $min,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Applies a minimum according to the current type:

| Current value | Compared value |
| --- | --- |
| String | Byte length from `strlen()` |
| Array | Entry count from `count()` |
| Other | The value itself |

The method does not perform an independent type check. Validate the expected type earlier in the chain.

### `max()`

```php
max(
    int $max,
    string $errorMessage = '',
    mixed $data = null,
): Validator
```

Applies a maximum using the same type behavior as `min()`.

## Custom validation and transformation

### `transform()`

```php
transform(callable $callback): Validator
```

Calls the callback with the current value and replaces the value with the callback's result:

```php
function (mixed $value): mixed
```

### `validate()`

```php
validate(callable $callback): Validator
```

Calls the callback with the current value. The callback's return value is ignored. Throw `ValidatorException` from the callback to reject the value.

## Exceptions

Every built-in rule failure throws `ValidatorException`.

### `getMessage()`

Inherited from PHP's `Exception`. Returns the rule's `$errorMessage`.

### `getData()`

```php
getData(): mixed
```

Returns the contextual `$data` passed to the failed rule or to the exception constructor.

```php
try {
    (new Validator($value))
        ->isEmail('Enter a valid email', 'contact.email');
} catch (ValidatorException $exception) {
    echo $exception->getData();
    echo $exception->getMessage();
}
```

## Internal value container

`ValidatorValue` stores the current value and optional lock state. It is public only because of the current package structure; application code should normally use `Validator` instead.
