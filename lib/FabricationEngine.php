<?php

namespace Fabrication\Library;

/**
 * Fabrication Engine
 * 
 * Template engine without placeholders, and much more.
 * 
 * There are many words in English which end with -ation. This is a interesting 
 * and useful pattern to learn forming nouns Denoting an action or an instance 
 * of it. Here are just a few common examples exploration, hesitation, vibration
 * reputation, starvation, nation, relation, location.
 * From the Latin suffix -atio, an alternative form of -tio (from whence -tion).
 * 
 * Fabric From French fabrique, from Latin fabrica a workshop, art, trade, 
 * product of art, structure, fabric, from faber (artisan, workman).
 * 
 * Fabrication to invent or produce the act of fabricating, constructing,
 * construction; manufacture, factory.
 * 
 * For examples see tests/ as these will become the documentation.
 * 
 * The documentation will be generated from the UnitTestCases comments and
 * the 
 * 
 * <code>
 * 
 * $engine = new FabricationEngine();
 * $engine->input('hello', 'world');
 * 
 * print $engine->output('hello');
 * print $engine->output('hello', 'php.string');
 * print $engine->output('hello', 'php.array');
 * 
 * // Generates: class Hello { public $hello='world'; }
 * print $engine->output('hello', 'php.object');
 * 
 * // TODO
 * // Instantiate a class called 'CustomOutput' assign input variables and then 
 * // call the __toString method on the 'CustomOutput' class. 
 * print $engine->output('hello', 'php.object.CustomOutput);
 * 
 * </code>
 * 
 * @author David Stevens <mail.davro@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html
 * 
 */

// Base engine element to extend from used in the specifcation.
class FabricationElement {
    
    public function execute(FabricationEngine $engine, \DOMElement $element) {
	
	$element->appendChild($engine->create('div', 'ExecuteTest'));
    }
    
}

class FabricationEngine extends \DOMDocument {
    
    /**
     * Symbol for id attribute assignment.
     * 
     */
    const symbol_id ='.';

    /**
     * Symbol for class attributes assignment.
     * 
     */
    const symbol_class ='#';

    /**
     * Debug switch.
     * 
     * @var type 
     */
    private static $debug = true;
    
    /**
     * Input container.
     * 
     * @var type 
     */
    public $input = array();

    /**
     * Input meta array.
     * 
     * @var type 
     */
    public $input_meta = array();

    /**
     * Output container.
     * 
     * @var type 
     */
    public $output = array();

    /**
     *
     * doctype                  now we have a choice !!
     * process                  nice word for hacks to clean up DOMDocument retardedness.
     * process.doctype          Doctype depending on doctype option.
     * process.body.image       Image tags to be valid depending on doctype option.
     * process.body.br          br tags to be valid depending on doctype option.
     * process.body.hr          hr tags to be valid depending on doctype option.
     * process.body.a           A tags and match a tags with #name to document anchors.
     *
     * @var array       Doctypes avaliable.
     */
    public $options = array(
	//'doctype'               => 'html.5',
	//'doctype'               => 'html.4.01.strict',
	'doctype' => 'html.4.01.transitional',
	'process' => true,
	'process.doctype' => true,
	'process.body.image' => true,
	'process.body.br' => true,
	'process.body.hr' => true,
	'process.body.anchor' => false,
    );

    /**
     * Current selected doctype.
     * 
     * @var string
     */
    public $doctype;

    /**
     * List of suggested doctypes.
     * 
     * @var array
     */
    public $doctypes;
    
    // house keeping needed for these.
    public $head;
    
    public $title = 'FAB';
    
    public $body;
    
    private $styles;
    
    private $metas;
    
    private $scripts;
    
    private $document;

