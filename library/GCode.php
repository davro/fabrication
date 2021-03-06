<?php
/**
 * GCode is the common name for the most widely used numerical control programming language. 
 * It is used mainly in computer-aided manufacturing to control automated machine tools.
 * 
 * The first four general codes apart from thou shall spawn PHP everywhere ;)
 * 
 * Please use library with CARE as this is very experimental!
 * 
 * 
 * G00 Rapid positioning
 * G01 Linear interpolation
 * G02 Circular interpolation, clockwise
 * G03 Circular interpolation, counterclockwise
 * 
 * Add the following to the config file in the [FILTER] section of your machine's config
 * then LinuxCnc can read the string output of the PHP script!
 *
 * PROGRAM_EXTENSION = .php PHP Script
 * php = php
 * 
 * NOTES 
 * http://linuxcnc.org/docs/html/gcode.html
 * http://www.tormach.com/g40_g41_g42.html
 * 
 * http://www.practicalmachinist.com/vb/cnc-machining/newbie-g-code-tool-compensation-help-124118/
 * G41 = Cutter Comp left; that is, the cutter WILL ALWAYS BE CUTTING TO THE LEFT OF THE PART. This is 'climb' milling, and is the preferred method.
 * G42 = Cutter Comp right; that is, the cutter WILL ALWAYS CUT TO THE RIGHT OF THE PART. This is 'conventional' milling, and generally doesn't give as good of a finish, and re-cutting chips is a problem.
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
	 * The Tool Size Diameter
	 * 
	 * @var integer
	 */
	protected $toolSizeDiameter = 0;
	
	/**
	 * The Tool Size Radius
	 * 
	 * @var integer
	 */
	protected $toolSizeRadius = 0;
	
	/**
	 * The Feed Rate
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
	 * GCODE reference
	 * 
	 * http://linuxcnc.org/docs/html/gcode.html
	 *
	 * @var array
	 */
	protected $gcodeReference = [
		
		// Motion 	(X Y Z A B C U V W apply to all motions) 
		'G0'   => ['code' => 'motion', 'description' => 'Rapid Motion', 'parameters' => []],
		'G1'   => ['code' => 'motion', 'description' => 'Coordinated motion ("Straight feed")', 'parameters' => []],
		'G2'   => ['code' => 'motion', 'description' => 'Coordinated helical motion ("Arc feed") CW', 'parameters' => ['I','J','K','R','P']],
		'G3'   => ['code' => 'motion', 'description' => 'Coordinated helical motion ("Arc feed") CCW', 'parameters' => ['I','J','K','R','P']],
		'G4'   => ['code' => 'motion', 'description' => 'Dwell (no motion for P seconds)', 'parameters' => ['P']],
		'G5'   => ['code' => 'motion', 'description' => 'Cubic spline', 'parameters' => ['I','J','P','Q']],
		'G5.1' => ['code' => 'motion', 'description' => 'Quadratic spline', 'parameters' => ['I','J']],
		'G5.2' => ['code' => 'motion', 'description' => 'NURBS, add control point ', 'parameters' => ['P','L']],
		'G5.3' => ['code' => 'motion', 'description' => 'NURBS, execute ', 'parameters' => []],
		
		// Canned cycles 	(X Y Z or U V W apply to canned cycles, depending on active plane) 
		'G81' => ['code' => 'canned-cycles', 'description' => '', 'parameters' => []],
		
		// Distance Mode 
		'G90' => ['code' => 'distance-mode', 'description' => '', 'parameters' => []],
		
		// Feed Rate Mode 
		'G93' => ['code' => 'feed-rate-mode', 'description' => '', 'parameters' => []],
		'G94' => ['code' => 'feed-rate-mode', 'description' => '', 'parameters' => []],
		'G95' => ['code' => 'feed-rate-mode', 'description' => '', 'parameters' => []],
		
	];
	
	
	
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
	
	public function setToolSize($value)
	{
		$this->toolSizeDiameter = $value;
		$this->toolSizeRadius   = $value/2;
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
		
		return;
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
	 * @param float $x1
	 * @param float $y1
	 * @param float $z1
	 * @param float $x2
	 * @param float $y2
	 * @param float $z2
	 * @return \GCode
	 */
	public function drawBox($x1, $y1, $z1, $x2, $y2, $z2)
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
	 * Draw a line on a XY plane
	 * 
	 * @param float $xStart
	 * @param float $yStart
	 * @param float $zStart
	 * @param float $xEnd
	 * @param float $yEnd
	 * @param float $zEnd
	 * @return \GCode
	 */
	public function drawLine($xStart, $yStart, $zStart, $xEnd, $yEnd, $zEnd)
	{
		$this->setCode("(line)");
		$this->setCode("G0 X{$xStart} Y{$yStart} Z{$this->spindleAxisStartPosition}");
		$this->setCode("G1 Z{$zStart}");
		
		// testing tool setup tool metric M8 diameter 4mm
		$this->setCode("T1 M08");
		$this->setCode("G41 D4");
		
		$this->setCode("G1 X{$xStart} Y{$yStart} Z{$zStart}");
		$this->setCode("G1 X{$xEnd} Y{$yEnd} Z{$zEnd}");

		$this->setCode("G0 Z{$this->spindleAxisStartPosition}");
		$this->setCode("G0 X{$xStart} Y{$yStart}");
		$this->setCode("(/line)\n");
		
		return $this;
	}
	
	/**
	 * G5 creates a cubic B-spline in the XY plane with the X and Y axes only. 
	 * P and Q must both be specified for every G5 command.
	 * 
	 * @param float $xStart
	 * @param float $yStart
	 * @param float $xFromEnd
	 * @param float $yFromEnd
	 * @param float $xEnd
	 * @param float $yEnd
	 * @return \GCode
	 */
	public function drawCubicSpline($xStart, $yStart, $xFromEnd, $yFromEnd, $xEnd, $yEnd)
	{
		$this->setCode("(cubic spline)");
		$this->setCode("G0 X{$xStart} Y{$yStart} Z{$this->spindleAxisStartPosition}");
//		$this->setCode("G1 Z{$zStart}");
		
		// G5 Cubic spline
		$this->setCode("G5 I{$xStart} J{$yStart} P{$xFromEnd} Q-{$yFromEnd} X{$xEnd} Y{$yEnd}");
		
		$this->setCode("G0 Z{$this->spindleAxisStartPosition}");
		$this->setCode("G0 X{$xStart} Y{$yStart}");
		$this->setCode("(/cubic spline)");
		
		return $this;
	}
}