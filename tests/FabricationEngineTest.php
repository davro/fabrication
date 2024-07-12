<?php

namespace Fabrication\Tests;

use PHPUnit\Framework\TestCase;
use Fabrication\FabricationEngine;
use Fabrication\Html\Table;
use Fabrication\Html\Form;

// Fabrication Framework minimum configuration.
define('FRAMEWORK_ROOT_DIR', dirname(dirname(__FILE__)));
define('FRAMEWORK_ROOT_PHAR', false);  // testing phar archive.
define('FRAMEWORK_VERSION', 0.1);
define('FRAMEWORK_ENVIRONMENT', 'dev');
define('FRAMEWORK_DISPATCHER', false);

// Fabrication Project minimum configuration.
define('PROJECT_HOSTNAME', 'localhost');
define('PROJECT_NAME', 'workspace');
define('PROJECT_ROOT_DIR', realpath(dirname(dirname(__FILE__))));

/**
 * Fabrication Engine TestCase.
 */
class FabricationEngineTest extends TestCase
{
    public function setUp(): void
    {
        $this->engine = new FabricationEngine();
    }

    public function testInstance()
    {
        $engine = new FabricationEngine();
        $this->assertIsObject($engine);
        $this->assertInstanceOf('Fabrication\FabricationEngine', $engine);
    }

    public function testAttributes()
    {
        $engine = new FabricationEngine();
        $this->assertObjectHasProperty('input',   $engine);
        $this->assertObjectHasProperty('output',  $engine);
        $this->assertObjectHasProperty('options', $engine);
    }

    public function testGetEngine()
    {
        $engine = new FabricationEngine();
        $newEngine = $engine->getEngine();
        $this->assertIsObject($newEngine);
        $this->assertInstanceOf('Fabrication\FabricationEngine', $newEngine);
    }

    public function testTimeStarted()
    {
        $engine = new FabricationEngine();
        $this->assertIsFloat($engine->timeStarted());
    }

    public function testTimeTaken()
    {
        $engine = new FabricationEngine();
        $this->assertIsFloat($engine->timeTaken());
    }

    public function testDumpString()
    {
        $result = FabricationEngine::dump("Hello, World!", true);
        $this->assertStringContainsString("Hello, World!", $result);
    }

    public function testDumpArray()
    {
        $array = ['foo' => 'bar', 'baz' => 123];
        $expectedOutput = "\n\n" .
            str_repeat('-', 80) . "\n" .
            "| DUMP Type: array\tReturn: false\n" .
            str_repeat('-', 80) . "\n\n" .
            "['foo'] => 'bar'\n" .
            "['baz'] => 123\n\n";

        $result = FabricationEngine::dump($array, true);
        $this->assertEquals($expectedOutput, $result);
    }

    public function testDumpObject()
    {
        $dom = new \DOMDocument();
        $result = FabricationEngine::dump($dom, true);
        $this->assertStringContainsString("DOMDocument", $result);
    }

    public function testDumpNull()
    {
        $result = FabricationEngine::dump(null, true);
        $this->assertStringContainsString("NULL", $result);
    }

    // This example code is copyed into the README.md testing for consistency.
    public function testReadmeExample1()
    {
        $engine = new FabricationEngine();
        $engine->input('hello', 'world');

        $this->assertEquals($engine->output('hello'), 'world');
    }

    public function testReadmeExample2()
    {
        $engine = new FabricationEngine();
        $engine->input('#hello', 'world');
        $template = '<html><head></head><body><div id="hello"></div></body></html>\n';
        $engine->run($template);

        // Assertions.
        $this->assertEquals($engine->output('#hello'), 'world');
        $this->assertEquals('world', $engine->view('//div[@id="hello"]/text()'));
    }

    public function testReadmeExample3() {
        $engine = new FabricationEngine();
        $engine->input('.hello', 'world');
        $template = '<html><head></head><body><div class="hello"></div></body></html>';
        $engine->run($template);

        // Assertions.
        $this->assertEquals($engine->output('.hello'), 'world');
        $this->assertEquals('world', $engine->view('//div[@class="hello"]/text()'));
    }

    public function testReadmeExampleOutput()
    {
        $engine = new FabricationEngine();
        $engine->input('hello', 'world');
        $tag = '>';
        $this->assertEquals("<?php\n\$hello=\"world\";\n?$tag", $engine->output('hello', 'php')
        );
    }

    public function testReadmeExampleOutput1()
    {
        $engine = new FabricationEngine();
        $engine->input('hello', 'world');
        $this->assertEquals(
                "<?php
\$data=array(
'hello'=>'world',
);
?>", $engine->output('hello', 'php.array')
        );
    }

    public function testReadmeExampleOutput2()
    {
        $tag = '>';
        $engine = new FabricationEngine();
        $engine->input('hello', 'world');
        $this->assertEquals("<?php\n\$data=new stdClass;\n\$data->hello='world';\n?$tag", $engine->output('hello', 'php.class')
        );
    }

    public function testReadmeExampleOutput3()
    {
        $engine = new FabricationEngine();
        $engine->input('body', array('bgcolor' => '#999999'));
        $this->assertEquals("body {\nbgcolor: #999999;\n}\n", $engine->output('body', 'css')
        );
    }

