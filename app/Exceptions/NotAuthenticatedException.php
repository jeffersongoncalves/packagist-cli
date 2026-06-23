<?php

namespace App\Exceptions;

use RuntimeException;

class NotAuthenticatedException extends RuntimeException
{
    public function __construct(string $message = "Not authenticated. Run 'packagist auth' first.")
    {
        parent::__construct($message);
    }
}
