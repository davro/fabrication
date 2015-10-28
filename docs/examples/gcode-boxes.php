<?php

require dirname(__FILE__) . '/../../library/GCode.php';

$gcode = new GCode();
$gcode->setUnitType('metric');
$gcode->setFeedRate(300);

$size   = 40;
$margin = 10;

$gcode->drawBox(0, 0, 0, $size, $size, 0)
    ->drawBox(($size*2) + $margin,    0, 0, $size + $margin,       $size, 0)
    ->drawBox(($size*3) + $margin *2, 0, 0, $size *2 + $margin *2, $size, 0)
    ->drawBox(($size*4) + $margin *3, 0, 0, $size *3 + $margin *3, $size, 0)
    ->drawBox(($size*5) + $margin *4, 0, 0, $size *4 + $margin *4, $size, 0);

echo $gcode;