    public function testReadmeExampleOptionDoctype()
    {
        $engine = new FabricationEngine();
        $template = '<html><head></head><body></body></html>';
        $engine->run($template);

        $this->assertEquals('html.5', $engine->setOption('doctype', 'html.5'));
        $this->assertEquals('<!DOCTYPE HTML>', $engine->getDoctype());
    }

    public function testReadmeExampleCreate()
    {
        $engine = new FabricationEngine();

        $hi = $engine->create('div', 'Hello World', array('id' => 'hello-world'));

        $this->assertEquals('div', $hi->nodeName);
        $this->assertEquals('Hello World', $hi->nodeValue);
        $this->assertEquals('id', $hi->attributes->getNamedItem('id')->nodeName);
        $this->assertEquals('hello-world', $hi->attributes->getNamedItem('id')->nodeValue);

        $engine->appendChild($hi);

        $this->assertEquals("<div id=\"hello-world\">Hello World</div>\n",
            $engine->saveHTML()
        );
    }

    public function testReadmeExampleCreate1()
    {
        $engine = new FabricationEngine();

        $hi = $engine->create('div', '', array('id' => 'hello-world'), array(
            array('name' => 'u', 'value' => 'Hello',
                'attributes' => array('id' => 'hello'),
                'children' => array()
            ),
            array('name' => 'strong',
                'attributes' => array('id' => 'world'),
                'children' => array(
                    array('name' => 'i', 'value' => 'W'),
                    array('name' => 'i', 'value' => 'o'),
                    array('name' => 'i', 'value' => 'r'),
                    array('name' => 'i', 'value' => 'l'),
                    array('name' => 'i', 'value' => 'd')
                )
            )
                )
        );

        $this->assertEquals('div', $hi->nodeName);
        $this->assertEquals('HelloWorld', $hi->nodeValue);
        $this->assertEquals('id', $hi->attributes->getNamedItem('id')->nodeName);
        $this->assertEquals('hello-world', $hi->attributes->getNamedItem('id')->nodeValue);

        $engine->appendChild($hi);

        $this->assertEquals(
            '<strong id="world"><i>W</i><i>o</i><i>r</i><i>l</i><i>d</i></strong>',
            $engine->view('//div[@id="hello-world"]/strong[@id="world"]')
        );
    }

    public function testDoctypeDefault()
    {
        $engine = new FabricationEngine();

        $this->assertEquals(
                '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"' .
                "\n" . '   "http://www.w3.org/TR/html4/loose.dtd">'
                , $engine->getDoctype()
        );
    }

    public function testDoctypeHTML5()
    {
        $engine = new FabricationEngine();

        // Change the doctype option.
        $engine->setOption('doctype', 'html.5');

        // Assertion doctype option.
        $this->assertEquals('html.5', $engine->getOption('doctype'));

        // Assertion doctype output.
        $this->assertEquals('<!DOCTYPE HTML>'
                , $engine->getDoctype()
        );
    }

    public function testDoctypeXHTML1Strict()
    {
        $engine = new FabricationEngine();

        // Change the doctype option.
        $engine->setOption('doctype', 'xhtml.1.0.strict');

        // Assertion doctype option.
        $this->assertEquals('xhtml.1.0.strict', $engine->getOption('doctype'));

        // Assertion doctype output.
        $this->assertEquals(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' .
            "\n" . '   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
            $engine->getDoctype()
        );
    }

    public function testDoctypeXHTML1Frameset()
    {
        $engine = new FabricationEngine();

        // Change the doctype option.
        $engine->setOption('doctype', 'xhtml.1.0.frameset');

        // Assertion doctype option.
        $this->assertEquals('xhtml.1.0.frameset', $engine->getOption('doctype'));

        // Assertion doctype output.
        $this->assertEquals(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"' .
            "\n" . '   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
            $engine->getDoctype()
        );
    }

    public function testAllOptionsDefaults()
    {
        $engine = new FabricationEngine();

        $this->assertEquals(true, $engine->getOption('process'));
        $this->assertEquals(true, $engine->getOption('process.body.image'));
        $this->assertEquals(true, $engine->getOption('process.body.br'));
        $this->assertEquals(true, $engine->getOption('process.body.hr'));
    }

    public function testInput()
    {
        $engine = new FabricationEngine();

        $engine->input('hello', 'world');
        $this->assertEquals('world', $engine->output('hello'));
    }

    public function testInputBoolean()
    {
        $engine = new FabricationEngine();

        $engine->input(true, false);
        $this->assertEquals(false, $engine->output(true));

        $engine->input(false, true);
        $this->assertEquals(true, $engine->output(false));

        $engine->input(1, 0);
        $this->assertEquals(0, $engine->output(1));

        $engine->input(0, 1);
        $this->assertEquals(1, $engine->output(0));
    }

    public function testInputArray()
    {
        $engine = new FabricationEngine();

        $engine->input('hello', array('world'));
        $this->assertEquals(array('world'), $engine->output('hello'));
    }

    public function testInputIDAndRenderOutput()
    {
        $engine = new FabricationEngine();

        $engine->input('#hello', 'world');
        $this->assertEquals('world', $engine->output('#hello'));

        $engine->run('<div id="hello"></div>');

        $this->assertEquals(
            '<div id="hello">world</div>',
            $engine->view('//div[@id="hello"]')
        );
    }

