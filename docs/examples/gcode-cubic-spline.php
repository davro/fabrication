<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(150);

$gcode->drawCubicSpline(0, 50, 0, 50, 100, 100);

echo $gcode;
