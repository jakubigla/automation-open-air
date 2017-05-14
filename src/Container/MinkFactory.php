<?php

namespace OpenAir\Container;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MinkFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mink = new Mink([
            'selenium' => new Session(new Selenium2Driver(
                'chrome',
                null,
                'http://webdriver:4444/wd/hub'
            ))
        ]);

        $mink->setDefaultSessionName('selenium');

        return $mink;
    }
}