    public function testInputClassAndRenderOutput()
    {
        $engine = new FabricationEngine();

        $engine->input('.hello', 'world');
        $this->assertEquals('world', $engine->output('.hello'));

        $engine->run('<div class="hello"></div>');

        $this->assertEquals(
            '<div class="hello">world</div>',
            $engine->view('//div[@class="hello"]')
        );
    }

    public function testInputSymbolHashId()
    {
        $engine = new FabricationEngine();

        $engine->input('#hello', 'world');
        $engine->run('<div id="hello"></div>');

        $this->assertEquals('world', $engine->output('#hello'));

        $this->assertEquals(
            '<div id="hello">world</div>',
            $engine->view('//div[@id="hello"]')
        );

        $this->assertEquals(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">' .
            "\n" .
            "<html><body><div id=\"hello\">world</div></body></html>\n",
            $engine->saveHtml()
        );
    }

    public function testInputSymbolDotClass()
    {
        $engine = new FabricationEngine();

        $engine->input('.hello', 'world');
        $engine->run('<div class="hello"></div>');

        $this->assertEquals('world', $engine->output('.hello'));

        // Start the engine.
        $engine->run('<div class="hello"></div>');

        // Assertions.
        $this->assertEquals(
            '<div class="hello">world</div>', $engine->view('//div[@class="hello"]')
        );

        $this->assertEquals(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">' .
            "\n" .
            '<html><body><div class="hello">world</div></body></html>' .
            "\n",
            $engine->saveHtml()
        );
    }

    public function testInputSymbolsMixed()
    {
        $engine = new FabricationEngine();

        $inputs = array(
            '#hello' => 'world',
            '.hello' => 'world',
            '#foo' => 'bar',
            '.foo' => 'bar'
        );

        // Input the key value pairs.
        foreach ($inputs as $key => $value) {
            $engine->input($key, $value);
        }

        // Start the engine.
        $engine->run(
                '<div id="hello"></div>' .
                '<div class="hello"></div>' .
                '<p id="foo"></p>' .
                '<b class="foo"></b>'
        );

        // Assertions.
        foreach ($inputs as $key => $value) {
            $this->assertEquals($value, $engine->output($key));
        }
        $this->assertEquals('<div id="hello">world</div>', $engine->view('//div[@id="hello"]'));
        $this->assertEquals('<div class="hello">world</div>', $engine->view('//div[@class="hello"]'));
        $this->assertEquals('<p id="foo">bar</p>', $engine->view('//p[@id="foo"]'));
        $this->assertEquals('<b class="foo">bar</b>', $engine->view('//b[@class="foo"]'));
    }

    public function testInputSymbolsMixedFetchDOMElements()
    {
        $engine = new FabricationEngine();

        $inputs = array(
            '#hello' => 'world',
            '.hello' => 'world',
            '#foo' => 'bar',
            '.foo' => 'bar'
        );

        // Input key value pairs.
        foreach ($inputs as $key => $value) {
            $engine->input($key, $value);
        }

        // Start the engine.
        $engine->run(
            '<div id="hello"></div>' .
            '<div class="hello"></div>' .
            '<p id="foo"></p>' .
            '<b class="foo"></b>'
        );

        // Assertions.
        foreach ($inputs as $key => $value) {
            $this->assertEquals($value, $engine->output($key));
        }
        $this->assertEquals('<div id="hello">world</div>', $engine->view('//div[@id="hello"]'));
        $this->assertEquals('<div class="hello">world</div>', $engine->view('//div[@class="hello"]'));
        $this->assertEquals('<p id="foo">bar</p>', $engine->view('//p[@id="foo"]'));
        $this->assertEquals('<b class="foo">bar</b>', $engine->view('//b[@class="foo"]'));

        // Fetch the engine html body elements.
        $nodeList = $engine->getBody();

        // Assertions DOM Element structure.
        $this->assertEquals('DOMNodeList', get_class($nodeList));
        $this->assertEquals(1, $nodeList->length);
        $this->assertEquals(4, $nodeList->item(0)->childNodes->length);

        $this->assertEquals('div', $nodeList->item(0)->childNodes->item(0)->nodeName);
        $this->assertEquals('world', $nodeList->item(0)->childNodes->item(0)->nodeValue);
        $this->assertEquals('div', $nodeList->item(0)->childNodes->item(1)->nodeName);
        $this->assertEquals('world', $nodeList->item(0)->childNodes->item(1)->nodeValue);
        $this->assertEquals('p', $nodeList->item(0)->childNodes->item(2)->nodeName);
        $this->assertEquals('bar', $nodeList->item(0)->childNodes->item(2)->nodeValue);
        $this->assertEquals('b', $nodeList->item(0)->childNodes->item(3)->nodeName);
        $this->assertEquals('bar', $nodeList->item(0)->childNodes->item(3)->nodeValue);
    }

    public function testConvert()
    {
        $engine = new FabricationEngine();

        $string = '<div><strong>Hello</strong> World</div>';
        $div    = $engine->convert($string);

        $this->assertEquals('DOMElement', get_class($div));
        $this->assertEquals('div', $div->nodeName);
        $this->assertEquals('Hello World', $div->nodeValue);
    }

