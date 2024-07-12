<?php
namespace Fabrication\Tests;

use PHPUnit\Framework\TestCase;
use Fabrication\FabricationEngine;

class FabricationEngineHtml5Test extends TestCase
{
    public function testDoctypeHTML5()
    {
        $engine = new FabricationEngine();

        $this->assertEquals('html.5', $engine->setOption('doctype', 'html.5'));
        $this->assertEquals('html.5', $engine->getOption('doctype'));
        $this->assertEquals('html.5', $engine->getOption('doctype'));
        $this->assertEquals('<!DOCTYPE HTML>', $engine->getDoctype());
    }
}
