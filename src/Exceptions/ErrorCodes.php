<?php

declare(strict_types=1);

namespace YarakuTranslate\TranslateApiV2\Exceptions;

class ErrorCodes
{
    public const API_ACCESS_DENIED = 'apiAccessDenied';
    public const AUTH_KEY_INVALID = 'authKeyInvalid';
    public const AUTH_KEY_NOT_STRING = 'authKeyNotString';
    public const AUTH_KEY_OWNER_DEACTIVATED = 'authKeyOwnerDeactivated';
    public const DAILY_CHARACTER_LIMIT_EXCEEDED = 'dailyCharacterLimitExceeded';
    public const MACHINE_TRANSLATION_ENGINE_NOT_CONFIGURED = 'machineTranslationEngineNotConfigured';
    public const MINUTE_CHARACTER_LIMIT_EXCEEDED = 'minuteCharacterLimitExceeded';
    public const MINUTE_REQUEST_LIMIT_EXCEEDED = 'minuteRequestLimitExceeded';
    public const REQUEST_CHARACTER_LIMIT_EXCEEDED = 'requestCharacterLimitExceeded';
}
