<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2\Exceptions;

class ServerResponseException extends ResponseException implements ServerError
{
    public function __construct(string $message, int $code, string $errorMessage)
    {
        parent::__construct($message, $code);
        $this->errorMessage = $errorMessage;
    }
}
