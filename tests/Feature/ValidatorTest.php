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
})->throws(ValidatorException::class);

it("Supports optional values", function () {
    $v = new Validator(null);
    try {
        $v->optional()->isString();
        expect($v->get())->toBeNull();
    } catch (ValidatorException $e) {
        expect(false)->toBeTrue("Validation failed: " . $e->getMessage());
    }

    $v = new Validator("Lorem Ipsum");
    try {
        $v->optional()->isString();
        expect($v->get())->toBe("Lorem Ipsum");
    } catch (ValidatorException $e) {
        expect(false)->toBeTrue("Validation failed: " . $e->getMessage());
    }

    $v = new Validator(20);
    try {
        $v->optional()->isString();
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }
});

it("Supports conditionally optional values")->todo();

it("Can clean strings", function () {
    $v = new Validator("  test  ");
    $v->cleanString();
    expect($v->get())->toBe("test");

    $v = new Validator("<h1>hello world</h1>");
    $v->cleanString();
    expect($v->get())->toBe("&lt;h1&gt;hello world&lt;/h1&gt;");

    $v = new Validator(20);
    try {
        $v->cleanString();
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }
});

it("Can convert strings to dates", function () {
    $v = new Validator("2021-01-01");
    $v->isDate();
    expect($v->get())->toBeInstanceOf(DateTime::class);

    $v = new Validator("2021-01-01 12:00:00");
    $v->isDate();
    expect($v->get())->toBeInstanceOf(DateTime::class);

    $v = new Validator("2021-01-01 12:00:00.000000");
    $v->isDate();
    expect($v->get())->toBeInstanceOf(DateTime::class);

    $v = new Validator("2021-01-01T12:00:00");
    $v->isDate();
    expect($v->get())->toBeInstanceOf(DateTime::class);

    $v = new Validator("01.01.2021");
    $v->isDate();
    expect($v->get())->toBeInstanceOf(DateTime::class);

    $v = new Validator("01.01.2021 12:00:00");
    $v->isDate();
    expect($v->get())->toBeInstanceOf(DateTime::class);

    $v = new Validator(30);
    try {
        $v->isDate();
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }

    $v = new Validator("abcdefg");
    try {
        $v->isDate();
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }
});

it("Supports min() on strings", function () {
    $v = new Validator("test");
    $v->isString()->min(2);
    expect($v->get())->toBe("test");

    $v = new Validator("test");
    try {
        $v->isString()->min(5);
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }
});

it("Supports max() on strings", function () {
    $v = new Validator("test");
    $v->isString()->max(4);
    expect($v->get())->toBe("test");

    $v = new Validator("test");
    try {
        $v->isString()->max(2);
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }
});

it("Supports min() on arrays", function () {
    $v = new Validator([1, 2, 3]);
    $v->isArray()->min(2);
    expect($v->get())->toBeArray()->toHaveCount(3);

    $v = new Validator([1, 2, 3]);
    try {
        $v->isArray()->min(5);
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }
});

it("Supports max() on arrays", function () {
    $v = new Validator([1, 2, 3]);
    $v->isArray()->max(4);
    expect($v->get())->toBeArray()->toHaveCount(3);

    $v = new Validator([1, 2, 3]);
    try {
        $v->isArray()->max(2);
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }
});

it("Supports min() on integers", function () {
    $v = new Validator(5);
    $v->isInt()->min(2);
    expect($v->get())->toBe(5);

    $v = new Validator(5);
    try {
        $v->isInt()->min(10);
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }
});

it("Supports max() on integers", function () {
    $v = new Validator(5);
    $v->isInt()->max(10);
    expect($v->get())->toBe(5);

    $v = new Validator(5);
    try {
        $v->isInt()->max(2);
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }
});

it("Can check for unique values", function () {
    $v = new Validator([1, 2, 3]);
    $v->isUnique();
    expect($v->get())->toBeArray()->toHaveCount(3);

    $v = new Validator(["hello", "world"]);
    $v->isUnique();
    expect($v->get())->toBeArray()->toHaveCount(2);

    $v = new Validator([1, 2, 3, 1]);
    try {
        $v->isUnique();
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }

    $v = new Validator(["hello", "world", "hello"]);
    try {
        $v->isUnique();
        expect(false)->toBeTrue("Validation should have failed");
    } catch (ValidatorException $e) {
        expect(true)->toBeTrue();
    }
});

it("Can check for regex matches")->todo();

it("Can validate emails")->todo();

it("Can validate URLs")->todo();
