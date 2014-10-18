<?php

require dirname(__FILE__) . '/../../library/FabricationEngine.php';

$engine = new \Library\FabricationEngine();
echo $engine->getDoctype() . "\n";
$engine->setOption('doctype', 'html.5');
echo $engine->getDoctype() . "\n";

//die;
$template =
    '<html><head><title></title></head><body>'.
    '<h2>Hello 1</h2><article>Hello World 1</article>'.
    '<h2>Hello 2</h2><article>Hello World 2</article>'.
    '</body></html>';

$engine->run($template);

$result = $engine->getArticle();

var_dump($result->item(0)->nodeValue);
var_dump($result->item(1)->nodeValue);

$engine->view('//article[position()=1]');
$engine->view('//article[position()=2]');
