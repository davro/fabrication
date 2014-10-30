<?php

require dirname(__FILE__) . '/../../library/FabricationEngine.php';

$engine = new \Library\FabricationEngine();
$engine->input('#hello', 'world');
$template = '<html><head></head><body><div id="hello"></div></body></html>';
$engine->run($template);

var_dump($engine->output('#hello'));
var_dump($engine->saveHTML('//div[@id="hello"]/text()'));
var_dump($engine->saveHTML());

