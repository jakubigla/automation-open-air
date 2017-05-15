<?php

namespace OpenAirTest\Parser;

use OpenAir\OpenAir;
use OpenAir\Parser\ForeachParser;
use PHPUnit\Framework\TestCase;
use Zend\ServiceManager\ServiceManager;

class ParserTest extends TestCase
{
    public function testForeach()
    {
        $value = '{% for item in modules | moduleName on_site %}
        test 
        {% endfor %}';

        $openAir = new OpenAir(new ServiceManager([]), 'ons-newport');
        $config  = $openAir->getConfig();

        $parser = new ForeachParser();
        $result = $parser->gerParsed($value, $config['modules']);
        $this->assertEquals('
        test
        test
        ', $result);
    }
}
