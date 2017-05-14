<?php

namespace OpenAir\Module;

use OpenAir\ConfigParser;
use OpenAir\MinkSessionFacade;

class OnSite implements ModuleInterface
{
    /** @var array */
    private $config = [];

    /**
     * Onsite constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config       = $config;
    }

    public function run(MinkSessionFacade $session): void
    {
        $session->getPage()->findLink('Expenses')->click();
        sleep(3);
        $session->screenShot('start');

        $session->getPage()->find('css', '.oa3_toolbox_button')->click();
        sleep(1);

        $session->getPage()->findLink('New ...')->click();
        sleep(3);
        $session->screenShot('new receipt');






        $note = $this->configParser->parse($this->config['travel']['note']);
    }
}