    public function testConvertAndAppend()
    {
        $engine = new FabricationEngine();

        $string = '<div><strong>Hello</strong> World</div>';
        $div    = $engine->convert($string);

        $this->assertEquals('DOMElement', get_class($div));
        $this->assertEquals('div', $div->nodeName);
        $this->assertEquals('Hello World', $div->nodeValue);

        //$div->nodeValue = "Changed";
        //<div>Changed</div>

        $engine->appendChild($div);

        $this->assertEquals($string, $engine->view());
    }

    public function testCreateElement()
    {
        $engine = new FabricationEngine();

        $value  = "Hello World!";
        $div    = $engine->create('div', $value, array(), array());
        $engine->appendChild($div);

        $this->assertEquals('DOMElement', get_class($div));
        $this->assertEquals('div', $div->nodeName);
        $this->assertEquals($value, $div->nodeValue);
    }

    public function testCreateElementWithAttributes()
    {
        $engine = new FabricationEngine();

        $value = "Hello World!";
        $div = $engine->create('div', $value, array('id' => 'welcome'), array());
        $engine->appendChild($div);

        $this->assertEquals('DOMElement', get_class($div));
        $this->assertEquals('div', $div->nodeName);
        $this->assertEquals($value, $div->nodeValue);
    }

    public function testCreateElementWithAttributesAndChildren()
    {
        $engine = new FabricationEngine();

        $message = $engine->create('div', 'Hello ',
            array('id' => 'welcome'), array(
                array(
                    'name' => 'div'
                    , 'attributes' => array('id' => 'message')
                    , 'children' => array(
                        array('name' => 'b', 'value' => 'W'),
                        array('name' => 'u', 'value' => 'o'),
                        array('name' => 'b', 'value' => 'r'),
                        array('name' => 'u', 'value' => 'l'),
                        array('name' => 'b', 'value' => 'd'),
                    )
                )
            )
        );
        // Append new element to the engine.
        $engine->appendChild($message);

        // Assertions.
        $this->assertEquals('DOMElement', get_class($message));
        $this->assertEquals('div', $message->nodeName);
        $this->assertEquals('Hello World', $message->nodeValue);

        // Assertions output.
        $this->assertEquals(
            '<div id="welcome">' .
            'Hello <div id="message">' .
            "<b>W</b><u>o</u><b>r</b><u>l</u><b>d</b></div></div>\n",
            $engine->saveHtml()
        );

        // Element text.
        $this->assertEquals(
            'Hello', $engine->view('//div[@id="welcome"]/text()')
        );

        // Element b text values.
        $this->assertEquals(
            'Wrd', $engine->view('//div[@id="message"]/b/text()')
        );

        // Element u text values.
        $this->assertEquals(
            'ol', $engine->view('//div[@id="message"]/u/text()')
        );
    }

//	public function testCreateStyleElement() {
//
//		$id    = 'hello';
//		$name  = 'div';
//		$value = 'Hello World!';
//
//		$attributes = array('id'=>$id);
//		$children   = array();
//		$styles     = array("{$name}#{$id} { color:#999999; }");
//		$styles		= $this->engine->create('style', 'testing',
//			array(), array(), array(), array()
//		);
//		$scripts    = array();
//
//
//		$result = $this->engine->create($name, $value
//			, $attributes, $children, $styles, $scripts
//		);
//
//		$this->assertEquals('DOMElement', get_class($result));
//		$this->assertEquals($name,  $result->nodeName);
//		$this->assertEquals($value, $result->nodeValue);
//
//		$this->engine->appendChild($result);
//
//		$this->assertEquals("<$name id=\"hello\">$value</$name>"
//			, $this->engine->saveHTML()
//		);
//
//		$this->assertEquals(array(),
//			$this->engine->getStyles()
//		);
//	}

    public function testCreateElementFromData()
    {
        //$map = 'id';
        $map = 'class';

        $template = $this->engine->create('div', 'Template:');
        $template->appendChild($this->engine->create('div', 'UID.', array($map => 'uid')));
        $template->appendChild($this->engine->create('div', 'Title.', array($map => 'title')));
        $template->appendChild($this->engine->create('div', 'Content.', array($map => 'content')));

        // Create dataset with the array keys matching the template children attributes.
        $dataset = [
            ['uid' => 1, 'title' => 'Title 1', 'content' => 'Content 1'],
            ['uid' => 2, 'title' => 'Title 2', 'content' => 'Content 2'],
            ['uid' => 3, 'title' => 'Title 3', 'content' => 'Content 3'],
        ];

        // Pattern the output to a result variable.
        $result = $this->engine->template($template, $dataset, $map);

        $this->assertEquals(
            '<div>Template:UID.Title.Content.' .
            // ROW 1
            '<div ' . $map . '="uid_1">1</div>' .
            '<div ' . $map . '="title_1">Title 1</div>' .
            '<div ' . $map . '="content_1">Content 1</div>' .
            // ROW 2
            '<div ' . $map . '="uid_2">2</div>' .
            '<div ' . $map . '="title_2">Title 2</div>' .
            '<div ' . $map . '="content_2">Content 2</div>' .
            // ROW 3
            '<div ' . $map . '="uid_3">3</div>' .
            '<div ' . $map . '="title_3">Title 3</div>' .
            '<div ' . $map . '="content_3">Content 3</div>' .
            '</div>', $this->engine->saveXML($result)
        );

        // Append the result to the engine.
        $this->engine->appendChild($result);

        // View assertions.
        $this->assertEquals(1, $this->engine->view("//div[@$map='uid_1']/text()"));
        $this->assertEquals('Title 1', $this->engine->view("//div[@$map='title_1']/text()"));
        $this->assertEquals('Content 1', $this->engine->view("//div[@$map='content_1']/text()"));
        $this->assertEquals(2, $this->engine->view("//div[@$map='uid_2']/text()"));
        $this->assertEquals('Title 2', $this->engine->view("//div[@$map='title_2']/text()"));
        $this->assertEquals('Content 2', $this->engine->view("//div[@$map='content_2']/text()"));
        $this->assertEquals(3, $this->engine->view("//div[@$map='uid_3']/text()"));
        $this->assertEquals('Title 3', $this->engine->view("//div[@$map='title_3']/text()"));
        $this->assertEquals('Content 3', $this->engine->view("//div[@$map='content_3']/text()"));
    }

//	public function testPattern() {
//
//		$result = (string) $this->engine->createPattern();
//
//		$this->assertInternalType('object', $this->engine->createPattern());
//		$this->assertInstanceOf('Library\Pattern\Html', $this->engine->createPattern());
//
//	}

