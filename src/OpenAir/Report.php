<?php declare(strict_types=1);

namespace App\OpenAir;

use Behat\Mink\Element\NodeElement;
use App\MinkSessionFacade;

class Report
{
    private $type;
    private $name;
    private $exists = false;
    private $expense_id;

    /**
     * Report constructor.
     *
     * @param $type
     * @param $name
     */
    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    public function create(MinkSessionFacade $session): void
    {
        $session->getPage()->findLink('Expenses')->click();
        sleep(3);
        $session->getPage()->getHtml();

        $session->getPage()->find('xpath', "//a[@class='tabLink' and contains(text(), 'Open')]")->click();
        sleep(3);
        $session->screenShot('Expenses list');

        $element = $session->getPage()->find('xpath', "//*[contains(text(), '" . $this->name . "')]");
        if (! is_null($element)) {
            $this->exists = true;
            $element->click();
        } else {
            $session->getPage()->find('css', '.oa3_toolbox_button')->click();
            sleep(2);

            $session->getPage()->findLink('New ...')->click();
            sleep(2);

            $page = $session->getPage();

            $page->fillField('name', $this->name);
            $page->selectFieldOption('custom_117', $this->type);

            $session->screenShot('New ' . $this->type);

            $page->findButton('save')->click();
        }

        sleep(3);
        $element = $session->getPage()->find('css', '#app_header_title .aht_top');
        $txt = $element->getText();
        preg_match('|(^[0-9])*([0-9]+).*|', $txt, $match);
        $this->expense_id = $match[2];
    }

    public function submit(MinkSessionFacade $session): void
    {
        $session->getPage()->findLink('Submit/Approve')->click();
        sleep(3);
        $session->screenShot('Submit');

        //todo: do the actual submit
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function getExpenseId(): string
    {
        return $this->expense_id;
    }
}
