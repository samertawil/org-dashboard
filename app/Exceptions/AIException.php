<?php

namespace App\Exceptions;

use Exception;

class AIException extends Exception
{
    public static function quotaExceeded(string $message = 'AI Quota exceeded.'): self
    {
        return new self($message, 429);
    }

    public static function connectionError(string $message = 'Connection to AI service failed.'): self
    {
        return new self($message, 503);
    }

    public static function generalError(string $message = 'An unexpected AI error occurred.'): self
    {
        return new self($message, 500);
    }
}
