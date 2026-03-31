<?php

namespace Modules\Shared\Domain\Exceptions;

use Exception;

class DomainException extends Exception
{
    /**
     * Base class for all errors related to business rule violations.
     *
     * @param string $message  The exception message.
     * @param int $code  The HTTP error code associated with the exception.
     * @return void
     */
    public function __construct(string $message = "Domain logic violation", int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
