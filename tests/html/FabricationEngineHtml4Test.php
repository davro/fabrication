<?php

namespace Fabrication\Tests;

use Fabrication\FabricationEngine;

class FabricationEngineHtml4Test extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->header  = 'HEADER';
        $this->content = 'CONTENT';
        $this->footer  = 'FOOTER';

        $this->engine = new FabricationEngine();

        $this->assertInternalType('object', $this->engine);
        $this->assertInstanceOf('Fabrication\FabricationEngine', $this->engine);
    }

    public function testDoctypeHTML4Strict()
    {
        $this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));

        $this->engine->setOption('doctype', 'html.4.01.strict');
        $this->assertEquals('html.4.01.strict', $this->engine->getOption('doctype'));
        $this->assertEquals('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"' . "\n" . '   "http://www.w3.org/TR/html4/strict.dtd">'
                , $this->engine->getDoctype()
        );
    }

    public function testDoctypeHTML4Transitional()
    {
        $this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));

        $this->engine->setOption('doctype', 'html.4.01.transitional');
        $this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
        $this->assertEquals('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"' . "\n" . '   "http://www.w3.org/TR/html4/loose.dtd">'
                , $this->engine->getDoctype()
        );
    }

    public function testDoctypeHTML4Frameset()
    {
        // test default doctype.
        $this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));

        $this->engine->setOption('doctype', 'html.4.01.frameset');
        $this->assertEquals('html.4.01.frameset', $this->engine->getOption('doctype'));
        $this->assertEquals('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"' . "\n" . '   "http://www.w3.org/TR/html4/frameset.dtd">'
                , $this->engine->getDoctype()
        );
    }
}
