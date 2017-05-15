<?php

namespace OpenAir;

use Behat\Mink\Mink;
use OpenAir\Module\ModuleInterface;
use OpenAir\Parser\ChainParser;
use Psr\Container\ContainerInterface;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\Filter\Word\UnderscoreToCamelCase;

class OpenAir
{
    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $receiptName;

    /** @var array */
    private $config;

    /** @var ModuleInterface[] */
    private $modules = [];

    /**
     * OpenAir constructor.
     *
     * @param ContainerInterface $container
     * @param string             $receiptName
     */
    public function __construct(ContainerInterface $container, string $receiptName)
    {
        $this->container   = $container;
        $this->receiptName = $receiptName;

        $this->config = (new ConfigProvider($receiptName))->getModules(new ChainParser());

        foreach ($this->config['modules'] as $module) {
            $moduleName = \key($module);
            $this->addModule($moduleName, $module[$moduleName]);
        }
    }

    public function run()
    {
        $mink = $this->container->get(Mink::class);
        $sessionFacade = new MinkSessionFacade($mink->getSession(), $this->receiptName);

        foreach ($this->modules as $module) {
            $sessionFacade->setNamespace((new CamelCaseToUnderscore())->filter((new \ReflectionClass($module))->getShortName()));
            $module->run($sessionFacade);
        }
    }

    public function addModule(string $moduleName, array $config): void
    {
        $filter = new UnderscoreToCamelCase();
        $class  = sprintf("%s\\%s", 'OpenAir\Module', \ucfirst($filter->filter($moduleName)));
        $module = new $class($config);

        if (! $module instanceof ModuleInterface) {
            throw new \RuntimeException('Invalid module');
        }

        $this->modules[] = $module;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
