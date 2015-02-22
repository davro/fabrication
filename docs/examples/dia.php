<?php
namespace Library;

require dirname(__FILE__) . '/../../library/FabricationEngine.php';
require dirname(__FILE__) . '/../../library/PhpClass.php';
require dirname(__FILE__) . '/../../library/UML.php';

try {
	if (PHP_SAPI !== 'cli') {
		throw new Exception('This is command line script');
	} else {

		$engine = new \Fabrication\FabricationEngine();
		$UML    = new \Fabrication\UML($engine);

		// get diagram name from script argument or default.
		$diagramName = isset($argv[1]) ? $argv[1] : 'Application';
		$diagramPath = dirname(__FILE__) . '/' . $diagramName . '.dia';

		// load the diagram
		$engine->run($diagramPath, 'file', 'xml');

		// show layer objects in a php class representation.
		foreach($UML->retriveLayerObjects() as $name => $object) {
			$objectType = $object->attributes->getNamedItem('type')->nodeValue; 

			// UML - Class
			if ($object->nodeName == 'dia:object' && $objectType == "UML - Class") {

				$phpClass = new \Fabrication\PhpClass();
				$phpClass->setName($name);
				$phpClass->setStereotype($UML->getStereoType($object));

				$umlAttributes = $UML->getAttributes($object);
				if (is_object($umlAttributes) && $umlAttributes->length > 0 ) {
					foreach($umlAttributes as $umlAttribute) {
						$phpClass->setProperty(
							[
								'name'       => $UML->retriveAttributeName($umlAttribute),
								'type'       => $UML->retriveAttributeType($umlAttribute),
								'visibility' => $UML->retriveAttributeVisibility($umlAttribute),
							]
						);
					}
				}

				$umlOperations = $UML->getOperations($object);
				if (is_object($umlOperations) && $umlOperations->length > 0 ) {
					foreach($umlOperations as $umlOperation) {
						$phpClass->setFunction(
							[
								'name'       => $UML->retriveOperationName($umlOperation),
								'type'       => $UML->retriveOperationType($umlOperation),
								'visibility' => $UML->retriveOperationVisibility($umlOperation),
							]
						);
					}
				}

				// build autoload path (psr4 ~)
				$pathClassName = __DIR__ . '/' . $diagramName . '/' . $name . '.php';

				// create structure and put contents into a file.
				if (!is_dir($diagramName) && !mkdir($diagramName, 0777, true)) {
					var_dump('Could not create directory ' . $diagramName);
				}
				var_export((string) $phpClass);
				file_put_contents($pathClassName, $phpClass);
			}

			// UML - Dependency (TODO)
		}
	}

} catch(\Exception $ex) {
	
	echo $ex->getMessage();
	die;
}
