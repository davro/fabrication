<?php
/**
 * Example: Create a div element with an id attribute and value.
 * 
 */

require_once(dirname(__FILE__) . '/../../library/FabricationEngine.php');

$engine = new Fabrication\FabricationEngine();

# Create DOM Element of the type div with an id attribute with the value of hello-world.
$hi = $engine->create('div', 'Hello', ['id'=>'hello']);

# Render the element.
$helloText =  $hi->ownerDocument->saveXML($hi);

# Append to the engine and view the html structure

$engine->appendChild($hi);
$engine->input('#hello', 'world');
echo $engine->saveHTML();



