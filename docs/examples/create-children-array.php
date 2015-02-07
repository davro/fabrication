<?php
/**
 * Example: Create a div element with an id attribute and value.
 * with children recursively.
 * 
 */
require_once(dirname(__FILE__) . '/../../library/FabricationEngine.php');

$engine = new Fabrication\FabricationEngine();

$hi = $engine->create('div', '', array('id'=>'hello-world'),
	array(
		array(
			'name'       => 'u', 
			'value'      => 'Hello',
			'attributes' => array('id' => 'hello'),
			'children'   => array()
		),
		array(
			'name'       => 'strong', 
			'attributes' => array('id' => 'world'), 
			'children'   => array(
				array('name' => 'i', 'value' => 'W'),
				array('name' => 'i', 'value' => 'o'),
				array('name' => 'i', 'value' => 'r'),
				array('name' => 'i', 'value' => 'l'),
				array('name' => 'i', 'value' => 'd')
			)
		)
	)
);

$engine->appendChild($hi);

echo $engine->saveHTML();
