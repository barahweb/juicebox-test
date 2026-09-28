<?php

namespace App\Exceptions;

use Exception;

class WeatherException extends Exception
{
    public function __construct(string $message, public readonly int $statusCode)
    {
        parent::__construct($message);
    }
}
