# Fabrication Engine

## Introduction

Next generation template engine based on the Document Object Model

The Fabrication engine or "Fabic" for short.

The Fabrication Engine represents and interacts with objects in HTML, XHTML and 
XML documents using DOM and XPath its "Elements" are addressed and manipulated 
within the syntax of public interface, direct XPath queries can be executed on 
the Document Object Model or simply use one of the many built in query methods.

The FabricationEngine is not like most of the other templating engine's on the 
market, maily because the engine has no concept of place holders, it is only 
concerned with elements and attributes, structures, expressions and instructions 
for content that requires processing.

Structures are the templates and expressions are paths to the elements contained
within the Document Object Model. The FabricationEngine extends the PHP builtin 
DOMDocument in many ways and enables the native usage of the XPath object. 
This allows for an insanely flexible and extensible templating engine.

You can create a DOM structure by loading a html, xhtml, xml string or simply by
loading a file, or you can build your own document structure by using the native
DOMDoument API. Also there is a specifcation method with the ability to recursivly
create structures. There are also some advanced dateset templating features.


## Features

Templating without place holders.
Document specification pattern methods.
Dataset Templating.

## Documentation


### Example: Input key, value pair and output.
Simple example of Input/Output.

    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');
      
    $engine = new Fabrication\Library\FabricationEngine();
    $engine->input('hello', 'world');

    echo $engine->output('hello');
	#
    # world
	#

	?>


### Example: Input id #key, value pair.
ID keys are automatically mapped to elements with matching id identifiers.

    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');
      
    $template = '<html><head></head><body><div id="hello"></div></body></html>';

    $engine = new Fabrication\Library\FabricationEngine();
    $engine->input('#hello', 'world');

    $engine->run($template);

    echo $engine->output('#hello');
	#
    # world
	#

    echo $engine->saveHTML();
	#
    # <html><head></head><body><div id="hello">world</div></body></html>
	#

    echo $engine->saveFabric();
	#
    # <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    #    "http://www.w3.org/TR/html4/loose.dtd">'.
    # <html><head></head><body><div id="hello">world</div></body></html>
	#

    ?>


### Example: Input class .key, value pair.
Class keys are automatically mapped to elements with matching class identifiers.

    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');
      
    $template = '<html><head></head><body><div class="hello"></div></body></html>';

    $engine = new Fabrication\Library\FabricationEngine();
    $engine->input('.hello', 'world');

    $engine->run($template);

    echo $engine->output('.hello'); 
	#
    # world
	#

    echo $engine->saveHTML();
	#
    # <html><head></head><body><div class="hello">world</div></body></html>
	#

    echo $engine->saveFabric();
	#
    # <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    #    "http://www.w3.org/TR/html4/loose.dtd">'.
    # <html><head></head><body><div class="hello">world</div></body></html>
	#

    ?>


### Example: Output patterns.
Output method retrives key, value pairs from the stack.
With the Option to apply patterns to the stack or parts of the stack.
You have seen the simplest example of Output in the first example, so we can 
move on to something more advanced, like turning the plain text Input data 
into known patterns on the fly cool huh!

    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');

    $engine = new FabricationEngine();
    $engine->input('hello', 'world');

    echo $engine->output('hello', 'php');
    #
    # <?php
    # $hello="world";
    # ?>
    #

    ?>

Output method using a standard php array.

    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');

    $engine = new FabricationEngine();
    $engine->input('hello', 'world');

    echo $engine->output('hello', 'php.array');
    #
    # <?php
    # $data=array(
    # 'hello'=>'world',
    # );
    # ?>
    #

    ?>

Output method using standard php class.

    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');

    $engine = new FabricationEngine();
    $engine->input('hello', 'world');

    echo $engine->output('hello', 'php.class');
    #
    # <?php
    # $data=new stdClass;
    # $data->hello='world';
    # ?>
    #

    ?>

Output method using css template.

    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');

    $engine = new FabricationEngine();
    $engine->input('body', array('bgcolor'=>'#999999'));

    echo $engine->output('body', 'css');
    #
    # body {
    # bgcolor: #999999;
    # } 
    #

    ?>


### Example: Option change doctype from default to HTML5.
Doctypes are selected from the current supported list.

    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');
      
    $template = '<html><head></head><body></body></html>';

    $engine = new FabricationEngine();
    $engine->run($template);

    $engine->setOption('doctype', 'html.5');

    echo $engine->saveFabric();
    # <!DOCTYPE HTML>
    # <html><head></head><body></body></html>
	#

    ?>

## TODO


### Example: View                    (paths)

### Example: Create                  (..)

### Example: CreateElement           (..)

### Example: createElementRecursion  (..)

### Example: Template                (datasets)

### Example: Query                   (paths)

### Example: Specification Calls     (types)

### Example: Dump and Debug          (types)



## Contributors

* David Stevens (davro)


## License

Fabrication Engine is released under the LGPL license.

