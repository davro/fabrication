<?php
namespace Fabrication\Tests;

use Library\FabricationEngine;

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/library/FabricationEngine.php');

class FabricationEngineHtml4Test extends \PHPUnit_Framework_TestCase {

    public function setUp() {

        $this->header = 'HEADER';
        $this->content = 'CONTENT';
        $this->footer = 'FOOTER';

        $this->engine = new FabricationEngine();
        
        // BUG DOMDocument loadHTML stripping self closing tags, but not with loadHTMLFile.
        $this->design =
            '<!DOCTYPE HTML>'.
            '<html>'.
            '<head>'.
            '<title></title>'.
            '</head>'.
            '<body>'.
            '<div id="header"><img src="test1.png" /></div>'.
            '<div id="content"></div>'.
            '<div id="footer">'.
            '<a href=""><img class="image" src="test2.png" alt="" /></a>'.
            '<a href=""><img src="test3.png" /></a>'.
            '<img src="test4.png" />'.
            '<img src="test5.png"><img src=""><br><hr>'. // invalid line.
            '</div>'.
            '</body>'.
            '</html>';

        $this->expected =
            '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"'.
			"\n".
			'   "http://www.w3.org/TR/html4/loose.dtd">'.
			"\n".
            '<html>'.
            '<head>'.
            '<title></title>'.
            '</head>'.
            '<body>'.
            '<div id="header"><img src="test1.png" /></div>'.
            '<div id="content"></div>'.
            '<div id="footer">'.
            '<a href=""><img class="image" src="test2.png" alt="" /></a>'.
            '<a href=""><img src="test3.png" /></a>'.
            '<img src="test4.png" />'.
            '<img src="test5.png" /><img src="" /><br /><hr />'. // process fixed line.
            '</div>'.
            '</body>'.
            "</html>\n";

        $this->assertInternalType('object', $this->engine);
        $this->assertInstanceOf('Library\FabricationEngine', $this->engine);
    }

	
	public function testDoctypeHTML4Strict() {
		
		// test default doctype.
		$this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
		
		$this->engine->setOption('doctype', 'html.4.01.strict');
		$this->assertEquals('html.4.01.strict', $this->engine->getOption('doctype'));
		$this->assertEquals('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"'."\n".'   "http://www.w3.org/TR/html4/strict.dtd">'
			, $this->engine->getDoctype()
		);
	}

	
	public function testDoctypeHTML4Transitional() {
		
		// test default doctype.
		$this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
		
		$this->engine->setOption('doctype', 'html.4.01.transitional');
		$this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
		$this->assertEquals('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"'."\n".'   "http://www.w3.org/TR/html4/loose.dtd">'
			, $this->engine->getDoctype()
		);
	}

	
	public function testDoctypeHTML4Frameset() {
		
		// test default doctype.
		$this->assertEquals('html.4.01.transitional', $this->engine->getOption('doctype'));
		
		$this->engine->setOption('doctype', 'html.4.01.frameset');
		$this->assertEquals('html.4.01.frameset', $this->engine->getOption('doctype'));
		$this->assertEquals('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"'."\n".'   "http://www.w3.org/TR/html4/frameset.dtd">'
			, $this->engine->getDoctype()
		);
	}


    public function testSanityLoadHtmlString() {

        $this->assertEquals(true, $this->engine->getOption('process'));
        //$this->assertEquals(true, $this->engine->setOption('process', false));
        //$this->assertEquals(false, $this->engine->getOption('process'));
		$this->assertTrue($this->engine->run($this->design));

        $NodeList=$this->engine->getHtml();
        $this->assertInstanceOf('DOMNodeList', $NodeList);
        $this->assertEquals(1, $NodeList->length);
        $this->assertEquals('html', $NodeList->item(0)->tagName);

        //$this->assertEquals($this->expected, $this->engine->outputHTML());
    }


	public function testSanityLoadHtmlFile() {

		//$this->assertFalse($this->engine->run('missing.html', 'file'));
		
		$designPath = dirname(dirname(dirname(__FILE__))).'/fixture/design.html';
		$this->assertTrue($this->engine->run($designPath, 'html', 'file'));
		
		$nodeList=$this->engine->getHtml();
		$this->assertInstanceOf('DOMNodeList', $nodeList);
		$this->assertEquals(1, $nodeList->length);
		$this->assertEquals('html', $nodeList->item(0)->tagName);
	}


	public function XtestMessingWithSearchEngines() {

		//$webpage = file_get_contents('http://www.bing.com/');
		//$webpage = file_get_contents('http://www.google.com/');
		$webpage = file_get_contents('http://www.duckduckgo.com/');

		$this->assertTrue($this->engine->run($webpage, 'html', 'string'));
		
		$NodeList=$this->engine->getHtml();
		$this->assertInstanceOf('DOMNodeList', $NodeList);
		$this->assertEquals(1, $NodeList->length);
		$this->assertEquals('html', $NodeList->item(0)->tagName);
	}


    public function testSanityCreateProcessingInstruction() {
		
        $this->assertTrue($this->engine->run('<html><head><body></html>', 'html', 'string'));

        $this->engine->getElementsByTagName('body')->item(0)->appendChild(
            $this->engine->createProcessingInstruction('php', 'echo PHP_VERSION; ?')
        );

		$this->assertEquals('<body><?php echo PHP_VERSION; ?></body>',
			$this->engine->view('//body') 
		);
    }
	

    // DOM and output xml stop adding messed up doctype and fudged xml declaration.
    public function XtestSanityOutputXml() {

        $html='<?xml version="1.0" standalone="yes"?>'.
            "\n".
            '<html><head><title>TEST</title></head><body>TEST</body></html>';
        
	$this->assertEquals('string', $this->engine->run($html));
	//$this->assertEquals('string', $this->engine->run($this->design));
        $this->assertEquals($html, $NodeList=$this->engine->outputXml());
    }


    // HTML
    public function testHtml() {

	$this->engine->run($this->design);
        $NodeList=$this->engine->getHtml();

//        $this->engine->dump($this->engine); exit;
        
        $this->assertInstanceOf('DOMNodeList', $NodeList);
        $this->assertEquals(1, $NodeList->length);
        $this->assertEquals('html', $NodeList->item(0)->tagName);
        $this->assertEquals(2, $NodeList->item(0)->childNodes->length);
    }


    // HTML HEAD
    public function testHtmlHead() {

        $this->engine->run($this->design);
        $NodeList=$this->engine->getHtml('/head');

        $this->assertInstanceOf('DOMNodeList', $NodeList);
        $this->assertEquals(1, $NodeList->length);
        $this->assertEquals('head', $NodeList->item(0)->tagName);
        $this->assertEquals('head', $NodeList->item(0)->nodeName);
        //$this->assertEquals(1, $NodeList->item(0)->childNodes->length); // makes test a little brittle.
    }


    public function testHtmlHeadTitle() {
        
	$this->engine->run($this->design);
        $NodeList=$this->engine->getHtml('/head/title');

        $this->assertInstanceOf('DOMNodeList', $NodeList);
        $this->assertEquals(1, $NodeList->length);
        $this->assertEquals('title', $NodeList->item(0)->tagName);
    }


    public function testHtmlHeadTitleSetValue() {
       
	$this->engine->run($this->design);

        $value='Fabrication';
        
        $this->engine->setHtml('/head/title', $value);
        
    }


    public function testHtmlHeadTitleGetValue() {    

        $this->assertTrue($this->engine->run($this->design));

        $value = 'Fabrication';
        $this->engine->setHtml('/head/title', $value);
        
        $title=$this->engine->getHtml('/head/title');
        
        $this->assertInternalType('object', $title);
        $this->assertInstanceOf('DOMNodeList', $title);
        $this->assertEquals(1, $title->length);
        
        $this->assertEquals('title', $title->item(0)->nodeName);
        $this->assertEquals($value, $title->item(0)->nodeValue);
        $this->assertEquals($value, $this->engine->query('/html/head/title')->item(0)->nodeValue);
    }


