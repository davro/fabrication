<?php

require dirname(__FILE__) . '/../../library/FabricationEngine.php';

$engine = new \Library\FabricationEngine();
$engine->run('http://slashdot.org', 'file');

print $engine->saveHTML();
