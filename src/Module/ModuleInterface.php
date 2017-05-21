<?php declare(strict_types=1);

namespace App\Module;

use App\MinkSessionFacade;

/**
 * Interface ModuleInterface
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
interface ModuleInterface
{
    const KEY_MODULE_NAME = 'module_name';

    public function run(MinkSessionFacade $session): void;

    public function getConfig(): array;
}
