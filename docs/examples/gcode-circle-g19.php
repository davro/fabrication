<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(1000);

$gcode->drawCircle(0, 15, 15, 10, "G2", "G19");

echo $gcode;

