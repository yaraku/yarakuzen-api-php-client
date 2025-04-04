<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2\Exceptions;

use Throwable;

interface ClientError extends Throwable
{
    function getErrorMessage(): string;
}
