<?php declare(strict_types=1);

namespace App\Module;

use Behat\Mink\Element\NodeElement;
use App\MinkSessionFacade;
use App\OpenAir\Receipt;
use App\OpenAir\Report;

/**
 * Class OnSite
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
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
        $this->config = $config;
    }

    public function run(MinkSessionFacade $session): void
    {
        $report = new Report('Travel Request', $this->config['name']);
        $report->create($session);

        $receipt = Receipt::trainFareFactory(
            $this->config['client'],
            $this->config['task'],
            $this->config['in'],
            $this->config['travel']['note']
        );

        $this->config['output']['expense_id'] = $report->getExpenseId();

        if ($report->exists()) {
            $receipt->create($session);
        }

        $receipt->fillAndSave($session);

        $receipt = Receipt::accommodationFactory(
            $this->config['client'],
            $this->config['task'],
            $this->config['in'],
            $this->config['accommodation']['note']
        );

        $receipt->create($session);
        $receipt->fillAndSave($session);

        $report->submit($session);

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
