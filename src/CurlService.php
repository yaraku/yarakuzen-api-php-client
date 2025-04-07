<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2;

class CurlService
{
    /**
     * @param string[] $payload
     */
    public function makeRequest(array $payload, string $apiUrl): CurlResponse
    {
        $channel = curl_init($apiUrl);
        curl_setopt($channel, CURLOPT_POST, true);
        curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($channel, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($channel);
        $httpCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        curl_close($channel);
        return $this->formatResponse($httpCode, $response);
    }

    public function formatResponse(int $httpCode, string $response): CurlResponse
    {
        $decodedResponse = json_decode($response, true);
        return new CurlResponse(
            $httpCode,
            $decodedResponse['translations'] ?? [],
            $decodedResponse['error']['code'] ?? null,
            $decodedResponse['error']['message'] ?? null
        );
    }
}
