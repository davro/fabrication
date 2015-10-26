<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(1000);

// row 1
$gcode->drawCircle(50,  50, 0, 30);
$gcode->drawCircle(120, 50, 0, 30);
$gcode->drawCircle(190, 50, 0, 30);
$gcode->drawCircle(260, 50, 0, 30);

// row 2 
$gcode->drawCircle(50,  120, 0, 30);
$gcode->drawCircle(120, 120, 0, 30);
$gcode->drawCircle(190, 120, 0, 30);
$gcode->drawCircle(260, 120, 0, 30);

// output gcode
echo $gcode;