    public function testSpecification()
    {
        //$this->engine->input('html', array());

        $contract = array(
            array(
                'name' => 'head',
                'value' => '',
                'children' => array(
                    array('name' => 'title', 'value' => 'Specification')
                )
            ),
            array(
                'name' => 'body',
                'value' => '',
                'children' => array(
                    array(
                        'name' => 'div',
                        'attributes' => array('id' => 'header'),
                        'children' => array(),
                    ),
                    array(
                        'name' => 'div',
                        'attributes' => array('id' => 'content'),
                        'children' => array(
                            array(
                                'name' => 'h1',
                                'value' => 'Welcome to this test case.'
                            ),
                            array(
                                'name' => 'p',
                                'value' => 'Stuff here.'
                            )
                        )
                    ),
                    array('name' => 'div', 'attributes' => array('id' => 'footer'))
                )
            )
        );

        $result = $this->engine->specification('html', '', array(), $contract)->saveFabric();
        //$result = $this->engine->create('html', '',  array(), $contract);
        // @todo Change assertions to test the DOMElements rather than the output.
        $this->assertEquals(
                '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd"><html><head><title>Specification</title></head><body><div id="header"></div><div id="content"><h1>Welcome to this test case.</h1><p>Stuff here.</p></div><div id="footer"></div></body></html>', $result);
    }

//	public function testPatternRequirementsHtmlAsString() {
//
//		//$this->engine->input('html', array());
//
//		$this->engine->requirements = array(
//			array(
//				'name' => 'head',
//				'value' => '',
//				'children'=> array(
//					array('name'=>'title', 'value'=>'Requirements')
//				)
//			),
//			array(
//				'name' => 'body',
//				'value' => '',
//				'children'=> array(
//					array('name'=>'div', 'attributes'=> array('id'=>'header')),
//					array(
//						'name'=>'div',
//						'attributes'=> array('id'=>'content'),
//						'children'=>array(
//							array('name'=>'h1', 'value'=>'Welcome to this test case.'),
//							array('name'=>'p', 'value'=>'Stuff here.')
//						)
//					),
//					array('name'=>'div', 'attributes'=> array('id'=>'footer'))
//				)
//			)
//		);
//
//		$result = (string) $this->engine->createPattern();
//
//		$this->assertEquals(
//'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
//   "http://www.w3.org/TR/html4/loose.dtd"><html><head><title>Requirements</title></head><body><div id="header"></div><div id="content"><h1>Welcome to this test case.</h1><p>Stuff here.</p></div><div id="footer"></div></body></html>
//', $result);
//
//	}
//
//	public function testPatternXmlKeyValue() {
//
//		// TODO xml doctype correct output.
//	}
//
//	public function testPatternSpecification() {
//
//		$this->assertEquals(2,
//			sizeof($this->engine->createPattern()->specification)
//		);
//	}
//
//	public function testPatternHtmlTable() {
//
//		$this->assertEquals(
//			'<table></table>',
//			(string) $this->engine->createPattern('Html\Table')
//		);
//	}
//
//	public function testPatternHtmlTableWithSingleRow() {
//
//		$htmlTable = $this->engine->createPattern('Html\Table', array(),
//			array(array('hello', 'world'))
//		);
//
//		$this->assertEquals(
//			"<table><tr><td>hello</td>\n".
//			"<td>world</td>\n".
//			"</tr>\n".
//			"</table>",
//			(string) $htmlTable
//		);
//	}
//
//	public function testPatternHtmlTableWithAttributes() {
//
//		$data = array(
//			array('hello', 'world', 'are you awake?'),
//		);
//
//		$htmlTable = $this->engine->createPattern('Html\Table',
//			array('id'=>'hello', 'class'=>'world'), $data
//		);
//
//		$this->assertEquals(
//			"<table id=\"hello\" class=\"world\"><tr><td>hello</td>\n".
//			"<td>world</td>\n".
//			"<td>are you awake?</td>\n".
//			"</tr>\n".
//			"</table>"
//			, (string) $htmlTable
//		);
//	}
//
//	public function testPatternHtmlTableWithData() {
//
//		$data = array(
//			array('hello', 'world', 'are you really awake?'),
//			array('foo', 'bar')
//		);
//
//		$htmlTable = new \Library\Html\Table($data);
//
//		$this->assertEquals(
//			"<table><tr><td>hello</td>\n".
//			"<td>world</td>\n".
//			"<td>are you really awake?</td>\n".
//			"</tr>\n".
//			"<tr><td>foo</td>\n".
//			"<td>bar</td>\n".
//			"</tr>\n".
//			"</table>"
//			, (string) $htmlTable
//		);
//	}
//
//	public function testPatternHtmlForm() {
//
//		$htmlForm = new Library\Html\Form($data);
//
//		$this->assertEquals(
//			'<form></form>',
//			(string) $htmlForm
//		);
//	}
//
//	public function testPatternHtmlFormWithAttributes() {
//
//		$htmlForm = $this->engine->createPattern('Html\Form', array('id'=>'hello'));
//
//		$this->assertEquals(
//			'<form id="hello"></form>',
//			(string) $htmlForm
//		);
//	}

