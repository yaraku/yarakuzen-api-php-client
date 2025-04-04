<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2\Exceptions;

use Throwable;

interface ServerError extends Throwable
{
    function getErrorMessage(): string;
}
