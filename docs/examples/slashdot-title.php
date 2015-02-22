<?php

require dirname(__FILE__) . '/../../library/FabricationEngine.php';

$engine = new \Fabrication\FabricationEngine();
$engine->run('http://slashdot.org', 'file');

$htmlTitle = $engine->query('/html/head/title');

echo $htmlTitle->item(0)->nodeName . ' = ' . $htmlTitle->item(0)->nodeValue . "\n";
