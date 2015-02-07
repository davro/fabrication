<?php

require dirname(__FILE__) . '/../../library/FabricationEngine.php';

$engine = new \Fabrication\FabricationEngine();
$engine->input('#greeting', $engine->create('div', 'World'));

$template = '<html><head></head><body><div id="greeting">Hello</div></body></html>';
$engine->run($template);

var_dump($engine->saveHTML());
