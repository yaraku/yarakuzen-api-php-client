<?php

declare(strict_types=1);

namespace YarakuZenTranslateApi\Exceptions;

use RuntimeException;

class ServerResponseException extends RuntimeException implements ServerError
{
    private string $errorMessage;

    public function __construct(string $message, int $code, string $errorMessage)
    {
        parent::__construct($message, $code);
        $this->errorMessage = $errorMessage;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
