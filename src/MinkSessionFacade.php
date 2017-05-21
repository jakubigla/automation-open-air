<?php declare(strict_types=1);

namespace App;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Session;

class MinkSessionFacade
{
    /** @var Session */
    private $session;

    /** @var string */
    private $receiptName;

    /** @var string */
    private $namespace = 'general';

    /** @var string */
    private $runId;

    /** @var string */
    private $sid;

    /**
     * MinkSessionFacade constructor.
     *
     * @param Session $session
     * @param string  $receiptName
     */
    public function __construct(Session $session, string $receiptName)
    {
        $this->session     = $session;
        $this->receiptName = $receiptName;
        $this->runId       = date('Y_m_d_H_i_s');
    }

    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function visit(string $url): void
    {
        $this->session->visit($url);
        sleep(3);
    }

    public function screenShot(string $label): void
    {
        $label = str_replace(' ', '_', $label);
        $path = \realpath(\getcwd() . '/screenshots') .
            DIRECTORY_SEPARATOR .
            $this->receiptName .
            DIRECTORY_SEPARATOR .
            $this->runId;

        @\mkdir($path, 0755, true);
        $screenShot = $this->session->getDriver()->getScreenshot();
        \file_put_contents($path . DIRECTORY_SEPARATOR . $this->namespace . '_' . $label . '.png', $screenShot);
        echo "[" . $this->namespace . "] screen shot done: " . $label . PHP_EOL;
    }

    public function getPage(): DocumentElement
    {
        return $this->session->getPage();
    }
}
