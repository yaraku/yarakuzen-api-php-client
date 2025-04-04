<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2\Exceptions;

use Throwable;

interface Error extends Throwable
{
    function getErrorCode(): int;
    function getErrorMessage(): string;
    function getMessage(): string;
}
