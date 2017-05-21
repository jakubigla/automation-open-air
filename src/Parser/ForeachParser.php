<?php declare(strict_types=1);

namespace App\Parser;

use App\Filter\FilterInterface;

/**
 * Class ForeachParser
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class ForeachParser implements ParserInterface
{
    const VAR_REGEX = '/\{\%\sfor\s([a-zA-Z0-9]*)\sin\s([a-zA-Z0-9_\.]+)(\s\|\s([a-zA-Z0-9_]+)+(\s([a-zA-Z0-9_\-\s]+)?)?)?\s\%\}(.*)\{\%\sendfor\s\%\}/ims';

    /**
     * @throws \RuntimeException
     */
    public function gerParsed(string $value, array $data): string
    {
        $variableParser = new VariableParser();

        \preg_match_all(self::VAR_REGEX, $value, $matches);

        foreach ($matches[0] as $key => $match) {
            $list = ParserUtils::getValueByPath($matches[2][$key], $data);
            $val  = '';

            $filterName = $matches[4][$key];
            if ($filterName) {
                $args   = \explode(' ', $matches[6][$key]);
                $class  = \sprintf("%s\\%s", 'App\Filter', \ucfirst($filterName));
                $filter = new $class($args[0]);

                if (! $filter instanceof FilterInterface) {
                    throw new \RuntimeException('Invalid filter provided');
                }

                $list = $filter->filter($list);
            }

            foreach ($list as $item) {
                $itemVal = $variableParser->gerParsed($matches[7][$key], array_merge($data, ['item' => $item]));
                $val .= $itemVal;
            }

            $value = \str_replace($match, $val, $value);
        }

        return (string)$value;
    }
}
