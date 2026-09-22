---
title: Contributing
description: Set up PHP Validator locally and validate documentation or implementation changes.
---

# Contributing

## Local setup

Clone the repository and install its Composer autoloader:

```bash
git clone https://github.com/EinLinuus/php-validator.git
cd php-validator
composer install
```

The package currently has no third-party runtime dependencies.

## Repository workflow

Create a focused branch, make the smallest complete change, and keep examples and documentation aligned with public behavior.

When changing a public method:

- Preserve chainability unless the change intentionally introduces a breaking API.
- Keep the error-message and context-data argument convention consistent.
- Decide whether the method must honor the optional lock.
- Document transformations and type changes explicitly.
- Update the [API reference](../reference/api.md) and any affected guide.
- Add or update an example when it is the clearest executable demonstration.

## Run the examples

The repository includes standalone examples:

```bash
php examples/simple.php
php examples/arrays.php
php examples/arrays_with_keys.php
php examples/optional.php
php examples/transforms.php
```

Each script loads `vendor/autoload.php`, so run `composer install` first.

## Validate changes

No automated test suite, linter, or static analyzer is currently configured. At minimum:

1. Validate Composer metadata.
2. Syntax-check every PHP file.
3. Run all examples.
4. Confirm new failure cases throw `ValidatorException`.
5. Confirm transformations produce the expected value from `get()`.

```bash
composer validate
find src examples -name '*.php' -print0 | xargs -0 -n1 php -l
```

For array changes, verify both list callbacks and shape callbacks, including nested transformations, missing keys, and extra keys.

For optional changes, verify all PHP empty values as well as a non-empty value.

## Documentation conventions

Documentation is consumed as a standalone subtree by an external website:

- Put every page below `docs/`.
- Start every page with `title` and `description` YAML front matter.
- Keep navigation in `docs/index.md` and mirror it in the repository README.
- Use relative links within `docs/`.
- Do not link outside the docs subtree with `../`; use absolute HTTPS links for repository resources.
- Use standard Markdown and language-tagged fenced code blocks.
- Keep nesting to one directory below `docs/`.
- Preserve existing filenames and headings when possible because external routes may depend on them.

## Reporting issues

Open an issue in the [GitHub repository](https://github.com/EinLinuus/php-validator/issues) with:

- The PHP version.
- The package version.
- A minimal input and validation chain.
- The expected result.
- The actual exception or returned value.

Do not include sensitive production input.

## Releases

Published releases are tagged in GitHub and distributed through Composer-compatible package metadata. Before creating a release:

1. Confirm the public API reference matches the implementation.
2. Run the validation steps above.
3. Review compatibility notes for changed validation or transformation behavior.
4. Create the version tag and corresponding GitHub release.

PHP Validator is licensed under the [GNU General Public License v3.0](https://github.com/EinLinuus/php-validator/blob/main/LICENSE).
