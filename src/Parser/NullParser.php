<?php declare(strict_types=1);

namespace App\Parser;

/**
 * Class NullParser
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class NullParser implements ParserInterface
{
    public function gerParsed(string $value, array $data): string
    {
        return $value;
    }
}