    private $specification = array(
	'html.4.01.transitional' => array(
	    'head'	=> array(),
	    'body'	=> array(),
	    'base'	=> array(),
	    'script'	=> array(),
	    'noscript'	=> array(),
	    'div'	=> array(),
	    'span'	=> array(),
	    'h1'	=> array(),
	    'h2'	=> array(),
	    'h3'	=> array(),
	    'h4'	=> array(),
	    'h5'	=> array(),
	    'h6'	=> array(),
	    'address'	=> array(),
	    'form'	=> array(),
	    'strong'	=> array(),
	    'code'	=> array(),
	    'em'	=> array(),
	    'cite'	=> array(),
	    'dfn'	=> array(),
	    'samp'	=> array(),
	    'kbd'	=> array(),
	    'var'	=> array(),
	    'abbr'	=> array(),
	    'acronym'	=> array(),
	    'p'		=> array(),
	    'br'	=> array(),
	    'pre'	=> array(),
	    'ins'	=> array(),
	    'del'	=> array(),
	    'ul'	=> array(),
	    'ol'	=> array(),
	    'dl'	=> array(),
	    'dt'	=> array(),
	    'dd'	=> array(),
	    'table'	=> array(),
	    'caption'	=> array(),
	    'thead'	=> array(),
	    'tbody'	=> array(),
	    'tfoot'	=> array(),
	    'colgroup'	=> array(),
	    'col'	=> array(),
	    'tr'	=> array(),
	    'th'	=> array(),
	    'td'	=> array(),
	    'a'		=> array(),
	    'img'	=> array(),
	    'object'	=> array(),
	    'param'	=> array(),
	    'map'	=> array(),

	    // HTML 5 
	    'article'	=> array(),
	)
    );
    //public $xpath;

