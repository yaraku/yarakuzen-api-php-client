<?php

declare(strict_types=1);

namespace YarakuZenTranslateApi;

use Throwable;
use YarakuZenTranslateApi\Exceptions\ServerResponseException;

class Client
{
    const DEFAULT_PRODUCTION_URL = "https://app.yarakuzen.com/api/translate/v2";

    private string $apiKey;
    private string $apiUrl;

    public function __construct(
        string $apiKey,
        string $apiUrl = self::DEFAULT_PRODUCTION_URL
    ) {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }

    /**
     * @var string[] $texts
     * @return string[]
     */
    public function translate(
        array $texts,
        string $textLanguage,
        string $translationLanguage
    ): array {
        $result = $this->makeRequest($this->createPayload(
            $texts,
            $textLanguage,
            $translationLanguage
        ));
        return $this->handleErrorsAndTransform($result);
    }

    /**
     * @var string[] $texts
     * @return string[]
     */
    private function createPayload(
        array $texts,
        string $sourceLanguage,
        string $targetLanguage
    ): array {
        return [
            'authKey' => $this->apiKey,
            'texts' => $texts,
            'textLanguage' => $sourceLanguage,
            'translationLanguage' => $targetLanguage
        ];
    }

    /**
     * @var string[] $payload
     */
    private function makeRequest(array $payload): array
    {
        $channel = curl_init($this->apiUrl);
        curl_setopt($channel, CURLOPT_POST, true);
        curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($channel, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($channel);
        $httpCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        curl_close($channel);
        return array_merge(['http-code' => $httpCode], json_decode($response, true));
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
            throw new ServerResponseException(
                $exception->getMessage(),
                $httpCode,
                'Server Response formatted incorrectly'
            );
        }

        switch ($errorCode) {
            case 'apiAccessDenied':
                throw new Exceptions\Client\ApiAccessDeniedException($errorCode, $httpCode, $message);
            case 'authKeyInvalid':
                throw new Exceptions\Client\AuthKeyInvalidException($errorCode, $httpCode, $message);
            case 'authKeyNotString':
                throw new Exceptions\Client\AuthKeyNotStringException($errorCode, $httpCode, $message);
            case 'authKeyOwnerDeactivated':
                throw new Exceptions\Client\AuthKeyOwnerDeactivatedException($errorCode, $httpCode, $message);
            case 'dailyCharacterLimitExceeded':
                throw new Exceptions\Client\DailyCharacterLimitReachedException($errorCode, $httpCode, $message);
            case 'machineTranslationEngineNotConfigured':
                throw new Exceptions\Client\MachineTranslationEngineNotConfigured($errorCode, $httpCode, $message);
            case 'minuteCharacterLimitExceeded':
                throw new Exceptions\Client\MinuteCharacterLimitReachedException($errorCode, $httpCode, $message);
            case 'minuteRequestLimitExceeded':
                throw new Exceptions\Client\MinuteRequestLimitReachedException($errorCode, $httpCode, $message);
            case 'requestCharacterLimitExceeded':
                throw new Exceptions\Client\RequestCharacterLimitReachedException($errorCode, $httpCode, $message);
            default:
                if ($this->isClientError($httpCode)) {
                    throw new Exceptions\Client\ClientResponseException($errorCode, $httpCode, $message);
                }
                throw new Exceptions\ServerResponseException($errorCode, $httpCode, $message);
        }
    }

    private function isClientError(int $httpCode): bool
    {
        return strval($httpCode)[0] === '4';
    }
}
