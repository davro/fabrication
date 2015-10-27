<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setToolSize(8);
$gcode->setFeedRate(2000);

$gcode->drawBox(10, 10, 0, 50, 50, 0);

echo $gcode;

