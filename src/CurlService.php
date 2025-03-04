<?php

declare(strict_types=1);

namespace YarakuZenTranslateApi;

class CurlService
{
    /**
     * @var string[] $payload
     * @return string[]
     */
    public function makeRequest(array $payload, string $apiUrl): array
    {
        $channel = curl_init($apiUrl);
        curl_setopt($channel, CURLOPT_POST, true);
        curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($channel, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($channel);
        $httpCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        curl_close($channel);
        return array_merge(['http-code' => $httpCode], json_decode($response, true));
    }
}
