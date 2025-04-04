<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2\Exceptions;

use RuntimeException;

class ResponseException extends RuntimeException implements Error
{
    protected string $errorMessage;

    public function getErrorCode(): int
    {
        return $this->getCode();
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
