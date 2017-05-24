<?php declare(strict_types=1);

namespace App\Module;

use App\OpenAir\Timesheet;
use App\OpenAir\TimesheetItem;
use Behat\Mink\Element\NodeElement;
use App\ConfigParser;
use App\MinkSessionFacade;

/**
 * Class Timesheet
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Timesheets implements ModuleInterface
{
    /** @var array */
    private $config = [];

    /** @var string */
    private $startingDate;

    /**
     * Onsite constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config       = $config;
        $this->startingDate = empty($this->config['starting_date'])
            ? date('d-m-y', strtotime('last Monday'))
            : date('d-m-y', strtotime('last Monday', strtotime($this->config['starting_date'])));
    }

    public function run(MinkSessionFacade $session): void
    {
        $lineItems = [];

        foreach ($this->config['items'] as $item) {
            $client = $item['client'];
            $task   = $item['task'];
            unset($item['client']);
            unset($item['task']);

            $lineItems[] = new TimesheetItem($client, $task, $item);
        }

        $timesheet = new Timesheet($this->startingDate, $lineItems);
        $timesheet->create($session);

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
