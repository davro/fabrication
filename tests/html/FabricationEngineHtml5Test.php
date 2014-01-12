<?php
namespace Fabrication\Tests;

use Library\FabricationEngine;

require_once(dirname(dirname(dirname(__FILE__))).'/library/FabricationEngine.php');

class FabricationEngineHtml5Test extends \PHPUnit_Framework_TestCase {

    public function setUp() {

		$this->engine = new FabricationEngine();

		$this->html = 
			'<!DOCTYPE HTML>'.
			'<html>'.
			'<head>'.
			'<title>Hello World!</title>'.
			'</head>'.
			'<body>'.
			'Hello World'.
			'</body>'.
			'</html>';
		
		// test default doctype.
		$this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
		
		// setup doctype for html5 testcases.
		$this->engine->setOption('doctype', 'html.5');
	}
	
	public function testDoctypeHTML5() {
		
		$this->assertEquals('html.5', $this->engine->getOption('doctype'));
		$this->assertEquals('<!DOCTYPE HTML>', $this->engine->getDoctype());
	}
	
	public function testHtml() {

		$this->engine->run($this->html);
		$result = $this->engine->getHtml();

		$this->assertEquals(1, $result->length);

		// NOTE test fails on PHP 5.3.2
		/*
		$this->assertEquals(
			'<html><head><title>Hello World!</title></head><body>Hello World</body></html>',
			$this->engine->view('//html') 
		);
		//*/
	}
	
	public function testHead() {

		$this->engine->run($this->html);
		$result = $this->engine->getHead();

		$this->assertEquals(1, $result->length);

		$this->assertEquals('<head><title>Hello World!</title></head>',
			$this->engine->view('//head') 
		);
	}
	
	public function testTitle() {

//		$this->engine->run($this->html);
//		$result = $this->engine->getTitle();
//
//		$this->assertEquals(1, $result->length);
//
//		$this->assertEquals('<title>Hello World!</title>',
//			$this->engine->view('//title') 
//		);
//
//		$this->assertEquals('Hello World!',
//			$this->engine->view('//title/text()') 
//		);
	}
	
	public function testBase() {}
	public function testLink() {}
	public function testMeta() {}
	public function testStyle() {}
	public function testScript() {}
	public function testNoScript() {}
	public function testBody() {}
	public function testSection() {}
	public function testNav() {}	

	public function XtestArticle() {

		$template = 
			'<!DOCTYPE HTML>'.
			'<html>'.
			'<head>'.
			'<title></title>'.
			'</head>'.
			'<body>'.
			'<article>Hello World 1</article>'.
			'<h2>TEST</h2>'.
			'<article>Hello World 2</article>'.
			'</body>'.
			'</html>';

		$this->engine->run($template);

		$result = $this->engine->getArticle();

		$this->assertEquals(2, $result->length);
		$this->assertEquals('article', $result->item(0)->nodeName);
		$this->assertEquals('Hello World 1', $result->item(0)->nodeValue);
		$this->assertEquals('Hello World 2', $result->item(1)->nodeValue);

		$this->assertEquals('<article>Hello World 1</article>',
			$this->engine->view('//article[position()=1]') 
		);
		$this->assertEquals('<article>Hello World 2</article>',
			$this->engine->view('//article[position()=2]') 
		);

		$this->assertEquals('Hello World 2',
			$this->engine->view('child::body/article[2]/text()') 
		);	
	}

	public function testASide() {
		//
	}
	
	public function testH1() {}
	public function testH2() {}
	public function testH3() {}
	public function testH4() {}
	public function testH5() {}
	public function testH6() {}

	public function testHGroup() {}
	public function testHeader() {}
	public function testFooter() {}
	public function testAddress() {}

	public function testSpecification() {

		$specification = $this->engine->getSpecification();

		if (sizeof($specification) > 0) {

//			foreach($specification as $nodeName => $attributes) {
//
//				$value = 'testing';
//				$attributes = array_flip($attributes);
//				$attributeKeys = array_keys($attributes);
//
//				$element = $this->engine->create($nodeName, $value, $attributes);
//
//				$this->assertEquals($nodeName, $element->nodeName);
//				$this->assertEquals('testing', $element->nodeValue);
//
//				if (sizeof($attributes) > 0) {
//					foreach($attributes as $key => $value) {
//						$this->assertEquals($value, 
//							$element->attributes->getNamedItem($key)->nodeValue
//						);
//					}
//				}
//			}
		}
	}
}