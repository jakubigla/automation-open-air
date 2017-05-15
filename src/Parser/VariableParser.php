<?php

namespace OpenAir\Parser;

use OpenAir\Filter\FilterInterface;

class VariableParser implements ParserInterface
{
    const VAR_REGEX = '/\{\{\s([a-zA-Z0-9_\.]+)(\s\|\s([a-zA-Z0-9_]+)+(\s([a-zA-Z0-9_\-\s]+)?)?)?\s\}\}/i';

    public function gerParsed(string $value, array $data): string
    {
        \preg_match_all(self::VAR_REGEX, $value, $matches);

        foreach ($matches[0] as $key => $match) {
            $val = ParserUtils::getValueByPath($matches[1][$key], $data);

            $filterName = $matches[3][$key];
            if ($filterName) {
                $args   = \explode(' ', $matches[5][$key]);
                $class  = \sprintf("%s\\%s", 'OpenAir\Filter', \ucfirst($filterName));
                $filter = new $class($args[0]);

                if (! $filter instanceof FilterInterface) {
                    throw new \RuntimeException('Invalid filter provided');
                }

                $val = $filter->filter($val);
            }

            $value = \str_replace($match, $val, $value);
        }

        return (string)$value;
    }
}
