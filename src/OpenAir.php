<?php

namespace OpenAir;

use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Driver\Selenium2Driver;
use OpenAir\Module\ModuleInterface;
use Psr\Container\ContainerInterface;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\Filter\Word\UnderscoreToCamelCase;

class OpenAir
{
    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $receiptName;

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

        $modules = (new ConfigProvider($receiptName))->getModules();

        var_dump($modules);exit;

        foreach ($modules as $module) {
            $moduleName = key($module);
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
}
