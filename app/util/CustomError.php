<?php

namespace App\Util;

use Error;

class CustomError extends Error
{
    private int $statusCode;

    public function __construct(
        string $message = "",
        int $statusCode = 400
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
