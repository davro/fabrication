<?php
namespace Fabrication\Tests;


use Library\Fabrication;
use Library\FabricationEngine;

//require_once(dirname(dirname(__FILE__)).'/library/Fabrication.php');
//require_once(dirname(dirname(__FILE__)).'/library/FabricationEngine.php');

/**
 * Testing W3C
 */
/*
class FabricationEngineValidatorTest extends \PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->engine = new FabricationEngine();
	}

	public function testEngineInstance() {
		$this->assertInternalType('object', $this->engine);
		$this->assertInstanceOf('Library\FabricationEngine', $this->engine);
	}

	public function XtestW3CValidatorLocalhost() {

		$message = Fabrication::W3C('http://localhost/', 'xhtml');

		$this->assertInternalType('object', $message);
		$this->assertEquals('Library\FabricationEngine', get_class($message));

		// test localhost check for local w3c validator.
	}

	public function XtestW3CValidatorBing() {

		$message = Fabrication::W3C('http://bing.com/', 'xhtml');

		$this->assertInternalType('object', $message);
		$this->assertEquals('Library\FabricationEngine', get_class($message));
		$this->assertEquals('env:Envelope', $message->getEngine()->getElementsByTagName('Envelope')->item(0)->nodeName);
		$this->assertEquals('env:Body', $message->getEngine()->getElementsByTagName('Body')->item(0)->nodeName);

		$validity = $message->getEngine()->getElementsByTagName('validity')->item(0);
		$this->assertEquals('m:validity', $validity->nodeName);
		$this->assertEquals('false', $validity->nodeValue);
		//$this->assertEquals('true', $validity->nodeValue);

		//$message->getEngine()->dump($validity);
		//$path = $validity->getNodePath();
		//print $path;

		//$test = $message->getEngine()->query($path);
		//die;
	}
	
	// XML feeds tests for removing the need for magpierss.
	
	public function XXtestXMLFeed() {
		
		$data  = file_get_contents('/tmp/slashdot');
		$this->engine->run($data, 'xml', 'string');
		
		$data = $this->engine->saveXML();
//		$this->assertEquals('', $data);
		
		$result = $this->engine->query('//*');
		$this->assertInternalType('object', $result);
		$this->assertEquals(225, $result->length);

		foreach($result as $key => $value) {
			
			$this->assertEquals('', $value->nodeName);	
		}
		
	}

}
//*/
