<?php

namespace Fabrication\Tests;

use Fabrication\Library\FabricationEngine;

//require_once(dirname(dirname(__FILE__)).'/lib/Fabrication.php');
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

	
	public function testDefaultDoctype() {
		
		$this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
		$this->assertEquals('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"' . "\n" . '   "http://www.w3.org/TR/html4/loose.dtd">', $this->engine->getDoctype());
	}

	
	public function testOptionSettingDoctype() {
		
		$this->assertEquals('html.5', $this->engine->setOption('doctype', 'html.5'));
		$this->assertEquals('<!DOCTYPE HTML>', $this->engine->getDoctype());
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

	
	public function testCreatingElement() {

		$value = "Hello World!";
		$div = $this->engine->create('div', $value, array(), array());
		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals($value, $div->nodeValue);
	}

	
	public function testCreatingElementThenView() {

		$value = "";
		$div = $this->engine->create('div', $value, array(), array());
		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals($value, $div->nodeValue);

		$this->assertEquals('<div></div>', $this->engine->view());
	}

	
	public function testCreatingElementWithValueThenView() {

		$value = "Hello World!";
		$div = $this->engine->create('div', $value, array(), array());
		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals($value, $div->nodeValue);

		$this->assertEquals('<div>Hello World!</div>', $this->engine->view());
	}

	
	public function testCreatingElementWithSingleAttributeAndValueThenView() {

		$value = "Hello World!";
		$div = $this->engine->create('div', $value, array('id' => 'hello'));
		$this->engine->appendChild($div);

		$this->assertEquals('DOMElement', get_class($div));
		$this->assertEquals('div', $div->nodeName);
		$this->assertEquals($value, $div->nodeValue);

		$this->assertEquals('<div id="hello">Hello World!</div>', $this->engine->view());
	}

	
	public function testCreatingElementWithRecursionThenView() {

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
		$this->assertEquals(
			'<div id="hello" class="world">Hello World!' .
			'<div>TEST<div>1</div></div>' .
			'<div id="test2">TEST<div>2</div></div>' .
			'</div>', $this->engine->view()
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

		$map = 'id';
		//$map = 'class';

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
			, $this->engine->output('', 'php.object')
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
			$this->engine->output('', 'php.object',
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

	
	public function testPhptemplateClassConstructorAll() {

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

	
	public function testPhpTemplate() {

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

	
	public function testJavascriptTemplate() {

		$this->assertEquals(
			'$(document).ready(function () { $("p").text("The DOM is now loaded and can be manipulated."); });' . "\n"
			, $this->engine->output('', 'javascript.template', array(
			    'return' => true,
			    'tags' => false,
			    'class.methods' => array(
				'$(document).ready' => array(
				    //'parameters'=>array('param1'=>true, 'param2'=>false),
				    'code' => array(
					'(function () { $("p").text("The DOM is now loaded and can be manipulated."); })',
				    )
				)
			    )
				)
			)
		);
	}

	
	public function testJavascriptTemplatewithTags() {

		$this->assertEquals(
			'<script>' . "\n" .
			'$(document).ready(function () { $("p").text("The DOM is now loaded and can be manipulated."); });' . "\n" .
			'$("div").addClass(function(index, currentClass) { } );' . "\n" .
			'</script>' . "\n"
			, $this->engine->output('', 'javascript.template', array(
			    'return' => true,
			    'tags' => true,
			    'class.methods' => array(
				'$(document).ready' => array(
				    //'parameters'=>array('param1'=>true, 'param2'=>false),
				    'code' => array(
					'(function () { $("p").text("The DOM is now loaded and can be manipulated."); })',
				    )
				),
				'$("div").addClass' => array(
				    'code' => array(
					'(function(index, currentClass) { } )',
				    )
				)
			    )
				)
			)
		);
	}

	
	/*
	 * TODO reduced the method list in the engine by using __call to organise 
	 *  allowed method s depending on the doctype. This will also allow for more 
	 * granular control over the attributes.
	 * 
	 */
	public function testDynamicCall() {

		$result = $this->engine->getArticle();

		$this->assertInternalType('object', $result);
		$this->assertEquals('DOMNodeList', get_class($result));
	}

	
	// TODO
	public function testDiagram() {

		$xml_string = file_get_contents(dirname(dirname(__FILE__)) . '/tests/fixture/dia/Example.dia');
		$this->engine->run($xml_string, 'string', 'xml');

		$xml = $this->engine->outputXML();
		//$this->engine->dump($xml); exit;
	}

}