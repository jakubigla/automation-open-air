<?php

namespace App\OpenAir;

use App\MinkSessionFacade;
use Assert\Assert;
use Behat\Mink\Element\NodeElement;

/**
 * Class Timesheet
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Timesheet
{
    /** @var string */
    private $startingDate;

    /** @var TimesheetItem[] */
    private $lineItems;

    /** @var bool */
    private $exists = false;

    /**
     * Timesheet constructor.
     *
     * @param string          $startingDate
     * @param TimesheetItem[] $lineItems
     */
    public function __construct(string $startingDate, array $lineItems)
    {
        Assert::that($startingDate)->date('d-m-y');

        $this->startingDate = $startingDate;
        $this->lineItems    = $lineItems;
    }

    public function create(MinkSessionFacade $session): void
    {
        $session->getPage()->findLink('Timesheets')->click();
        sleep(3);
        $session->getPage()->getHtml();

        $session->getPage()->find('xpath', "//a[@class='tabLink' and contains(text(), 'Open')]")->click();
        sleep(3);

        $session->screenShot('Timesheets list');

        $item = $session->getPage()
            ->find('xpath', "//a[contains(text(), '" . $this->startingDate . "')]");

        if (! is_null($item)) {
            $item->click();
            $this->exists = true;
            sleep(3);
        } else {
            $session->getPage()->find('css', '.oa3_toolbox_button')->click();
            sleep(2);

            $session->getPage()->findLink('New ...')->click();
            sleep(2);

            $page = $session->getPage();
            $page->selectFieldOption('range', $this->startingDate);

            $page->findButton('save')->click();
            sleep(2);

            $session->screenShot('New timesheet');
        }

        foreach ($this->lineItems as $item) {
            $parentRow = $session->getPage()->find('css', 'table.timesheet tbody tr:last-of-type');
            $parentRow = $session->getPage()->find('css', 'table.timesheet tbody tr[data-row-index="' . $parentRow->getAttribute('data-row-index') . '"]');

            $parentRow->find('css','.timesheetControlPopupCustomerProject')->selectOption($item->getClient());
            sleep(1);
            $parentRow->find('css','.timesheetControlPopup')->selectOption($item->getTask());

            foreach ($item->getHours() as $day => $hours) {
                $class = $this->dayToTdClassName($day);
                $parentRow->find('css', 'td.' . $class . ' input.timesheetInputHour')
                    ->setValue($hours == 0 ? null : $hours);
            }
        }

        //$session->getPage()->find('css', '#save_grid_submit')->click();
        $session->getPage()->find('css', '#timesheet_savebutton')->click();
        sleep(3);
    }

    private function dayToTdClassName(string $day): string
    {
        switch ($day) {
            case 'mon':
                return 'timesheetFixedColumn7';
            case 'tue':
                return 'timesheetFixedColumn6';
            case 'wed':
                return 'timesheetFixedColumn5';
            case 'thu':
                return 'timesheetFixedColumn4';
            case 'fri':
                return 'timesheetFixedColumn3';
            case 'sat':
                return 'timesheetFixedColumn2';
            case 'sun':
                return 'timesheetFixedColumn1';
            default:
                throw new \RuntimeException('Invalid day');
        }
    }
}
