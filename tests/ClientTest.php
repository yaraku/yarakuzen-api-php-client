<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use YarakuTranslate\TranslateApiV2;

class ClientTest extends TestCase
{
    private MockObject $curlServiceMock;
    private TranslateApiV2\Client $client;

    protected function setUp(): void
    {
        $this->curlServiceMock = $this->createMock(TranslateApiV2\CurlService::class);
        $this->client = new TranslateApiV2\Client('key', 'key');

        $reflection = new ReflectionClass($this->client);
        $curlServiceProperty = $reflection->getProperty('curlService');
        $curlServiceProperty->setAccessible(true);
        $curlServiceProperty->setValue($this->client, $this->curlServiceMock);
    }

    /**
     * @covers Client::translate
     */
    public function testTranslateWithValidResponse(): void
    {
        $this->curlServiceMock->method('makeRequest')
            ->willReturn(
                new TranslateApiV2\CurlResponse(
                    200,
                    ['ねこ', '犬']
                )
            );

        $result = $this->client->translate(['Cat', 'Dog'], 'en', 'ja');
        self::assertEquals('ねこ', $result[0]);
        self::assertEquals('犬', $result[1]);
    }

    /**
     * @covers Client::translate
     */
    public function testTranslateWithKnownClientError(): void
    {
        $expectedExceptionCode = TranslateApiV2\Exceptions\ErrorCodes::API_ACCESS_DENIED;
        static::expectException(TranslateApiV2\Exceptions\Client\ApiAccessDeniedException::class);
        static::expectExceptionMessage($expectedExceptionCode);

        $this->curlServiceMock->method('makeRequest')
            ->willReturn(
                new TranslateApiV2\CurlResponse(
                    404,
                    [],
                    $expectedExceptionCode,
                    'The access token is invalid.'
                )
            );
        $this->client->translate(['Cat', 'Dog'], 'en', 'ja');
    }

    /**
     * @covers Client::translate
     */
    public function testTranslateWithUnknownClientError(): void
    {
        static::expectException(TranslateApiV2\Exceptions\Client\ClientResponseException::class);
        $this->curlServiceMock->method('makeRequest')
            ->willReturn(
                new TranslateApiV2\CurlResponse(
                    402,
                    [],
                    'unknownError',
                    'The access token is invalid.'
                )
            );
        $this->client->translate(['Cat', 'Dog'], 'en', 'ja');
    }

    /**
     * @covers Client::translate
     */
    public function testTranslateWithServerError(): void
    {
        static::expectException(TranslateApiV2\Exceptions\ServerResponseException::class);
        $this->curlServiceMock->method('makeRequest')
            ->willReturn(
                new TranslateApiV2\CurlResponse(
                    502,
                    [],
                    'serverFailed',
                    'Something went wrong.'
                )
            );
        $this->client->translate(['Cat', 'Dog'], 'en', 'ja');
    }
}