    public function testHtmlHeadMeta() {
        $html='
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    
    <body></body>
</html>
';
        
        $this->assertTrue($this->engine->run($html));
        $meta=$this->engine->getHtml('/head/meta');
        
        $this->assertInternalType('object', $meta);
        $this->assertInstanceOf('DOMNodeList', $meta);
        $this->assertEquals(2, $meta->length);

        $this->assertEquals('meta', $meta->item(0)->nodeName);
        $this->assertEquals('Author', $meta->item(0)->getAttribute('name'));
        $this->assertEquals('David Stevens', $meta->item(0)->getAttribute('content'));
        
        $this->assertEquals('meta', $meta->item(1)->nodeName);
        $this->assertEquals('keywords', $meta->item(1)->getAttribute('name'));
        $this->assertEquals('en', $meta->item(1)->getAttribute('lang'));
        $this->assertEquals('fabrication, template, engine, fabric', $meta->item(1)->getAttribute('content'));
        
        // direct query check.
        $this->assertEquals('David Stevens', $this->engine->query('/html/head/meta')->item(0)->getAttribute('content'));
    }


    public function testHtmlHeadLink() {
        $html='
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
        
        <link rel="stylesheet" type="text/css" href="theme/css/default.css" />
        <link rel="help" href="http://www.example.com/help/" title="Example Help" />
        <link rel="copyright" href="http://www.example.com/copyright/" title="Copyright" />
    </head>
    
    <body></body>
</html>
';
        
        $this->assertTrue($this->engine->run($html));
        $link=$this->engine->getHtml('/head/link');
        
        $this->assertInternalType('object', $link);
        $this->assertInstanceOf('DOMNodeList', $link);
        $this->assertEquals(3, $link->length);

        $this->assertEquals('link', $link->item(0)->nodeName);
        $this->assertEquals('stylesheet', $link->item(0)->getAttribute('rel'));
        $this->assertEquals('text/css', $link->item(0)->getAttribute('type'));
        $this->assertEquals('theme/css/default.css', $link->item(0)->getAttribute('href'));
        
        $this->assertEquals('link', $link->item(1)->nodeName);
        $this->assertEquals('help', $link->item(1)->getAttribute('rel'));
        $this->assertEquals('Example Help', $link->item(1)->getAttribute('title'));
        $this->assertEquals('http://www.example.com/help/', $link->item(1)->getAttribute('href'));
        
        $this->assertEquals('link', $link->item(2)->nodeName);
        $this->assertEquals('copyright', $link->item(2)->getAttribute('rel'));
        $this->assertEquals('Copyright', $link->item(2)->getAttribute('title'));
        $this->assertEquals('http://www.example.com/copyright/', $link->item(2)->getAttribute('href'));
        
        // DEBUG
        //$this->engine->dump($this->engine); exit; 
    }


    public function testHtmlHeadStyle() {
        $html='
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
        
        <link rel="stylesheet" type="text/css" href="theme/css/default.css" />
        <link rel="help" href="http://www.example.com/help/" title="Example Help" />
        <link rel="copyright" href="http://www.example.com/copyright/" title="Copyright" />
        
        <style type="text/css" media="all">
            @import url("http://www.example.com/theme/css/default.css");
            @import url("http://www.example.com/theme/css/custom.css");
        </style>

        <!--[if IE]><![if gte IE 6]><![endif]-->
        <style type="text/css" media="print">
            @import url("http://www.example.com/theme/css/ie.css");
        </style>
    </head>
    
    <body></body>
</html>
';
        
        $this->assertTrue($this->engine->run($html));
        $style=$this->engine->getHtml('/head/style');
        
        $this->assertInternalType('object', $style);
        $this->assertInstanceOf('DOMNodeList', $style);
        $this->assertEquals(2, $style->length);

        $this->assertEquals('style', $style->item(0)->nodeName);
        $this->assertEquals('text/css', $style->item(0)->getAttribute('type'));
        $this->assertEquals('all', $style->item(0)->getAttribute('media'));
        $this->assertEquals('
            @import url("http://www.example.com/theme/css/default.css");
            @import url("http://www.example.com/theme/css/custom.css");
        ', $style->item(0)->nodeValue);
        
        $this->assertEquals('style', $style->item(1)->nodeName);
        $this->assertEquals('text/css', $style->item(1)->getAttribute('type'));
        $this->assertEquals('print', $style->item(1)->getAttribute('media'));
        $this->assertEquals('
            @import url("http://www.example.com/theme/css/ie.css");
        ', $style->item(1)->nodeValue);        
    }


    public function testHtmlHeadScript() {
        
        $html='
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
        
        <link rel="stylesheet" type="text/css" href="theme/css/default.css" />
        <link rel="help" href="http://www.example.com/help/" title="Example Help" />
        <link rel="copyright" href="http://www.example.com/copyright/" title="Copyright" />
        
        <style type="text/css" media="all">
            @import url("http://www.example.com/theme/css/default.css");
            @import url("http://www.example.com/theme/css/custom.css");
        </style>

        <!--[if IE]><![if gte IE 6]><![endif]-->
        <style type="text/css" media="print">
            @import url("http://www.example.com/theme/css/ie.css");
        </style>
        
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            var toggleImage = function(elem) {
                if ($(elem).hasClass("shown")) {
                    $(elem).removeClass("shown").addClass("hidden");
                    $("img", elem).attr("src", "/images/notes-add.gif");
                }
                else {
                    $(elem).removeClass("hidden").addClass("shown");
                    $("img", elem).attr("src", "/images/notes-reject.gif");
                }
            };
        });
        </script>
    </head>
    
    <body></body>
</html>

';

        
        $this->assertTrue($this->engine->run($html));
        $script=$this->engine->getHtml('/head/script');
        
        $this->assertInternalType('object', $script);
        $this->assertInstanceOf('DOMNodeList', $script);
        $this->assertEquals(3, $script->length);
        
        $this->assertEquals('script', $script->item(0)->nodeName);
        $this->assertEquals('text/javascript', $script->item(0)->getAttribute('type'));
        $this->assertEquals('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js', $script->item(0)->getAttribute('src'));
        $this->assertEquals('', $script->item(0)->nodeValue);
        
        $this->assertEquals('script', $script->item(1)->nodeName);
        $this->assertEquals('text/javascript', $script->item(1)->getAttribute('type'));
        $this->assertEquals('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js', $script->item(1)->getAttribute('src'));
        $this->assertEquals('', $script->item(1)->nodeValue);
        
        $this->assertEquals('script', $script->item(2)->nodeName);
        $this->assertEquals('text/javascript', $script->item(2)->getAttribute('type'));
        $this->assertEquals('
        $(document).ready(function() {
            var toggleImage = function(elem) {
                if ($(elem).hasClass("shown")) {
                    $(elem).removeClass("shown").addClass("hidden");
                    $("img", elem).attr("src", "/images/notes-add.gif");
                }
                else {
                    $(elem).removeClass("hidden").addClass("shown");
                    $("img", elem).attr("src", "/images/notes-reject.gif");
                }
            };
        });
        ', $script->item(2)->nodeValue);
        
    }


    public function testNoScript() {

		// <noscript>Your browser does not support JavaScript!</noscript>
		
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
		<BASE href="http://www.aviary.com/products/intro.html">
		
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    
    <body>
	<noscript>Your browser does not support JavaScript!</noscript>
	</body>
</html>
FIXTURE;

        $this->assertTrue($this->engine->run($html));
        
        //$result=$this->engine->getHtml('/head/noscript'); // not working strange.
        //$result=$this->engine->query('//noscript'); // works
	
        $result=$this->engine->getNoScript();
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        $this->assertEquals(1, $result->length);
        
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('noscript', $element->nodeName);
            $this->assertEquals('Your browser does not support JavaScript!', $element->nodeValue);
        }
    }


