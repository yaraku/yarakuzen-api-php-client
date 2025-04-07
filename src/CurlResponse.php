<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2;

class CurlResponse
{
    private int $httpCode;
    /** @var string[] $translations */
    private array $translations;
    private ?string $errorCode;
    private ?string $errorMessage;

    public function __construct(
        int $httpCode,
        array $translations,
        string $errorCode = null,
        string $errorMessage = null
    ) {
        $this->httpCode = $httpCode;
        $this->translations = $translations;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function isResponseOk(): bool
    {
        return $this->getHttpCode() === 200;
    }

    public function isClientError(): bool
    {
        return strval($this->getHttpCode())[0] === '4';
    }
}
