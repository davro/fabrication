<?php

require dirname(__FILE__) . '/../../library/FabricationEngine.php';

$engine = new \Library\FabricationEngine();
$engine->registerNamespace('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
$engine->registerNamespace('xmlns:slash', 'http://purl.org/rss/1.0/modules/slash/');
$engine->run('https://packagist.org/feeds/vendor.components.rss', 'file', 'html');

$channelTitle       = $engine->query('//rss/channel/title');
$channelDescription = $engine->query('//rss/channel/description');
$channelGenerator   = $engine->query('//rss/channel/generator');
$channelItems       = $engine->query('//rss/channel/item');
//$channelItems       = $engine->getElementsByTagName('item');
//print $channelTitle->item(0)->nodeValue . "\n";

$output="{\n";
foreach($channelItems as $item) {
    foreach($item->childNodes as $key => $value) {
        if ($value->nodeName == "guid") {
            $parts = explode(" ", $value->nodeValue);
            $output.="\t\"require\": {\n";
            $output.="\t\t\t\"{$parts[0]}\": \"{$parts[1]}\"\n";
            $output.="\t},\n";
        }
    }
}
$output.="}\n\n";

print $output;

