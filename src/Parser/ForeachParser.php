<?php

namespace OpenAir\Parser;

use OpenAir\Filter\FilterInterface;

class ForeachParser implements ParserInterface
{
    const VAR_REGEX = '/\{\%\sfor\s([a-zA-Z0-9]*)\sin\s([a-zA-Z0-9_\.]+)(\s\|\s([a-zA-Z0-9_]+)+(\s([a-zA-Z0-9_\-\s]+)?)?)?\s\%\}(.*)\{\%\sendfor\s\%\}/ims';

    public function gerParsed(string $value, array $data): string
    {
        \preg_match_all(self::VAR_REGEX, $value, $matches);

        foreach ($matches[0] as $key => $match) {
            $val = ParserUtils::getValueByPath($matches[2][$key], $data);

            $filterName = $matches[4][$key];
            if ($filterName) {
                $args   = \explode(' ', $matches[6][$key]);
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
