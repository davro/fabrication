# Fabrication Engine

## Introduction

Template engine based on the Document Object Model.

The FabricationEngine engine is not like most of the other templating engine's 
on the market, mainly because the engine has no concept of place holders, it is 
only concerned with elements and attributes, structures, expressions and 
instructions for content that requires processing.

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
      
    $engine = new Fabrication\FabricationEngine();
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
      
    $template = '<html><head></head><body><div id="hello"></div></body></html>';

    $engine = new Fabrication\FabricationEngine();
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
    
    $template = '<html><head></head><body><div class="hello"></div></body></html>';

    $engine = new Fabrication\FabricationEngine();
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

## Contributors

* David Stevens (davro)


## License

Fabrication Engine is released under the LGPL license.

