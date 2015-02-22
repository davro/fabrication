<?php
namespace Fabrication;

require dirname(__FILE__) . '/Fabricator.php';

class PhpClass extends \Fabrication\Fabricator
{
	public $type;
	public $stereotype;
	public $properties;
	public $functions;
	
	public function setStereotype($stereotype)
	{
		$this->stereotype = $stereotype;
	}
	
	public function getProperties()
	{
		return $this->functions;
	}
	
	public function getFunctions()
	{
		return $this->properties;
	}
	
	public function setProperty(array $value)
	{
		$this->properties[] = $value;
	}
	
	public function setFunction(array $value)
	{
		$this->functions[] = $value;
	}
	
	public function getSignature()
	{	
		if (! empty($this->stereotype)) {
			return $this->name . ' extends ' . $this->stereotype;
		} else {
			return $this->name; 
		}
	}
	
	public function __toString()
	{
		$uses='';
		if ($this->stereotype) {

            // TODO check first character in stereotype for a backslash.
            // if exist don't add Application prefix or backslash.
			$uses.="use Application\\" . $this->stereotype . ";\n";
		}
		
		$classSignature  = $this->getSignature();
		$classProperties = $this->getProperties();
		$classFunctions  = $this->getFunctions();
		
		$propertyString='';
		if ($classProperties > 0) {
			foreach($classProperties as $property) {
				$propertyString.= 
					"    /**\n" . 
					"     * " . ucfirst($property['name']) . ".\n" .
					"     * \n" . 
					"     * @var {$property['type']}\n" .
					"     */\n" .
					"    " . $property['visibility'] . ' $' . $property['name'] . ";\n\n";
			}
		}
		
		$functionString='';
		if ($classFunctions > 0) {
			$defaultStrategy = 'return;';
			foreach($classFunctions as $function) {
				$functionString.= 
					"    /**\n" . 
					"     * " . ucfirst($function['name']) . ".\n" .
					"     * \n" . 
					"     */\n" .
					"    " . $function['visibility'] . ' function ' . $function['name'] . "()\n" . 
					"    {\n" .
					"        $defaultStrategy\n" .
					"    }\n" .
					"\n";
			}
		}

		// class template
		$output = <<<EOT
<?php
namespace Application;

$uses
/**
 * The $this->name was generated using the FabricationEngine
 */
class $classSignature 
{
$propertyString
$functionString
}

EOT;

		return $output;
	}
}
