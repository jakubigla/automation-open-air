<?php declare(strict_types=1);

namespace App\Filter;

use App\Module\ModuleInterface;

/**
 * Class ModuleName
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class ModuleName implements FilterInterface
{
    /** @var string */
    private $moduleName;

    /**
     * ModuleName constructor.
     *
     * @param string $moduleName
     */
    public function __construct(string $moduleName)
    {
        $this->moduleName = $moduleName;
    }

    public function filter($value)
    {
        $filtered = [];
        foreach ($value as $module) {
            if ($module[ModuleInterface::KEY_MODULE_NAME] == $this->moduleName) {
                $filtered[] = $module;
            }
        }

        return $filtered;
    }
}
