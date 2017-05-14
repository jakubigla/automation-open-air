<?php

namespace OpenAir\Module;

use OpenAir\ConfigParser;
use OpenAir\MinkSessionFacade;

class Authentication implements ModuleInterface
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
        $session->visit('https://www.openair.com/index.pl');
        $session->screenShot('start');

        $page = $session->getPage();
        $page->fillField('input_company', $this->config['company']);
        $page->fillField('input_user', $this->config['user']);
        $page->fillField('input_password', $this->config['password']);
        $session->screenShot('filled');
        $page->findButton('oa_comp_login_submit')->click();

        sleep(3);
        $session->screenShot('logged_in');
    }
}