    public function testBase() {
		
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
		<BASE href="http://www.aviary.com/products/intro.html">
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    
    <body></body>
</html>
FIXTURE;

        $this->assertTrue($this->engine->run($html));
        //$result=$this->engine->getHtml('/head/base'); // not working strange.
        //$result=$this->engine->query('//base');
        $result=$this->engine->getBase();
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        $this->assertEquals(1, $result->length);
        
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('base', $element->nodeName);
            $this->assertEquals('', $element->nodeValue);
        }
        
    }
    
    // HTML BODY
    public function testHtmlBody() {

        $this->engine->run($this->design);
	
        //$NodeList = $this->engine->getHtml('/body');
        $NodeList = $this->engine->getBody();
        
        $this->assertInstanceOf('DOMNodeList', $NodeList);
        $this->assertEquals(1, $NodeList->length);
        $this->assertEquals('body', $NodeList->item(0)->tagName);
        $this->assertEquals('body', $NodeList->item(0)->nodeName);
        $this->assertEquals('', $NodeList->item(0)->nodeValue); # interesting counts spaces and tabs in body 
        $this->assertEquals(3, $NodeList->item(0)->childNodes->length);
        $this->assertEquals('div', $NodeList->item(0)->childNodes->item(0)->tagName);
        $this->assertEquals('div', $NodeList->item(0)->childNodes->item(1)->tagName);
        $this->assertEquals('div', $NodeList->item(0)->childNodes->item(2)->tagName);
        //$this->assertEquals('header', $NodeList->item(0)->childNodes->item(0)->attributes);
        $this->assertEquals('header', $NodeList->item(0)->childNodes->item(0)->attributes->getNamedItem('id')->nodeValue);
        $this->assertEquals('content', $NodeList->item(0)->childNodes->item(1)->attributes->getNamedItem('id')->nodeValue);
        $this->assertEquals('footer', $NodeList->item(0)->childNodes->item(2)->attributes->getNamedItem('id')->nodeValue);
    }

    // HTML Body 
    
    // Block-level and inline elements
    // 7.5.4 Grouping elements: the DIV and SPAN elements
    public function testDiv() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    
    <body>
    <div id="header" class="site"><h1>Welcome to the Fabrication Engine</h1></div>
    <div id="content" class="site">
        <div><h2>Fabrication Engine 2</h2></div>
        <div><h2>Fabrication Engine 2.1</h2> testing</div>
        <div><h3>Fabrication Engine 3</h3></div>
        <div><h3>Fabrication Engine 3.1</h3></div>
        <div><h3>Fabrication Engine 3.2</h3></div>
        <div><h4>Fabrication Engine 4</h4></div>
        <div><h5>Fabrication Engine 5</h5></div>
        <div><h6>Fabrication Engine 6</h6></div>
        <div class="classy"></div>
        <div id=" nasty block "></div>
    </div>
    <div id="footer" class="site"></div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        // getter for all div's.
        $divs=$this->engine->getDiv();
        
        $this->assertInternalType('object', $divs);
        $this->assertInstanceOf('DOMNodeList', $divs);
        $this->assertEquals(13, $divs->length);
        $this->assertEquals('div', $divs->item(0)->nodeName);

        // getter for div's with a id attribute (default).
        $divs=$this->engine->getDiv('[@*]');
        $this->assertInternalType('object', $divs);
        $this->assertInstanceOf('DOMNodeList', $divs);
        $this->assertEquals(5, $divs->length); // includes tag with spaces.
        $this->assertEquals('div', $divs->item(0)->nodeName);

        // getter for div's with a class attribute.
        $divs=$this->engine->getDiv('[@class]');
        $this->assertInternalType('object', $divs);
        $this->assertInstanceOf('DOMNodeList', $divs);
        $this->assertEquals(4, $divs->length);

        $this->assertEquals('div', $divs->item(0)->nodeName);
        $this->assertTrue($divs->item(0)->hasChildNodes());
        $this->assertEquals('h1', $divs->item(0)->childNodes->item(0)->nodeName);
        $this->assertEquals('Welcome to the Fabrication Engine', $divs->item(0)->childNodes->item(0)->nodeValue);

        $this->assertEquals('div', $divs->item(1)->nodeName);
        $this->assertTrue($divs->item(1)->hasChildNodes());
        $this->assertEquals('content', $divs->item(1)->attributes->getNamedItem('id')->nodeValue);
        $this->assertEquals('site', $divs->item(1)->attributes->getNamedItem('class')->nodeValue);
        $this->assertEquals("\n        ",               $divs->item(1)->childNodes->item(0)->nodeValue);  // newline, whitespace.
        $this->assertEquals('Fabrication Engine 2',$divs->item(1)->childNodes->item(1)->nodeValue);
        $this->assertEquals("\n        ", $divs->item(1)->childNodes->item(2)->nodeValue);  // newline, whitespace.
        $this->assertEquals('div', $divs->item(1)->childNodes->item(3)->nodeName);
        $this->assertEquals('h2', $divs->item(1)->childNodes->item(3)->childNodes->item(0)->nodeName);
        $this->assertEquals('Fabrication Engine 2.1', $divs->item(1)->childNodes->item(3)->childNodes->item(0)->nodeValue);
        $this->assertEquals(' testing', $divs->item(1)->childNodes->item(3)->childNodes->item(1)->nodeValue);
        $this->assertEquals(12, $divs->item(1)->childNodes->item(3)->getLineNo());
        
        $this->assertEquals('div', $divs->item(2)->nodeName);
        $this->assertFalse($divs->item(2)->hasChildNodes());
        $this->assertEquals('classy', $divs->item(2)->attributes->getNamedItem('class')->nodeValue);

        $this->assertEquals('div', $divs->item(3)->nodeName);
        $this->assertFalse($divs->item(3)->hasChildNodes());
        $this->assertEquals('footer', $divs->item(3)->attributes->getNamedItem('id')->nodeValue);
        $this->assertEquals('site', $divs->item(3)->attributes->getNamedItem('class')->nodeValue);
    }

    public function testSpan() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    
    <body>
    <div id="header" class="site"><h1>Welcome to the Fabrication Engine</h1><span>Testing1</span></div>
    <div id="content" class="site">
        <div><h2>Fabrication Engine 2</h2><span>Testing2</span></div>
        <div><h2>Fabrication Engine 2.1</h2></div>
        <div><h3>Fabrication Engine 3</h3><span>Testing3</span></div>
        <div><h3>Fabrication Engine 3.1</h3></div>
        <div><h3>Fabrication Engine 3.2</h3></div>
        <div><h4>Fabrication Engine 4</h4></div>
        <div><h5>Fabrication Engine 5</h5></div>
        <div><h6>Fabrication Engine 6</h6></div>
        <div class="classy"></div>
        <div id=" nasty block should not show up "></div>
    </div>
    <div id="footer" class="site"></div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $divs=$this->engine->getSpan();
        
        $this->assertInternalType('object', $divs);
        $this->assertInstanceOf('DOMNodeList', $divs);
        $this->assertEquals(3, $divs->length);
        
        $this->assertEquals('span', $divs->item(0)->nodeName);
        $this->assertTrue($divs->item(0)->hasChildNodes());
        $this->assertEquals('Testing1', $divs->item(0)->nodeValue);
        
        $this->assertEquals('span', $divs->item(1)->nodeName);
        $this->assertTrue($divs->item(1)->hasChildNodes());
        $this->assertEquals('Testing2', $divs->item(1)->nodeValue);
        
        $this->assertEquals('span', $divs->item(2)->nodeName);
        $this->assertTrue($divs->item(2)->hasChildNodes());
        $this->assertEquals('Testing3', $divs->item(2)->nodeValue);
        
    }


    //Headings: The H1, H2, H3, H4, H5, H6 elements
    //The ADDRESS element
        
    public function testHeadingH1H2H3H4H5H6() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    
    <body>
    <div><h1>Welcome to the Fabrication Engine</h1></div>
    <h2>Fabrication Engine 2</h2>
    <h2>Fabrication Engine 2.1</h2>
    <h3>Fabrication Engine 3</h3>
    <h3>Fabrication Engine 3.1</h3>
    <h3>Fabrication Engine 3.2</h3>
    <h4>Fabrication Engine 4</h4>
    <h5>Fabrication Engine 5</h5>
    <h6>Fabrication Engine 6</h6>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $heading=$this->engine->getHeadings();
        
        $this->assertInternalType('object', $heading);
        $this->assertInstanceOf('DOMNodeList', $heading);
        $this->assertEquals(9, $heading->length);
        $this->assertEquals('h1', $heading->item(0)->nodeName);
        $this->assertEquals('Welcome to the Fabrication Engine', $heading->item(0)->nodeValue);
	$this->assertEquals('Fabrication Engine 2', $heading->item(1)->nodeValue);
	$this->assertEquals('Fabrication Engine 2.1', $heading->item(2)->nodeValue);
	$this->assertEquals('Fabrication Engine 3', $heading->item(3)->nodeValue);
	$this->assertEquals('Fabrication Engine 3.1', $heading->item(4)->nodeValue);
	$this->assertEquals('Fabrication Engine 3.2', $heading->item(5)->nodeValue);
	$this->assertEquals('Fabrication Engine 4', $heading->item(6)->nodeValue);
	$this->assertEquals('Fabrication Engine 5', $heading->item(7)->nodeValue);
	$this->assertEquals('Fabrication Engine 6', $heading->item(8)->nodeValue);
        
        $heading=$this->engine->getH1();
        $this->assertInternalType('object', $heading);
        $this->assertInstanceOf('DOMNodeList', $heading);
        $this->assertEquals(1, $heading->length);
        $this->assertEquals('h1', $heading->item(0)->nodeName);
        $this->assertEquals('Welcome to the Fabrication Engine', $heading->item(0)->nodeValue);

        $heading=$this->engine->getH2();
        $this->assertInternalType('object', $heading);
        $this->assertInstanceOf('DOMNodeList', $heading);
        $this->assertEquals(2, $heading->length);
        $this->assertEquals('h2', $heading->item(0)->nodeName);
        $this->assertEquals('Fabrication Engine 2', $heading->item(0)->nodeValue);
        $this->assertEquals('h2', $heading->item(1)->nodeName);
        $this->assertEquals('Fabrication Engine 2.1', $heading->item(1)->nodeValue);
        
        $heading=$this->engine->getH3();
        $this->assertInternalType('object', $heading);
        $this->assertInstanceOf('DOMNodeList', $heading);
        $this->assertEquals(3, $heading->length);
        $this->assertEquals('h3', $heading->item(0)->nodeName);
        $this->assertEquals('Fabrication Engine 3', $heading->item(0)->nodeValue);
        $this->assertEquals('h3', $heading->item(1)->nodeName);
        $this->assertEquals('Fabrication Engine 3.1', $heading->item(1)->nodeValue);
        $this->assertEquals('h3', $heading->item(2)->nodeName);
        $this->assertEquals('Fabrication Engine 3.2', $heading->item(2)->nodeValue);
        
        $heading=$this->engine->getH4();
        $this->assertInternalType('object', $heading);
        $this->assertInstanceOf('DOMNodeList', $heading);
        $this->assertEquals(1, $heading->length);
        $this->assertEquals('h4', $heading->item(0)->nodeName);
        $this->assertEquals('Fabrication Engine 4', $heading->item(0)->nodeValue);
        
        $heading=$this->engine->getH5();
        $this->assertInternalType('object', $heading);
        $this->assertInstanceOf('DOMNodeList', $heading);
        $this->assertEquals(1, $heading->length);
        $this->assertEquals('h5', $heading->item(0)->nodeName);
        $this->assertEquals('Fabrication Engine 5', $heading->item(0)->nodeValue);
        
        $heading=$this->engine->getH6();
        $this->assertInternalType('object', $heading);
        $this->assertInstanceOf('DOMNodeList', $heading);
        $this->assertEquals(1, $heading->length);
        $this->assertEquals('h6', $heading->item(0)->nodeName);
        $this->assertEquals('Fabrication Engine 6', $heading->item(0)->nodeValue);
    }

     public function testAddress() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    
    <body>

    <ADDRESS>
    <A href="../People/Raggett/">Dave Raggett</A>, 
    <A href="../People/Arnaud/">Arnaud Le Hors</A>, 
    contact persons for the <A href="Activity">W3C HTML Activity</A><BR>    
    </ADDRESS>

    <address><A href="/address">David Stevens</a></ADDRESS>

    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $heading=$this->engine->getAddress();
        $this->assertInternalType('object', $heading);
        $this->assertInstanceOf('DOMNodeList', $heading);
        $this->assertEquals(2, $heading->length);

        // first element test.
        $this->assertEquals('address', $heading->item(0)->nodeName);
        $this->assertEquals('
    Dave Raggett, 
    Arnaud Le Hors, 
    contact persons for the W3C HTML Activity', $heading->item(0)->nodeValue);
        $this->assertEquals('#text', $heading->item(0)->childNodes->item(0)->nodeName);
        $this->assertEquals("\n    ", $heading->item(0)->childNodes->item(0)->nodeValue);
        $this->assertEquals('a', $heading->item(0)->childNodes->item(1)->nodeName);
        $this->assertEquals('Dave Raggett', $heading->item(0)->childNodes->item(1)->nodeValue);
        $this->assertEquals(', 
    ', $heading->item(0)->childNodes->item(2)->nodeValue);
        $this->assertEquals('a', $heading->item(0)->childNodes->item(3)->nodeName);
        $this->assertEquals('Arnaud Le Hors', $heading->item(0)->childNodes->item(3)->nodeValue);
        $this->assertEquals(', 
    contact persons for the ', $heading->item(0)->childNodes->item(4)->nodeValue);
        $this->assertEquals('a', $heading->item(0)->childNodes->item(5)->nodeName);
        $this->assertEquals('W3C HTML Activity', $heading->item(0)->childNodes->item(5)->nodeValue);
        $this->assertEquals('br', $heading->item(0)->childNodes->item(6)->nodeName); // br tag!
        $this->assertEquals('', $heading->item(0)->childNodes->item(6)->nodeValue);  // br tag value kinda weird!
        //$this->assertEquals('', $heading->item(0)->childNodes->item(7)->nodeValue); non value.
        
        // second address element test.
        $this->assertEquals('address', $heading->item(1)->nodeName);
        $this->assertEquals('David Stevens', $heading->item(1)->nodeValue);
        $this->assertTrue($heading->item(1)->hasChildNodes());
    }

    public function testEm() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
    <em>Emphasized text 1</em>
    <em>Emphasized text 2</em>
    <em>Emphasized text 3</em>
    <em>Emphasized text 4</em>
    <p>Emphasized <em>TEXT</em> 5</p>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $heading=$this->engine->getEm();
        $this->assertInternalType('object', $heading);
        $this->assertInstanceOf('DOMNodeList', $heading);
        $this->assertEquals(5, $heading->length);

        // first element test.
        $this->assertEquals('em', $heading->item(0)->nodeName);
        
        // add more tests here.
        
    }
    
    public function testStrong() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
    <strong>Strong text 1 </strong>
    <strong>Strong text 2 </strong>
    <strong>Strong text 3 </strong>
    <strong>Strong text 4 </strong>
    <strong>Strong text 5 </strong>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $strong=$this->engine->getStrong();
        
        $this->assertInternalType('object', $strong);
        $this->assertInstanceOf('DOMNodeList', $strong);
        $this->assertEquals(5, $strong->length);

        // first element test.
        $this->assertEquals('strong', $strong->item(0)->nodeName);
        
        // add more tests here.
        
    }
    
    public function testCitation() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
    <cite>Citation 1</cite>
    <cite>Citation 2</cite>
    <cite>Citation 3</cite>
    <cite>Citation 4</cite>
    <cite>Citation 5</cite>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $citation=$this->engine->getCite();
        
        $this->assertInternalType('object', $citation);
        $this->assertInstanceOf('DOMNodeList', $citation);
        $this->assertEquals(5, $citation->length);

        // first element test.
        $this->assertEquals('cite', $citation->item(0)->nodeName);
        
        // add more tests here.
        
    }
    
    public function testDefinition() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
    <dfn>Definition term 1</dfn>
    <dfn>Definition term 2</dfn>
    <dfn>Definition term 3</dfn>
    <dfn>Definition term 4</dfn>
    <dfn>Definition term 5</dfn>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $definition=$this->engine->getDfn();
        
        $this->assertInternalType('object', $definition);
        $this->assertInstanceOf('DOMNodeList', $definition);
        $this->assertEquals(5, $definition->length);

        // first element test.
        $this->assertEquals('dfn', $definition->item(0)->nodeName);
        
        // add more tests here.
        
    }
    
    public function testCode() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
    <code>Computer code text 1</code>
    <code>Computer code text 2</code>
    <code>Computer code text 3</code>
    <code>Computer code text 4</code>
    <code>Computer code text 5</code>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $code=$this->engine->getCode();
        
        $this->assertInternalType('object', $code);
        $this->assertInstanceOf('DOMNodeList', $code);
        $this->assertEquals(5, $code->length);

        // first element test.
        $this->assertEquals('code', $code->item(0)->nodeName);
        
        // add more tests here.
        
    }
    
    public function testSample() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
    <samp>Sample computer code text 1</samp>
    <samp>Sample computer code text 2</samp>
    <samp>Sample computer code text 3</samp>
    <samp>Sample computer code text 4</samp>
    <samp>Sample computer code text 5</samp>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $sample=$this->engine->getSamp();
        
        $this->assertInternalType('object', $sample);
        $this->assertInstanceOf('DOMNodeList', $sample);
        $this->assertEquals(5, $sample->length);

        // first element test.
        $this->assertEquals('samp', $sample->item(0)->nodeName);
        
        // add more tests here.
        
    }
    
    public function testKeyboard() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
    <kbd>Keyboard text 1</kbd>
    <kbd>Keyboard text 2</kbd>
    <kbd>Keyboard text 3</kbd>
    <kbd>Keyboard text 4</kbd>
    <kbd>Keyboard text 5</kbd>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $keyboard=$this->engine->getKbd();
        
        $this->assertInternalType('object', $keyboard);
        $this->assertInstanceOf('DOMNodeList', $keyboard);
        $this->assertEquals(5, $keyboard->length);

        // first element test.
        $this->assertEquals('kbd', $keyboard->item(0)->nodeName);
        
        // add more tests here.
        
    }
    
    public function testVariable() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
    <var>Variable 1</var>
    <var>Variable 2</var>
    <var>Variable 3</var>
    <var>Variable 4</var>
    <var>Variable 5</var>
    <abbr title="World Wide Web">WWW</abbr>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $variable=$this->engine->getVar();
        
        $this->assertInternalType('object', $variable);
        $this->assertInstanceOf('DOMNodeList', $variable);
        $this->assertEquals(5, $variable->length);

        // first element test.
        $this->assertEquals('var', $variable->item(0)->nodeName);
        
        // add more tests here.
        
    }
    
    public function testAbbreviation() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
    <abbr title="World Wide Web">WWW</abbr>
    <abbr title="United Kingdom -- fuck the king and the queen bunch of chiefs">UK</abbr>
    <abbr title="British Bullshit Corporation">BBC</abbr>
    <abbr title="Loser On Line">LOL</abbr>
    <abbr title="STFU">PMSL</abbr>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $abbreviation=$this->engine->getAbbr();
        
        $this->assertInternalType('object', $abbreviation);
        $this->assertInstanceOf('DOMNodeList', $abbreviation);
        $this->assertEquals(5, $abbreviation->length);

        // first element test.
        $this->assertEquals('abbr', $abbreviation->item(0)->nodeName);
        
        // add more tests here.
        
    }
    
    public function testAcronym() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <acronym title="as soon as possible">ASAP</acronym>
        <acronym title="test2">TEST2</acronym>
        <acronym title="test3">TEST3</acronym>
        <acronym title="test4">TEST4</acronym>
        <acronym title="test5">TEST5</acronym>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $acronym=$this->engine->getAcronym();
        
        $this->assertInternalType('object', $acronym);
        $this->assertInstanceOf('DOMNodeList', $acronym);
        $this->assertEquals(5, $acronym->length);

        // first element test.
        $this->assertEquals('acronym', $acronym->item(0)->nodeName);
        
        // add more tests here.
        
    }
    
    public function testParagraph() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <p>Testing 1</p>
        <p>Testing 2</p>
        <p>Testing 3</p>
        <p>Testing 4</p>
        <p>Testing 5</p>
        <!--
        TEST for nested p tags error as expected.
        DOMDocument::loadHTML(): Unexpected end tag : p in Entity
        <p>Testing 6 <p>Invalid</p></p>
        -->
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $paragraph=$this->engine->getP();
        
        $this->assertInternalType('object', $paragraph);
        $this->assertInstanceOf('DOMNodeList', $paragraph);
        $this->assertEquals(5, $paragraph->length);

        // first element test.
        $this->assertEquals('p', $paragraph->item(0)->nodeName);
        
        // add more tests here.

    }

    public function testBr() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <br /><br>
    <div>
        <br>
        <br>
        <br />
        <br />
        <br /><br>
    </div>
    <br>
    </body><br>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $br=$this->engine->getBr();
        
        $this->assertInternalType('object', $br);
        $this->assertInstanceOf('DOMNodeList', $br);
        $this->assertEquals(10, $br->length);

        // first element test.
        $this->assertEquals('br', $br->item(0)->nodeName);
        
        // add more tests here.

    }
    
    public function testPre() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <pre>Testing 1</pre>
        <pre>Testing 2</pre>
        <pre>Testing 3</pre>
        <pre>Testing 4</pre>
        <pre>Testing 5</pre>
        
    </div>
        <PRE>Testing 6</pre>
        <pre>Testing 7</pre>
        <pre>Testing 8</PRE>
        <pre>Testing 9</pre>
        <pre>Testing <pre>TEST Nested</pre> 10</pre>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $pre=$this->engine->getPre();
        
        $this->assertInternalType('object', $pre);
        $this->assertInstanceOf('DOMNodeList', $pre);
        $this->assertEquals(11, $pre->length);

        // first element test.
        $this->assertEquals('pre', $pre->item(0)->nodeName);
        
        // add more tests here.

    }
    
    public function testInserted() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <INS>1</INS>
        <ins>2</ins>
        <ins>3</ins>
        <ins>4</ins>
        <ins>5</ins>
        <ins>6</ins>
        <ins>7</ins>
        <INS>8</INS>
        <INS>9</INS>
        <INS>10</INS>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $inserted=$this->engine->getIns();
        
        $this->assertInternalType('object', $inserted);
        $this->assertInstanceOf('DOMNodeList', $inserted);
        $this->assertEquals(10, $inserted->length);

        // first element test.
        $this->assertEquals('ins', $inserted->item(0)->nodeName);
        
        // add more tests here.

    }
    
    public function testDeleted() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <del>1</del>
        <del>2</del>
        <del>3</del>
        <del>4</del>
        <del>5</del>
        <del>6</del>
        <del>7</del>
        <del>8</del>
        <del>9</del><del>10</del>
        
        
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $deleted=$this->engine->getDel();
        
        $this->assertInternalType('object', $deleted);
        $this->assertInstanceOf('DOMNodeList', $deleted);
        $this->assertEquals(10, $deleted->length);

        // first element test.
        $this->assertEquals('del', $deleted->item(0)->nodeName);
        
        // add more tests here.

    }
        
    public function testUnorderedlist() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <ul>
            <li>Testing 1</li>
            <li>Testing 2</li>
        </ul>
        
        <ul>
            <li>Testing 1</li>
            <li>Testing 2</li>
            <li>Testing 3</li>
        </ul>

        <ul>
            <li>Testing 1</li>
            <li>Testing 2</li>
            <li>Testing 3</li>
            <li>Testing 4</li>
        </ul>

        <ul>
            <li>Testing 1</li>
            <li>Testing 2</li>
            <li>Testing 3</li>
            <li>Testing 4</li>
            <li>Testing 5</li>
            <li>Testing 6</li>
            <li>Testing 7</li>
            <li>Testing 8</li>
            <li>Testing 49/li>
            <li>Testing 10</li>
        </ul>

    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html));

        $deleted=$this->engine->getUl();
        
        $this->assertInternalType('object', $deleted);
        $this->assertInstanceOf('DOMNodeList', $deleted);
        $this->assertEquals(4, $deleted->length);

        // first element test.
        $this->assertEquals('ul', $deleted->item(0)->nodeName);
        
        // add more tests here.

    }
        
    public function testOrderedlist() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <ol>
            <li>Testing 1</li>
            <li>Testing 2</li>
        </ol>
        
        <ol>
            <li>Testing 1</li>
            <li>Testing 2</li>
            <li>Testing 3</li>
        </ol>

        <ol>
            <li>Testing 1</li>
            <li>Testing 2</li>
            <li>Testing 3</li>
            <li>Testing 4</li>
        </ol>

        <ol>
            <li>Testing 1</li>
            <li>Testing 2</li>
            <li>Testing 3</li>
            <li>Testing 4</li>
            <li>Testing 5</li>
            <li>Testing 6</li>
            <li>Testing 7</li>
            <li>Testing 8</li>
            <li>Testing 49/li>
            <li>Testing 10</li>
        </ol>

    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $deleted=$this->engine->getOl();
        
        $this->assertInternalType('object', $deleted);
        $this->assertInstanceOf('DOMNodeList', $deleted);
        $this->assertEquals(4, $deleted->length);

        // first element test.
        $this->assertEquals('ol', $deleted->item(0)->nodeName);
        
        // add more tests here.

    }
    
    public function testDefinitionList() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <DL>
          <DT>Dweeb
          <DD>young excitable person who may mature
            into a <EM>Nerd</EM> or <EM>Geek</EM>

          <DT>Hacker
          <DD>a clever programmer

          <DT>Nerd
          <DD>technically bright but socially inept person

        </DL>
        
        <DL>
          <DT>Testing 1
          <DD>Testing Testing

          <DT>Testing 2
          <DD>Testing Testing

          <DT>Testing 3
          <DD>Testing Testing

        </DL>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $dl=$this->engine->getDl();
        
        $this->assertInternalType('object', $dl);
        $this->assertInstanceOf('DOMNodeList', $dl);
        $this->assertEquals(2, $dl->length);

        // first element test.
        $this->assertEquals('dl', $dl->item(0)->nodeName);
        
        // add more tests here.

    }
    
    public function testDefinitionType() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <DL>
          <DT>Dweeb
          <DD>young excitable person who may mature
            into a <EM>Nerd</EM> or <EM>Geek</EM>

          <DT>Hacker
          <DD>a clever programmer

          <DT>Nerd
          <DD>technically bright but socially inept person

        </DL>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $dt=$this->engine->getDt();
        
        $this->assertInternalType('object', $dt);
        $this->assertInstanceOf('DOMNodeList', $dt);
        $this->assertEquals(3, $dt->length);

        // first element test.
        $this->assertEquals('dt', $dt->item(0)->nodeName);
        
        // add more tests here.

    }
        
    public function testDefinitionData() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <DL>
          <DT>Dweeb
          <DD>young excitable person who may mature
            into a <EM>Nerd</EM> or <EM>Geek</EM>

          <DT>Hacker
          <DD>a clever programmer

          <DT>Nerd
          <DD>technically bright but socially inept person

        </DL>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $dd=$this->engine->getDd();
        
        $this->assertInternalType('object', $dd);
        $this->assertInstanceOf('DOMNodeList', $dd);
        $this->assertEquals(3, $dd->length);

        // first element test.
        $this->assertEquals('dd', $dd->item(0)->nodeName);
        
        // add more tests here.

    }
    
    public function testTable() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div>
        <table dir="testing1"></table>
        <table dir="testing2"></table>
        <table dir="testing3"></table>
        <table dir="testing4"></table>
        <table dir="testing5"></table>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getTable();
        
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        $this->assertEquals(5, $result->length);

        // first element test.
        $this->assertEquals('table', $result->item(0)->nodeName);
        
        // add more tests here.

    }
    
    public function testTableCaption() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div id="container1">Testing 1
        <table>
        <caption id="one">Testing One</caption>
        <thead>
             <TR> ...header information...
        </thead>
        <tfoot>
             <TR> ...footer information...
        </tfoot>
        <tbody>
             <TR> ...first row of block one data...
             <TR> ...second row of block one data...
        </tbody>
        <tbody>
             <TR> ...first row of block two data...
             <TR> ...second row of block two data...
             <TR> ...third row of block two data...
        </tbody>
        </table>
    </div>
    <div id="container2" class="container">Testing 2
        <TABLE>
        <caption id="two">Testing Two</caption>
        <THEAD>
             <TR> ...header information...
        </THEAD>
        <TFOOT>
             <TR> ...footer information...
        </TFOOT>
        <TBODY>
             <TR> ...first row of block one data...
             <TR> ...second row of block one data...
        </TBODY>
        <TBODY>
             <TR> ...first row of block two data...
             <TR> ...second row of block two data...
             <TR> ...third row of block two data...
        </TBODY>
        </TABLE>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        //$result=$this->engine->getTable()->list();
        $result=$this->engine->getCaption('[@id="one"]');
        
        
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        
        $this->assertEquals(1, $result->length);
 
        // foreach and DOMNodeList :) should clean up tests.
        foreach($result as $key => $element) {
            $this->assertEquals(0, $key);
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('caption', $element->nodeName);
            $this->assertEquals('Testing One', $element->nodeValue);
            $this->assertEquals('/html/body/div[1]/table/caption', $element->getNodePath());
        }
        // $result=$this->engine->getTableCaption();
        // $els = $this->engine->getElementsByTagName('caption');
        //
        // //$this->engine->dump($els); exit;
        // for ($i = $els->length; --$i >= 0; ) {
        // //$this->engine->dump($i); exit;
        //     $el = $els->item($i);
        // //$this->engine->dump($el); exit;
        // //$this->engine->dump($el->nodeName); exit;
        // //$this->engine->dump($el->getAttribute('class')); exit;
        // //switch ($el->getAttribute('class')) {
        //     switch ($el->nodeName) {
        //         case 'caption' :
        //             $el->parentNode->removeChild($el);
        //             break;
        //         case 'inputfile' :
        //             $el->setAttribute('type', 'text');
        //             break;
        //     }
        // }
        //
        // $this->engine->dump($els); exit;
        // $this->assertEquals(1, $result->length);
        // $this->assertEquals('address', $result->item(1)->nodeName);
        // $this->assertEquals('David Stevens', $result->item(1)->nodeValue);
        // $this->assertTrue($result->item(1)->hasChildNodes());
        // $this->engine->dump(
        //        $result
        //        //$result->item(0)
        //        );
        // exit; // needed for phpunit.
    }

    public function testTableTHead() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div id="container1">Testing 1
        <table>
        <caption id="one">Testing One</caption>
        <thead id="table1-header">
             <TR> ...header information...
        </thead>
        <tfoot>
             <TR> ...footer information...
        </tfoot>
        <tbody>
             <TR> ...first row of block one data...
             <TR> ...second row of block one data...
        </tbody>
        <tbody>
             <TR> ...first row of block two data...
             <TR> ...second row of block two data...
             <TR> ...third row of block two data...
        </tbody>
        </table>
    </div>
    <div id="container2" class="container">Testing 2
        <TABLE>
        <caption id="two">Testing Two</caption>
        <THEAD id="table2-header">
             <TR> ...header information...
        </THEAD>
        <TFOOT>
             <TR> ...footer information...
        </TFOOT>
        <TBODY>
             <TR> ...first row of block one data...
             <TR> ...second row of block one data...
        </TBODY>
        <TBODY>
             <TR> ...first row of block two data...
             <TR> ...second row of block two data...
             <TR> ...third row of block two data...
        </TBODY>
        </TABLE>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        //$result=$this->engine->getTable()->list();
        $result=$this->engine->getTHead('[@id="table1-header"]');
        
        
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        
        $this->assertEquals(1, $result->length);
 
        // foreach and DOMNodeList :) should clean up tests.
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('thead', $element->nodeName);
            $this->assertEquals(' ...header information...
        ', $element->nodeValue);
            $this->assertEquals('/html/body/div[1]/table/thead', $element->getNodePath());
        }
    }

    public function testTableTFoot() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div id="container1">Testing 1
        <table>
        <caption id="one">Testing One</caption>
        <thead id="table1-header">
             <TR> ...header information...
        </thead>
        <tfoot id="table1-footer">
             <TR> ...footer information...
        </tfoot>
        <tbody id="table1-body">
             <TR> ...first row of block one data...
             <TR> ...second row of block one data...
        </tbody>
        <tbody>
             <TR> ...first row of block two data...
             <TR> ...second row of block two data...
             <TR> ...third row of block two data...
        </tbody>
        </table>
    </div>
    <div id="container2" class="container">Testing 2
        <TABLE>
        <caption id="two">Testing Two</caption>
        <THEAD id="table2-header">
             <TR> ...header information...
        </THEAD>
        <TFOOT id="table2-footer">
             <TR> ...footer information...
        </TFOOT>
        <TBODY id="table2-body">
             <TR> ...first row of block one data...
             <TR> ...second row of block one data...
        </TBODY>
        <TBODY>
             <TR> ...first row of block two data...
             <TR> ...second row of block two data...
             <TR> ...third row of block two data...
        </TBODY>
        </TABLE>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        //$result=$this->engine->getTable()->list();
        $result=$this->engine->getTFoot('[@id="table1-footer"]');
        
        
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        
        $this->assertEquals(1, $result->length);
 
        // foreach and DOMNodeList :) should clean up tests.
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('tfoot', $element->nodeName);
            $this->assertEquals(' ...footer information...
        ', $element->nodeValue);
            $this->assertEquals('/html/body/div[1]/table/tfoot', $element->getNodePath());
        }
    }

    public function testTableTBody() {
        $html= <<< FIXTURE
<html>
    <head>
        <title></title>
        <meta name="Author" content="David Stevens">
        <meta name="keywords" lang="en" content="fabrication, template, engine, fabric">
    </head>
    <body>
    <div id="container1">Testing 1
        <table>
        <caption id="one">Testing One</caption>
        <thead id="table1-header">
             <TR> ...header information...
        </thead>
        <tfoot id="table1-footer">
             <TR> ...footer information...
        </tfoot>
        <tbody id="table1-body">
             <TR>...first row of block one data...
             <TR>...second row of block one data...
        </tbody>
        <tbody>
             <TR> ...first row of block two data...
             <TR> ...second row of block two data...
             <TR> ...third row of block two data...
        </tbody>
        </table>
    </div>
    <div id="container2" class="container">Testing 2
        <TABLE>
        <caption id="two">Testing Two</caption>
        <THEAD id="table2-header">
             <TR> ...header information...
        </THEAD>
        <TFOOT id="table2-footer">
             <TR> ...footer information...
        </TFOOT>
        <TBODY id="table2-body">
             <TR> ...first row of block one data...
             <TR> ...second row of block one data...
        </TBODY>
        <TBODY>
             <TR> ...first row of block two data...
             <TR> ...second row of block two data...
             <TR> ...third row of block two data...
        </TBODY>
        </TABLE>
    </div>
    </body>
</html>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getTBody('[@id="table1-body"]');
        
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        
        $this->assertEquals(1, $result->length);
 
        // foreach and DOMNodeList :) should clean up tests.
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('tbody', $element->nodeName);
            $this->assertEquals('...first row of block one data...
             ...second row of block one data...
        ', $element->nodeValue);
            $this->assertEquals('/html/body/div[1]/table/tbody[1]', $element->getNodePath());
        }
    }


    public function testTableColGroup() {

        // fixture example from http://www.w3.org/TR/html401/struct/tables.html#sample
        $html= <<< FIXTURE
<TABLE border="2" frame="hsides" rules="groups"
          summary="Code page support in different versions
                   of MS Windows.">
<CAPTION>CODE-PAGE SUPPORT IN MICROSOFT WINDOWS</CAPTION>
<COLGROUP align="center">
<COLGROUP align="left">
<COLGROUP align="center" span="2">
<COLGROUP align="center" span="3">
<THEAD valign="top">
<TR>
<TH>Code-Page<BR>ID
<TH>Name
<TH>ACP
<TH>OEMCP
<TH>Windows<BR>NT 3.1
<TH>Windows<BR>NT 3.51
<TH>Windows<BR>95
<TBODY>
<TR><TD>1200<TD>Unicode (BMP of ISO/IEC-10646)<TD><TD><TD>X<TD>X<TD>*
<TR><TD>1250<TD>Windows 3.1 Eastern European<TD>X<TD><TD>X<TD>X<TD>X
<TR><TD>1251<TD>Windows 3.1 Cyrillic<TD>X<TD><TD>X<TD>X<TD>X
<TR><TD>1252<TD>Windows 3.1 US (ANSI)<TD>X<TD><TD>X<TD>X<TD>X
<TR><TD>1253<TD>Windows 3.1 Greek<TD>X<TD><TD>X<TD>X<TD>X
<TR><TD>1254<TD>Windows 3.1 Turkish<TD>X<TD><TD>X<TD>X<TD>X
<TR><TD>1255<TD>Hebrew<TD>X<TD><TD><TD><TD>X
<TR><TD>1256<TD>Arabic<TD>X<TD><TD><TD><TD>X
<TR><TD>1257<TD>Baltic<TD>X<TD><TD><TD><TD>X
<TR><TD>1361<TD>Korean (Johab)<TD>X<TD><TD><TD>**<TD>X
<TBODY>
<TR><TD>437<TD>MS-DOS United States<TD><TD>X<TD>X<TD>X<TD>X
<TR><TD>708<TD>Arabic (ASMO 708)<TD><TD>X<TD><TD><TD>X
<TR><TD>709<TD>Arabic (ASMO 449+, BCON V4)<TD><TD>X<TD><TD><TD>X
<TR><TD>710<TD>Arabic (Transparent Arabic)<TD><TD>X<TD><TD><TD>X
<TR><TD>720<TD>Arabic (Transparent ASMO)<TD><TD>X<TD><TD><TD>X
</TABLE>

FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getColGroup('[@align="center"]');
        
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        
        $this->assertEquals(3, $result->length);
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('colgroup', $element->nodeName);
            $this->assertEquals('', $element->nodeValue);
        }
    }

    public function testTableCol() {

        $html= <<< FIXTURE
<TABLE>
<COLGROUP>
   <COL width="30">
<COLGROUP>
   <COL width="30">
   <COL width="0*">
   <COL width="2*">
<COLGROUP align="center">
   <COL width="1*">
   <COL width="3*" align="char" char=":">
<THEAD>
<TR><TD> ...
...rows...
</TABLE>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getCol();
        
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        
        $this->assertEquals(6, $result->length);
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('col', $element->nodeName);
            $this->assertEquals('', $element->nodeValue);
        }
    }
    
    public function testTableTr() {

        $html= <<< FIXTURE
<TABLE summary="This table charts the number of cups
                   of coffee consumed by each senator, the type 
                   of coffee (decaf or regular), and whether 
                   taken with sugar.">
<CAPTION>Cups of coffee consumed by each senator</CAPTION>
<TR>
   <TH>Name</TH>
   <TH>Cups</TH>
   <TH>Type of Coffee</TH>
   <TH>Sugar?</TH>
<TR>
   <TD>T. Sexton</TD>
   <TD>10</TD>
   <TD>Espresso</TD>
   <TD>No</TD>
<TR>
   <TD>J. Dinnen</TD>
   <TD>5</TD>
   <TD>Decaf</TD>
   <TD>Yes</TD>
</TABLE>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getTr();
        
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        
        $this->assertEquals(3, $result->length);
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('tr', $element->nodeName);
            //$this->assertEquals('', $element->nodeValue);
        }
    }

    public function testTableTh() {

        $html= <<< FIXTURE
<TABLE summary="This table charts the number of cups
                   of coffee consumed by each senator, the type 
                   of coffee (decaf or regular), and whether 
                   taken with sugar.">
<CAPTION>Cups of coffee consumed by each senator</CAPTION>
<TR>
   <TH>Name</TH>
   <TH>Cups</TH>
   <TH>Type of Coffee</TH>
   <TH>Sugar?</TH>
<TR>
   <TD>T. Sexton</TD>
   <TD>10</TD>
   <TD>Espresso</TD>
   <TD>No</TD>
<TR>
   <TD>J. Dinnen</TD>
   <TD>5</TD>
   <TD>Decaf</TD>
   <TD>Yes</TD>
</TABLE>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getTh();
        
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        
        $this->assertEquals(4, $result->length);
        
        $values=array('Name', 'Cups', 'Type of Coffee', 'Sugar?');
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('th', $element->nodeName);
            $this->assertEquals($values[$key], $element->nodeValue);
        }
    }


    public function testTableTd() {

        $html= <<< FIXTURE
<TABLE summary="This table charts the number of cups
                   of coffee consumed by each senator, the type 
                   of coffee (decaf or regular), and whether 
                   taken with sugar.">
<CAPTION>Cups of coffee consumed by each senator</CAPTION>
<TR>
   <TH>Name</TH>
   <TH>Cups</TH>
   <TH>Type of Coffee</TH>
   <TH>Sugar?</TH>
<TR>
   <TD>T. Sexton</TD>
   <TD>10</TD>
   <TD>Espresso</TD>
   <TD>No</TD>
<TR>
   <TD>J. Dinnen</TD>
   <TD>5</TD>
   <TD>Decaf</TD>
   <TD>Yes</TD>
</TABLE>
FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getTd();
        
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        
        $this->assertEquals(8, $result->length);
        
        $values=array('T. Sexton', '10', 'Espresso', 'No', 'J. Dinnen', '5', 'Decaf', 'Yes');
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('td', $element->nodeName);
            $this->assertEquals($values[$key], $element->nodeValue);
        }
    }

    public function testA() {

        $html= <<< FIXTURE
For more information about W3C, please consult the 
<A href="http://www.w3.org/" charset="ISO-8859-1">W3C Web site</A> 

...text before the anchor...
<A name="anchor-one">This is the location of anchor one.</A>
...text after the anchor...

I just returned from vacation! Here's a
<A name="anchor-two" 
   href="http://www.somecompany.com/People/Ian/vacation/family.png">
photo of my family at the lake.</A>.

FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getA();
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        $this->assertEquals(3, $result->length);
        $values=array('W3C Web site', 'This is the location of anchor one.', "\nphoto of my family at the lake.");
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('a', $element->nodeName);
            $this->assertEquals($values[$key], $element->nodeValue);
        }
        
        $result=$this->engine->getA('[@name="anchor-one"]');
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        $this->assertEquals(1, $result->length);
        $values=array('This is the location of anchor one.');
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('a', $element->nodeName);
            $this->assertEquals($values[$key], $element->nodeValue);
        }
    }

    
    public function testImage() {
	
        $this->engine->run($this->design);
        $result=$this->engine->getImg();
        
        $this->assertInstanceOf('DOMNodeList', $result);
        $this->assertEquals(6, $result->length);
    }

    
    public function testLinkWithImage() {
	
        $this->engine->run($this->design);
        $NodeList=$this->engine->getLinkWithImage();

        $this->assertInstanceOf('DOMNodeList', $NodeList);
        $this->assertEquals(2, $NodeList->length);
    }

    
    public function testImageWithAltTag() {
	
        $this->engine->run($this->design);
        $NodeList=$this->engine->getImageWithAltTag();

        $this->assertInstanceOf('DOMNodeList', $NodeList);
        $this->assertEquals(1, $NodeList->length);   
    }

    
    public function testImageWithoutAltTag() {
	
        $this->engine->run($this->design);
        $NodeList=$this->engine->getImageWithoutAltTag();

        $this->assertInstanceOf('DOMNodeList', $NodeList);
        $this->assertEquals(5, $NodeList->length);
    }
    
    
    public function testObject() {

        $html= <<< FIXTURE
<HTML>
   <HEAD>
      <TITLE>The cool site!</TITLE>
   </HEAD>
   <BODY>
     <P><OBJECT data="navbar1.gif" type="image/gif" usemap="#map1"></OBJECT>

     ...the rest of the page here...

     <MAP name="map1">
       <P>Navigate the site:
       <A href="guide.html" shape="rect" coords="0,0,118,28">Access Guide</a> |
       <A href="shortcut.html" shape="rect" coords="118,0,184,28">Go</A> |
       <A href="search.html" shape="circle" coords="184,200,60">Search</A> |
       <A href="top10.html" shape="poly" coords="276,0,276,28,100,200,50,50,276,0">Top Ten</A>
     </MAP>
   </BODY>
</HTML>

FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getObject();
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        $this->assertEquals(1, $result->length);
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('object', $element->nodeName);
            $this->assertEquals('', $element->nodeValue);
        }
        
    }
    
    
    public function testParam() {

        $html= <<< FIXTURE
<HTML>
   <HEAD>
      <TITLE>The cool site!</TITLE>
   </HEAD>
   <BODY>
     <OBJECT data="navbar1.gif" type="image/gif" usemap="#map1"></OBJECT>

    <OBJECT classid="http://www.miamachina.it/analogclock.py">
    <PARAM name="height" value="40" valuetype="data">
    <PARAM name="width" value="40" valuetype="data">
    This user agent cannot render Python applications.
    </OBJECT>

     ...the rest of the page here...

     <MAP name="map1">
       <P>Navigate the site:
       <A href="guide.html" shape="rect" coords="0,0,118,28">Access Guide</a> |
       <A href="shortcut.html" shape="rect" coords="118,0,184,28">Go</A> |
       <A href="search.html" shape="circle" coords="184,200,60">Search</A> |
       <A href="top10.html" shape="poly" coords="276,0,276,28,100,200,50,50,276,0">Top Ten</A>
     </MAP>
   </BODY>
</HTML>

FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getParam();
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        $this->assertEquals(2, $result->length);
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('param', $element->nodeName);
            $this->assertEquals('', $element->nodeValue);
        }
        
    }
    
    public function testMap() {

        $html= <<< FIXTURE
<HTML>
   <HEAD>
      <TITLE>The cool site!</TITLE>
   </HEAD>
   <BODY>
     <OBJECT data="navbar1.gif" type="image/gif" usemap="#map1"></OBJECT>

    <OBJECT classid="http://www.miamachina.it/analogclock.py">
    <PARAM name="height" value="40" valuetype="data">
    <PARAM name="width" value="40" valuetype="data">
    This user agent cannot render Python applications.
    </OBJECT>

     ...the rest of the page here...

     <MAP name="map1">
       <P>Navigate the site:
       <A href="guide.html" shape="rect" coords="0,0,118,28">Access Guide</a> |
       <A href="shortcut.html" shape="rect" coords="118,0,184,28">Go</A> |
       <A href="search.html" shape="circle" coords="184,200,60">Search</A> |
       <A href="top10.html" shape="poly" coords="276,0,276,28,100,200,50,50,276,0">Top Ten</A>
     </MAP>
   </BODY>
</HTML>

FIXTURE;
        
        $this->assertTrue($this->engine->run($html, 'html', 'string'));

        $result=$this->engine->getMap();
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf('DOMNodeList', $result);
        $this->assertEquals(1, $result->length);
        foreach($result as $key => $element) {
            $this->assertInstanceOf('DOMElement', $element);
            $this->assertEquals('map', $element->nodeName);
            $this->assertEquals("Navigate the site:\n       Access Guide |\n       Go |\n       Search |\n       Top Ten\n     ", $element->nodeValue);
        }
        
    }
    
    // Framesets 
    //   and 
    // Forms
    //   then
    // HTML5
    
    
    
    // MAPPING for input<->output
    public function testSettingMapping() {
	
        $this->assertTrue($this->engine->input('.hello', 'World'));
    }

    
    public function testInputOutputMappingForHtmlIds() {
	
        $this->assertTrue($this->engine->input('.hello', 'World'));
        $this->assertEquals('World', $this->engine->output('.hello'));
    }

    
    public function XtestBuildingFromDesignMappingIds() {
	
        $expected =
            $this->engine->getDoctype().
            '<html>'.
            '<head>'.
            '<title></title>'.
            '</head>'.
            '<body>'.
            '<div id="header">!'.$this->header.'!</div>'.
            '<div id="content">!'.$this->content.'!</div>'.
            '<div id="footer">!'.$this->footer.'!</div>'.
            '</body>'.
            '</html>';

        $this->engine->input('.header', $this->header);
        $this->engine->input('.content', $this->content);
        $this->engine->input('.footer', $this->footer);

        // run after inputs have been set.
        $this->engine->run($this->design);

        $this->assertEquals($this->header, $this->engine->getElementById('header'));
        $this->assertEquals($this->content, $this->engine->getElementById('content'));
        $this->assertEquals($this->footer, $this->engine->getElementById('footer'));

        $this->assertEquals('!HEADER!', $this->engine->setElementById('header', '!HEADER!'));
        $this->assertEquals('!CONTENT!', $this->engine->setElementById('content', '!CONTENT!'));
        $this->assertEquals('!FOOTER!', $this->engine->setElementById('footer', '!FOOTER!'));

        $this->assertEquals('!HEADER!', $this->engine->getElementBy('id', 'header'));
        $this->assertEquals('!CONTENT!', $this->engine->getElementBy('id', 'content'));
        $this->assertEquals('!FOOTER!', $this->engine->getElementBy('id', 'footer'));

        $this->assertEquals($expected, $this->engine->outputHTML());
    }
}
