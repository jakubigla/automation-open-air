<?php declare(strict_types=1);

namespace App\OpenAir;

use App\MinkSessionFacade;

/**
 * Class Receipt
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Receipt
{
    private $_item_id;
    private $_payment_type_id;
    private $_ticket_status;
    private $customer_project;
    private $_project_task_id;
    private $tax_location_id;
    private $date;
    private $vendorLocation;
    private $paymentMethod;
    private $cost;
    private $notes;

    /**
     * Receipt constructor.
     *
     * @param $_item_id
     * @param $_payment_type_id
     * @param $_ticket_status
     * @param $customer_project
     * @param $_project_task_id
     * @param $tax_location_id
     * @param $date
     * @param $vendorLocation
     * @param $paymentMethod
     * @param $cost
     * @param $notes
     */
    public function __construct(
        $_item_id,
        $_payment_type_id,
        $_ticket_status,
        $customer_project,
        $_project_task_id,
        $tax_location_id,
        $date,
        $vendorLocation,
        $paymentMethod,
        $cost,
        $notes
    ) {
        $this->_item_id = $_item_id;
        $this->_payment_type_id = $_payment_type_id;
        $this->_ticket_status = $_ticket_status;
        $this->customer_project = $customer_project;
        $this->_project_task_id = $_project_task_id;
        $this->tax_location_id = $tax_location_id;
        $this->date = $date;
        $this->vendorLocation = $vendorLocation;
        $this->paymentMethod = $paymentMethod;
        $this->cost = $cost;
        $this->notes = $notes;
    }

    public function create(MinkSessionFacade $session): void
    {
        $session->getPage()->find('css', '.oa3_toolbox_button')->click();
        sleep(2);

        $session->getPage()->getHtml();
        $session->getPage()->find('xpath', "//a[contains(text(), 'Receipt')]")->click();
        sleep(3);

        $session->getPage()->find('xpath', "//*[contains(text(), 'New receipt')]")->click();
    }

    public function fillAndSave(MinkSessionFacade $session): void
    {
        $page = $session->getPage();
        $page->selectFieldOption('_item_id', $this->_item_id);
        $page->selectFieldOption('_payment_type_id', $this->_payment_type_id);
        sleep(1);
        $page->selectFieldOption('_ticket_status', $this->_ticket_status);
        $page->selectFieldOption('customer_project', $this->customer_project);
        sleep(2);
        $page->selectFieldOption('_project_task_id', $this->_project_task_id);
        $page->selectFieldOption('tax_location_id', $this->tax_location_id);
        $page->findField('date')->setValue(date('d-m-y', strtotime($this->date)));
        $page->selectFieldOption('custom_96', $this->vendorLocation);

        $paymentMethodElement = $page->findField('custom_125');
        if (! is_null($paymentMethodElement)) {
            $page->selectFieldOption('custom_125', $this->paymentMethod);
        }

        $page->findField('cost')->setValue($this->cost);
        $page->findField('notes')->setValue($this->notes);

        $session->screenShot('Filled receipt');

        $page->findButton('Save')->focus();
        $page->findButton('Save')->click();
        sleep(3);
    }

    public static function trainFareFactory(string $client, string $task, string $date, string $note): self
    {
        $receipt = new self(
            'Travel - Train Fare',
            'Company',
            'Non-reimbursable',
            $client,
            $task,
            'Zero Rate 0%',
            $date,
            'UK',
            'Trainline',
            '0.01',
            $note
        );

        return $receipt;
    }

    public static function accommodationFactory(string $client, string $task, string $date, string $note): self
    {
        $receipt = new self(
            'Accommodation - Hotel',
            'Company',
            'Non-reimbursable',
            $client,
            $task,
            'Zero Rate 0%',
            $date,
            'UK',
            '(blank)',
            '0.01',
            $note
        );

        return $receipt;
    }

    public static function travelBonusFactory(string $client, string $task, string $date, string $note): self
    {
        $receipt = new self(
            'Travel Bonus : £15',
            'Paid by Expenses',
            'Reimbursable',
            $client,
            $task,
            'Zero Rate 0%',
            $date,
            'UK',
            '(blank)',
            '15',
            $note
        );

        return $receipt;
    }

    public static function subsistenceFactory(string $client, string $task, string $date): self
    {
        $receipt = new self(
            'Subsistence - Subsistence Allowance : £35',
            'Paid by Expenses',
            'Reimbursable',
            $client,
            $task,
            'Zero Rate 0%',
            $date,
            'UK',
            '(blank)',
            '35',
            null
        );

        return $receipt;
    }
}
