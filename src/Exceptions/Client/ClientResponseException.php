<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2\Exceptions\Client;

use YarakuTranslate\TranslateApiV2\Exceptions;

class ClientResponseException extends Exceptions\ResponseException implements Exceptions\ClientError
{
}
