<?php declare(strict_types=1);

namespace App\Filter;

/**
 * Class RailStationName
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
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

    /**
     * @throws \RuntimeException
     */
    public function filter($value)
    {
        if (! array_key_exists($value, $this->stations)) {
            throw new \RuntimeException('Code not found the station by code');
        }

        return $this->stations[$value];
    }
}
