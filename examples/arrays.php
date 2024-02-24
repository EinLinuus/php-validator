<?php
declare(strict_types=1);

use EinLinuus\PhpValidator\EinLinuus\PhpValidator\Validator;
use EinLinuus\PhpValidator\EinLinuus\PhpValidator\ValidatorException;

require_once __DIR__ . "/../vendor/autoload.php";

$input = [
    25,
    12,
    93,
    27,
    29
];

$v = new Validator($input);
try {
    $v->isArray(fn(Validator $v) => $v->isInt("Input must be an integer"))
        ->isUnique("Array must not contain duplicate values")
        ->min(2, "Input must have at least 2 elements")
        ->max(5, "Input must have at most 5 elements");
} catch (ValidatorException $exception) {
    die("Invalid: " . $exception->getMessage());
}

$validated = $v->get();
var_dump($validated);
