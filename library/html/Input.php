<?php
namespace Library\Html;

use Library\Controller;

/**
 * HTML Input
 * 
 * @author	David Stevens <davro@davro.net> 
 */
class Input extends Controller {
	
	
	function __construct($data = array()) {
		$this->setData($data);
	}
	
	
	function compileAttributes() {
		$string = '';
		
		foreach ($this->getData() as $key => $value) {
			$string.= "$key=\"$value\" ";
		}
		
		return $string;
	}
	
	
	function __toString() {
		
		$format = '<input %s/>';
		
		return sprintf($format, $this->compileAttributes());
	}
}

