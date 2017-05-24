<?php declare(strict_types=1);

namespace App;

use App\Module\ModuleInterface;
use App\Parser\ParserInterface;
use App\PostAction\PostActionInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * Class ConfigProvider
 *
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

    public function getModules(ParserInterface $parser): array
    {
        $modules = $this->config['modules'];

        foreach ($modules as &$module) {
            $key = \array_keys($module)[0];
            $cfg = $module[$key];
            $cfg[ModuleInterface::KEY_MODULE_NAME] = $key;

            if (isset($cfg['out']) && isset($cfg['out'])) {
                $cfg['out'] = date('Y-m-d', strtotime($cfg['out'], strtotime($cfg['in'])));
            }

            \array_walk_recursive($cfg, function (&$item) use ($parser, $cfg) {
                if (is_string($item)) {
                    $item = $parser->gerParsed($item, $cfg);
                }
            });

            $module = $cfg;
        }

        return $modules;
    }

    public function getPostActions(ParserInterface $parser, array $modulesConfig): array
    {
        if (! array_key_exists('post_actions', $this->config)) {
            return [];
        }

        $postActions = $this->config['post_actions'];

        foreach ($postActions as &$action) {
            $key = \array_keys($action)[0];
            $cfg = $action[$key];
            $cfg[PostActionInterface::KEY_POST_ACTION_NAME] = $key;
            $cfg['modules'] = $modulesConfig;

            \array_walk_recursive($cfg, function (&$item) use ($parser, $cfg) {
                if (is_string($item)) {
                    $item = $parser->gerParsed($item, $cfg);
                }
            });

            $action = $cfg;
        }

        return $postActions;
    }

    public function getRaw(): array
    {
        return $this->config;
    }

    private function getDefaults(): array
    {
        $defaultsFile = 'config/defaults.yml';

        if (! \file_exists($defaultsFile)) {
            throw new \RuntimeException('Default config does not exist');
        }

        return \yaml_parse(\file_get_contents($defaultsFile));
    }

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
