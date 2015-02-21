<?php
require_once(dirname(__FILE__) . '/../../library/FabricationEngine.php');

$engine = new Fabrication\FabricationEngine();
		
$pattern = <<<EOD
<div>PatternTemplate:
    <div class="uid">UID.</div>
    <div class="title">Title.</div>
    <div class="content">Content.</div>
</div>
EOD;

$dataset = array(
    array('uid' => 1, 'title' => 'Title 1', 'content' => 'Content 1'),
    array('uid' => 2, 'title' => 'Title 2', 'content' => 'Content 2'),
    array('uid' => 3, 'title' => 'Title 3', 'content' => 'Content 3'),
);

echo $engine->saveXML($engine->template($pattern, $dataset, 'class'));

//echo $engine->saveXML($result);

//// Access the result using the dom structure.
//echo $result->childNodes->item(1)->childNodes->item(0)->nodeValue;
//echo $result->childNodes->item(2)->childNodes->item(0)->nodeValue;
//echo $result->childNodes->item(3)->childNodes->item(0)->nodeValue;
//echo $result->childNodes->item(4)->childNodes->item(0)->nodeValue;
//echo $result->childNodes->item(5)->childNodes->item(0)->nodeValue;
//echo $result->childNodes->item(6)->childNodes->item(0)->nodeValue;
//echo $result->childNodes->item(7)->childNodes->item(0)->nodeValue;
//echo $result->childNodes->item(8)->childNodes->item(0)->nodeValue;
//echo $result->childNodes->item(9)->childNodes->item(0)->nodeValue;
//
//
//# Append the result to a fabricationengine and view some xpath results.
//$engine->appendChild($result);
//
//echo $engine->view("//div[@class='uid_1']/text()");
//echo $engine->view("//div[@class='title_1']/text()");
//echo $engine->view("//div[@class='content_1']/text()");
//echo $engine->view("//div[@class='uid_2']/text()");
//echo $engine->view("//div[@class='title_2']/text()");
//echo $engine->view("//div[@class='content_2']/text()");
//echo $engine->view("//div[@class='uid_3']/text()");
//echo $engine->view("//div[@class='title_3']/text()");
//echo $engine->view("//div[@class='content_3']/text()");
