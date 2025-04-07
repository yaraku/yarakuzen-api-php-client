<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2;

use YarakuTranslate\TranslateApiV2\Exceptions\ErrorCodes;

class Client
{
    private string $apiKey;
    private string $apiUrl;
    private CurlService $curlService;

    public function __construct(
        string $apiKey,
        string $apiUrl
    ) {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->curlService = new CurlService();
    }

    /**
     * @throws Exceptions\Client\ApiAccessDeniedException
     * @throws Exceptions\Client\AuthKeyInvalidException
     * @throws Exceptions\Client\AuthKeyOwnerDeactivatedException
     * @throws Exceptions\Client\DailyCharacterLimitReachedException
     * @throws Exceptions\Client\MachineTranslationEngineNotConfigured
     * @throws Exceptions\Client\MinuteCharacterLimitReachedException
     * @throws Exceptions\Client\MinuteRequestLimitReachedException
     * @throws Exceptions\Client\RequestCharacterLimitReachedException
     * @throws Exceptions\Client\ClientResponseException
     * @throws Exceptions\ServerResponseException
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
     * @throws Exceptions\Client\ApiAccessDeniedException
     * @throws Exceptions\Client\AuthKeyInvalidException
     * @throws Exceptions\Client\AuthKeyOwnerDeactivatedException
     * @throws Exceptions\Client\DailyCharacterLimitReachedException
     * @throws Exceptions\Client\MachineTranslationEngineNotConfigured
     * @throws Exceptions\Client\MinuteCharacterLimitReachedException
     * @throws Exceptions\Client\MinuteRequestLimitReachedException
     * @throws Exceptions\Client\RequestCharacterLimitReachedException
     * @throws Exceptions\Client\ClientResponseException
     * @throws Exceptions\ServerResponseException
     *
     * @return string[]
     */
    private function handleErrorsAndTransform(CurlResponse $result): array
    {
        if ($result->isResponseOk()) {
            return $result->getTranslations();
        }

        switch ($result->getErrorCode()) {
            case ErrorCodes::API_ACCESS_DENIED:
                throw new Exceptions\Client\ApiAccessDeniedException($result);
            case ErrorCodes::AUTH_KEY_INVALID:
                throw new Exceptions\Client\AuthKeyInvalidException($result);
            case ErrorCodes::AUTH_KEY_NOT_STRING:
                throw new Exceptions\Client\AuthKeyNotStringException($result);
            case ErrorCodes::AUTH_KEY_OWNER_DEACTIVATED:
                throw new Exceptions\Client\AuthKeyOwnerDeactivatedException($result);
            case ErrorCodes::DAILY_CHARACTER_LIMIT_EXCEEDED:
                throw new Exceptions\Client\DailyCharacterLimitReachedException($result);
            case ErrorCodes::MACHINE_TRANSLATION_ENGINE_NOT_CONFIGURED:
                throw new Exceptions\Client\MachineTranslationEngineNotConfigured($result);
            case ErrorCodes::MINUTE_CHARACTER_LIMIT_EXCEEDED:
                throw new Exceptions\Client\MinuteCharacterLimitReachedException($result);
            case ErrorCodes::MINUTE_REQUEST_LIMIT_EXCEEDED:
                throw new Exceptions\Client\MinuteRequestLimitReachedException($result);
            case ErrorCodes::REQUEST_CHARACTER_LIMIT_EXCEEDED:
                throw new Exceptions\Client\RequestCharacterLimitReachedException($result);
            default:
                if ($result->isClientError()) {
                    throw new Exceptions\Client\ClientResponseException($result);
                }
                throw new Exceptions\ServerResponseException($result);
        }
    }
}
