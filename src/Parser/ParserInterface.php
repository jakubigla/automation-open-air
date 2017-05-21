<?php declare(strict_types=1);

namespace App\Parser;

/**
 * Interface ParserInterface
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
interface ParserInterface
{
    /**
     * @param string $value
     * @param array  $data
     *
     * @return string
     */
    public function gerParsed(string $value, array $data): string;
}
