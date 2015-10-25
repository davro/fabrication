<?php

class GCode 
{

	protected $code;
	protected $feedRate = 0;
	protected $spindleSpeed = 0;

	public function __construct()
	{
	}

	public function setUnitType($value)
	{
		if ($value == 'metric') {
			$this->setCode('G21 (Metric mm)');
		} else {
			$this->setCode('G20 (Imperial inch)');
		}
	}

	public function setFeedRate($value)
	{
		$this->feedRate = $value;
	}

	public function getHeader()
	{
		$output = "(GCode fabricated on " . date('r') . " http://davro.net)\n";
		$output.= "G90 (absolute mode)\n";

		// Feed Rate Depending on the setting of the Feed Mode toggle the rate may be in 
		// units-per-minute or units-per-rev of the spindle. The units are those defined by the G20/G21 mode. 
		$output.= "F{$this->feedRate}  (Feed Rate)\n";
		$output.= "S{$this->spindleSpeed}  (Spindle Speed)\n";

		return $output;
	}

	public function getFooter()
	{
        	$output = "M2\n";

	        return $output;
	}

	public function setCode($code)
	{
		$this->code.= $code . "\n";
	}

	public function drawCircle($x, $y, $z, $radius, $motion = 'G2', $plane = 'G17')
	{
		// TODO extend to allow for plane selections
		// G17	XY Plane
		// G18	XZ Plane
		// G19  YZ Plane

		// Motion
		// G2	Clockwise Arcs
		// G3	Counter-Clockwise Arcs

		$ofx = $x - $radius;
		$ofy = $y - $radius;

		$this->setCode("\n(circle x={$x} y={$y} z={$z} radius={$radius} plane={$plane} ofx={$ofx} ofy={$ofy})");
		$this->setCode("G0 X{$ofx} Y{$y} (rapid start)");
		$this->setCode("G1 Z{$z}");
		$this->setCode("{$plane} {$motion} X{$ofx} Y{$y} I{$radius} J0.00 Z{$z} (Plane)");
		$this->setCode("G0 Z10 (move z)");
		$this->setCode("(/circle)");

		return true;
	}

	public function __toString()
	{
		$output = $this->getHeader();
		$output.= $this->code;
		$output.= $this->getFooter();

		return $output;
	}
}

