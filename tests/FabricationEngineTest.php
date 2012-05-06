<?php
namespace Fabrication\Tests;

use Fabrication\Library\FabricationEngine;

require_once(dirname(dirname(__FILE__)) . '/lib/FabricationEngine.php');

class FabricationEngineTest extends \PHPUnit_Framework_TestCase {

	
	public function setUp() {
		
		$this->engine = new FabricationEngine();
	}

	
	public function testInstance() {
		
		$this->assertInternalType('object', $this->engine);
		$this->assertInstanceOf('Fabrication\Library\FabricationEngine', $this->engine);
	}

	
	public function testGet() {
		
		$engine = $this->engine->getEngine();
		$this->assertInternalType('object', $engine);
		$this->assertInstanceOf('Fabrication\Library\FabricationEngine', $engine);
	}

	
	public function testAttributes() {
		
		$this->assertObjectHasAttribute('input', $this->engine);
		$this->assertObjectHasAttribute('output', $this->engine);
		$this->assertObjectHasAttribute('options', $this->engine);
	}


	// This example code is copyied into the README.md testing for consistency.
	// Simplest example.
	public function testReadmeExample1() {

		$engine = new FabricationEngine();
		$engine->input('hello', 'world');
		
		$this->assertEquals($engine->output('hello'), 'world');
	}


	public function testReadmeExample2() {

		$engine = new FabricationEngine();
		$engine->input('#hello', 'world');
		$template = '<html><head></head><body><div id="hello"></div></body></html>';
		$engine->run($template);
		
		$this->assertEquals($engine->output('#hello'), 'world');

		$this->assertEquals('world',
			$engine->saveHTML('//div[@id="hello"]/text()')
		);
	}


	public function testReadmeExample3() {

		$engine = new FabricationEngine();
		$engine->input('.hello', 'world');
		$template = '<html><head></head><body><div class="hello"></div></body></html>';
		$engine->run($template);

		$this->assertEquals($engine->output('.hello'), 'world');

		$this->assertEquals('world',
			$engine->saveHTML('//div[@class="hello"]/text()')
		);
	}


	public function testReadmeExampleOutput() {

		$tag='>';
		$engine = new FabricationEngine();
		$engine->input('hello', 'world');
		$this->assertEquals(
			"<?php\n\$hello=\"world\";\n?$tag",
			$engine->output('hello', 'php')
		);
	}


	public function testReadmeExampleOutput1() {

		$engine = new FabricationEngine();
		$engine->input('hello', 'world');
		$this->assertEquals(
"<?php
\$data=array(
'hello'=>'world',
);
?>", 
			$engine->output('hello', 'php.array')
		);
	}


	public function testReadmeExampleOutput2() {

		$tag='>';
		$engine = new FabricationEngine();
		$engine->input('hello', 'world');
		$this->assertEquals("<?php\n\$data=new stdClass;\n\$data->hello='world';\n?$tag", 
			$engine->output('hello', 'php.class')
		);
	}


	public function testReadmeExampleOutput3() {

		$engine = new FabricationEngine();
		$engine->input('body', array('bgcolor'=>'#999999'));
		$this->assertEquals(
			"body {\nbgcolor: #999999;\n}\n", 
			$engine->output('body', 'css')
		);
	}
	
	
	public function testReadmeExampleOptionDoctype() {

		$engine = new FabricationEngine();
		$template = '<html><head></head><body></body></html>';
		$engine->run($template);

		$this->assertEquals('html.5', $engine->setOption('doctype', 'html.5'));
		$this->assertEquals('<!DOCTYPE HTML>', $engine->getDoctype());


	}


	public function testReadmeExampleTemplateDataset() {
		// SEE:  testCreatingHTMLAndTemplateFromData();
	}


	public function testReadmeExampleCreate() {

		$engine = new FabricationEngine();

		$hi = $engine->create('div', 'Hello World', array('id'=>'hello-world'));

		$this->assertEquals('div', $hi->nodeName);
		$this->assertEquals('Hello World', $hi->nodeValue);
		$this->assertEquals('id', $hi->attributes->getNamedItem('id')->nodeName);
		$this->assertEquals('hello-world', $hi->attributes->getNamedItem('id')->nodeValue);

		$engine->appendChild($hi);

		$this->assertEquals(
			'<div id="hello-world">Hello World</div>', 
			$engine->saveHTML()
		);
	}


