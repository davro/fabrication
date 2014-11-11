<?php
namespace Library;

/**
 * Unified Modeling Language
 * 
 * Design Object Systems with Standard UML
 * 
 */
class UML
{
	/**
	 * Fabrication Engine instance
	 * 
	 * @var \Library\FabricationEngine
	 */
	protected $engine;

	/**
	 * List of avaliable property visibilities
	 * 
	 * @var array
	 */
	static $propertyVisibilityTypes = ['public', 'private', 'protected'];

	/**
	 * 
	 * @param \Library\FabricationEngine $engine
	 */
	public function __construct(\Library\FabricationEngine $engine)
	{	
		$this->engine = $engine;
		$this->engine->registerNamespace('xmlns:dia', 'http://www.lysator.liu.se/~alla/dia/');
	}
	
	/**
	 * Getter for retrieving the stereotype from a DIA object structure.
	 * 
	 * @param \DOMElement $diaObject
	 * @return boolean
	 */
	public function getStereoType(\DOMElement $diaObject)
	{
		$xpath    = $diaObject->getNodePath() . '/dia:attribute[@name="stereotype"]/dia:string';
		$nodeList = $this->engine->query($xpath);
		
		if ($nodeList->length == 1) {
			
			$stereoType = str_replace('#', '', $nodeList->item(0)->nodeValue);
			
			return $stereoType;
		}
		return false;
	}

	/**
	 * Getter for retrieving all attributes from a DIA object structure
	 * 
	 * @param \DOMElement $diaObject
	 * @return boolean
	 */
	public function getAttributes(\DOMElement $diaObject)
	{
		$xpath    = $diaObject->getNodePath() . '/dia:attribute[@name="attributes"]/*';
		$nodeList = $this->engine->query($xpath);
		
		if ($nodeList->length > 0) {
			return $nodeList;
		}
		return false;
	}
	
	public function getOperations(\DOMElement $diaObject)
	{
		$xpath    = $diaObject->getNodePath() . '/dia:attribute[@name="operations"]/*';
		$nodeList = $this->engine->query($xpath);
		
		if ($nodeList->length > 0) {
			return $nodeList;
		}
		return false;
	}
	
	public function getElement(\DOMElement $nodeList, $level = 0, $showPaths = false)
	{	
		$tabs = str_repeat("\t", $level);
		$output = '';
		if (! $showPaths) {
			$output = "";
			foreach($nodeList as $attribute) {
				$output.= $tabs . $attribute->nodeName . " = " . $attribute->nodeValue . "\n";
			}
			
			$output.= $tabs . $nodeList->nodeName . " = " . $nodeMap->nodeValue . "\n";
		} else {
			$output.= $nodeList->getNodePath() . "\n";
		}
		
		foreach($nodeList->childNodes as $childObject) {
			
			if ($childObject instanceof DOMElementq) {
				$output.= $this->getElement($childObject, $level + 1, $showPaths);
			} elseif ($showPaths){
				$output.= $childObject->getNodePath() . "\n";
			}
		}
		
		return $output;
	}
	
	public function getPaths(\DOMElement $nodeList, $level = 0)
	{
		$output = $nodeList->getNodePath();
			
		foreach($nodeList->childNodes as $childObject) {
			
			$output.= $childObject->getNodePath() . "\n";
			
			if ($childObject instanceof DOMElement) {
				$output.= $this->getElement($childObject, $level + 1, true);
			}
		}
		return $output;
	}
	
	public function retriveOperationName(\DOMElement $element)
	{
		return $this->retriveName($element);
	}
	
	public function retriveOperationType(\DOMElement $element)
	{
		return $this->retriveType($element);
	}
	
	public function retriveOperationVisibility(\DOMElement $element)
	{
		return $this->retriveVisibility($element);
	}
	
	public function retriveAttributeName(\DOMElement $element)
	{
		return $this->retriveName($element);
	}
	
	public function retriveAttributeType(\DOMElement $element)
	{	
		$name = $this->engine->query($element->getNodePath() . '/dia:attribute[@name="type"]/dia:string');
		return $this->cleanString($name->item(0)->nodeValue);
	}
	
	public function retriveAttributeVisibility(\DOMElement $element)
	{
		$name = $this->engine->query($element->getNodePath() . '/dia:attribute[@name="visibility"]/dia:enum');
		$propertyVisibility = $name->item(0)->attributes->getNamedItem('val')->nodeValue;
		
		return self::$propertyVisibilityTypes[$propertyVisibility];
	}
	
	public function retriveName(\DOMElement $element)
	{
		$name = $this->engine->query($element->getNodePath() . '/dia:attribute[@name="name"]/dia:string');
		return $this->cleanString($name->item(0)->nodeValue);
	}
	
	public function retriveType(\DOMElement $element)
	{
		$name = $this->engine->query($element->getNodePath() . '/dia:attribute[@name="type"]/dia:string');
		return $this->cleanString($name->item(0)->nodeValue);
	}

	public function retriveVisibility(\DOMElement $element)
	{
		$name = $this->engine->query($element->getNodePath() . '/dia:attribute[@name="visibility"]/dia:enum');
		$propertyVisibility = $name->item(0)->attributes->getNamedItem('val')->nodeValue;
		
		return self::$propertyVisibilityTypes[$propertyVisibility];
	}
	
	public function retriveLayerObjects()
	{	
		$classes = [];
		foreach($this->engine->query('//dia:diagram/dia:layer') as $layer) {
			if ($layer->nodeName == 'dia:layer') {
				foreach($layer->childNodes as $elementObject) {
					if ($elementObject->nodeName == 'dia:object') {
						foreach($elementObject->childNodes as $childNodesAttributes) {
							if ($childNodesAttributes->nodeName == 'dia:attribute' &&
								$childNodesAttributes->attributes->getNamedItem("name")->nodeValue == 'name'
									) {
								$name = str_replace(
									['#'], 
									[''], 
									$childNodesAttributes->childNodes->item(1)->nodeValue
								);
								
								$classes[$name] = $elementObject;									
							}
						}
					}
				}
			}
		}
		
		return $classes;
	}
	
	public function cleanString($value) 
	{
		return str_replace('#', '', $value);
	}
	
}