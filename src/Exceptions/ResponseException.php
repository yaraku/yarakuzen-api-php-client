<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2\Exceptions;

use RuntimeException;
use YarakuTranslate\TranslateApiV2\CurlResponse;

class ResponseException extends RuntimeException implements Error
{
    protected string $errorMessage;

    public function __construct(CurlResponse $error)
    {
        parent::__construct($error->getErrorCode(), $error->getHttpCode());
        $this->errorMessage = $error->getErrorMessage();
    }

    public function getErrorCode(): int
    {
        return $this->getCode();
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
