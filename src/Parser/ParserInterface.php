<?php

namespace OpenAir\Parser;

/**
 * Interface ParserInterface
 *
 * @package OpenAir\Parser
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
