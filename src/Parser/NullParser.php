<?php

namespace OpenAir\Parser;

class NullParser implements ParserInterface
{
    public function gerParsed(string $value, array $data): string
    {
        return $value;
    }
}