	public function testReadmeExampleCreate1() {

		$engine = new FabricationEngine();

		$hi = $engine->create('div', '', array('id'=>'hello-world'),
			array(
				array('name'=>'u', 'value'=>'Hello', 
					'attributes'=>array('id'=>'hello'), 
					'children'=>array()
				), 
				array('name'=>'strong', 
					'attributes'=>array('id'=>'world'), 
					'children'=>array(
						array('name'=>'i', 'value'=>'W'),
						array('name'=>'i', 'value'=>'o'),
						array('name'=>'i', 'value'=>'r'),
						array('name'=>'i', 'value'=>'l'),
						array('name'=>'i', 'value'=>'d')
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
			$engine->saveHTML('//div[@id="hello-world"]/strong[@id="world"]')
		);
		
	}


	public function testReadmeExampleSpecification() {}
	public function testReadmeExampleQuery() {}
	public function testReadmeExampleView() {}
	public function testReadmeExampleDumpDebug() {}


	public function testDoctypeDefault() {
		
		$this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
		$this->assertEquals(
			'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"'.
			"\n" . '   "http://www.w3.org/TR/html4/loose.dtd">'
			, $this->engine->getDoctype()
		);
	}

	
	public function testDoctypeXHTML1Strict() {
		
		// test default doctype.
		$this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
		
		$this->engine->setOption('doctype', 'xhtml.1.0.strict');
		$this->assertEquals('xhtml.1.0.strict', $this->engine->getOption('doctype'));
		$this->assertEquals('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"'."\n".'   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
			, $this->engine->getDoctype()
		);
	}


	public function testDoctypeXHTML1Transitional() {
		
		// test default doctype.
		$this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
		
		$this->engine->setOption('doctype', 'xhtml.1.0.transitional');
		$this->assertEquals('xhtml.1.0.transitional', $this->engine->getOption('doctype'));
		$this->assertEquals('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"'."\n".'   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
			, $this->engine->getDoctype()
		);
	}


	public function testDoctypeXHTML1Frameset() {
		
		// test default doctype.
		$this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
		
		$this->engine->setOption('doctype', 'xhtml.1.0.frameset');
		$this->assertEquals('xhtml.1.0.frameset', $this->engine->getOption('doctype'));
		$this->assertEquals('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"'."\n".'   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">'
			, $this->engine->getDoctype()
		);
	}


	public function testOptionSettingDoctype() {
		
		$this->assertEquals('html.5', $this->engine->setOption('doctype', 'html.5'));
		$this->assertEquals('<!DOCTYPE HTML>', $this->engine->getDoctype());
		
		$this->engine->run('<h1>TESTING</h1>');
		
		$this->assertEquals(
			//'<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'.
			//'<!DOCTYPE HTML>'.
			//"\n".
			'<html><body>'.
			'<h1>TESTING</h1>'.
			'</body></html>'
			, $this->engine->saveHTML()
		);
	}

	
	public function testAllOptionsDefaults() {
		
		$this->assertEquals(true, $this->engine->getOption('process'));
		$this->assertEquals(true, $this->engine->getOption('process.body.image'));
		$this->assertEquals(true, $this->engine->getOption('process.body.br'));
		$this->assertEquals(true, $this->engine->getOption('process.body.hr'));
	}

	
	public function testIO() {
		
		$this->engine->input('hello', 'world');
		$this->assertEquals('world', $this->engine->output('hello'));
	}


	public function testInputIDAndRenderOutput() {
		
		$this->engine->input('#hello', 'world');
		$this->assertEquals('world', $this->engine->output('#hello'));

		$this->engine->run('<div id="hello"></div>');

		$this->assertEquals(
			//'<div id="hello">world</div>',
			'<div id="hello">world</div>',
			$this->engine->view('//div[@id="hello"]')
		);
	}
	
	public function testInputClassAndRenderOutput() {
		
		$this->engine->input('.hello', 'world');
		$this->assertEquals('world', $this->engine->output('.hello'));

		$this->engine->run('<div class="hello"></div>');

		$this->assertEquals(
			'<div class="hello">world</div>',
			$this->engine->view('//div[@class="hello"]')
		);
	}
	
	public function testInputIDClassAndViewHtmlOutput() {
		
		$this->engine->input('#hello', 'world');
		$this->engine->input('.hello', 'world');
		$this->assertEquals('world', $this->engine->output('#hello'));
		$this->assertEquals('world', $this->engine->output('.hello'));

		$this->engine->run('<div id="hello"></div><div class="hello"></div>');

		$this->assertEquals(
			'<div id="hello">world</div>',
			$this->engine->view('//div[@id="hello"]')
		);

		$this->assertEquals(
			'<div class="hello">world</div>',
			$this->engine->view('//div[@class="hello"]')
		);
	}


	public function testMultipleInputIDClassAndViewOutput() {
		
		$inputs = array(
			'#hello' => 'world',
			'.hello' => 'world',
			'#foo'   => 'bar',
			'.foo'   => 'bar'
		);

		foreach ($inputs as $key => $value) {
			$this->engine->input($key, $value);
		}
		foreach ($inputs as $key => $value) {
			$this->assertEquals($value, $this->engine->output($key));
		}

		$this->engine->run(
			'<div id="hello"></div>'.
			'<div class="hello"></div>'.
			'<p id="foo"></p>'.
			'<b class="foo"></b>'
		);

		$this->assertEquals(
			'<div id="hello">world</div>',
			$this->engine->view('//div[@id="hello"]')
		);

		$this->assertEquals(
			'<div class="hello">world</div>',
			$this->engine->view('//div[@class="hello"]')
		);

		$this->assertEquals(
			'<p id="foo">bar</p>',
			$this->engine->view('//p[@id="foo"]')
		);

		$this->assertEquals(
			'<b class="foo">bar</b>',
			$this->engine->view('//b[@class="foo"]')
		);
	}


	public function testInputIDClassAndFetchDOMElements() {

		$inputs = array(
			'#hello' => 'world',
			'.hello' => 'world',
			'#foo'   => 'bar',
			'.foo'   => 'bar'
		);

		foreach ($inputs as $key => $value) {
			$this->engine->input($key, $value);
		}
		foreach ($inputs as $key => $value) {
			$this->assertEquals($value, $this->engine->output($key));
		}

		$this->engine->run(
			'<div id="hello"></div>'.
			'<div class="hello"></div>'.
			'<p id="foo"></p>'.
			'<b class="foo"></b>'
		);

		$this->assertEquals(
			'<div id="hello">world</div>',
			$this->engine->view('//div[@id="hello"]')
		);

		$this->assertEquals(
			'<div class="hello">world</div>',
			$this->engine->view('//div[@class="hello"]')
		);

		$this->assertEquals(
			'<p id="foo">bar</p>',
			$this->engine->view('//p[@id="foo"]')
		);

		$this->assertEquals(
			'<b class="foo">bar</b>',
			$this->engine->view('//b[@class="foo"]')
		);

		// fetch the engine html body contents.
		$nodeList = $this->engine->getBody();

		// make some assertions on the load html DOM structure.
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


	public function testCreateElementNotInDoctype() {

		$this->assertFalse($this->engine->create('not-in-doctype'));
	}


	public function testCreateElement() {

		$value = "Hello World!";
		$div = $this->engine->create('div', $value, array(), array());
		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals($value, $div->nodeValue);
	}


	/**
	 * Ensure style are DOMElements only no text styles.
	 */
	public function testCreateStyleNoElement() {

		$value = "Hello World";
		$style = "<style>Incorrect use a DOMElement</style>";

		$this->engine->create('b', 'foo bar', 
			array(),		# attributes.
			array(),		# children.
			array($style),	# style.
			array()			# script.
		);

		$this->assertEquals(
			array(),
			$this->engine->getStyles()
		);
	}


	public function testCreateStyleElement() {

		$value = "Hello World";
		$style = $this->engine->create('style', $value, array(), array());
		$this->engine->appendChild($style);

		$this->assertEquals('DOMElement', get_class($style));
		$this->assertEquals('style', $style->nodeName);
		$this->assertEquals($value, $style->nodeValue);

		$this->assertEquals(
			"<style>$value</style>",
			$this->engine->saveHTML()
		);
	}


	public function testCreatingElementWithStyle() {

		$id    = 'hello';
		$name  = 'div';
		$value = 'Hello World!';

		$attributes = array('id'=>$id);
		$children   = array();
		$styles     = array("{$name}#{$id} { color:#999999; }");
		$styles		= $this->engine->create('style', 'testing',
			array(), array(), array(), array()
		);
		$scripts    = array();

		
		$result = $this->engine->create($name, $value
			, $attributes, $children, $styles, $scripts
		);

		$this->assertEquals('DOMElement', get_class($result));
		$this->assertEquals($name,  $result->nodeName);
		$this->assertEquals($value, $result->nodeValue);

		$this->engine->appendChild($result);

		$this->assertEquals("<$name id=\"hello\">$value</$name>"
			, $this->engine->saveHTML()
		);

		$this->assertEquals(array(),
			$this->engine->getStyles()
		);
	}


	public function testCreatingElementThenView() {

		$div = $this->engine->create('div', '', array(), array());
		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals('', $div->nodeValue);

		$this->assertEquals('<div></div>', $this->engine->view());
	}


	public function testCreatingElementWithValueThenView() {

		$value = "Hello World!";
		$div   = $this->engine->create('div', $value, array(), array());
		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals($value, $div->nodeValue);

		$this->assertEquals('<div>Hello World!</div>', $this->engine->view());
	}


	public function testCreatingElementWithSingleAttributeAndValueThenView() {

		$value = "Hello World!";
		$div   = $this->engine->create('div', $value, array('id' => 'hello'));
		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals($value, $div->nodeValue);

		$this->assertEquals('<div id="hello">Hello World!</div>', $this->engine->view());
	}


	public function testCreatingElementWithRecursionThenView() {

		$value = "Hello World!";

		$div = $this->engine->create('div', $value, 
			array('id' => 'hello', 'class' => 'world'), array(
				array('name' => 'div', 'value' => 'TEST', 'children' => 
					array(array('name' => 'div', 'value' => '1'))
				),
				array('name' => 'div', 'value' => 'TEST', 'attributes' => array('id' => 'test2'), 'children' => 
					array(array('name' => 'div', 'value' => '2'))
				),
			)
		);

		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals($value . 'TEST1TEST2', $div->nodeValue);
		
		$this->assertEquals(
			$value, $this->engine->saveHTML('//div[@id="hello"]/text()')
		);

		// div 1 ??
		$this->assertEquals(
			'TEST1TEST', $this->engine->saveHTML('//div[1]/div/text()')
		);
		
		$this->assertEquals(
			2, $this->engine->saveHTML('//div[@id="test2"]/div/text()')
		);

	}
	

	public function testCreatingElementThenViewByXPath() {

		$value = "Hello World!";
		$div = $this->engine->create('div', $value, array('id' => 'hello', 'class' => 'world'), array());
		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals($value, $div->nodeValue);

		// start running some xpath querys on the engine and view in html :))
		$this->assertEquals('<div id="hello" class="world">' . $value . '</div>', $this->engine->view("//*"));
		$this->assertEquals($value, $this->engine->view('//div[@id="hello"]/text()'));
	}

	
	public function testCreatingElementWithRecursionThenViewByXPath() {

		$value = "Hello World!";
		$div = $this->engine->create(
			'div', $value, array('id' => 'hello', 'class' => 'world'), array(
		    array('name' => 'div', 'value' => 'TEST', 'children' => array(array('name' => 'div', 'value' => '1'))),
		    array('name' => 'div', 'value' => 'TEST', 'attributes' => array('id' => 'test2'), 'children' => array(array('name' => 'div', 'value' => '2'))),
			)
		);
		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals($value . 'TEST1TEST2', $div->nodeValue);

		// start running some xpath querys on the engine and view in html :))
		$this->assertEquals("<div>2</div>", $this->engine->view('//div[@id="test2"]/div'));
		$this->assertEquals("2", $this->engine->view("//div[@id='test2']/div/text()"));
	}

	
	public function testCreatingDOMElementAndTemplateFromData() {

		// create the dom structure effectively the html that will be used as the 
		// template for the dataset, the id holding element must have an id with 
		// a value and the children id attributes must match the data array key.

		//$map = 'id';
		$map = 'class';

		$element = $this->engine->create('div', 'Template:');
		$element->appendChild($this->engine->create('div', 'UID.', array($map => 'uid')));
		$element->appendChild($this->engine->create('div', 'Title.', array($map => 'title')));
		$element->appendChild($this->engine->create('div', 'Content.', array($map => 'content')));

		// create some data with the same array keys as the template children.
		$data = array(
			array('uid' => 1, 'title' => 'Title 1', 'content' => 'Content 1'),
			array('uid' => 2, 'title' => 'Title 2', 'content' => 'Content 2'),
			array('uid' => 3, 'title' => 'Title 3', 'content' => 'Content 3'),
		);

		$result = $this->engine->template($element, $data, $map);

		$this->assertEquals(
			'<div>Template:UID.Title.Content.' .
				// ROW 1
				'<div '.$map.'="uid_1">1</div>' .
				'<div '.$map.'="title_1">Title 1</div>' .
				'<div '.$map.'="content_1">Content 1</div>' .
				// ROW 2
				'<div '.$map.'="uid_2">2</div>' .
				'<div '.$map.'="title_2">Title 2</div>' .
				'<div '.$map.'="content_2">Content 2</div>' .
				// ROW 3
				'<div '.$map.'="uid_3">3</div>' .
				'<div '.$map.'="title_3">Title 3</div>' .
				'<div '.$map.'="content_3">Content 3</div>' .
			'</div>',
			$this->engine->saveXML($result)
		);
		
		// attach the result and search using the view method.
		$this->engine->appendChild($result);

		$this->assertEquals(1, $this->engine->view("//div[@$map='uid_1']/text()"));
		$this->assertEquals(2, $this->engine->view("//div[@$map='uid_2']/text()"));
		$this->assertEquals(3, $this->engine->view("//div[@$map='uid_3']/text()"));
	}


	public function testCreatingHTMLAndTemplateFromData() {

		//$map = 'id';
		$map = 'class';

		$element = 
			'<div>Template:'.
				'<div '.$map.'="uid">UID.</div>'.
				'<div '.$map.'="title" dir="rtl">Title.</div>'.
				'<div '.$map.'="content" dir="rtl" style="test">Content.</div>'.
			'</div>';

		// create some data with the same array keys as the template children.
		$data = array(
			array('uid' => 1, 'title' => 'Title 1', 'content' => 'Content 1'),
			array('uid' => 2, 'title' => 'Title 2', 'content' => 'Content 2'),
			array('uid' => 3, 'title' => 'Title 3', 'content' => 'Content 3'),
		);

		$result = $this->engine->template($element, $data, $map);
		
		$this->assertEquals(
			'<div>Template:UID.Title.Content.' .
				// ROW 1
				'<div '.$map.'="uid_1">1</div>' .
				'<div '.$map.'="title_1" dir="rtl">Title 1</div>' .
				'<div '.$map.'="content_1" dir="rtl" style="test">Content 1</div>' .
				// ROW 2
				'<div '.$map.'="uid_2">2</div>' .
				'<div '.$map.'="title_2" dir="rtl">Title 2</div>' .
				'<div '.$map.'="content_2" dir="rtl" style="test">Content 2</div>' .
				// ROW 3
				'<div '.$map.'="uid_3">3</div>' .
				'<div '.$map.'="title_3" dir="rtl">Title 3</div>' .
				'<div '.$map.'="content_3" dir="rtl" style="test">Content 3</div>' .
			'</div>',
			$this->engine->saveXML($result)
		);

		// sweet example just append the output to the engine.
		$this->engine->appendChild($result);

		$this->assertEquals(1, $this->engine->view("//div[@$map='uid_1']/text()"));
		$this->assertEquals('Title 1', $this->engine->view("//div[@$map='title_1']/text()"));
		$this->assertEquals('Content 1', $this->engine->view("//div[@$map='content_1']/text()"));
		$this->assertEquals(2, $this->engine->view("//div[@$map='uid_2']/text()"));
		$this->assertEquals('Title 2', $this->engine->view("//div[@$map='title_2']/text()"));
		$this->assertEquals('Content 2', $this->engine->view("//div[@$map='content_2']/text()"));
		$this->assertEquals('Title 3', $this->engine->view("//div[@$map='title_3']/text()"));
		$this->assertEquals('Content 3', $this->engine->view("//div[@$map='content_3']/text()"));

		$this->assertEquals(3, $this->engine->view("//div[@$map='uid_3']/text()"));

	}


	public function testPattern() {

		$result = (string) $this->engine->createPattern();
		
		$this->assertInternalType('object', $this->engine->createPattern());
		$this->assertInstanceOf('Fabrication\Library\Pattern\Html', $this->engine->createPattern());

	}

	
	public function testSpecification() {

		//$this->engine->input('html', array());
		
		$contract = array(
			array(
				'name' => 'head', 
				'value' => '',
				'children'=> array(
					array('name'=>'title', 'value'=>'Specification')
				)
			),
			array(
				'name' => 'body', 
				'value' => '',
				'children'=> array(
					array(
						'name'=>'div', 
						'attributes'=> array('id'=>'header'),
						'children'=>array(),
					), 
					array(
						'name'=>'div', 
						'attributes'=> array('id'=>'content'),
						'children'=>array(
							array(
								'name'=>'h1', 
								'value'=>'Welcome to this test case.'
							),
							array(
								'name'=>'p', 
								'value'=>'Stuff here.'
							)
						)
					), 
					array('name'=>'div', 'attributes'=> array('id'=>'footer'))
				)
			)
		);
				
		$result = $this->engine->specification('html', '',  array(), $contract)->saveFabric();
		//$result = $this->engine->create('html', '',  array(), $contract);


		$this->assertEquals(
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd"><html><head><title>Specification</title>
</head>
<body><div id="header"></div>
<div id="content"><h1>Welcome to this test case.</h1>
<p>Stuff here.</p>
</div>
<div id="footer"></div>
</body>
</html>
', $result);

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
//   "http://www.w3.org/TR/html4/loose.dtd"><html><head><title>Requirements</title>
//</head>
//<body><div id="header"></div>
//<div id="content"><h1>Welcome to this test case.</h1>
//<p>Stuff here.</p>
//</div>
//<div id="footer"></div>
//</body>
//</html>
//', $result);
//
//	}


	public function testPatternXmlKeyValue() {

		// TODO xml doctype correct output.
	}


	public function testPatternSpecification() {

		$this->assertEquals(2,
			sizeof($this->engine->createPattern()->specification)
		);
	}


	public function testPatternHtmlTable() {

		$this->assertEquals(
			'<table></table>',
			(string) $this->engine->createPattern('Html\Table')
		);
	}


	public function testPatternHtmlTableWithSingleRow() {

		$htmlTable = $this->engine->createPattern('Html\Table', array(), 
			array(array('hello', 'world'))
		);
		
		$this->assertEquals(
			"<table><tr><td>hello</td>\n".
			"<td>world</td>\n".
			"</tr>\n".
			"</table>",
			(string) $htmlTable
		);
	}


	public function testPatternHtmlTableWithAttributes() {

		$data = array(
			array('hello', 'world', 'are you awake?'),
		);
		
		$htmlTable = $this->engine->createPattern('Html\Table',
			array('id'=>'hello', 'class'=>'world'), $data
		);
		
		$this->assertEquals(
			"<table id=\"hello\" class=\"world\"><tr><td>hello</td>\n".
			"<td>world</td>\n".
			"<td>are you awake?</td>\n".
			"</tr>\n".
			"</table>"
			, (string) $htmlTable
		);
	}


	public function testPatternHtmlTableWithData() {

		$data = array(
			array('hello', 'world', 'are you really awake?'),
			array('foo', 'bar')
		);
		
		$htmlTable = $this->engine->createPattern('Html\Table', array(), $data);
		
		$this->assertEquals(
			"<table><tr><td>hello</td>\n".
			"<td>world</td>\n".
			"<td>are you really awake?</td>\n".
			"</tr>\n".
			"<tr><td>foo</td>\n".
			"<td>bar</td>\n".
			"</tr>\n".
			"</table>"
			, (string) $htmlTable
		);
	}


	public function testPatternHtmlForm() {

		$htmlForm = $this->engine->createPattern('Html\Form');
		
		$this->assertEquals(
			'<form></form>',
			(string) $htmlForm
		);
	}


	public function testPatternHtmlFormWithAttributes() {

		$htmlForm = $this->engine->createPattern('Html\Form', array('id'=>'hello'));
		
		$this->assertEquals(
			'<form id="hello"></form>',
			(string) $htmlForm
		);
	}


	public function testIOAsObject() {
		$this->engine->input('hello', 'world');
//        $this->assertEquals('world', $this->engine->output('hello')->length());
	}

	
	public function testPhpStringSingleInput() {
		$this->engine->input('hello', 'world');

		$this->assertEquals(
			'<?php' . "\n" .
			'$hello="world";' . "\n" .
			'?>', $this->engine->output('hello', 'php.string')
		);
	}

	
	public function testPhpStringSingleInputEcho() {
		$this->engine->input('hello', 'world');

		$this->assertEquals(
			'<?php' . "\n" .
			'$hello="world";' . "\n" .
			'echo $hello;' . "\n" .
			'?>',
			
			$this->engine->output('', 'php.string', 
				array(
					'return' => true,
					'tags' => true,
					'echo' => true
				)
			)
		);
	}

	
	public function testPhpNoTagsStringSingleSelectHello() {
		$this->engine->input('hello', 'world');

		$this->assertEquals('$hello="world";' . "\n", 
			$this->engine->output('hello', 'php.string', 
				array('return' => true, 'tags' => false)
			)
		);
	}

	
	public function testPhpStringMultiple() {
		$this->engine->input('hello', 'world');
		$this->engine->input('foo', 'bar');

		$this->assertEquals(
			'<?php' . "\n" .
			'$hello="world";' . "\n" .
			'$foo="bar";' . "\n" .
			'?>', $this->engine->output('', 'php.string')
		);
	}

	
	public function testPhpStringMultipleEcho() {
		$this->engine->input('hello', 'world');
		$this->engine->input('foo', 'bar');

		$this->assertEquals(
			'<?php' . "\n" .
			'$hello="world";' . "\n" .
			'$foo="bar";' . "\n" .
			'echo $hello;' . "\n" .
			'echo $foo;' . "\n" .
			'?>', 
			$this->engine->output('', 'php.string', 
				array(
					'return' => true,
					'tags' => true,
					'echo' => true
				)
			)
		);
	}

	
	public function testPhpStringMultipleGetSingle() {
		$this->engine->input('hello', 'world');
		$this->engine->input('foo', 'bar');

		$this->assertEquals(
			'<?php' . "\n" .
			'$hello="world";' . "\n" .
			'?>', $this->engine->output('hello', 'php.string')
		);
	}

	
	public function testPhpArraySingleString() {
		$this->engine->input('hello', 'world');

		$this->assertEquals(
			'<?php' . "\n" .
			'$data=array(' . "\n" .
			"'hello'=>'world',\n" .
			");\n" .
			'?>', $this->engine->output('', 'php.array')
		);
	}

	
	public function testPhpArrayMultipleStringSelectFoo() {
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

	
	public function testPhpArrayMultipleString() {
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
	

	public function testPhpArrayMultipleMixed() {
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

	
	public function testPhpStdClass() {
		$this->engine->input('hello', 'world');
		$this->engine->input('foo', 'bar');

		$this->assertEquals(
			'<?php' . "\n" .
			'$' . "data=new stdClass;\n" .
			'$' . "data->hello='world';\n" .
			'$' . "data->foo='bar';\n" .
			'?>'
			, $this->engine->output('', 'php.class')
		);
	}
	

	// TODO add recursive method for handling nested arrays.
	public function testPhpNoTagsStdClass() {

		$testing = array('testing');

		$this->engine->input('hello', 'world');
		$this->engine->input('foo', 'bar');
		//$this->engine->input('test', $testing);

		$this->assertEquals(
			'$data=new stdClass;'."\n"
			.'$' . "data->hello='world';\n"
			.'$' . "data->foo='bar';\n"
		//	.'$' . "data->test=" . var_export($testing, true) . ";\n"
			, 
			$this->engine->output('', 'php.class',
				array('return' => true, 'tags' => false)
			)
		);
	}

	
	public function testPhpNoTagsCustomClass() {

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

	
	public function testPhpTemplateNoTagsClassWithStereotype() {

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

	
	public function testPhpTemplateClassConstructor() {

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

	
	public function testPhpTemplateClassConstructorAll() {

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
	

	public function testPhpTemplateClassConstructorAndMethodAll() {

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
	

	public function testPhpTemplateClassConstructorAndMethodMultipleAll() {

		$testing = array('testing');

		$this->engine->input('hello', 'world');
		$this->engine->input('foo', 'bar');
		$this->engine->input('testing', $testing);

		$this->assertEquals(
			"<?php\n" .
			"class Custom extends CustomStereotype {\n" .
//            "\t".'public $hello=\'world\';'."\n".
//            "\t".'public $foo=\'bar\';'."\n".
			"\t" . 'public $testing=' . var_export($testing, true) . ";\n" .
			"\t" . 'public function __construct($param1=true,$param2=false,$param3=\'string\') {' . "\n" .
			"\t\t" . 'parent::__construct();' . "\n" .
			"\t\t" . "\n" .
			"\t\t" . '$data=new stdClass();' . "\n" .
			"\t\t" . '$hello="world";' . "\n" .
			"\t\t" . '// comment in constructor.;' . "\n" .
			"\t" . "}\n" .
			"\t" . 'public function methodName1($param1=true,$param2=false,$param3=\'string\') {' . "\n" .
			"\t\t" . '$' . 'foo="bar";' . "\n" .
			"\t\t" . '// comment.;' . "\n" .
			"\t" . "}\n" .
			"}\n" .
			"?>"
			, $this->engine->output(
				//'',
				'testing', 'php.class', array(
			    'return' => true,
			    //'echo'=>true,
			    'tags' => true,
			    'tabs' => true,
			    'class' => 'Custom',
			    'class.stereotype' => 'CustomStereotype',
			    'class.methods' => array(
				'__construct' => array(
				    'parameters' => array('param1' => true, 'param2' => false, 'param3' => 'string'),
				    'code' => array(
					'parent::__construct()',
					'',
					'$data=new stdClass()',
					'$hello="world"',
					'// comment in constructor.'
				    )
				),
				'methodName1' => array(
				    'parameters' => array('param1' => true, 'param2' => false, 'param3' => 'string'),
				    'code' => array('$foo="bar"', '// comment.')
				)
			    )
				)
			)
		);
	}

	
	public function XtestPhpTemplate() {

		$data = array(
		    '$data=new stdClass()',
		    '$hello="world"',
		    '// comment.',
		    '$test1=array()',
		    '$test2=array(\'key\'=>\'value\')',
		    '$test3=true',
		    '$test4=false',
		    '$test5=1',
		    '$test6=0',
		    '$test7=0.1',
		);
		// assign all data.
		foreach ($data as $key => $value) {
			$this->engine->input($key, $value);
		}

		$this->assertEquals(
			'$data=new stdClass();' . "\n" .
			'$hello="world";' . "\n" .
			'// comment.;' . "\n" . // minor issue but hey its a comment.
			'$test1=array();' . "\n" .
			'$test2=array(\'key\'=>\'value\');' . "\n" .
			'$test3=true;' . "\n" .
			'$test4=false;' . "\n" .
			'$test5=1;' . "\n" .
			'$test6=0;' . "\n" .
			'$test7=0.1;' . "\n"
			,
			//$this->engine->output('hello', 'php.template', 
			$this->engine->output('', 'php.template', array(
			    'return' => true,
			    'tags' => false,
				)
			)
		);
	}

	
	public function testCssTemplate() {

		$this->engine->input('body, html', '');

		$this->assertEquals(
			'body, html {' . "\n" .
			'}' . "\n"
			, $this->engine->output('body, html', 'css.template', array(
			    'return' => true,
				//'tags'=>true, // no tags with css.
				)
			)
		);
	}

	
	public function testCssTemplateMultiple() {

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
			//#releaseBox, #candidateBox {
			//	border : 1px dotted #999;
			//	margin : 0 0 5px 0;
			//	padding: 2px;
			//}
		);
		// assign all data for input.
		foreach ($data as $key => $value) {
			$this->engine->input($key, $value);
		}

		// make assertions that data output is css?
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

	
	/*
	 * TODO reduced the method list in the engine by using __call to organise 
	 * allowed method s depending on the doctype. This will also allow for more 
	 * granular control over the allowed attributes.
	 * 
	 */
	public function testDynamicCall() {

		$result = $this->engine->getDiv();

		$this->assertInternalType('object', $result);
		$this->assertEquals('DOMNodeList', get_class($result));
		
		
		//$this->assertTrue($this->engine->setTesting('', 'Test', 'Testing', array()));
	}

	
	// TODO
	public function testDiagram() {

		$xml_string = file_get_contents(dirname(dirname(__FILE__)) . '/tests/fixture/dia/Example.dia');
		$this->engine->run($xml_string, 'string', 'xml');

		$xml = $this->engine->outputXML();
		//$this->engine->dump($xml); exit;
	}

	
	public function testOverrideDOMsaveHTML() {

		$this->assertEquals("", $this->engine->saveHTML());
		
		// DOMDocument default returns a single newline...
		$this->assertEquals("\n", $this->engine->saveHTML('', false));
	}


	public function testOverrideDOMsaveHTMLwithHTMLString() {

		$this->engine->run('<div id="test"></div>');

		$this->assertEquals(
			'<html><body><div id="test"></div></body></html>'
			, $this->engine->saveHTML()
		);
	}


	public function testOverrideDOMsaveHTMLBodyOnly() {

		$this->engine->run('<div id="test"></div>');

		$this->assertEquals(
			'<body><div id="test"></div></body>',
			$this->engine->saveHTML('/html/body')
		);
	}


	public function testOverrideDOMsaveHTMLDivOnly() {

		$this->engine->run('<div id="test"></div>');

		$this->assertEquals(
			'<div id="test"></div>',
			$this->engine->saveHTML('/html/body/div')
		);
	}
}
