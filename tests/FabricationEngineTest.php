<?php
namespace Fabrication\Tests;

use Fabrication\Library\FabricationEngine;

//require_once(dirname(dirname(__FILE__)).'/lib/Fabrication.php');
require_once(dirname(dirname(__FILE__)).'/lib/FabricationEngine.php');

class FabricationEngineTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->engine = new FabricationEngine();
    }

    public function testEngineInstance() {
        $this->assertInternalType('object', $this->engine);
        $this->assertInstanceOf('Fabrication\Library\FabricationEngine', $this->engine);
    }
    
    public function testEngineGet() {
        $engine=$this->engine->getEngine();
        $this->assertInternalType('object', $engine);
        $this->assertInstanceOf('Fabrication\Library\FabricationEngine', $engine);
    }
    
    public function testEngineAttributes() {
        $this->assertObjectHasAttribute('input', $this->engine);
        $this->assertObjectHasAttribute('output', $this->engine);
        $this->assertObjectHasAttribute('options', $this->engine);
    }
    
    public function testEngineDefaultDoctype() {
        $this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
        $this->assertEquals('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"'."\n".'   "http://www.w3.org/TR/html4/loose.dtd">', $this->engine->getDoctype());
    }
        
    
    public function testEngineOptionSettingDoctype() {
        $this->assertEquals('html.5', $this->engine->setOption('doctype', 'html.5'));
        $this->assertEquals('<!DOCTYPE HTML>', $this->engine->getDoctype());
    }
    
    public function testEngineAllOptionsDefaults() {
        $this->assertEquals(true, $this->engine->getOption('process'));
        $this->assertEquals(true, $this->engine->getOption('process.body.image'));
        $this->assertEquals(true, $this->engine->getOption('process.body.br'));
        $this->assertEquals(true, $this->engine->getOption('process.body.hr'));
    }

    public function testEngineIO() {
        $this->engine->input('hello', 'world');
        $this->assertEquals('world', $this->engine->output('hello'));
    }

    public function testEngineCreateElement() {

	$value = "Hello World!";
	$div = $this->engine->create('div', $value, array(), array());
	$this->engine->appendChild($div);
	
	$this->assertEquals('DOMElement', get_class($div));
	$this->assertEquals('div', $div->nodeName);
	$this->assertEquals($value, $div->nodeValue);
    }

    public function testEngineCreateElementThenView() {
	
	$value = "";
	$div = $this->engine->create('div', $value, array(), array());
	$this->engine->appendChild($div);
	
	$this->assertEquals('DOMElement', get_class($div));
	$this->assertEquals('div', $div->nodeName);
	$this->assertEquals($value, $div->nodeValue);
	
	$this->assertEquals('<div></div>', $this->engine->view());
    }
    
    public function testEngineCreateElementWithValueThenView() {
	
	$value = "Hello World!";
	$div = $this->engine->create('div', $value, array(), array());
	$this->engine->appendChild($div);
	
	$this->assertEquals('DOMElement', get_class($div));
	$this->assertEquals('div', $div->nodeName);
	$this->assertEquals($value, $div->nodeValue);
	
	$this->assertEquals('<div>Hello World!</div>', $this->engine->view());
    }

    public function testEngineCreateElementWithSingleAttributeAndValueThenView() {
	
	$value = "Hello World!";
	$div = $this->engine->create('div', $value, array('id'=>'hello'));
	$this->engine->appendChild($div);
	
	$this->assertEquals('DOMElement', get_class($div));
	$this->assertEquals('div', $div->nodeName);
	$this->assertEquals($value, $div->nodeValue);
	
	$this->assertEquals('<div id="hello">Hello World!</div>', $this->engine->view());
    }

    public function testEngineCreateElementWithRecursionThenView() {

	$value = "Hello World!";
	$div = $this->engine->create(
	    'div',
	    $value,
	    array('id'=>'hello', 'class'=>'world'),
	    array(
		array('name'=>'div', 'value'=>'TEST', 'children'=>array(array('name'=>'div', 'value'=>'1'))),
		array('name'=>'div', 'value'=>'TEST', 'attributes'=>array('id'=>'test2'), 'children'=>array(array('name'=>'div', 'value'=>'2'))),
	    )

	);
	$this->engine->appendChild($div);
	
	$this->assertEquals('DOMElement', get_class($div));
	$this->assertEquals('div', $div->nodeName);
	$this->assertEquals($value.'TEST1TEST2', $div->nodeValue);
	$this->assertEquals(
	    '<div id="hello" class="world">Hello World!'.
		'<div>TEST<div>1</div></div>'.
		'<div id="test2">TEST<div>2</div></div>'.
	    '</div>',
	    $this->engine->view()
	);
    }
    
    public function testEngineCreateElementThenViewByXPath() {
	
	$value = "Hello World!";
	$div = $this->engine->create('div', $value, array('id'=>'hello', 'class'=>'world'), array());
	$this->engine->appendChild($div);
	
	$this->assertEquals('DOMElement', get_class($div));
	$this->assertEquals('div', $div->nodeName);
	$this->assertEquals($value, $div->nodeValue);
	
	// start running some xpath querys on the engine and view in html :))
	$this->assertEquals('<div id="hello" class="world">'.$value.'</div>', $this->engine->view("//*"));
	$this->assertEquals($value, $this->engine->view('//div[@id="hello"]/text()'));
    }
    
    public function testEngineCreateElementWithRecursionThenViewByXPath() {
	
	$value = "Hello World!";
	$div = $this->engine->create(
	    'div',
	    $value,
	    array('id'=>'hello', 'class'=>'world'),
	    array(
		array('name'=>'div', 'value'=>'TEST', 'children'=>array(array('name'=>'div', 'value'=>'1'))),
		array('name'=>'div', 'value'=>'TEST', 'attributes'=>array('id'=>'test2'), 'children'=>array(array('name'=>'div', 'value'=>'2'))),
	    )

	);
	$this->engine->appendChild($div);
	
	$this->assertEquals('DOMElement', get_class($div));
	$this->assertEquals('div', $div->nodeName);
	$this->assertEquals($value.'TEST1TEST2', $div->nodeValue);
	
	// start running some xpath querys on the engine and view in html :))
	$this->assertEquals("<div>2</div>", $this->engine->view('//div[@id="test2"]/div'));
	$this->assertEquals("2", $this->engine->view("//div[@id='test2']/div/text()"));
    }

    public function testEngineCreateListViewFromData() {
	
	$listview = $this->engine->create('div', '', array('id'=>'listview'));
	$listview->appendChild($this->engine->create('div', '', array('id'=>'data_id')));
	$listview->appendChild($this->engine->create('div', '', array('id'=>'data_title')));
	$listview->appendChild($this->engine->create('div', '', array('id'=>'data_content')));
	
//	$this->engine->appendChild($listview);
//	
//	$this->assertEquals(
//	    '<div id="listview">'.
//	    '<div id="data_id"></div>'.
//	    '<div id="data_title"></div>'.
//	    '<div id="data_content"></div>'.
//	    '</div>',
//	    $this->engine->view('//div[@id="listview"]')
//	);

	$data = array(
	    array('data_id'=>1, 'data_title'=>'Title Testing1', 'data_content'=>'Content Testing1'),
	    array('data_id'=>2, 'data_title'=>'Title Testing2', 'data_content'=>'Content Testing2'),
	    array('data_id'=>3, 'data_title'=>'Title Testing3', 'data_content'=>'Content Testing3'),
	);
	
	$this->assertEquals(
	    '<div id="listview">'.
	    '<div id="data_id_1">1</div>'.
	    '<div id="data_title_1">Title Testing1</div>'.
	    '<div id="data_content_1">Content Testing1</div>'.
	    '<div id="data_id_2">2</div>'.
	    '<div id="data_title_2">Title Testing2</div>'.
	    '<div id="data_content_2">Content Testing2</div>'.
	    '<div id="data_id_3">3</div>'.
	    '<div id="data_title_3">Title Testing3</div>'.
	    '<div id="data_content_3">Content Testing3</div>'.
	    '</div>',
	    $this->engine->template($listview, $data)
	);

	//$this->engine->template('//div[@id="listview"]', $data)
    }
    
    public function testEngineIOAsObject() {
        $this->engine->input('hello', 'world');
//        $this->assertEquals('world', $this->engine->output('hello')->length());
    }
    
    public function testEnginePhpStringSingleInput() {
        $this->engine->input('hello', 'world');
        
        $this->assertEquals(
            '<?php'."\n".
            '$hello="world";'."\n".
            '?>', 
            $this->engine->output('hello', 'php.string')
        );
	
    }

    public function testEnginePhpStringSingleInputEcho() {
        $this->engine->input('hello', 'world');
        
        $this->assertEquals(
            '<?php'."\n".
            '$hello="world";'."\n".
            'echo $hello;'."\n".
            '?>',
                
            $this->engine->output('', 'php.string',
                array(
                    'return'=>true,
                    'tags'=>true,
                    'echo'=>true
                    
                )
            )
        );
    }
    
    public function testEnginePhpNoTagsStringSingleSelectHello() {
        $this->engine->input('hello', 'world');
        
        $this->assertEquals(
            '$hello="world";'."\n",
            $this->engine->output('hello', 'php.string', 
                array('return'=>true, 'tags'=>false)
            )
        );
    }
    
    public function testEnginePhpStringMultiple() {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        
        $this->assertEquals(
            '<?php'."\n".
            '$hello="world";'."\n".
            '$foo="bar";'."\n".
            '?>', 
            $this->engine->output('', 'php.string')
        );
    }
    
    public function testEnginePhpStringMultipleEcho() {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        
        $this->assertEquals(
            '<?php'."\n".
            '$hello="world";'."\n".
            '$foo="bar";'."\n".
            'echo $hello;'."\n".
            'echo $foo;'."\n".
            '?>', 
            $this->engine->output('', 'php.string', 
                array(
                    'return'=>true,
                    'tags'=>true,
                    'echo'=>true
                    
                )
            )
        );
    }
    
    public function testEnginePhpStringMultipleGetSingle() {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        
        $this->assertEquals(
            '<?php'."\n".
            '$hello="world";'."\n".
            '?>', 
            $this->engine->output('hello', 'php.string')
        );
    }
    
    public function testEnginePhpArraySingleString() {
        $this->engine->input('hello', 'world');
        
        $this->assertEquals(
            '<?php'."\n".
            '$data=array('."\n".
            "'hello'=>'world',\n".
            ");\n".
            '?>', 
            $this->engine->output('', 'php.array')
        );
    }

    public function testEnginePhpArrayMultipleStringSelectFoo() {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');

        $this->assertEquals(
            '<?php'."\n".
            '$data=array('."\n".
            "'foo'=>'bar',\n".
            ");\n".
            '?>', 
            $this->engine->output('foo', 'php.array')
        );
    }
    

    public function testEnginePhpArrayMultipleString() {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');

        $this->assertEquals(
            '<?php'."\n".
            '$data=array('."\n".
            "'hello'=>'world',\n".
            "'foo'=>'bar',\n".
            ");\n".
            '?>', 
            $this->engine->output('', 'php.array')
        );
    }
    
    public function testEnginePhpArrayMultipleMixed() {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        $this->engine->input('test', array('hello'=>$this->engine->output('hello'),'foo'=>$this->engine->output('foo')));
        
        $this->assertEquals(
            '<?php'."\n".
            '$data=array('."\n".
            "'hello'=>'world',\n".
            "'foo'=>'bar',\n".
            "'test'=>array (\n".
            "  'hello' => 'world',\n".
            "  'foo' => 'bar',\n"."),\n".
            ");\n".
            '?>',
            $this->engine->output('', 'php.array')
        );
    }
    
    public function XtestEnginePhpStdClass() {
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        
        $this->assertEquals(
            '<?php'."\n".
            '$'."data=new stdClass;\n".
            '$'."data->hello='world';\n".
            '$'."data->foo='bar';\n".
            '?>'
            ,
            $this->engine->output('', 'php.object')
        );
    }
    
    public function XtestEnginePhpNoTagsStdClass() {
        
        $testing=array('testing');
        
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        $this->engine->input('test', $testing);
        
        $this->assertEquals(
            '$'."data=new stdClass;\n".
            '$'."data->hello='world';\n".
            '$'."data->foo='bar';\n".
            '$'."data->test=".var_export($testing,true).";\n"
            ,
            $this->engine->output('', 'php.class', array('return'=>true, 'tags'=>false))
        );
    }

    public function testEnginePhpNoTagsCustomClass() {
       
        $this->assertEquals(
            "class Custom {\n".
            "}\n"
            //'$'."objectCustom=new Custom;\n".
            //'$'."objectCustom->hello='world';\n".
            //'$'."objectCustom->foo='bar';\n".
            //'$'."objectCustom->testing=".var_export($testing, true).";\n".
            //'echo $objectCustom;'
            ,
            $this->engine->output('', 'php.class', 
                array(
                    'return'=>true, 
                    //'echo'=>true,
                    'tags'=>false, 
                    'class'=>'Custom',
                )
            )
        );
    }

    public function testEnginePhpTemplateNoTagsClassWithStereotype() {
        
        $this->assertEquals(
            "class Custom extends CustomStereotype {\n".
            "}\n"
            ,
            $this->engine->output('', 'php.class', 
                array(
                    'return'=>true, 
                    'tags'=>false, 
                    'class'=>'Custom',
                    'class.stereotype'=>'CustomStereotype',
                )
            )
        );
    }

    public function testEnginePhpTemplateClassConstructor() {
        
        $testing=array('testing');
        
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        $this->engine->input('testing', $testing);

        $this->assertEquals(
            "<?php\n".
            "class Custom extends CustomStereotype {\n".
            'public $hello=\'world\';'."\n".
            'public $foo=\'bar\';'."\n".
            'public $testing='.var_export($testing, true).";\n".
            'public function __construct($param1=true,$param2=false) {'."\n".
            "}\n".
            "}\n".
            "?>"
            ,
            $this->engine->output('', 'php.class', 
                array(
                    'return'=>true, 
                    //'echo'=>true,
                    'tags'=>true, 
                    'class'=>'Custom',
                    'class.stereotype'=>'CustomStereotype',
                    'class.methods'=>array(
                        '__construct'=>array(
                            'parameters'=>array('param1'=>true, 'param2'=>false),
                            'code'=>array() 
                        )
                    )
                )
            )
        );
    }

    public function testEnginePhptemplateClassConstructorAll() {
        
        $testing=array('testing');
        
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        $this->engine->input('testing', $testing);

        $this->assertEquals(
            "<?php\n".
            "class Custom extends CustomStereotype {\n".
            'public $hello=\'world\';'."\n".
            'public $foo=\'bar\';'."\n".
            'public $testing='.var_export($testing, true).";\n".
            'public function __construct($param1=true,$param2=false) {'."\n".
            'parent::__construct();'."\n".
            '$data=new stdClass();'."\n".
            "}\n".
            "}\n".
            "?>"
            ,
            $this->engine->output('', 'php.class', 
                array(
                    'return'=>true, 
                    //'echo'=>true,
                    'tags'=>true, 
                    'class'=>'Custom',
                    'class.stereotype'=>'CustomStereotype',
                    'class.methods'=>array(
                        '__construct'=>array(
                            'parameters'=>array('param1'=>true, 'param2'=>false),
                            'code'=>array(
                                'parent::__construct()',
                                '$data=new stdClass()',
                            ) 
                        )
                    )
                )
            )
        );
    }
    
    public function testEnginePhpTemplateClassConstructorAndMethodAll() {
        
        $testing=array('testing');
        
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar');
        $this->engine->input('testing', $testing);

        $this->assertEquals(
            "<?php\n".
            "class Custom extends CustomStereotype {\n".
            'public $hello=\'world\';'."\n".
            'public $foo=\'bar\';'."\n".
            'public $testing='.var_export($testing, true).";\n".
            'public function __construct($param1=true,$param2=false) {'."\n".
            'parent::__construct();'."\n".
            '$data=new stdClass();'."\n".
            "}\n".
            'public function methodName1($param1=true) {'."\n".
            '$'.'foo="bar";'."\n".
            '// comment.;'."\n".
            "}\n".
            "}\n".
            "?>"
            ,
            $this->engine->output('', 'php.class', 
                array(
                    'return'=>true, 
                    //'echo'=>true,
                    'tags'=>true, 
                    'class'=>'Custom',
                    'class.stereotype'=>'CustomStereotype',
                    'class.methods'=>array(
                        '__construct'=>array(
                            'parameters'=>array('param1'=>true, 'param2'=>false),
                            'code'=>array(
                                'parent::__construct()',
                                '$data=new stdClass()',
                            ) 
                        ),
                        'methodName1'=>array(
                            'parameters'=>array('param1'=>true),
                            'code'=>array('$foo="bar"', '// comment.') 
                        )
                    )
                )
            )
        );
    }

    public function testEnginePhpTemplateClassConstructorAndMethodMultipleAll() {
        
        $testing=array('testing');
        
        $this->engine->input('hello', 'world');
        $this->engine->input('foo', 'bar'); 
        $this->engine->input('testing', $testing);

        $this->assertEquals(
            "<?php\n".
            "class Custom extends CustomStereotype {\n".
//            "\t".'public $hello=\'world\';'."\n".
//            "\t".'public $foo=\'bar\';'."\n".
            "\t".'public $testing='.var_export($testing, true).";\n".
            "\t".'public function __construct($param1=true,$param2=false,$param3=\'string\') {'."\n".
            "\t\t".'parent::__construct();'."\n".
            "\t\t"."\n".
            "\t\t".'$data=new stdClass();'."\n".
            "\t\t".'$hello="world";'."\n".
            "\t\t".'// comment in constructor.;'."\n".
            "\t"."}\n".
            "\t".'public function methodName1($param1=true,$param2=false,$param3=\'string\') {'."\n".
            "\t\t".'$'.'foo="bar";'."\n".
            "\t\t".'// comment.;'."\n".
            "\t"."}\n".
            "}\n".
            "?>"
            ,
            $this->engine->output(
                //'',
                'testing',
                'php.class', 
                array(
                    'return'=>true, 
                    //'echo'=>true,
                    'tags'=>true, 
                    'tabs'=>true, 
                    'class'=>'Custom',
                    'class.stereotype'=>'CustomStereotype',
                    'class.methods'=>array(
                        '__construct'=>array(
                            'parameters'=>array('param1'=>true, 'param2'=>false, 'param3'=>'string'),
                            'code'=>array(
                                'parent::__construct()',
                                '',
                                '$data=new stdClass()',
                                '$hello="world"', 
                                '// comment in constructor.'
                            ) 
                        ),
                        'methodName1'=>array(
                            'parameters'=>array('param1'=>true, 'param2'=>false, 'param3'=>'string'),
                            'code'=>array('$foo="bar"', '// comment.') 
                        )
                    )
                )
            )
        );
    }
    
    public function testEnginePhpTemplate() {
        
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
            '$data=new stdClass();'."\n".
            '$hello="world";'."\n".
            '// comment.;'."\n".    // minor issue but hey its a comment.
            '$test1=array();'."\n".
            '$test2=array(\'key\'=>\'value\');'."\n".
            '$test3=true;'."\n".
            '$test4=false;'."\n".
            '$test5=1;'."\n".
            '$test6=0;'."\n".
            '$test7=0.1;'."\n"
            ,
            //$this->engine->output('hello', 'php.template', 
            $this->engine->output('', 'php.template', 
                array(
                    'return'=>true, 
                    'tags'=>false,
                )
            )
        );
    }

    public function testEngineCssTemplate() {

        $this->engine->input('body, html', '');

        $this->assertEquals(
            'body, html {'."\n".
            '}'."\n"
            ,
            $this->engine->output('body, html', 'css.template', 
                array(
                    'return'=>true, 
                    //'tags'=>true, // no tags with css.
                )
            )
        );
    }
    
    public function testEngineCssTemplateMultiple() {
        
        $data = array(
            'body, html'=>array(
                'margin'            => '0px',
                'padding'           => '0px',
                'color'             => 'black',
                'background-color'  => 'white',
            ),
            'body, input, textarea, select, option'=>array(
                'font-family'=>'verdana, arial, helvetica, sans-serif',
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
            '/**'."\n".
            ' * CSS Generated by the FabricationEngine.'."\n".
            ' */'."\n".
            'body, html {'."\n".
            'margin: 0px;'."\n".
            'padding: 0px;'."\n".
            'color: black;'."\n".
            'background-color: white;'."\n".
            '}'."\n".
            'body, input, textarea, select, option {'."\n".
            'font-family: verdana, arial, helvetica, sans-serif;'."\n".
            '}'."\n"
            ,
            $this->engine->output('', 'css.template', 
                array(
                    'return'=>true, 
                    'header'=>true,
                )
            )
        );
    }
    
    public function testEngineJavascriptTemplate() {

        $this->assertEquals(
            '$(document).ready(function () { $("p").text("The DOM is now loaded and can be manipulated."); });'."\n"
            ,
            $this->engine->output('', 'javascript.template', 
                array(
                    'return'=>true, 
                    'tags'=>false,
                    'class.methods'=>array(
                        '$(document).ready'=>array(
                            //'parameters'=>array('param1'=>true, 'param2'=>false),
                            'code'=>array(
                                '(function () { $("p").text("The DOM is now loaded and can be manipulated."); })',
                            ) 
                        )
                    )
                )
            )
        );
    }
    
    public function testEngineJavascriptTemplatewithTags() {

        $this->assertEquals(
            '<script>'."\n".
            '$(document).ready(function () { $("p").text("The DOM is now loaded and can be manipulated."); });'."\n".
            '$("div").addClass(function(index, currentClass) { } );'."\n".
            '</script>'."\n"
            ,
            $this->engine->output('', 'javascript.template', 
                array(
                    'return'=>true, 
                    'tags'=>true,
                    'class.methods'=>array(
                        '$(document).ready'=>array(
                            //'parameters'=>array('param1'=>true, 'param2'=>false),
                            'code'=>array(
                                '(function () { $("p").text("The DOM is now loaded and can be manipulated."); })',
                            ) 
                        ),
                        '$("div").addClass'=>array(
                            'code'=>array(
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
    
    public function testEngineDynamicCall() { 
	
	$result = $this->engine->getArticle();
	
	$this->assertInternalType('object', $result);
	$this->assertEquals('DOMNodeList', get_class($result));
    }
    
    // TODO
    public function testEngineDiagram() { 
	
	$xml_string = file_get_contents(dirname(dirname(__FILE__)).'/tests/fixture/dia/Example.dia');
	$this->engine->run($xml_string, 'string', 'xml');
	
	$xml = $this->engine->outputXML();
	//$this->engine->dump($xml); exit;
    }
}