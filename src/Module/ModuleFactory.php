<?php declare(strict_types=1);

namespace App\Module;

use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Class ModuleFactory
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class ModuleFactory
{
    public static function fromConfig(array $config): ModuleInterface
    {
        $moduleName = $config[ModuleInterface::KEY_MODULE_NAME];
        $filter = new UnderscoreToCamelCase();
        $class  = \sprintf("%s\\%s", 'App\Module', \ucfirst($filter->filter($moduleName)));
        $module = new $class($config);

        if (! $module instanceof ModuleInterface) {
            throw new \RuntimeException('Invalid module');
        }

        return $module;
    }
}
