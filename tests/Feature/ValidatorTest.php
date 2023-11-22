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
