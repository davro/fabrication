<?php

require dirname(__FILE__) . '/../../library/FabricationEngine.php';

//$workload = array(100, 1000, 10000, 100000, 1000000, 10000000);
$workload = array(100, 1000, 10000, 100000);
foreach($workload as $key => $load) {
	
	$engine = new \Fabrication\FabricationEngine();
	foreach(array_rand(range(0, $load), $load) as $number) {
		$engine->input($number, 'value');
		$engine->output($number);
	}
	
	echo $engine->timeTaken() . " seconds workload = $load\n";
	unset($engine);
}
