<?php
namespace Fabrication\Tests;

use PHPUnit\Framework\TestCase;
use Fabrication\FabricationEngine;

class FabricationEngineHtml5Test extends TestCase
{
    public function setUp(): void
    {
        $this->engine = new FabricationEngine();

        $this->html = 
            '<!DOCTYPE HTML>'.
            '<html>'.
            '<head>'.
            '<title>Hello World!</title>'.
            '</head>'.
            '<body>'.
            'Hello World'.
            '</body>'.
            '</html>';
	
        // default doctype.
        $this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));

        // doctype for html5 testcases.
        $this->engine->setOption('doctype', 'html.5');
    }

    public function testDoctypeHTML5()
    {
        $this->assertEquals('html.5', $this->engine->getOption('doctype'));
        $this->assertEquals('<!DOCTYPE HTML>', $this->engine->getDoctype());
    }
}
