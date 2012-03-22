<?php
namespace Fabrication\Library\Pattern;

class Xml {

	public $pattern = array(
		//'head' => array(), 
		//'body' => array()
	);

	public $specification = array();

	public $doctypes = array(
		'xml.1'	=> '<xml doctype testing>',
	);

	public function __construct($engine = array()) {

		$this->engine = $engine;
	}

	public function __toString() {

		$xml = $this->engine->output('xml');
		if ($xml) {
			$this->pattern = $xml;
		}

		$result = $this->engine->specification(
			$this->pattern, 'xml', array()
		)->saveFabric();

		return $result;
	}
}
