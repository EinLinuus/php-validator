<?php
declare(strict_types=1);

class ValidatorValue
{

    private mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function isString(): bool
    {
        return is_string($this->value);
    }

    public function isInt(): bool
    {
        return is_int($this->value);
    }

    public function isFloat(): bool
    {
        return is_float($this->value);
    }

    public function isBool(): bool
    {
        return is_bool($this->value);
    }

    public function isArray(): bool
    {
        return is_array($this->value);
    }

    public function isNull(): bool
    {
        return is_null($this->value);
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isDate(): bool
    {
        return $this->value instanceof DateTime;
    }

    public function set(mixed $value): void
    {
        $this->value = $value;
    }

}