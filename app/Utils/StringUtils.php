<?php

namespace App\Utils;

class StringUtils
{
    public static function isJson(string $string): bool
    {
        json_decode($string);

        return JSON_ERROR_NONE == json_last_error();
    }
}
