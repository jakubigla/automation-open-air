<?php

namespace OpenAir\Filter;

class RailStationName implements FilterInterface
{
    private $stations;

    /**
     * RailStationName constructor.
     */
    public function __construct()
    {
        $fp = fopen('data/station_codes.csv', 'r');
        $i = 0;
        while ($row = fgetcsv($fp, 999)) {
            if (++$i == 1) {
                continue;
            }

            $this->stations[$row[1]] = $row[0];
        }
    }

    public function filter($value)
    {
        if (! array_key_exists($value, $this->stations)) {
            throw new \RuntimeException('Code not found the station by code');
        }

        return $this->stations[$value];
    }
}
