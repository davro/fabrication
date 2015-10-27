<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(2000);

// draw a box that extends from -0.5 to 0.5 and at Z of 0
$gcode->drawBox(0,0,0.0,0.5,0.5,6);

echo $gcode;

