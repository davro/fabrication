<?php

require dirname(__FILE__) . '/../../library/FabricationEngine.php';

$engine = new \Fabrication\FabricationEngine();
$engine->run('http://slashdot.org', 'file');

echo $engine->saveHTML();