    public function testOutputString()
    {
        $this->engine->input('hello', 'world');

        $this->assertEquals('world', $this->engine->output('hello'));
    }

    public function testOutputArray()
    {
        $this->engine->input('hello', array('world'));

        $this->assertEquals(array('world'), $this->engine->output('hello'));
    }

    public function testOutputPatternPhpString()
    {
        $this->engine->input('hello', 'world');

        $this->assertEquals(
                '<?php' . "\n" .
                '$hello="world";' . "\n" .
                '?>', $this->engine->output('hello', 'php.string')
        );
    }

    public function testPhpStringSingleInputEcho()
    {
        $this->engine->input('hello', 'world');

        $this->assertEquals(
                '<?php' . "\n" .
                '$hello="world";' . "\n" .
                'echo $hello;' . "\n" .
                '?>', $this->engine->output('', 'php.string', array(
                    'return' => true,
                    'tags' => true,
                    'echo' => true
                        )
                )
        );
    }

    public function testPhpNoTagsStringSingleSelectHello()
    {
        $this->engine->input('hello', 'world');

        $this->assertEquals('$hello="world";' . "\n", $this->engine->output('hello', 'php.string', array('return' => true, 'tags' => false)
                )
        );
    }

    public function testPhpStringMultiple()
    {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');

        $this->assertEquals(
                '<?php' . "\n" .
                '$hello="world";' . "\n" .
                '$foo="bar";' . "\n" .
                '?>', $this->engine->output('', 'php.string')
        );
    }

    public function testPhpStringMultipleEcho()
    {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');

        $this->assertEquals(
                '<?php' . "\n" .
                '$hello="world";' . "\n" .
                '$foo="bar";' . "\n" .
                'echo $hello;' . "\n" .
                'echo $foo;' . "\n" .
                '?>', $this->engine->output('', 'php.string', array(
                    'return' => true,
                    'tags' => true,
                    'echo' => true
                        )
                )
        );
    }

    public function testPhpStringMultipleGetSingle()
    {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');

        $this->assertEquals(
                '<?php' . "\n" .
                '$hello="world";' . "\n" .
                '?>', $this->engine->output('hello', 'php.string')
        );
    }

    public function testOutputPatternPhpArray()
    {
        $this->engine->input('hello', 'world');

        $this->assertEquals(
                '<?php' . "\n" .
                '$data=array(' . "\n" .
                "'hello'=>'world',\n" .
                ");\n" .
                '?>', $this->engine->output('', 'php.array')
        );
    }

    public function testPhpArrayMultipleStringSelectFoo()
    {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');

        $this->assertEquals(
                '<?php' . "\n" .
                '$data=array(' . "\n" .
                "'foo'=>'bar',\n" .
                ");\n" .
                '?>', $this->engine->output('foo', 'php.array')
        );
    }

    public function testPhpArrayMultipleString()
    {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');

        $this->assertEquals(
                '<?php' . "\n" .
                '$data=array(' . "\n" .
                "'hello'=>'world',\n" .
                "'foo'=>'bar',\n" .
                ");\n" .
                '?>', $this->engine->output('', 'php.array')
        );
    }

    public function testPhpArrayMultipleMixed()
    {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        $this->engine->input('test', array('hello' => $this->engine->output('hello'), 'foo' => $this->engine->output('foo')));

        $this->assertEquals(
                '<?php' . "\n" .
                '$data=array(' . "\n" .
                "'hello'=>'world',\n" .
                "'foo'=>'bar',\n" .
                "'test'=>array (\n" .
                "  'hello' => 'world',\n" .
                "  'foo' => 'bar',\n" . "),\n" .
                ");\n" .
                '?>', $this->engine->output('', 'php.array')
        );
    }

    public function testOutputPatternPhpClass()
    {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');

        $this->assertEquals(
                '<?php' . "\n" .
                '$data=new stdClass;' . "\n" .
                '$data->hello=\'world\';' . "\n" .
                '$data->foo=\'bar\';' . "\n" .
                '?>'
                , $this->engine->output('', 'php.class')
        );
    }

