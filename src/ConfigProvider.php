<?php

namespace OpenAir;

use OpenAir\Parser\ParserInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * Class ConfigProvider
 *
 * @package OpenAir
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class ConfigProvider
{
    /** @var array */
    private $config = [];

    /**
     * ConfigProvider constructor.
     *
     * @param string $receiptName
     */
    public function __construct(string $receiptName)
    {
        $receiptFile = \sprintf('receipts/%s.yml', $receiptName);
        if (! \file_exists($receiptFile)) {
            throw new \RuntimeException('Receipt does\'t exist');
        }

        $receiptConfig = \yaml_parse(\file_get_contents($receiptFile));

        $this->config = ArrayUtils::merge($this->getDefaults(), $receiptConfig);

        $this->applyGlobals();
    }

    /**
     * @param ParserInterface $parser
     *
     * @return array
     */
    public function getModules(ParserInterface $parser): array
    {
        $modules = $this->config['modules'];

        foreach ($modules as &$module) {
            $cfg = $module[\key($module)];
            \array_walk_recursive($cfg, function (&$item) use ($parser, $cfg) {
                if (is_string($item)) {
                    $item = $parser->gerParsed($item, $cfg);
                }
            });

            $modules[\key($module)] = $cfg;
        }

        return $modules;
    }

    /**
     * @return array
     */
    private function getDefaults(): array
    {
        $defaultsFile = 'config/defaults.yml';

        if (! \file_exists($defaultsFile)) {
            throw new \RuntimeException('Default config does not exist');
        }

        return \yaml_parse(\file_get_contents($defaultsFile));
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
}
