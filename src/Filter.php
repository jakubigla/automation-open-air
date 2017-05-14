<?php

namespace OpenAir;

class Filter
{
    public static function dateFormat(string $date, string $format): string
    {

    }

    public static function getNameByCode(string $code): string
    {
        return Stations::getNameByCode($code);
    }
}
