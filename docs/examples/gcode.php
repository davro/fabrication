<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(150);

echo $gcode;