    // TODO add recursive method for handling nested arrays.
    public function testPhpNoTagsStdClass()
    {
        $testing = array('testing');

        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        //$this->engine->input('test', $testing);

        $this->assertEquals(
                '$data=new stdClass;' . "\n"
                . '$' . "data->hello='world';\n"
                . '$' . "data->foo='bar';\n"
                //	.'$' . "data->test=" . var_export($testing, true) . ";\n"
                , $this->engine->output('', 'php.class', array('return' => true, 'tags' => false)
                )
        );
    }

    public function testPhpNoTagsCustomClass()
    {
        $this->assertEquals(
                "class Custom {\n" .
                "}\n"
                //'$'."objectCustom=new Custom;\n".
                //'$'."objectCustom->hello='world';\n".
                //'$'."objectCustom->foo='bar';\n".
                //'$'."objectCustom->testing=".var_export($testing, true).";\n".
                //'echo $objectCustom;'
                , $this->engine->output('', 'php.class', array(
                    'return' => true,
                    //'echo'=>true,
                    'tags' => false,
                    'class' => 'Custom',
                        )
                )
        );
    }

    public function testPhpTemplateNoTagsClassWithStereotype()
    {
        $this->assertEquals(
                "class Custom extends CustomStereotype {\n" .
                "}\n"
                , $this->engine->output('', 'php.class', array(
                    'return' => true,
                    'tags' => false,
                    'class' => 'Custom',
                    'class.stereotype' => 'CustomStereotype',
                        )
                )
        );
    }

    public function testPhpTemplateClassConstructor()
    {
        $testing = array('testing');

        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        $this->engine->input('testing', $testing);

        $this->assertEquals(
                "<?php\n" .
                "class Custom extends CustomStereotype {\n" .
                'public $hello=\'world\';' . "\n" .
                'public $foo=\'bar\';' . "\n" .
                'public $testing=' . var_export($testing, true) . ";\n" .
                'public function __construct($param1=true,$param2=false) {' . "\n" .
                "}\n" .
                "}\n" .
                "?>"
                , $this->engine->output('', 'php.class', array(
                    'return' => true,
                    //'echo'=>true,
                    'tags' => true,
                    'class' => 'Custom',
                    'class.stereotype' => 'CustomStereotype',
                    'class.methods' => array(
                        '__construct' => array(
                            'parameters' => array('param1' => true, 'param2' => false),
                            'code' => array()
                        )
                    )
                        )
                )
        );
    }

    public function testPhpTemplateClassConstructorAll()
    {
        $testing = array('testing');

        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        $this->engine->input('testing', $testing);

        $this->assertEquals(
                "<?php\n" .
                "class Custom extends CustomStereotype {\n" .
                'public $hello=\'world\';' . "\n" .
                'public $foo=\'bar\';' . "\n" .
                'public $testing=' . var_export($testing, true) . ";\n" .
                'public function __construct($param1=true,$param2=false) {' . "\n" .
                'parent::__construct();' . "\n" .
                '$data=new stdClass();' . "\n" .
                "}\n" .
                "}\n" .
                "?>"
                , $this->engine->output('', 'php.class', array(
                    'return' => true,
                    //'echo'=>true,
                    'tags' => true,
                    'class' => 'Custom',
                    'class.stereotype' => 'CustomStereotype',
                    'class.methods' => array(
                        '__construct' => array(
                            'parameters' => array('param1' => true, 'param2' => false),
                            'code' => array(
                                'parent::__construct()',
                                '$data=new stdClass()',
                            )
                        )
                    )
                        )
                )
        );
    }

    public function testPhpTemplateClassConstructorAndMethodAll()
    {
        $testing = array('testing');

        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        $this->engine->input('testing', $testing);

        $this->assertEquals(
                "<?php\n" .
                "class Custom extends CustomStereotype {\n" .
                'public $hello=\'world\';' . "\n" .
                'public $foo=\'bar\';' . "\n" .
                'public $testing=' . var_export($testing, true) . ";\n" .
                'public function __construct($param1=true,$param2=false) {' . "\n" .
                'parent::__construct();' . "\n" .
                '$data=new stdClass();' . "\n" .
                "}\n" .
                'public function methodName1($param1=true) {' . "\n" .
                '$' . 'foo="bar";' . "\n" .
                '// comment.;' . "\n" .
                "}\n" .
                "}\n" .
                "?>"
                , $this->engine->output('', 'php.class', array(
                    'return' => true,
                    //'echo'=>true,
                    'tags' => true,
                    'class' => 'Custom',
                    'class.stereotype' => 'CustomStereotype',
                    'class.methods' => array(
                        '__construct' => array(
                            'parameters' => array('param1' => true, 'param2' => false),
                            'code' => array(
                                'parent::__construct()',
                                '$data=new stdClass()',
                            )
                        ),
                        'methodName1' => array(
                            'parameters' => array('param1' => true),
                            'code' => array('$foo="bar"', '// comment.')
                        )
                    )
                        )
                )
        );
    }

