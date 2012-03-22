<?php
namespace Fabrication\Library;

use Fabrication\Library\FabricationEngine;

require_once(dirname(dirname(__FILE__)).'/lib/FabricationEngine.php');

/**
 * Testing 
 */
class FabricationMessage {

	private $engine;
    
	public function __construct(FabricationEngine $engine) {
		$this->engine = $engine;
	}
    
	public function getEngine() {
		return $this->engine;
	}
}
