<?php declare(strict_types=1);

namespace App\Module;

use Behat\Mink\Element\NodeElement;
use App\ConfigParser;
use App\MinkSessionFacade;

/**
 * Class Authentication
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Authentication implements ModuleInterface
{
    const FIELD_MAP = [
        'input_company'  => 'company',
        'input_user'     => 'user',
        'input_password' => 'password',
    ];

    /** @var array */
    private $config = [];

    /**
     * Onsite constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function run(MinkSessionFacade $session): void
    {
        $session->visit('https://www.openair.com/index.pl');
        $page = $session->getPage();
        $session->screenShot('start');

        foreach (self::FIELD_MAP as $fieldName => $configKey) {
            $page->fillField($fieldName, $this->config[$configKey]);
        }
        $page->findButton('oa_comp_login_submit')->click();
        $session->screenShot('end');
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
