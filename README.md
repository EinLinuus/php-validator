# PHP Validator

A small, chainable PHP library for validating and transforming input values.

## Installation

Install the package with [Composer](https://getcomposer.org/):

```bash
composer require einlinuus/php-validator
```

## Quick start

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

## Documentation

- [Overview](docs/index.md)
- [Getting started](docs/getting-started.md)
- Guides
  - [Arrays and shapes](docs/guides/arrays-and-shapes.md)
  - [Optional values](docs/guides/optional-values.md)
  - [Transforms and custom rules](docs/guides/transforms-and-custom-rules.md)
- Reference
  - [API reference](docs/reference/api.md)
- Project documentation
  - [Architecture](docs/project/architecture.md)
  - [Contributing](docs/project/contributing.md)

## License

PHP Validator is licensed under the [GNU General Public License v3.0](LICENSE).
