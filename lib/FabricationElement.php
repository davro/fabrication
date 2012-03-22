<?php

// Base engine element to extend from used in the specification.
class FabricationElement {

	private $engine;

	private $element;

	/**
	 * Execution method from generating custom elements.
	 *
	 * @param FabricationElement $engine 
	 * @param \DOMElement $element
	 */
	public function execute(FabricationEngine $engine, \DOMElement $element) {

		$this->engine  = $engine;
		$this->element = $element;

		// fabricate your custom element.
		$element->appendChild($engine->create('div', 'ExecuteTest'));
	}
}


