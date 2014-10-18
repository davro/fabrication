<?php

require dirname(__FILE__) . '/../../library/FabricationEngine.php';

$engine = new \Library\FabricationEngine();
$engine->input('hello', 'world');

var_dump($engine->output('hello'));
