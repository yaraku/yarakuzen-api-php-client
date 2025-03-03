<?php

declare(strict_types=1);

namespace YarakuZenTranslateApi\Exceptions;

use Throwable;

interface ClientError extends Throwable
{
    function getErrorMessage(): string;
}
