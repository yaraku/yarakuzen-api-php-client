<?php

declare(strict_types=1);

namespace YarakuZenTranslateApi\Exceptions\Client;

use RuntimeException;
use YarakuZenTranslateApi\Exceptions\ClientError;

class ClientResponseException extends RuntimeException implements ClientError
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
