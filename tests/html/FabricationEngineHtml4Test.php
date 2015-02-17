<?php

namespace Fabrication\Tests;

use Fabrication\FabricationEngine;

require_once(dirname(dirname(dirname(__FILE__))) . '/library/FabricationEngine.php');

class FabricationEngineHtml4Test extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->header = 'HEADER';
        $this->content = 'CONTENT';
        $this->footer = 'FOOTER';

        $this->engine = new FabricationEngine();

        // BUG DOMDocument loadHTML stripping self closing tags, but not with loadHTMLFile.
        $this->design = '<!DOCTYPE HTML>' .
                '<html>' .
                '<head>' .
                '<title></title>' .
                '</head>' .
                '<body>' .
                '<div id="header"><img src="test1.png" /></div>' .
                '<div id="content"></div>' .
                '<div id="footer">' .
                '<a href=""><img class="image" src="test2.png" alt="" /></a>' .
                '<a href=""><img src="test3.png" /></a>' .
                '<img src="test4.png" />' .
                '<img src="test5.png"><img src=""><br><hr>' . // invalid line.
                '</div>' .
                '</body>' .
                '</html>';

        $this->expected = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"' .
                "\n" .
                '   "http://www.w3.org/TR/html4/loose.dtd">' .
                "\n" .
                '<html>' .
                '<head>' .
                '<title></title>' .
                '</head>' .
                '<body>' .
                '<div id="header"><img src="test1.png" /></div>' .
                '<div id="content"></div>' .
                '<div id="footer">' .
                '<a href=""><img class="image" src="test2.png" alt="" /></a>' .
                '<a href=""><img src="test3.png" /></a>' .
                '<img src="test4.png" />' .
                '<img src="test5.png" /><img src="" /><br /><hr />' . // process fixed line.
                '</div>' .
                '</body>' .
                "</html>\n";

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

    public function testSanityLoadHtmlString()
    {
        $this->assertEquals(true, $this->engine->getOption('process'));
        //$this->assertEquals(true, $this->engine->setOption('process', false));
        //$this->assertEquals(false, $this->engine->getOption('process'));
        $this->assertTrue($this->engine->run($this->design));

//        $NodeList = $this->engine->getHtml();
//        $this->assertInstanceOf('DOMNodeList', $NodeList);
//        $this->assertEquals(1, $NodeList->length);
//        $this->assertEquals('html', $NodeList->item(0)->tagName);

        //$this->assertEquals($this->expected, $this->engine->outputHTML());
    }

    public function XtestSanityLoadHtmlFile()
    {
        //$this->assertFalse($this->engine->run('missing.html', 'file'));

        $designPath = dirname(dirname(__FILE__)) . '/fixture/design.html';
        $this->assertTrue($this->engine->run($designPath, 'file', 'html'));

        $nodeList = $this->engine->getHtml();
        $this->assertInstanceOf('DOMNodeList', $nodeList);
        $this->assertEquals(1, $nodeList->length);
        $this->assertEquals('html', $nodeList->item(0)->tagName);
    }

    public function XtestMessingWithSearchEngines()
    {
        //$webpage = file_get_contents('http://www.bing.com/');
        //$webpage = file_get_contents('http://www.google.com/');
        $webpage = file_get_contents('http://www.duckduckgo.com/');

        $this->assertTrue($this->engine->run($webpage, 'string', 'html'));

        $NodeList = $this->engine->getHtml();
        $this->assertInstanceOf('DOMNodeList', $NodeList);
        $this->assertEquals(1, $NodeList->length);
        $this->assertEquals('html', $NodeList->item(0)->tagName);
    }

    public function testSanityCreateProcessingInstruction()
    {
        $this->assertTrue($this->engine->run('<html><head><body></html>', 'string', 'html'));

        $this->engine->getElementsByTagName('body')->item(0)->appendChild(
                $this->engine->createProcessingInstruction('php', 'echo PHP_VERSION; ?')
        );

        $this->assertEquals('<body><?php echo PHP_VERSION; ?></body>', $this->engine->view('//body')
        );
    }

}
