<?php
declare(strict_types=1);

it("can be created and read", function () {
    $value = new ValidatorValue("foo");
    expect($value->get())->toBe("foo");
});

it("can be locked", function () {
    $value = new ValidatorValue("foo");
    $value->lock();
    expect($value->get())->toBeNull();
});

it("can be locked with a default value", function () {
    $value = new ValidatorValue("foo");
    $value->lock("bar");
    expect($value->get())->toBe("bar");
});

it("can be unlocked", function () {
    $value = new ValidatorValue("foo");
    $value->lock("bar");
    $value->unlock();
    expect($value->get())->toBe("foo");
});

it("can be checked if it is a string", function () {
    $value = new ValidatorValue("foo");
    expect($value->isString())->toBeTrue();

    $value = new ValidatorValue(1);
    expect($value->isString())->toBeFalse();
});

it("can be checked if it is an int", function () {
    $value = new ValidatorValue(1);
    expect($value->isInt())->toBeTrue();

    $value = new ValidatorValue("foo");
    expect($value->isInt())->toBeFalse();

    $value = new ValidatorValue(1.1);
    expect($value->isInt())->toBeFalse();

    $value = new ValidatorValue(true);
    expect($value->isInt())->toBeFalse();

    $value = new ValidatorValue("1");
    expect($value->isInt())->toBeFalse();
});

it("can be checked if it is a float", function () {
    $value = new ValidatorValue(1.1);
    expect($value->isFloat())->toBeTrue();

    $value = new ValidatorValue(1);
    expect($value->isFloat())->toBeFalse();

    $value = new ValidatorValue("foo");
    expect($value->isFloat())->toBeFalse();

    $value = new ValidatorValue(true);
    expect($value->isFloat())->toBeFalse();

    $value = new ValidatorValue("1.1");
    expect($value->isFloat())->toBeFalse();
});

it("can be checked if it is a bool", function () {
    $value = new ValidatorValue(true);
    expect($value->isBool())->toBeTrue();

    $value = new ValidatorValue(false);
    expect($value->isBool())->toBeTrue();

    $value = new ValidatorValue(1);
    expect($value->isBool())->toBeFalse();

    $value = new ValidatorValue("foo");
    expect($value->isBool())->toBeFalse();

    $value = new ValidatorValue(0);
    expect($value->isBool())->toBeFalse();
});

it("can be checked if it is an array", function () {
    $value = new ValidatorValue([]);
    expect($value->isArray())->toBeTrue();

    $value = new ValidatorValue([1, 2, 3]);
    expect($value->isArray())->toBeTrue();

    $value = new ValidatorValue(["foo" => "bar"]);
    expect($value->isArray())->toBeTrue();

    $value = new ValidatorValue("foo");
    expect($value->isArray())->toBeFalse();

    $value = new ValidatorValue((object)["foo" => "bar"]);
    expect($value->isArray())->toBeFalse();
});

it("can be checked if it is null", function () {
    $value = new ValidatorValue(null);
    expect($value->isNull())->toBeTrue();

    $value = new ValidatorValue("foo");
    expect($value->isNull())->toBeFalse();

    $value = new ValidatorValue(1);
    expect($value->isNull())->toBeFalse();
});
