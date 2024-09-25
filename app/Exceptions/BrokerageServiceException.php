<?php

namespace App\Exceptions;

use Exception;

class BrokerageServiceException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 500);
    }
}
