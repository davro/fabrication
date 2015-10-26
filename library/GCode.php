<?php
/**
 * GCode is the common name for the most widely used numerical control (NC) programming language. 
 * It is used mainly in computer-aided manufacturing to control automated machine tools. 
 * GCode is a language in which LinuxCNC is going to tell the stepper motor tentacles how to make something. 
 * The "how" is defined by instructions on where to move, how fast to move, and what path to move.
 * 
 * The first four general codes
 * 
 * G00 Rapid positioning
 * G01 Linear interpolation
 * G02 Circular interpolation, clockwise
 * G03 Circular interpolation, counterclockwise
 * 
 * @license http://www.gnu.org/licenses/lgpl-3.0.en.html
 * @author David Stevens <mail.davro@gmail.com>
 */
class GCode 
{
	/**
	 * GCode string to current output
	 * 
	 * @var string
	 */
	protected $code;
	
	/**
	 * The current Feed Rate
	 * 
	 * @var integer
	 */
	protected $feedRate = 0;
	
	/**
	 * The current Spindle Speed
	 * 
	 * @var type 
	 */
	protected $spindleSpeed = 0;
	
	/**
	 * Class constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Setter for changing the unit type
	 * 
	 * @param string $value
	 */
	public function setUnitType($value)
	{
		if ($value == 'metric') {
			$this->setCode('G21 (Metric mm)');
		} else {
			$this->setCode('G20 (Imperial inch)');
		}
	}

	/**
	 * Setter for changing the feed rate
	 * 
	 * @param type $value
	 */
	public function setFeedRate($value)
	{
		$this->feedRate = $value;
	}

	/**
	 * Getter for retrieving the header GCodes
	 * 
	 * @return string
	 */
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

	/**
	 * Getter for retrieving the footer GCodes
	 * 
	 * @return string
	 */
	public function getFooter()
	{
        	$output = "M2\n";

	        return $output;
	}

	/**
	 * Setter for adding a line to the GCode
	 * 
	 * @param string $code	The GCode to add
	 */
	public function setCode($code)
	{
		$this->code.= $code . "\n";
	}

	/**
	 * Draw a circle on a plane
	 * With a certain motion clockwise or anticlockwise for conventional or climb milling
	 * 
	 * Plane selection
	 * G17	XY Plane
	 * G18	XZ Plane
	 * G19  YZ Plane
	 * 
	 * Motion control
	 * G2	Clockwise Arcs
	 * G3	Counter-Clockwise Arcs
	 * 
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 * @param float $radius
	 * @param string $motion
	 * @param string $plane
	 * @return boolean
	 */
	public function drawCircle($x, $y, $z, $radius, $motion = 'G2', $plane = 'G17')
	{
		$axisSpindleStart = 0;
		$axisSpindleSafe = 10;
				
		if ($plane == 'G17') {
			$axis1       = 'X';
			$axis2       = 'Y';
			$axisSpindle = 'Z';
			$arcFormat1  = 'I';
			$arcFormat2  = 'J';
		}
		if ($plane == 'G18') {
			$axis1       = 'X';
			$axis2       = 'Z';
			$axisSpindle = 'Y';
			$arcFormat1  = 'I';
			$arcFormat2  = 'K';
		}
		if ($plane == 'G19') {
			$axis1       = 'Y';
			$axis2       = 'Z';
			$axisSpindle = 'X';
			$arcFormat1  = 'J';
			$arcFormat2  = 'K';
		}
		
		// find the of point for x, y 
		$ofx = $x - $radius;
		$ofy = $y - $radius;

		$this->setCode("\n(circle x={$x} y={$y} z={$z} radius={$radius} plane={$plane} ofx={$ofx} ofy={$ofy})");
		$this->setCode("G0 X{$ofx} Y{$y} (rapid start)");
		$this->setCode("G1 {$axisSpindle}{$axisSpindleStart} (axis spindle start point)");
		// Cutting spindle movement todo ...
		$this->setCode("{$plane} {$motion} {$axis1}{$ofx} {$axis2}{$y} {$arcFormat1}{$radius} {$arcFormat2}0.00 {$axisSpindle}{$z}");
		$this->setCode("G0 {$axisSpindle}{$axisSpindleSafe} (axis spindle safe point)");
		$this->setCode("(/circle)");
		
		return $this;
	}
	
	/**
	 * Output HeaderCode MainCode and FooterCode in a single string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$output = $this->getHeader();
		$output.= $this->code;
		$output.= $this->getFooter();
		
		return $output;
	}
}

