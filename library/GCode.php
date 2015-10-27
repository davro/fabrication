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
	 * The spindle speed
	 * 
	 * @var integer 
	 */
	protected $spindleSpeed = 0;
	
	/**
	 * The spindle start position eg Z10
	 *
	 * @var integer
	 */
	protected $spindleAxisStartPosition = 10;
	
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
	 * Draw a circle on a XY plane
	 * With a certain motion clockwise or anticlockwise for conventional or climb milling
	 * 
	 * TODO Plane selection
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
		if ($plane == 'G17') {
			
			$of = $x - $radius;
			
			$this->setCode("(circle)");
			$this->setCode("G0 X{$of} Y{$y}");
			$this->setCode("G1 Z{$z} (axis spindle start point)");
			
			// TODO Tool size  ...
			// TODO Cutting spindle movement ...
			
			$this->setCode("{$plane} {$motion} X{$of} Y{$y} I{$radius} J0.00 Z{$z}");
			$this->setCode("G0 Z{$this->spindleAxisStartPosition} (axis spindle start position)");
			$this->setCode("(/circle)\n");
		}
		
		if ($plane == 'G18') {
			// G18 is disabled
			return;
		}
		
		if ($plane == 'G19') {
			// G19 is disabled
			return;
		}
		
		return $this;
	}
	
	/**
	 * Draw a box on a XY plane
	 * 
	 * @param type $x1
	 * @param type $y1
	 * @param type $z1
	 * @param type $x2
	 * @param type $y2
	 * @param type $z2
	 * @return \GCode
	 */
	function drawBox($x1, $y1, $z1, $x2, $y2, $z2)
	{
			$this->setCode("(box)");
			$this->setCode("G0 X{$x1} Y{$y1} Z{$this->spindleAxisStartPosition} (move to spindle axis start position)");
			$this->setCode("G1 Z{$z1}");
			$this->setCode("G1 X{$x1} Y{$y1} Z{$z1}");
			$this->setCode("G1 Y{$y2}");
			$this->setCode("G1 X{$x2}");
			$this->setCode("G1 Y{$y1}");
			$this->setCode("G1 X{$x1}");
			$this->setCode("G0 Z{$z2}");
			$this->setCode("(/box)\n");
			
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

