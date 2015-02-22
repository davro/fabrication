<?php
require_once(dirname(__FILE__) . '/../../library/FabricationEngine.php');

$engine = new Fabrication\FabricationEngine();
$engine->input('hello', 'world');

var_dump($engine->output('hello', 'php.array', array('tags'=>false)));
