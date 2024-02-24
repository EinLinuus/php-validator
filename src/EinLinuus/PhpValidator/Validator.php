<?php
declare(strict_types=1);

namespace EinLinuus\PhpValidator\EinLinuus\PhpValidator;

use DateTime;
use Exception;

class Validator
{

    private ValidatorValue $value;


    public function __construct(mixed $value)
    {
        if ($value instanceof ValidatorValue) {
            $this->value = $value;
        } else {
            $this->value = new ValidatorValue($value);
        }
    }

    public function optional(mixed $default = null): Validator
    {
        if (!empty($this->value->get())) {
            return $this;
        }

        $this->value->lock($default);
        return $this;
    }

    public function optionalIf(bool|callable $is_optional, mixed $default = null): Validator
    {
        if (is_callable($is_optional)) {
            $is_optional = $is_optional();
        }

        if (!$is_optional) {
            return $this;
        }

        $this->value->lock($default);
        return $this;
    }

    /**
     * STRINGS
     * - isString
     * - cleanString
     * - isNumeric
     * - isLowercase
     * - isUppercase
     * - isEmail
     * - isUrl
     * - matches
     * - contains
     * - notContains
     * - startsWith
     * - endsWith
     */

    /**
     * Checks if the value is a string.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isString(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }
        return $this;
    }

    /**
     * Cleans a string from whitespace, html tags, and multiple spaces.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function cleanString(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        $str = trim($str);
        $str = htmlspecialchars($str);
        $str = preg_replace('/\s+/', ' ', $str);
        $this->value->set($str);

        return $this;
    }

    /**
     * Checks if the value is numeric.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isNumeric(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        if (!is_numeric($str)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is lowercase.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isLowercase(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        if ($str !== strtolower($str)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is uppercase.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isUppercase(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        if ($str !== strtoupper($str)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is an email address.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     * @see https://www.php.net/manual/en/filter.filters.validate.php
     */
    public function isEmail(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        if (!filter_var($str, FILTER_VALIDATE_EMAIL)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is a URL address.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     * @see https://www.php.net/manual/en/filter.filters.validate.php
     */
    public function isUrl(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        if (!filter_var($str, FILTER_VALIDATE_URL)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value matches a regex pattern.
     *
     * @param string $pattern
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     * @see https://www.php.net/manual/en/function.preg-match.php
     */
    public function matches(string $pattern, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        if (!preg_match($pattern, $str)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value contains a string.
     *
     * @param string $needle
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function contains(string $needle, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        if (!str_contains($str, $needle)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Check if the value not contains a string.
     *
     * @param string $needle
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function notContains(string $needle, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        if (str_contains($str, $needle)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value starts with a string.
     *
     * @param string $needle
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function startsWith(string $needle, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        if (!str_starts_with($str, $needle)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value ends with a string.
     *
     * @param string $needle
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function endsWith(string $needle, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $str = $this->value->get();
        if (!str_ends_with($str, $needle)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * NUMBERS
     * - isInt
     * - isFloat
     * - isGreaterThan
     * - isGreaterThanOrEqual
     * - isLessThan
     * - isLessThanOrEqual
     * - isEqual
     * - isNotEqual
     * - isBetween
     * - isNotBetween
     * - isOneOf
     * - isNotOneOf
     */

    /**
     * Checks if the value is an integer.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isInt(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isInt()) {
            $this->error($errorMessage, $data);
        }
        return $this;
    }

    /**
     * Checks if the value is a float.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isFloat(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isFloat()) {
            $this->error($errorMessage, $data);
        }
        return $this;
    }

    /**
     * Checks if the value is greater than a number.
     *
     * @param int|float $value
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isGreaterThan(int|float $value, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isInt() && !$this->value->isFloat()) {
            $this->error($errorMessage, $data);
        }

        $num = $this->value->get();
        if ($num <= $value) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is greater than or equal to a number.
     *
     * @param int|float $value
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isGreaterThanOrEqual(int|float $value, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isInt() && !$this->value->isFloat()) {
            $this->error($errorMessage, $data);
        }

        $num = $this->value->get();
        if ($num < $value) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is less than a number.
     *
     * @param int|float $value
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isLessThan(int|float $value, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isInt() && !$this->value->isFloat()) {
            $this->error($errorMessage, $data);
        }

        $num = $this->value->get();
        if ($num >= $value) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is less than or equal to a number.
     *
     * @param int|float $value
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isLessThanOrEqual(int|float $value, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isInt() && !$this->value->isFloat()) {
            $this->error($errorMessage, $data);
        }

        $num = $this->value->get();
        if ($num > $value) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is equal to a number.
     *
     * @param int|float $value
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isEqual(int|float $value, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isInt() && !$this->value->isFloat()) {
            $this->error($errorMessage, $data);
        }

        $num = $this->value->get();
        if ($num !== $value) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is not equal to a number.
     *
     * @param int|float $value
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isNotEqual(int|float $value, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isInt() && !$this->value->isFloat()) {
            $this->error($errorMessage, $data);
        }

        $num = $this->value->get();
        if ($num === $value) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is between two numbers.
     *
     * @param int|float $min
     * @param int|float $max
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isBetween(int|float $min, int|float $max, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isInt() && !$this->value->isFloat()) {
            $this->error($errorMessage, $data);
        }

        $num = $this->value->get();
        if ($num < $min || $num > $max) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is not between two numbers.
     *
     * @param int|float $min
     * @param int|float $max
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isNotBetween(int|float $min, int|float $max, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isInt() && !$this->value->isFloat()) {
            $this->error($errorMessage, $data);
        }

        $num = $this->value->get();
        if ($num >= $min && $num <= $max) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * BOOLEANS
     * - isBool
     * - isTrue
     * - isFalse
     */

    /**
     * Checks if the value is a boolean.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isBool(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isBool()) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is true.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isTrue(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isBool()) {
            $this->error($errorMessage, $data);
        }

        if ($this->value->get() !== true) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is false.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isFalse(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isBool()) {
            $this->error($errorMessage, $data);
        }

        if ($this->value->get() !== false) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * DATES
     * - isDate
     * - isBetweenDates
     * - isNotBetweenDates
     * - isBeforeDate
     * - isAfterDate
     */

    /**
     * Checks if the value is a date.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     * @throws Exception
     */
    public function isDate(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isString()) {
            $this->error($errorMessage, $data);
        }

        $date = $this->value->get();

        try {
            $date = new DateTime($date);
        } catch (Exception $e) {
            $this->error($errorMessage, $data);
        }
        if ($date === false) {
            $this->error($errorMessage, $data);
        }

        $this->value->set($date);

        return $this;
    }

    /**
     * Checks if the value is between two dates.
     *
     * @param DateTime $min
     * @param DateTime $max
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws Exception
     * @throws ValidatorException
     */
    public function isBetweenDates(DateTime $min, DateTime $max, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isDate()) {
            $this->error($errorMessage, $data);
        }

        $date = $this->value->get();
        if ($date < $min || $date > $max) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is not between two dates.
     *
     * @param DateTime $min
     * @param DateTime $max
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws Exception
     * @throws ValidatorException
     */
    public function isNotBetweenDates(DateTime $min, DateTime $max, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isDate()) {
            $this->error($errorMessage, $data);
        }

        $date = $this->value->get();
        if ($date >= $min && $date <= $max) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is before a date.
     *
     * @param DateTime $date
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws Exception
     * @throws ValidatorException
     */
    public function isBeforeDate(DateTime $date, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isDate()) {
            $this->error($errorMessage, $data);
        }

        if ($this->value->get() >= $date) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is after a date.
     *
     * @param DateTime $date
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws Exception
     * @throws ValidatorException
     */
    public function isAfterDate(DateTime $date, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isDate()) {
            $this->error($errorMessage, $data);
        }

        if ($this->value->get() <= $date) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * ARRAYS
     * - isArray
     * - isArrayOfShape
     */

    /**
     * Checks if the value is an array. Can also validate the entries of the array.
     *
     * @param callable|null $shape
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isArray(?callable $shape = null, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isArray()) {
            $this->error($errorMessage, $data);
        }

        if ($shape === null) {
            return $this;
        }

        $arr = $this->value->get();

        foreach ($arr as $key => $value) {
            $validator = new Validator($value);
            call_user_func($shape, $validator, $key);
            $result = $validator->get();
            $arr[$key] = $result;
        }

        $this->value->set($arr);

        return $this;
    }

    /**
     * Checks if the value is an array of a specific shape.
     *
     * @param array<string, callable> $schema
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isArrayOfShape(array $schema = [], string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isArray()) {
            $this->error($errorMessage, $data);
        }

        if ($schema === null) {
            return $this;
        }

        $parsed_array = [];

        foreach ($schema as $key => $callback) {
            $validator = new Validator($this->value->get()[$key] ?? null);
            call_user_func($callback, $validator, $key);
            $parsed_array[$key] = $validator->get();
        }

        $this->value->set($parsed_array);

        return $this;
    }

    /**
     * OTHER
     * - isNull
     * - isNotNull
     * - isOneOf
     * - isNotOneOf
     * - transform
     * - validate
     */

    /**
     * Checks if the value is null.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isNull(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!$this->value->isNull()) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is not null.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isNotNull(string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if ($this->value->isNull()) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is one of the given values.
     *
     * @param array $values
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isOneOf(array $values = [], string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (!in_array($this->value->get(), $values)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the value is not one of the given values.
     *
     * @param array $values
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isNotOneOf(array $values = [], string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        if (in_array($this->value->get(), $values)) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Checks if the array has unique values.
     *
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function isUnique(string $errorMessage = "", mixed $data = null): Validator
    {
        if (!is_array($this->value->get())) {
            $this->error($errorMessage, $data);
        }

        if (count($this->value->get()) !== count(array_unique($this->value->get()))) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * Modifies the value with a custom callback.
     *
     * @param callable $callback
     * @return $this
     */
    public function transform(callable $callback): Validator
    {
        if ($this->value->locked()) return $this;

        $result = call_user_func($callback, $this->value->get());
        $this->value->set($result);
        return $this;
    }

    /**
     * Validates the value with a custom callback.
     *
     * @param callable $callback
     * @return $this
     */
    public function validate(callable $callback): Validator
    {
        if ($this->value->locked()) return $this;

        $callback($this->value->get());
        return $this;
    }

    /**
     * @param int $min
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function min(int $min, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        $num_to_compare = $this->value->get();
        if ($this->value->isString()) {
            $num_to_compare = strlen($num_to_compare);
        }

        if ($this->value->isArray()) {
            $num_to_compare = count($num_to_compare);
        }

        if ($num_to_compare < $min) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * @param int $max
     * @param string $errorMessage
     * @param mixed|null $data
     * @return $this
     * @throws ValidatorException
     */
    public function max(int $max, string $errorMessage = "", mixed $data = null): Validator
    {
        if ($this->value->locked()) return $this;

        $num_to_compare = $this->value->get();
        if ($this->value->isString()) {
            $num_to_compare = strlen($num_to_compare);
        }

        if ($this->value->isArray()) {
            $num_to_compare = count($num_to_compare);
        }

        if ($num_to_compare > $max) {
            $this->error($errorMessage, $data);
        }

        return $this;
    }

    /**
     * @throws ValidatorException
     */
    private function error(string $errorMessage, mixed $data = null): void
    {
        throw new ValidatorException($errorMessage, $data);
    }

    public function get(): mixed
    {
        return $this->value->get();
    }

}
