# PHP Validator

## Capabilities

- Check for datatype
- Check for length / amount
- Check for regex
- Check for min / max entries in array
- Check for unique entries in array
- Clean strings (remove double spaces, trim, etc.)
- Check for valid email format
- Check for valid URL format
- Check for specific array shape
- Custom validation functions
- Custom transformation functions

## Use cases

- Validate user input
- Validate API input
- Transform input (e.g. convert IDs to objects)

## Installation

This package can be installed via [composer]([url](https://getcomposer.org/)):

```
composer require einlinuus/php-validator
```

> **New to composer?**
> 
> Composer is a dependency manager for PHP. Composer can install this package from the [packagist.org registry](https://packagist.org/packages/einlinuus/php-validator). Once installed, you'll find a `vendor` directory in your project.
>
> You can import all required files by importing the `autoload.php` file created by composer: `require_once __DIR__ . "/vendor/autoload.php";` With the autoload-file included in your project, you now have access to all classes and functions provided by this package.

## Usage

Simply create a new instance of the Validator class and pass the input data to the constructor.

Next, chain the validation methods inside a try-catch block. If any of the validation methods fail, a ValidatorException
will be thrown.

After the validation methods, you can get the output data by calling the get() method. The output data is your input
data transformed by the transformation methods. If no transformation methods are used, the output data will be the same
as the input data.

```php
$input = "hello world";

$v = new Validator($input);
try {
    $v->isString("Input must be a string")
        ->isLowercase("Input must be lowercase")
        ->min(3, "Input must be at least 3 characters long")
        ->max(12, "Input must be at most 10 characters long");
} catch (ValidatorException $e) {
    die("Invalid: " . $e->getMessage());
}

$validated = $v->get();
var_dump($validated); // string(11) "hello world"
````

You can find more examples in the `examples` folder.
