<?php declare(strict_types=1);

namespace App\Parser;

use App\Filter\FilterInterface;

/**
 * Class VariableParser
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class VariableParser implements ParserInterface
{
    const VAR_REGEX = '/\{\{\s([a-zA-Z0-9_\.]+)(\s\|\s([a-zA-Z0-9_]+)+(\s([^\}]+)?)?)?\s\}\}/i';

    public function gerParsed(string $value, array $data): string
    {
        \preg_match_all(self::VAR_REGEX, $value, $matches);

        foreach ($matches[0] as $key => $match) {
            $val = ParserUtils::getValueByPath($matches[1][$key], $data);

            $filterName = $matches[3][$key];
            if ($filterName) {
                $args   = \explode(' ', $matches[5][$key]);
                $class  = \sprintf("%s\\%s", 'App\Filter', \ucfirst($filterName));
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
