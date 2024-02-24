<?php
declare(strict_types=1);

use EinLinuus\PhpValidator\EinLinuus\PhpValidator\Validator;
use EinLinuus\PhpValidator\EinLinuus\PhpValidator\ValidatorException;

require_once __DIR__ . "/../vendor/autoload.php";

$input = [
    "username" => "EinLinuus",
    "name" => "Linus",
    "age" => 19,
    "hobbies" => [
        "programming",
        "gaming",
    ],
    "contact" => [
        "email" => "linus@example.com",
        "phone" => "123456789",
    ],
];

$v = new Validator($input);
try {
    $v->isArrayOfShape([
        "username" => fn(Validator $v) => $v->isString("Username must be a string", "username")
            ->cleanString()
            ->transform(fn($v) => strtolower($v))
            ->matches("/^[a-z0-9]{3,16}$/", "Username must be 3-16 characters long and only contain a-z and 0-9", "username")
            ->isNotOneOf(["admin", "moderator"], "Username already taken", "username"),
        "name" => fn(Validator $v) => $v->isString("Name must be a string", "name")
            ->cleanString()
            ->min(3, "Name must be at least 3 characters long", "name")
            ->max(32, "Name must be at most 32 characters long", "name"),
        "age" => fn(Validator $v) => $v->isInt("Age must be an integer", "age")
            ->min(13, "You must be at least 13 years old", "age"),
        "hobbies" => fn(Validator $v) => $v->isArray(fn(Validator $v, int $index) => $v->isString("Hobby must be a string", "hobbies.$index")
            ->cleanString()
            ->min(1, "Hobby must be at least 1 character long", "hobbies.$index")
            ->max(20, "Hobby must be at most 10 characters long", "hobbies.$index"), "Hobbies must be an array", "hobbies")
            ->max(5, "You can only enter 5 hobbies", "hobbies"),
        "contact" => fn(Validator $v) => $v->isArrayOfShape([
            "email" => fn(Validator $v) => $v->isEmail("Email must be a valid email address", "contact.email"),
            "phone" => fn(Validator $v) => $v->isString("Phone must be a string", "contact.phone")
                ->matches("/^[0-9]{9,16}$/", "Phone must be 9-16 digits long", "contact.phone"),
        ]),
    ]);
} catch (ValidatorException $exception) {
    die("Invalid field " . $exception->getData() . ": " . $exception->getMessage());
}

$validated = $v->get();
var_dump($validated); // Notice how the username is lowercase because of the transform() call
