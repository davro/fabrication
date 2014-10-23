<?php

require dirname(__FILE__) . '/../../library/FabricationEngine.php';

$engine = new \Library\FabricationEngine();
$engine->run('http://slashdot.org', 'file');

$htmlTitle = $engine->query('/html/head/title');
//$htmlTitle = $engine->getElementsByTagName('title');

print $htmlTitle->item(0)->nodeName . ' = ' . $htmlTitle->item(0)->nodeValue . "\n";

