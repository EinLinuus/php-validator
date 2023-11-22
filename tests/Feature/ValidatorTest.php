<?php
declare(strict_types=1);

it("Can validate a string", function () {
    $v = new Validator("test");
    $v->isString();
})->throwsNoExceptions();

it("Can validate an invalid string", function () {
    $v = new Validator(1);
    $v->isString();
})->throws(ValidatorException::class);

it("Can combine multiple validations", function () {
    $v = new Validator("test");
    $v->isString()->min(2)->max(4);
})->throwsNoExceptions();

it("Can throw the correct exception", function () {
    $v = new Validator(1);
    try {
        $v->isString("Value is not a string");

        // This line should not be reached
    } catch (ValidatorException $e) {
        expect($e->getMessage())->toBe("Value is not a string");
    }
});

it("Can throw an exception with custom data", function () {
    $v = new Validator(1);
    try {
        $v->isString("Value is not a string", "custom_data");

        // This line should not be reached
    } catch (ValidatorException $e) {
        expect($e->getData())->toBe("custom_data");
    }
});

it("Can throw an exception with custom data and a custom message", function () {
    $v = new Validator(1);
    try {
        $v->isString("Value is not a string", "custom_data");

        // This line should not be reached
    } catch (ValidatorException $e) {
        expect($e->getData())->toBe("custom_data")
            ->and($e->getMessage())->toBe("Value is not a string");
    }
});

it("Can validate associative arrays", function () {
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

        $result = $v->get();

        expect($result)->toHaveKey("username")
            ->and($result["username"])->toBe("einlinuus")
            ->and($result)->toHaveKey("name")
            ->and($result["name"])->toBe("Linus")
            ->and($result)->toHaveKey("age")
            ->and($result["age"])->toBeInt()->toBe(19)
            ->and($result)->toHaveKey("hobbies")
            ->and($result["hobbies"])->toBeArray()->toHaveCount(2)
            ->and($result["hobbies"][0])->toBe("programming")
            ->and($result["hobbies"][1])->toBe("gaming")
            ->and($result)->toHaveKey("contact")
            ->and($result["contact"])->toBeArray()
            ->and($result["contact"])->toHaveKey("email")
            ->and($result["contact"]["email"])->toBe("linus@example.com")
            ->and($result["contact"])->toHaveKey("phone")
            ->and($result["contact"]["phone"])->toBe("123456789");
    } catch (ValidatorException $e) {
        expect(false)->toBeTrue("Validation failed: " . $e->getMessage());
    }
});

it("Can perform transformations", function () {
    $v = new Validator("test");
    $v->isString()->transform(fn($v) => strtoupper($v));
    expect($v->get())->toBe("TEST");
});

it("Can perform custom validations", function () {
    $v = new Validator("test");
    $v->isString()->validate(function ($value) {
        if ($value !== "test") {
            throw new ValidatorException("Value is not test");
        }
    });
})->throwsNoExceptions();

it("Can fail custom validations", function () {
    $v = new Validator("test 2");
    $v->isString()->validate(function ($value) {
        if ($value !== "test") {
            throw new ValidatorException("Value is not test");
        }
    });

    var_dump($v->get());
})->throws(ValidatorException::class);

it("Supports optional values")->todo();

it("Supports conditionally optional values")->todo();

it("Can clean strings")->todo();

it("Can convert strings to dates")->todo();

it("Supports min() on strings")->todo();

it("Supports max() on strings")->todo();

it("Supports min() on arrays")->todo();

it("Supports max() on arrays")->todo();

it("Supports min() on integers")->todo();

it("Supports max() on integers")->todo();

it("Can check for unique values")->todo();

it("Can check for regex matches")->todo();

it("Can validate emails")->todo();

it("Can validate URLs")->todo();
