<?php
declare(strict_types=1);

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