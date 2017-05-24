<?php declare(strict_types=1);

namespace App\Module;

use Behat\Mink\Element\NodeElement;
use App\MinkSessionFacade;
use App\OpenAir\Receipt;
use App\OpenAir\Report;

/**
 * Class Subsistence
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Subsistence implements ModuleInterface
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
        $dStart = new \DateTimeImmutable($this->config['in']);
        $dEnd   = new \DateTimeImmutable($this->config['out']);
        $dDiff  = $dStart->diff($dEnd)->days;

        $report = new Report('Subsistence', $this->config['name']);
        $report->create($session);

        for ($i = 0; $i < $dDiff; $i++) {
            $loopDate = $dStart->modify(sprintf('+%d day', $i));
            $receipt  = Receipt::subsistenceFactory(
                $this->config['client'],
                $this->config['task'],
                $loopDate->format('Y-m-d')
            );

            if (($i == 0 && $report->exists()) || $i !== 0) {
                $receipt->create($session);
            }

            $receipt->fillAndSave($session);
        }

        if ($this->config['travel_bonus']) {
            $receipt  = Receipt::travelBonusFactory(
                $this->config['client'],
                $this->config['task'],
                $dStart->format('Y-m-d'),
                'From home to the client\'s office'
            );
            $receipt->create($session);
            $receipt->fillAndSave($session);

            $receipt  = Receipt::travelBonusFactory(
                $this->config['client'],
                $this->config['task'],
                $dEnd->format('Y-m-d'),
                'From the client\'s office to home'
            );
            $receipt->create($session);
            $receipt->fillAndSave($session);
        }

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
