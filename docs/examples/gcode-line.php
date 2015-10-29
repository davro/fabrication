<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(150);

$gcode->drawLine(0, 0, 0, 0, 60, 0);

echo $gcode;
