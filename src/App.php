<?php declare(strict_types=1);

namespace App;

use Behat\Mink\Mink;
use App\Module\ModuleFactory;
use App\Module\ModuleInterface;
use App\PostAction\PostActionFactory;
use App\Parser\ChainParser;
use Psr\Container\ContainerInterface;
use Zend\Filter\Word\CamelCaseToUnderscore;

/**
 * Class App
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class App
{
    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $receiptName;

    /** @var ConfigProvider */
    private $configProvider;

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
        $this->container      = $container;
        $this->receiptName    = $receiptName;
        $this->configProvider = (new ConfigProvider($receiptName));

        foreach ($this->configProvider->getModules(new ChainParser()) as $moduleConfig) {
            $this->modules[] = ModuleFactory::fromConfig($moduleConfig);
        }
    }

    /**
     * @throws \Throwable
     */
    public function run(): void
    {
        $modulesConfig = [];
        $mink          = $this->container->get(Mink::class);
        $sessionFacade = new MinkSessionFacade($mink->getSession(), $this->receiptName);

        try {
            foreach ($this->modules as $module) {
                $sessionFacade->setNamespace(
                    (new CamelCaseToUnderscore())->filter((new \ReflectionClass($module))->getShortName())
                );
                $module->run($sessionFacade);

                $modulesConfig[] = $module->getConfig();
            }
        } catch (\Throwable $exception) {
            $sessionFacade->screenShot('error');
            throw $exception;
        }

        foreach ($this->configProvider->getPostActions(new ChainParser(), $modulesConfig) as $postActionConfig) {
            $postAction = PostActionFactory::fromConfig($postActionConfig);
            $postAction->run();
        }
    }

    public function getConfig(): array
    {
        return $this->configProvider->getRaw();
    }
}
