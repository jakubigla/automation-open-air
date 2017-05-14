<?php

namespace OpenAir;

class Stations
{
    public static function getNameByCode(string $code): string
    {
        $fp = fopen('data/station_codes.csv', 'r');
        $i = 0;
        while ($row = fgetcsv($fp, 999)) {
            if (++$i == 1) {
                continue;
            }

            if ($row[1] === $code) {
                return $row[0];
            }
        }

        throw new \RuntimeException('Code not found');
    }
}