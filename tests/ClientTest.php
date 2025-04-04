<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use YarakuTranslate\TranslateApiV2;

class ClientTest extends TestCase
{
    private MockObject $curlServiceMock;
    private TranslateApi\Client $client;
    protected function setUp(): void
    {
        $this->curlServiceMock = $this->createMock(TranslateApi\CurlService::class);
        $this->client = new TranslateApi\Client('key', null, $this->curlServiceMock);
    }

    /**
     * @covers Client::translate
     */
    public function testTranslateWithValidResponse(): void
    {
        $this->curlServiceMock->method('makeRequest')
            ->willReturn([
                'translations' => [
                    'ねこ',
                    '犬'
                ],
                'http-code' => 200
            ]);

        $result = $this->client->translate(['Cat', 'Dog'], 'en', 'ja');
        self::assertEquals('ねこ', $result[0]);
        self::assertEquals('犬', $result[1]);
    }

    /**
     * @covers Client::translate
     */
    public function testTranslateWithKnownClientError(): void
    {
        $expectedExceptionCode = 'apiAccessDenied';
        static::expectException(TranslateApi\Exceptions\Client\ApiAccessDeniedException::class);
        static::expectExceptionMessage($expectedExceptionCode);

        $this->curlServiceMock->method('makeRequest')
            ->willReturn([
                'error' => [
                    'code' => $expectedExceptionCode,
                    'message' => 'The access token is invalid.',
                ],
                'http-code' => 404
            ]);
        $this->client->translate(['Cat', 'Dog'], 'en', 'ja');
    }

    /**
     * @covers Client::translate
     */
    public function testTranslateWithUnknownClientError(): void
    {
        static::expectException(TranslateApi\Exceptions\Client\ClientResponseException::class);
        $this->curlServiceMock->method('makeRequest')
            ->willReturn([
                'error' => [
                    'code' => 'unknownError',
                    'message' => 'The access token is invalid.',
                ],
                'http-code' => 402
            ]);
        $this->client->translate(['Cat', 'Dog'], 'en', 'ja');
    }

    /**
     * @covers Client::translate
     */
    public function testTranslateWithServerError(): void
    {
        static::expectException(TranslateApi\Exceptions\ServerResponseException::class);
        $this->curlServiceMock->method('makeRequest')
            ->willReturn([
                'error' => [
                    'code' => 'serverFailed',
                    'message' => 'Something went wrong.',
                ],
                'http-code' => 502
            ]);
        $this->client->translate(['Cat', 'Dog'], 'en', 'ja');
    }

    /**
     * @covers Client::translate
     */
    public function testTranslateWithMalformedResponseFormat(): void
    {
        static::expectException(TranslateApi\Exceptions\ServerResponseException::class);
        $this->curlServiceMock->method('makeRequest')
            ->willReturn([
                'error' => [
                    'wrong-code' => 'unknownError',
                    'wrong-message' => 'The access token is invalid.',
                ],
                'http-code' => 502
            ]);
        $this->client->translate(['Cat', 'Dog'], 'en', 'ja');
    }
}