    /**
     * Main setup function for the Fabrication Engine.
     * 
     * Examples from http://www.w3.org/QA/2002/04/valid-dtd-list.html
     */
    public function __construct($version = '1.0', $encoding = 'utf-8') {
	// Construct the parent else error DOMDocument::createElement() 
	parent::__construct($version, $encoding);

	$this->doctypes['html.5'] = '<!DOCTYPE HTML>'; // HTML5 Not a standard.
	$this->doctypes['html.4.01.strict'] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"' . "\n" . '   "http://www.w3.org/TR/html4/strict.dtd">';
	$this->doctypes['html.4.01.transitional'] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"' . "\n" . '   "http://www.w3.org/TR/html4/loose.dtd">';
	$this->doctypes['html.4.01.frameset'] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"' . "\n" . '   "http://www.w3.org/TR/html4/frameset.dtd">';
	$this->doctypes['xhtml.1.0.strict'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' . "\n" . '   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	$this->doctypes['xhtml.1.0.transitional'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"' . "\n" . '   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	$this->doctypes['xhtml.1.0.frameset'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"' . "\n" . '   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
	
	//$this->setUp();
    }

    /**
     * Direct access to the Fabric of the engine.
     *
     * @return object           This current FabricationEngine object.
     */
    public function getEngine() {
	return $this;
    }

    /**
     * Getter for accessing an option value by key from the options array.
     * 
     */
    public function getOption($key) {
	return $this->options[$key];
    }

    /**
     * Setter for inserting a key value pair into the options array.
     * Once a key value pair has been insert the updateOptions method is executed. 
     * 
     * @return boolean      True on success.
     */
    public function setOption($key, $value) {

	$this->options[$key] = $value;
	//$this->debug("[test] setOption: KEY:$key VALUE:$value");
	return $this->options[$key];
    }

    /**
     * Getter for returning the current doctype output <DOCTYPE...
     *
     * @return string
     */
    public function getDoctype() {

	//return $this->output['doctype'];
	return $this->doctypes[$this->getOption('doctype')];
    }

    /**
     * Setup DOMDocument and XPath and stuff.
     * 
     */
    public function setUp($options = array()) {
	
	//libxml_clear_errors();
	// XPath object and other setting.
	// 
	// DOMDocument options.
	//$document->formatOutput = true;           // WORKS make optional.
	//$this->preserveWhiteSpace = false;
	//$document->substituteEntities = false;    // TESTING
	//$document->resolveExternals = true;       // TESTING
	$this->recover = true;			    // TESTING
	$this->strictErrorChecking = false;	    // TESTING

	$this->xpath = new \DomXpath($this);
    }

    /**
     * Register a prefix and uri to the xpath namespace.
     * 
     * @param type $prefix
     * @param type $namespaceURI 
     */
    public function registerNamespace($prefix, $namespaceURI) {
	
	$this->setUp();
	$this->xpath->registerNamespace($prefix, $namespaceURI);
    }
    
    /**
     * Run method once the all input have been set.
     * Then you will have a valid document with a searchable path.
     *
     * @param type $data
     * @param type $load
     * @param type $type
     * @return type 
     */
    public function run($data='', $load='string', $type='html') {

	//libxml_use_internal_errors(true);

	if (!empty($data)) {

	    switch ($type . '.' . $load) {

		case 'html.string':
		    //if ($this->loadHTML($html)) { // make error suppression optional.
		    if (@$this->loadHTML($data)) {
			// success.
		    } else {
			//print "[FAIL] problem loading html string.\n";
			return false;
		    }
		    break;

		case 'html.file':
		    //if ($this->loadHTMLFile($html)) { //if ($this->loadHTMLFile($html)) { 
		    if (@$this->loadHTMLFile($data)) {
			// success.
		    } else {
			return false;
		    }
		    break;

		case 'xml.string':
		    if (@$this->loadXML($data)) {
			// success.
		    } else {
			return false;
		    }
		    break;
	    }
	}

	// create a more flexable symbol loop, array based..
	// id assigner maps .keys with values.
	foreach ($this->input as $key => $value) {
	    
	    $id = substr($key, 0, 1);
	    
	    if ($id == self::symbol_id) {
		
		$id = str_replace(self::symbol_id, '', $key);
		$this->setElementById($id, $value);
	    }
	}

	// class assigner maps .keys with values.
	foreach ($this->input as $key => $value) {
	    
	    $class = substr($key, 0, 1);
	    
	    if ($id == self::symbol_class) {
		
		$class = str_replace(self::symbol_class, '', $key);
		$this->setElementById($class, $value); // change for class not id.
	    }
	}

	// Internal setup for DOMDocument and XPath.
	// for some reason setup borks if run in the constructor.
	$this->setUp();
	
	//$html=mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

	return true;
    }

    /**
     * Return the engine output html view.
     * 
     */
    public function outputHTML() {

	$this->output['raw'] = $this->saveHTML();
	$this->outputProcess();

	return $this->getDoctype('doctype') . $this->output['raw'];
    }

    /**
     * Return the engine output xml view.
     * 
     */
    public function outputXML() {

	$this->output['raw'] = $this->saveXML();
	$this->outputProcess();

	return $this->output['raw'];
    }
    
    /**
     * Time to sort out the plethora of DOMDocument bugs.
     * 
     */
    public function outputProcess() {


	// Remove doctype added by DOMDocument (hacky)
	$this->output['raw'] = preg_replace('/<!DOCTYPE[^>]+>/', '', $this->output['raw']);

	if ($this->getOption('process')) {
	    // Remove all whitespace between tags, for the extremist .
	    //$this->output['html'] = preg_replace("/>\s+</", "><", $this->output['html']);

	    /**
	     * Process img, br, hr, tags, change html to xhtml.
	     * 
	     * <img>  <img src="">  <img src="/image.png">
	     * 
	     * Are changed to xhtml
	     * <img />  <img src="" />  <img src="/image.png" />
	     */
	    if ($this->getOption('process.body.image')) {
		$this->output['raw'] = preg_replace('/<img(.*)>/sU', '<img\\1 />', $this->output['raw']);
	    }

	    if ($this->getOption('process.body.br')) {
		$this->output['raw'] = preg_replace('/<br(.*)>/sU', '<br\\1 />', $this->output['raw']);
	    }

	    if ($this->getOption('process.body.hr')) {
		$this->output['raw'] = preg_replace('/<hr(.*)>/sU', '<hr\\1 />', $this->output['raw']);
	    }
	}
	// Trim whitespace need this to get exactly the wanted data back for test cases, mmm.
	$this->output['raw'] = trim($this->output['raw']);

	return true;
    }

    
    public function isCli() {
	
	if (PHP_SAPI === 'cli') {
	    return true;
	}
	return false;
    }

    /**
     * Return a data dump.
     * 
     * $this->engine->dump(
     *  //$result
     *  $result->item(0)
     * );
     * exit;
     */
    function dump($data, $return=false, $options=array()) {
	
	if ($this->isCli()) {
	    $line_end = "\n";
	} else {
	    $line_end = "<br />\n";
	}
	
	if (empty($options)) {
	    $options = array(
		//'show.methods'=>true
		'show.methods' => false
	    );
	    //var_dump($options);
	}
	
	if (is_object($data)) {
	    $classname = get_class($data);

	    $result = $line_end;
	    $result.= str_repeat('-', 80) . $line_end;
	    $result.= "\tDUMP Type:" . gettype($data) . "\tReturn:" . var_export($return, true) . $line_end;
	    $result.= str_repeat('-', 80) . $line_end;
	    $result.= $line_end;
	    $result.= "ClassName: of $classname: $line_end";
	    $result.= $line_end;
	    $result.= "MethodList $line_end";

	    $class_methods = get_class_methods($data);
	    if (count($class_methods) > 0) {
		foreach ($class_methods as $method) {
		    $result.="Method:\t".$method.$line_end;
		}
	    } else {
		$result.="No methods found.$line_end";
	    }

	    $result.=$line_end;
	    $result.="NodeList:$line_end";
	    $result.="Instance: " . $classname . $line_end;
	    
	    switch ($classname) {
		
		case 'DOMAttr':
		    $result.= "DOMAttr XPath: {$data->getNodePath()}$line_end";
		    $result.= $data->ownerDocument->saveXML($data);
		    break;
		
		case 'DOMDocument':
		    $result.= "DOMDocument XPath: {$data->getNodePath()}$line_end";
		    $result.= $data->saveXML($data);
		    break;
		
		case 'DOMElement':
		    $result.= "DOMElement XPath: {$data->getNodePath()}$line_end";
		    $result.= $data->ownerDocument->saveXML($data);
		    break;
		
		case 'DOMNodeList':
		    for ($i = 0; $i < $data->length; $i++) {
			$result.= "DOMNodeList Item #$i, XPath: {$data->item($i)->getNodePath()}$line_end";
			$result.= "{$data->item($i)->ownerDocument->saveXML($data->item($i))}$line_end";
		    }
		    break;
		    
		default:
		    $result.= var_export($data, true);
	    }
	}

	if (is_array($data)) {
	    $result = $line_end;
	    $result = $line_end;
	    $result.= str_repeat('-', 80) . $line_end;
	    $result.= "| DUMP Type:" . gettype($data) . "\tReturn:" . var_export($return, true) . $line_end;
	    $result.= str_repeat('-', 80) . $line_end;
	    $result.= $line_end;

	    $result = var_export($data, true);
	}

	if (is_string($data)) {
	    $result = var_export($data, true);
	}

	if (is_int($data)) {
	    $result = var_export($data, true);
	}

	if ($return) {
	    return $result . $line_end;
	} else {
	    print $result . $line_end;
	}
    }

    /**
     * Create elements with attributes.
     * 
     *
     * @param type $name
     * @param type $value
     * @param type $attributes
     * @param type $children
     * @return type 
     */
    public function create($name, $value='', $attributes=array(), $children=array() ) {

	$element = $this->createElement($name, $value);
	
	if (count($children) > 0) {
	    //print '<pre>'.var_export($children, true).'</pre>'; die;
	    foreach($children as $child) {
		$element->appendChild(
		    $this->create(
			$child['name'], 
			@$child['value'],
			@$child['attributes'],
			@$child['children']
		    )
		);
	    }
	}
	
	if (count($attributes) > 0) {
	    //var_dump($attributes); die;
	    foreach ($attributes as $key => $value) {
		$element->setAttribute($key, $value);
	    }
	}
	return $element;
    }

    /**
     * Recursion method for creating and appending elements.
     * 
     * @param type $head
     * @param type $body
     * @param type $name
     * @param FabricationElement $value 
     */
    public function createElementRecursion(\DOMElement $element, $name, $value) {
	
	if (is_string($value)) {
	    
	    $element->appendChild($this->createElement($name, $value));
	}

	if (is_array($value)) {
	    
	    if (array_key_exists('name', $value)) {
		
		$newElement = $this->create(
		    $value['name'],
		    @$value['value'],
		    @$value['attributes'],
		    @$value['children']
		);
		
		$element->appendChild($newElement);
	    } 

	    if (@is_array($value[0])) {
		
		foreach($value as $k => $v) {
		    $this->createElementRecursion($element, $k, $v);
		}
	    }
	}

	if (is_object($value)) {
	    
	    // TESTING ...
	    if ($value instanceof FabricationElement) {
		
		$value->execute($this->getEngine(), $element);
	    }
	}
	
    }
    
    /**
     * HTML Specification pattern.
     * 
     * Three element required to build the basic html structure.
     * 
     * 1) /html
     * 2) /html/head
     * 3) /html/body
     * 
     * 
     */
    public function specification($specification = array()) {

	$sections = array('head', 'body');
	
	foreach($sections as $section) {
	    if (!array_key_exists($section, $specification) ) {
		die('Missing key section '.$section);
	    }
	}
	
	$this->setUp();

	// doctype handled by the engine using option methods.
	$html = $this->createElement('html', '');
	$head = $this->createElement('head', '');
	$body = $this->createElement('body', '');

	foreach ($specification as $key => $container) {

	    switch ($key) {

		case 'doctype':
		    break;

		case 'html':
		    break;

		case 'head':
		    foreach ($container as $name => $value) {
			$this->createElementRecursion($head, $name, $value);
		    }
		    break;

		case 'body':
		    foreach ($container as $name => $value) {
			$this->createElementRecursion($body, $name, $value);
		    }
		    break;
	    }
	}
	
	$html->appendChild($head);
	$html->appendChild($body);
	
	// First up the Fabrication Development (IDE)
	// $this->createFabricationIDE(); 
                                
	$this->appendChild($html);
	
	return $this;
    }


    function innerHtml($node) {

	$result = $this->saveXML($node);

	foreach ($node->childNodes as $child) {
	    $result.= $child->ownerDocument->saveXML($child);
	}

	return $result;
    }
    
    /**
     * Template method to create HTML from the current engine DOM and data array.
     * 
     * @param type $path
     * @param type $data
     * @return type 
     */
    public function template($element, $data, $weld = array()) {
	
	if (count($data) > 0) {
	    
//	    var_dump(get_class($element));
//	    var_dump($element->tagName);
//	    var_dump($element->nodeName);
//	    var_dump($element->nodeValue);
//	    var_dump($element->attributes->getNamedItem('id')->nodeName);
//	    var_dump($element->attributes->getNamedItem('id')->nodeValue);
//	    var_dump($element->childNodes->item(0)->attributes->getNamedItem('id')->nodeValue);
//
//$test = $element->cloneNode(true);
//
//print 'Container: ('.$element->attributes->getNamedItem('id')->nodeName.
//    '|'.$element->attributes->getNamedItem('id')->nodeValue.")\n";

$container = $this->create('div', '', array('id'=>$element->attributes->getNamedItem('id')->nodeValue));

// DATA = id, title, content
foreach($data as $key => $value) {

    
    foreach ($element->childNodes as $key => $child) {

	$nodeName = $child->nodeName;
	$nodeValue = $child->nodeValue;

	if ($nodeName == '#text') { continue; }
	
	$id = $child->attributes->getNamedItem('id')->nodeName;
	$idValue = $child->attributes->getNamedItem('id')->nodeValue;
	
	//var_export($value);
	
	if (in_array($idValue, array_keys($value)) ) {
	    
//	    if ($idValue == $data) {
		//print 'NODE: name='.$nodeName.' value='.$nodeValue."\n";
		print "\nID:".$id.' VALUE:'.$idValue."\n";
		var_export($value);

		$node = $this->create($nodeName, $value[$idValue], array($id=>$idValue.'_'.$key));
		$container->appendChild($node);
//	    }
	}
    }
    
    print "--------\n";
}
//die;

//return $this->innerHtml($element);
return $this->innerHtml($container);

//	    $template = new FabricationEngine();
//
//	    $container = $template->create(
//		$element->nodeName, 
//		$element->nodeValue, 
//		array('id'=>$element->attributes->getNamedItem('id')->nodeValue)
//	    );
//	    
//	    foreach($data as $key => $value) {
//		
//		var_dump($value['id']);
//		var_dump($value['title']);
//		var_dump($value['content']);
//		
//		foreach($element->childNodes as $elementChild) {
//		    
//		    if ($elementChild->nodeName == '#text') { continue; }
//		    
//		    print 'DataKey: '.$key."\n";
//		    print 'Name: '.$elementChild->nodeName ."\n";
//		    print 'Value: '.$child->nodeValue ."\n";		    
//		    var_dump($elementChild->attributes->getNamedItem($weld[0])->nodeName);
//		    var_dump($elementChild->attributes->getNamedItem($weld[0])->nodeValue);
//		}
//		
//		//var_dump($value);
//die;
//	    }
//
//	    var_dump($container->__asString());
//	    die;
//	    
//	    $template->appendChild($container);
//	    
//	    return $template->view();

	}
	return false;
    }
    
    /**
     * View the DOMTree in HTML either in full or search using XPath for the 
     * first argument, also trim, return and change the output type, html, xml.
     * 
     * @param type $path
     * @param type $trim
     * @param type $return
     * @param type $type
     * @return type 
     */
    public function view($path = '', $trim = true, $return = true, $type = 'html') {
	
	$this->setUp();
    
	$buffer = '';
	if (!empty($path)) { 

	    $results = $this->query($path);
	    
	    // create an empty template object for xpath query results.
	    $template = new FabricationEngine();
	    foreach($results as $result) {
		$node = $template->importNode($result, true);
		$template->appendChild($node);
	    }
	    
	    if ($trim) {
		$buffer = trim($template->saveHTML());
	    } else {
		$buffer = $template->saveHTML();
	    }

	} else {
	    
	    if ($trim) {
		$buffer = trim($this->saveHTML());
	    } else {
		$buffer = $this->saveHTML();
	    }
	}
	
	if ($return) {
	    return $buffer;
	}
	
	print $buffer;
    }
    
    /**
     * Main XPath query method with some basic sanity checking.
     * 
     */
    public function query($path) {

	if ($path) {
	    //var_dump($path); die;
	    return $this->xpath->query($path);
	}
	return false;
    }
    
    /**
     *
     * @param type $line 
     */
    public function debug($line) {
	
	if (self::$debug) {
	    file_put_contents('/tmp/fabric.txt', $line."\n", FILE_APPEND);
	}
	
    }
    
    /**
     * Input key pair value into the input array.
     *
     */
    public function input($key, $value, $meta=array()) {

	$this->input[$key] = $value;
//	$this->input_meta[$key] = $meta;
	return true;
    }

    /**
     * Output key value from the input array.
     * 
     * Generator option enabled on this output method currently outputs, php
     * code only but css, and javascipt coming soon then python ...
     * 
     * 
     * Two vectors on the option string to switch on.
     * 1) Language: php, css, javascript, python, ruby, bash bring it!
     * 2) Language types, class, function array, string.
     * 3) Class types, from loaded classes.
     * 
     * Generation subsystem needed ?? or just code smarter ??
     * 
     * generic language reserved variables.
     *
     */
    public function output($key, $query='', $options=array(), $output='') {

	if ($key == '' || array_key_exists($key, $this->input)) {

	    // ensure key based retrievals are returned first/fast if empty query.
	    if (empty($query)) {
		return $this->input[$key];
	    }

	    // setup standard options.
	    if (empty($options)) {
		$options = array(
		    'return' => true,
		    'tags' => true,
		    'echo' => false, // TODO echo of each variable, objects __toString.
		);
	    }

	    $query_parts = explode('.', $query);
	    $language = $query_parts[0];
	    $template = $query_parts[1];
	    $result = '';

	    switch ($language) {
		case 'php':
		    /**
		     * PHP 
		     * Language generation is all done in one switch based 
		     * loosely on php datatypes, template, class, array, string
		     * need to implement rest.
		     * 
		     */
		    switch ($template) {
			case 'template':
			    foreach ($this->input as $k => $v) {
				if ($key !== '' && $key !== $k) {
				    continue;
				}
				$result.=$v . ";\n";
			    }
			    break;
			case 'class':
			    // construction.
			    if (array_key_exists('class', $options)) {
				if ($options['class']) {
				    $stereotype = '';
				    if (array_key_exists('class.stereotype', $options)) {
					$stereotype = 'extends ' . $options['class.stereotype'] . ' ';
				    }
				    $class = $options['class'];
				    $result.='class ' . $class . " $stereotype{\n";
				    foreach ($this->input as $k => $v) {
					if ($key !== '' && $key !== $k) {
					    continue;
					}
					if (array_key_exists('tabs', $options)) {
					    $result.="\t";
					}
					$result.='public $' . $k . "=" . var_export($v, true) . ";\n";
				    }
				    if (array_key_exists('class.methods', $options)) {
					foreach ($options['class.methods'] as $k => $v) {
					    $parameters = '';
					    foreach ($v['parameters'] as $kk => $vv) {
						$parameters.='$' . $kk . '=' . var_export($vv, true) . ',';
					    }
					    $parameters = trim($parameters, ',');
					    if (array_key_exists('tabs', $options)) {
						$result.="\t";
					    }
					    $result.='public function ' . $k . '(' . $parameters . ') {' . "\n";
					    foreach ($v['code'] as $kk => $vv) {
						if (array_key_exists('tabs', $options)) {
						    $result.="\t\t";
						}
						if (empty($vv)) {
						    $result.="\n";
						} else {
						    $result.=$vv . ";\n";
						}
					    }
					    if (array_key_exists('tabs', $options)) {
						$result.="\t";
					    }
					    $result.='}' . "\n";
					}
				    }
				    $result.="}\n";
				}
			    } else {
				$result.='$data=new stdClass;' . "\n";
			    }
//                            // assignment.
//                            if (array_key_exists('class', $options)) {
//                                if  ($options['class']) {
//                                    $class=$options['class'];
//                                    foreach($this->input as $k => $v) {
//                                        if ($key !== '' && $key !== $k) { continue; }
//                                        $result.='$object'.$class."->".$k."=".var_export($v,true).";\n";
//                                    }
//                                }
//                            } else {                                
//                                foreach($this->input as $k => $v) {
//                                    if ($key !== '' && $k !== $k) { continue; }
//                                    $result.='$data->'.$k.'='.var_export($v,true).";\n";
//                                }
//                            }
//                            // echo, implementation.
//                            if (array_key_exists('echo', $options) && array_key_exists('class', $options)) { 
//                                if ($options['echo'] === true) {
//                                    $class=$options['class'];
//                                    $result.='echo $object'.$class.';';
//                                }
//                            }
			    break;
			case 'array':
			    // construction.
			    $result.='$data=array(' . "\n";
			    foreach ($this->input as $k => $v) {
				if ($key !== '' && $key !== $k) {
				    continue;
				}
				$result.="'$k'=>" . var_export($v, true) . ",\n";
			    }
			    $result.=");\n";
			    // echo, implementation.
			    if (array_key_exists('echo', $options) && array_key_exists('class', $options)) {
				if ($options['echo'] === true) {
				    $result.='echo $data' . $class . ';';
				}
			    }
			    break;
			case 'string':
			    // construction.
			    foreach ($this->input as $k => $v) {
				if ($key !== '' && $key !== $k) {
				    continue;
				}
				$result.='$' . $k . '="' . $v . '";' . "\n";
			    }
			    // echo, implementation.
			    if (array_key_exists('echo', $options)) {
				if ($options['echo'] === true) {
				    foreach ($this->input as $kk => $vv) {
					if ($key !== '' && $key !== $kk) {
					    continue;
					}
					$result.='echo $' . $kk . ';' . "\n";
				    }
				}
			    }
			    break;
		    }
		    // option :: language tags.
		    if (array_key_exists('tags', $options)) {
			if ($options['tags'] === true) {
			    $output = "<?php\n$result?>";
			} else {
			    $output = $result;
			}
		    } else {
			$output = $result;
		    }
		    break;

		case 'css':
		    /**
		     *  Generate CSS structure.
		     * 
		     *  section, or, sections {
		     *    name1: value1;
		     *    name2: value2;
		     *  }
		     * 
		     *  section, or, sections {
		     *    name1: value1;
		     *    name2: value2;
		     *  }
		     */
		    if (array_key_exists('header', $options)) {
			if ($options['header'] === true) {
			    $result.="/**\n";
			    $result.=" * CSS Generated by the FabricationEngine.\n";
			    $result.=" */\n";
			}
		    }
		    foreach ($this->input as $k => $v) {
			if ($key !== '' && $key !== $k) {
			    continue;
			}
			$result.=$k . " {\n";
			if (is_array($v))
			    foreach ($v as $kk => $vv) {
				$result.=$kk . ': ' . $vv . ";\n";
			    }
			$result.="}\n";
		    }
		    $output = $result;
		    break;

		case 'javascript':
		    /**
		     * JAVASCRIPT 
		     * This could get interesting.
		     * 
		     * Simplest example.
		     * 
		     * <script type="text/JavaScript">
		     *   $(document).ready(function(){
		     *     $("#generate").click(function(){
		     *       $("#quote p").load("ajax_script.php");
		     *     });
		     *   });
		     * </script>
		     * 
		     */
		    if (array_key_exists('class.methods', $options)) {
			foreach ($options['class.methods'] as $k => $v) {
			    $result.=$k;
			    foreach ($v['code'] as $line) {
				$result.=$line . ";\n";
			    }
			}
		    }

		    // option :: language tags.
		    if (array_key_exists('tags', $options)) {
			if ($options['tags'] === true) {
			    $output = "<script>\n$result</script>\n";
			} else {
			    $output = $result;
			}
		    } else {
			$output = $result;
		    }
		    break;

		case 'python':
		    // PYTHON
		    break;
	    }
	}

	//file_put_contents('/tmp/fabrication.out', $output);

	if (array_key_exists('return', $options)) {
	    if ($options['return']) {
		return $output;
	    }
	} else {
	    return false;
	}
    }
    
    /**
     * Magic method for handling specification and helper based method these 
     * each method has a configuration array for the helper xpath query. 
     * 
     * @param type $method
     * @param type $args
     * @return type 
     */
    public function __call($method, $args) {
	
	//print "Method:$method \n" . var_export($args); die;
	$this->setUp();
	
	$helpers = array(
	    'gethtml'			=> array('path'=>'/html'),
	    'getheadings'		=> array('path'=>'//h1|//h2|//h3|//h4|//h5|//h6'),
	    'getlinkwithimage'		=> array('path'=>'//a//img'),
	    'getlinkrelalternatehref'	=> array('path'=>'//link[@rel="alternate"]/@href'),
	    // div helper group.
	    'getdivswith'		=> array('path'=>'//div[@$element]'),	// TESTING
	    'getdivswithid'		=> array('path'=>'//div[@id]'),
	    'getdivswithclass'		=> array('path'=>'//div[@class]'),
	    'getdivswithstyle'		=> array('path'=>'//div[@class]'),
	    // image helper group.
	    'getimagewithalttag'	=> array('path'=>'//img[@alt]'),
	    'getimagewithoutalttag'	=> array('path'=>'//img[not(@alt)]'),
	    // TESTING preg replace
	    //'getdivswith'		=> array('pattern'=>'^(\w+)', 'replacement'=>'//div[@($1)]'),
	    //'getallwith'		=> array('pattern'=>'^(\w+)', 'replacement'=>'//*[@($1)]'),
	    //'getallwith'		=> array('pattern'=>'^(\w+)', 'replacement'=>'//*[@id="($1)"]'),
	);
	


	$arg_string = '';
	$arg_container = array();	    
	if (count($args) > 0 ) {
	    
	    foreach($args as $key => $arg) {
		
		if (preg_match('/^([a-zA-Z]{1})/', $arg, $matches)) {
		    $arg_container[]=$arg;
		} else {
		    $arg_string.=$arg;
		}
	    }
	}
	
	//
	// Change specification depending on doctype.
	//

	// GETTERS
	if (preg_match('/^get(.*)/', $method, $matches)) {
	    
	    $method = strtolower($method);
	    
	    $find = preg_replace('/^get/', '', $method);
	    
	    if (array_key_exists($find, $this->specification[$this->getOption('doctype')]) ) {
		$path = '//'.$find.$arg_string;
		return $this->query($path);
	    }
	    
	    // getter helpers.
	    $find = $method;
	    if (array_key_exists($find, $helpers) ) {
		
		if (array_key_exists('path', $helpers[$find])) {
		    $path = $helpers[$find]['path'].$arg_string;
		} else {
		    $path = $find.$arg_string;
		}
		return $this->query($path);
	    } else {
		
		die("\n[FAIL] __call Unknown method: $method Find:$find\n");
		return false;
	    }
	}
	
	// SETTERS
	if (preg_match('/^set(.*)/', $method, $matches)) {
	}
    }

    /**
     * 
     * 
     */
    public function setElementById($id, $value) {

	return $this->query("//*[@id='$id']")->item(0)->nodeValue = "$value";
    }

    /**
     * Setter for changing a element  
     * 
     */
    public function setElementBy($element, $value, $nodeValue) {

	$this->dump($this->query("//*[@$element='$value']"));

	return $this->query("//*[@$element='$value']")->item(0)->nodeValue = "$nodeValue";
    }

    /**
     * 
     * 
     */
    public function setHtml($q, $value) {

	$this->getHtml($q)->item(0)->nodeValue = "$value";
	return $this;
    }
}