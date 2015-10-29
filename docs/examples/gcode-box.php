<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(150);

$gcode->drawBox(0, 0, 0, 30, 30, 0);

echo $gcode;

