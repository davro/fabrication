<?php
namespace Fabrication\Tests;

use Fabrication\Library\Fabrication;
use Fabrication\Library\FabricationEngine;

require_once(dirname(dirname(__FILE__)).'/lib/Fabrication.php');
//require_once(dirname(dirname(__FILE__)).'/lib/FabricationEngine.php');

/**
 * Testing W3C
 */
class FabricationEngineValidatorTest extends \PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->engine = new FabricationEngine();
	}

	public function testEngineInstance() {
		$this->assertInternalType('object', $this->engine);
		$this->assertInstanceOf('Fabrication\Library\FabricationEngine', $this->engine);
	}

	public function testW3CValidatorLocalhost() {

		// local fixture code change needed.
		//$path = dirname(dirname(__FILE__)).'/tests/fixture/design.html';

		$message = Fabrication::W3C('http://localhost/', 'xhtml');

		$this->assertInternalType('object', $message);
		$this->assertEquals('Fabrication\Library\FabricationEngine', get_class($message));

		// test localhost check for local w3c validator.
	}

	public function XtestW3CValidatorBing() {

		// local fixture code change needed.
		//$path = dirname(dirname(__FILE__)).'/tests/fixture/design.html';

		$message = Fabrication::W3C('http://bing.com/', 'xhtml');

		$this->assertInternalType('object', $message);
		$this->assertEquals('Fabrication\Library\FabricationEngine', get_class($message));

		$this->assertEquals('env:Envelope', $message->getEngine()->getElementsByTagName('Envelope')->item(0)->nodeName);
		$this->assertEquals('env:Body', $message->getEngine()->getElementsByTagName('Body')->item(0)->nodeName);

		$validity = $message->getEngine()->getElementsByTagName('validity')->item(0);

		$this->assertEquals('m:validity', $validity->nodeName);
		$this->assertEquals('false', $validity->nodeValue);

		//$message->getEngine()->dump($validity);
		//$path = $validity->getNodePath();
		//print $path;

		//$test = $message->getEngine()->query($path);
		//die;
	}

}
