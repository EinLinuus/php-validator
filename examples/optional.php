<?php
declare(strict_types=1);

$input = [
    "gender" => "",
    "name" => "Linus",
    "public" => false,
    "username" => "EinLinuus",
];

$v = new Validator($input);
try {
    $v->isArrayOfShape([
        "name" => fn(Validator $v) => $v->isString("Name must be a string", "name")
            ->cleanString()
            ->min(3, "Name must be at least 3 characters long", "name")
            ->max(32, "Name must be at most 32 characters long", "name"),
        "gender" => fn(Validator $v) => $v->optional()->isOneOf(["male", "female"], "Please enter a valid gender", "gender"),
        "public" => fn(Validator $v) => $v->isBool("Public must be a boolean", "public"),
        "username" => fn(Validator $v) => $v->optionalIf(! (bool) $input["public"])->isString("Username must be a string", "username")
            ->cleanString()
            ->min(3, "Username must be at least 3 characters long", "username")
            ->max(16, "Username must be at most 32 characters long", "username")
    ]);
} catch (ValidatorException $exception) {
    die("Invalid field " . $exception->getData() . ": " . $exception->getMessage());
}

$validated = $v->get();
var_dump($validated);

