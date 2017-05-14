<?php

namespace OpenAir;

use OpenAir\Filter\FilterInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * Class ConfigProvider
 *
 * @package OpenAir
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class ConfigProvider
{
    const VAR_REGEX = '/\{\{\s([a-zA-Z0-9_\.]+)(\s\|\s([a-zA-Z0-9_]+)+(\s([a-zA-Z0-9_\-\s]+)?)?)?\s\}\}/i';

    /** @var array */
    private $config = [];

    /**
     * ConfigProvider constructor.
     *
     * @param string $receiptName
     */
    public function __construct(string $receiptName)
    {
        $defaultsFile = 'config/defaults.yml';
        if (! \file_exists($defaultsFile)) {
            throw new \RuntimeException('Default config does\'t exist');
        }
        $defaults = \yaml_parse(\file_get_contents($defaultsFile));

        $receiptFile = \sprintf('receipts/%s.yml', $receiptName);
        if (! \file_exists($receiptFile)) {
            throw new \RuntimeException('Receipt does\'t exist');
        }
        $receiptConfig = \yaml_parse(\file_get_contents($receiptFile));

        $this->config = ArrayUtils::merge($defaults, $receiptConfig);

        $this->applyGlobals();
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        $modules = $this->config['modules'];

        foreach ($modules as &$module) {
            $config = $module[\key($module)];
            \array_walk_recursive($config, function (&$item) use ($config) {
                $item = $this->getParsedValue($item, $config);
            });

            $module[\key($module)] = $config;
        }

        return $modules;
    }

    /**
     * @param string $value
     * @param mixed  $config
     *
     * @return string
     */
    private function getParsedValue(string $value, $config): string
    {
        \preg_match_all(self::VAR_REGEX, $value, $matches);

        foreach ($matches[0] as $key => $match) {
            $val = $this->getFromConfig($matches[1][$key], $config);

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

    /**
     * @return void
     */
    private function applyGlobals(): void
    {
        foreach ($this->config['modules'] as &$moduleContainer) {
            $moduleName   = \key($moduleContainer);
            $moduleConfig = $moduleContainer[$moduleName];

            $config = \array_key_exists('globals', $this->config) ? $this->config['globals'] : [];

            if (\array_key_exists($moduleName, $this->config['module_globals'])) {
                $config = ArrayUtils::merge($config, $this->config['module_globals'][$moduleName]);
            }

            $config = ArrayUtils::merge($config, $moduleConfig);

            $moduleContainer[$moduleName] = $config;
        }
    }

    /**
     * @param string $key
     * @param mixed  $config
     *
     * @return string
     */
    private function getFromConfig(string $key, $config): string
    {
        $keys = \explode('.', $key);
        $currentKey = $keys[0];

        if (! \array_key_exists($currentKey, $config)) {
            throw new \RuntimeException('Key does not exist');
        }

        $value = $config[$currentKey];

        \array_shift($keys);
        $key = \implode('.', $keys);

        if (\strlen($key) > 0) {
            if (! \is_array($value)) {
                throw new \RuntimeException('Value should be an array');
            }

            return $this->getFromConfig($key, $value);
        }

        return $value;
    }
}
