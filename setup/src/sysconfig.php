<?php

namespace System\Config;

final class sysconfig
{
    private static $PW_CODE = '[PWHASH]';

    private function __construct()
    {
    }

    /**
     * Get the salted code for password encryption.
     *
     * @return string
     */
    public static function getPwCode(): string
    {
        return self::$PW_CODE;
    }
}