    public function testOutputPatternPhpClassComplex()
    {
        $testing = array('testing');

        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        $this->engine->input('testing', $testing);

        $this->assertEquals(
                "<?php\n" .
                "class Custom extends CustomStereotype {\n" .
                "\t" . 'public $testing=' . var_export($testing, true) . ";\n" .
                "\t" . 'public function __construct($p1=true,$p2=false,$p3=\'string\') {' . "\n" .
                "\t\t" . 'parent::__construct();' . "\n" .
                "\t\t" . "\n" .
                "\t\t" . '$data=new stdClass();' . "\n" .
                "\t\t" . '$hello="world";' . "\n" .
                "\t\t" . '// comment in constructor.;' . "\n" .
                "\t" . "}\n" .
                "\t" . 'public function methodName1($p1=true,$p2=false,$p3=\'string\') {' . "\n" .
                "\t\t" . '$' . 'foo="bar";' . "\n" .
                "\t\t" . '// comment.;' . "\n" .
                "\t" . "}\n" .
                "}\n" .
                "?>", $this->engine->output('testing', 'php.class', array(
                    'return' => true,
                    //'echo'=>true,
                    'tags' => true,
                    'tabs' => true,
                    'class' => 'Custom',
                    'class.stereotype' => 'CustomStereotype',
                    'class.methods' => array(
                        '__construct' => array(
                            'parameters' => array(
                                'p1' => true,
                                'p2' => false,
                                'p3' => 'string'
                            ),
                            'code' => array(
                                'parent::__construct()',
                                '',
                                '$data=new stdClass()',
                                '$hello="world"',
                                '// comment in constructor.'
                            )
                        ),
                        'methodName1' => array(
                            'parameters' => array(
                                'p1' => true,
                                'p2' => false,
                                'p3' => 'string'
                            ),
                            'code' => array('$foo="bar"', '// comment.')
                        )
                    )
                        )
                )
        );
    }

    public function testOutputPatternCss()
    {
        $this->engine->input('body', array('bgcolor' => '#999999'));

        $this->assertEquals(
                'body {' . "\n" .
                'bgcolor: #999999;' . "\n" .
                '}' . "\n"
                , $this->engine->output('body', 'css.template')
        );
    }

    public function testOutputPatternCssMultiple()
    {
        $data = array(
            'body, html' => array(
                'margin' => '0px',
                'padding' => '0px',
                'color' => 'black',
                'background-color' => 'white',
            ),
            'body, input, textarea, select, option' => array(
                'font-family' => 'verdana, arial, helvetica, sans-serif',
            )
        );

        foreach ($data as $key => $value) {
            $this->engine->input($key, $value);
        }

        $this->assertEquals(
            '/**' . "\n" .
            ' * CSS Generated by the FabricationEngine.' . "\n" .
            ' */' . "\n" .
            'body, html {' . "\n" .
            'margin: 0px;' . "\n" .
            'padding: 0px;' . "\n" .
            'color: black;' . "\n" .
            'background-color: white;' . "\n" .
            '}' . "\n" .
            'body, input, textarea, select, option {' . "\n" .
            'font-family: verdana, arial, helvetica, sans-serif;' . "\n" .
            '}' . "\n"
            , $this->engine->output('', 'css.template', array(
                'return' => true,
                'header' => true,
                    )
            )
        );
    }

    public function testSanityLoadHtmlString()
    {
        $design = file_get_contents(dirname(__FILE__) . '/fixture/design.html');

        $this->assertEquals(true, $this->engine->getOption('process'));
        $this->assertTrue($this->engine->run($design));
    }

    public function testSanityLoadHtmlFile()
    {
        $designPath = dirname(__FILE__) . '/fixture/design.html';
        $this->assertTrue($this->engine->run($designPath, 'file', 'html'));
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

    //-------------------------------------------------------------------------
    // TODO X'ed out test cases
    public function XtestDiagram()
    {
        $stringXml = file_get_contents(dirname(dirname(__FILE__)) . '/tests/fixture/dia/Example.dia');
        $this->engine->run($stringXml, 'string', 'xml');

        $xml = $this->engine->outputXML();
        //$this->engine->dump($xml); exit;
    }

    public function XtestMessingWithSearchEngines()
    {
        $webpage = file_get_contents('http://www.duckduckgo.com/');

        $this->assertTrue($this->engine->run($webpage, 'string', 'html'));

        $NodeList = $this->engine->getHtml();
        $this->assertInstanceOf('DOMNodeList', $NodeList);
        $this->assertEquals(1, $NodeList->length);
        $this->assertEquals('html', $NodeList->item(0)->tagName);
    }

    public function XXtestJQuery()
    {
        $this->engine->input('document.ready', array(
            'a' => '$("a").click(function() { alert("Hello world!") });',
            'atest' => '$("a .test").click(function() { alert("Testing!") });',
                )
        );

        // make assertions that data output is css?
        $this->assertEquals(
            '/**' . "\n" .
            ' * JQuery Generated by the FabricationEngine.' . "\n" .
            ' */' . "\n" .
            '$(document).ready(function() {' . "\n" .
            '  $("a").click(function() {' . "\n" .
            '    alert("Hello world!")' . "\n" .
            '  });' . "\n" .
            '});' . "\n" .
            "\n",
            $this->engine->output('', 'jquery.template',
                array(
                    'return' => true,
                    'header' => true,
                )
            )
        );
    }
}
