<?php

namespace OpenAir\Module;

use OpenAir\MinkSessionFacade;

interface ModuleInterface
{
    public function run(MinkSessionFacade $session): void;
}
