<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(120);

// row 1
$gcode->drawCircle(50,  50, 0, 30);
$gcode->drawCircle(120, 50, 0, 30);
$gcode->drawCircle(190, 50, 0, 30);
$gcode->drawCircle(260, 50, 0, 30);
$gcode->drawCircle(330, 50, 0, 30);
$gcode->drawCircle(400, 50, 0, 30);
$gcode->drawCircle(470, 50, 0, 30);
$gcode->drawCircle(540, 50, 0, 30);

// row 2 
$gcode->drawCircle(540, 120, 0, 30);
$gcode->drawCircle(470, 120, 0, 30);
$gcode->drawCircle(400, 120, 0, 30);
$gcode->drawCircle(330, 120, 0, 30);
$gcode->drawCircle(260, 120, 0, 30);
$gcode->drawCircle(190, 120, 0, 30);
$gcode->drawCircle(120, 120, 0, 30);
$gcode->drawCircle(50,  120, 0, 30);

// row 3 
$gcode->drawCircle(50,  190, 0, 30);
$gcode->drawCircle(120, 190, 0, 30);
$gcode->drawCircle(190, 190, 0, 30);
$gcode->drawCircle(260, 190, 0, 30);
$gcode->drawCircle(330, 190, 0, 30);
$gcode->drawCircle(400, 190, 0, 30);
$gcode->drawCircle(470, 190, 0, 30);
$gcode->drawCircle(540, 190, 0, 30);

// row 4
$gcode->drawCircle(540, 260, 0, 30);
$gcode->drawCircle(470, 260, 0, 30);
$gcode->drawCircle(400, 260, 0, 30);
$gcode->drawCircle(330, 260, 0, 30);
$gcode->drawCircle(260, 260, 0, 30);
$gcode->drawCircle(190, 260, 0, 30);
$gcode->drawCircle(120, 260, 0, 30);
$gcode->drawCircle(50,  260, 0, 30);

// output gcode
echo $gcode;

