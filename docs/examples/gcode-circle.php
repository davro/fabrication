<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(300);

$gcode->drawCircle(15, 15, 0, 10);

echo $gcode;

