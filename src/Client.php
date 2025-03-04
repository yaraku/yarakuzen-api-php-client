<?php

declare(strict_types=1);

namespace YarakuZenTranslateApi;

use Throwable;

class Client
{
    const DEFAULT_PRODUCTION_URL = "https://app.yarakuzen.com/api/translate/v2";

    private string $apiKey;
    private string $apiUrl;
    private CurlService $curlService;

    public function __construct(
        string $apiKey,
        string $apiUrl = null,
        CurlService $curlService = null
    ) {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl ?? self::DEFAULT_PRODUCTION_URL;
        $this->curlService = $curlService ?? new CurlService();
    }

    /**
     * @param string[] $texts
     * @return string[]
     */
    public function translate(
        array $texts,
        string $textLanguage,
        string $translationLanguage
    ): array {
        $result = $this->curlService->makeRequest([
            'authKey' => $this->apiKey,
            'texts' => $texts,
            'textLanguage' => $textLanguage,
            'translationLanguage' => $translationLanguage
        ],
            $this->apiUrl
        );
        return $this->handleErrorsAndTransform($result);
    }

    /**
     * @return string[]
     */
    private function handleErrorsAndTransform(array $result): array
    {
        $httpCode = $result["http-code"];

        if ($httpCode === 200) {
            return $result['translations'];
        }

        try {
            $errorCode = $result["error"]["code"];
            $message = $result["error"]["message"];
        } catch (Throwable $exception) {
            throw new Exceptions\ServerResponseException(
                $exception->getMessage(),
                $httpCode,
                'Server Response formatted incorrectly'
            );
        }
        $errorPayload = [$errorCode, $httpCode, $message];
        switch ($errorCode) {
            case 'apiAccessDenied':
                throw new Exceptions\Client\ApiAccessDeniedException(...$errorPayload);
            case 'authKeyInvalid':
                throw new Exceptions\Client\AuthKeyInvalidException(...$errorPayload);
            case 'authKeyNotString':
                throw new Exceptions\Client\AuthKeyNotStringException(...$errorPayload);
            case 'authKeyOwnerDeactivated':
                throw new Exceptions\Client\AuthKeyOwnerDeactivatedException(...$errorPayload);
            case 'dailyCharacterLimitExceeded':
                throw new Exceptions\Client\DailyCharacterLimitReachedException(...$errorPayload);
            case 'machineTranslationEngineNotConfigured':
                throw new Exceptions\Client\MachineTranslationEngineNotConfigured(...$errorPayload);
            case 'minuteCharacterLimitExceeded':
                throw new Exceptions\Client\MinuteCharacterLimitReachedException(...$errorPayload);
            case 'minuteRequestLimitExceeded':
                throw new Exceptions\Client\MinuteRequestLimitReachedException(...$errorPayload);
            case 'requestCharacterLimitExceeded':
                throw new Exceptions\Client\RequestCharacterLimitReachedException(...$errorPayload);
            default:
                if ($this->isClientError($httpCode)) {
                    throw new Exceptions\Client\ClientResponseException(...$errorPayload);
                }
                throw new Exceptions\ServerResponseException(...$errorPayload);
        }
    }

    private function isClientError(int $httpCode): bool
    {
        return strval($httpCode)[0] === '4';
    }
}
