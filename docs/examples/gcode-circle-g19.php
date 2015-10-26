<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(1000);

$gcode->drawCircle(15, 15, 0, 10, "G2", "G19");

echo $gcode;

