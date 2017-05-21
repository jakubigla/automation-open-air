<?php

namespace OpenAirTest\Parser;

use OpenAir\App;
use OpenAir\Parser\ForeachParser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testForeach()
    {
        $value = '{% for item in modules | moduleName on_site %}
        {{ item.name }}
        {% endfor %}';

        $container = require 'config/container.php';
        $openAir = new App($container, 'ons-newport');
        $config  = $openAir->getConfig();

        $parser = new ForeachParser();
        $result = $parser->gerParsed($value, $config);
        $this->assertEquals('
        Travel to the ONS office in Newport
        ', $result);
    }
}
