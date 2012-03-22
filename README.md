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

```php
    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');
      
    $engine = new Fabrication\Library\FabricationEngine();
    $engine->input('hello', 'world');

    echo $engine->output('hello');
	#
    # world
	#

	?>
```

### Example: Input id #key, value pair.
ID keys are automatically mapped to elements with matching id identifiers.

```php
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
```

### Example: Input class .key, value pair.
Class keys are automatically mapped to elements with matching class identifiers.

```php
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
```

### Example: Output patterns.
Output method retrives key, value pairs from the stack.
With the Option to apply patterns to the stack or parts of the stack.
You have seen the simplest example of Output in the first example, so we can 
move on to something more advanced, like turning the plain text Input data 
into known patterns on the fly cool huh!

```php
    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');

    $engine = new Fabrication\Library\FabricationEngine();
    $engine->input('hello', 'world');

    echo $engine->output('hello', 'php');
    #
    # <?php
    # $hello="world";
    # ?>
    #

    ?>
```

Output method using a standard php array.

```php
    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');

    $engine = new Fabrication\Library\FabricationEngine();
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
```

Output method using standard php class.

```php
    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');

    $engine = new Fabrication\Library\FabricationEngine();
    $engine->input('hello', 'world');

    echo $engine->output('hello', 'php.class');
    #
    # <?php
    # $data=new stdClass;
    # $data->hello='world';
    # ?>
    #

    ?>
```

Output method using css template.

```php
    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');

    $engine = new Fabrication\Library\FabricationEngine();
    $engine->input('body', array('bgcolor'=>'#999999'));

    echo $engine->output('body', 'css');
    #
    # body {
    # bgcolor: #999999;
    # } 
    #

    ?>
```

### Example: Option change doctype from default to HTML5.
Doctypes are selected from the current supported list.

```php
    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');
      
    $template = '<html><head></head><body></body></html>';

    $engine = new Fabrication\Library\FabricationEngine();
    $engine->run($template);

    $engine->setOption('doctype', 'html.5');

    echo $engine->saveFabric();
    #
    # <!DOCTYPE HTML>
    # <html><head></head><body></body></html>
	#

    ?>
```

### Example: Template pattern with matching identifiers into dataset output.
Template method allows for an elements pattern to be templated onto the dataset
based on a map between to the element and dataset, this map will automatically 
bump an integer value for display auto incrementing dataset.

```php
    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');
      
    $engine = new Fabrication\Library\FabricationEngine();

    $pattern = 
        '<div>PatternTemplate:'.
        '   <div class="uid">UID.</div>'.
        '   <div class="title">Title.</div>'.
        '   <div class="content">Content.</div>'.
        '</div>';

    $dataset = array(
        array('uid' => 1, 'title' => 'Title 1', 'content' => 'Content 1'),
        array('uid' => 2, 'title' => 'Title 2', 'content' => 'Content 2'),
        array('uid' => 3, 'title' => 'Title 3', 'content' => 'Content 3'),
    );

    $result = $engine->template($pattern, $dataset, 'class');
		
	echo $engine->saveXML($result)
    #
    # <div>PatternTemplate:UID.Title.Content.
    #    <div class="uid_1">1</div>
    #    <div class="title_1">Title 1</div>
    #    <div class="content_1">Content 1</div>
    #
    #    <div class="uid_2">2</div>
    #    <div class="title_2">Title 2</div>
    #    <div class="content_2">Content 2</div>
    #
    #    <div class="uid_3">3</div>
    #    <div class="title_3">Title 3</div>
    #    <div class="content_3">Content 3</div>
    # </div>
    #

	# append the result and view some xpath results.
    $this->engine->appendChild($result);

    echo $this->engine->view("//div[@class='uid_1']/text()");
    echo $this->engine->view("//div[@class='title_1']/text()");
    echo $this->engine->view("//div[@class='content_1']/text()");
    #
    # 1
    # Title 1
    # Content 1
    #

    echo $this->engine->view("//div[@class='uid_2']/text()");
    echo $this->engine->view("//div[@class='title_2']/text()");
    echo $this->engine->view("//div[@class='content_2']/text()");
    #
    # 2
    # Title 2
    # Content 2
    #

    echo $this->engine->view("//div[@class='uid_3']/text()");
    echo $this->engine->view("//div[@class='title_3']/text()");
    echo $this->engine->view("//div[@class='content_3']/text()");
    #
    # 3
    # Title 3
    # Content 3
    #

    ?>
```

### Example: Create an element with attributes and children, recursively.
Create method extends the builtin method createElement adding attribute 
functionality and the ability to recursively add children to the element 
style and script elements.

NOTE: this method is experimental.

```php
    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');
      
    $engine = new Fabrication\Library\FabricationEngine();

    $hi = $engine->create('div', 'Hello World', array('id'=>'hello-world'));

    echo $hi->nodeName;
    #
    # div
    #

    echo $hi->nodeValue;
    #
    # Hello World
    #

	echo $hi->attributes->getNamedItem('id')->nodeName;
    #
    # id
    #

    echo $hi->attributes->getNamedItem('id')->nodeValue;
    #
    # hello-world
    #


    $engine->appendChild($hi);
    echo $engine->saveHTML();
    #
    # <div id="hello-world">Hello World</div>
    #

    ?>
```

Create method with children and recursion.

NOTE: this method is experimental.

```php
    <?php
    require_once(dirname(__FILE__) . '/lib/FabricationEngine.php');
      
    $engine = new Fabrication\Library\FabricationEngine();


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

    $engine->appendChild($hi);

    echo $engine->saveHTML()
    # 
    # <div id="hello-world">
    # <u id="hello">Hello</u>
    # <strong id="world">
    # <i>W</i><i>o</i><i>r</i><i>l</i><i>d</i>
    # </strong>
    # </div>
    #

    ?>
```

## TODO

### Example: Specification           (types)

### Example: Query                   (paths)

### Example: View                    (paths)

### Example: Dump and Debug          (types)


## Contributors

* David Stevens (davro)


## License

Fabrication Engine is released under the LGPL license.

