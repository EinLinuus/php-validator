<?php
declare(strict_types=1);


class ValidatorException extends Exception implements Throwable
{
    private mixed $data;

    /**
     * @param mixed $message
     * @param mixed $data
     */
    public function __construct(mixed $message, mixed $data = null)
    {
        $this->message = $message;
        $this->data = $data;

        parent::__construct($message);
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

}