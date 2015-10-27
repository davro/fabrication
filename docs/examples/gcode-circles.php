<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(300);

$gcode->drawCircle(50,  50, 0, 30)
	->drawCircle(120, 50, 0, 30)
	->drawCircle(190, 50, 0, 30)
	->drawCircle(260, 50, 0, 30);

$gcode->drawCircle(50,  120, 0, 30)
	->drawCircle(120, 120, 0, 30)
	->drawCircle(190, 120, 0, 30)
	->drawCircle(260, 120, 0, 30);

echo $gcode;