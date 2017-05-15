<?php

namespace OpenAir\Parser;

/**
 * Class ParserUtils
 *
 * @package OpenAir\Parser
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class ParserUtils
{
    const CONFIG_KEY_SEPARATOR = '.';

    /**
     * Get value from array
     *
     * @param string $path
     * @param array  $data
     * @param string $keySeparator
     *
     * @return string|string[]
     */
    public static function getValueByPath(string $path, array $data, $keySeparator = self::CONFIG_KEY_SEPARATOR)
    {
        $keys = \explode($keySeparator, $path);
        $currentKey = $keys[0];

        if (! \array_key_exists($currentKey, $data)) {
            throw new \RuntimeException(sprintf('Key does not exist: %s', $currentKey));
        }

        $value = $data[$currentKey];

        \array_shift($keys);
        $key = \implode('.', $keys);

        if (\strlen($key) > 0) {
            if (! \is_array($value)) {
                throw new \RuntimeException('Value should be an array');
            }

            return self::getValueByPath($key, $value);
        }

        return $value;
    }
}
