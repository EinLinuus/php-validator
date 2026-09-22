---
title: PHP Validator
description: Validate and transform PHP input with a small, chainable API.
---

# PHP Validator

PHP Validator is a lightweight library for validating scalar values, dates, arrays, and nested input structures. Rules are chainable, failures throw a `ValidatorException`, and successful transformations are returned by `get()`.

## Start here

- [Getting started](getting-started.md) - install the package, validate a value, and handle failures.

## Guides

- [Arrays and shapes](guides/arrays-and-shapes.md) - validate lists, associative input, and nested structures.
- [Optional values](guides/optional-values.md) - skip rules conditionally and provide defaults.
- [Transforms and custom rules](guides/transforms-and-custom-rules.md) - normalize input and add application-specific validation.

## Reference

- [API reference](reference/api.md) - signatures and behavior for every public validation method.

## Project documentation

- [Architecture](project/architecture.md) - data flow, repository structure, and implementation constraints.
- [Contributing](project/contributing.md) - local setup, change guidelines, and validation steps.

## Common use cases

- Validate form or API input before using it.
- Normalize strings while validating their format.
- Validate every entry in a list.
- Project an associative array onto a known schema.
- Turn identifiers into domain objects after validating them.
- Attach a field name or other context to validation failures.

## Installation

```bash
composer require einlinuus/php-validator
```

The public classes currently use this namespace:

```php
use EinLinuus\PhpValidator\EinLinuus\PhpValidator\Validator;
use EinLinuus\PhpValidator\EinLinuus\PhpValidator\ValidatorException;
```

Continue with [Getting started](getting-started.md) for a complete first example.
