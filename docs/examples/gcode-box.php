<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(300);

$gcode->drawBox(0, 0, 0, 50, 50, 0);

echo $gcode;

