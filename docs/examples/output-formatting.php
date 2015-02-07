<?php
require_once(dirname(__FILE__) . '/../../library/FabricationEngine.php');

$engine = new Fabrication\FabricationEngine();
$engine->input('hello', 'world');

//var_dump($engine->output('hello', 'php'));
//var_dump($engine->output('hello', 'php.array'));
var_dump($engine->output('hello', 'php.array', array('tags'=>false)));


//echo $engine->output('hello', 'php.class');
//
//
//$engine->input('body', array('bgcolor'=>'#999999'));
//
//echo $engine->output('body', 'css');
