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
 * For examples see tests/ as these will become the living documentation.
 * 
 * <code>
 * 
 * $engine = new FabricationEngine();
 * $engine->input('hello', 'world');
 * 
 * print $engine->output('hello');
 * 
 * // You can also use output templates, php strings, arrays, classes and more.
 * print $engine->output('hello', 'php.string');
 * print $engine->output('hello', 'php.array');
 * 
 * // Generates: class Hello { public $hello='world'; }
 * print $engine->output('hello', 'php.object');
 * 
 * </code>
 * 
 * @author David Stevens <mail.davro@gmail.com>
 * @license http://www.gnu.org/copyleft/lgpl.html
 * 
 */

class FabricationEngine extends \DOMDocument {
	/**
	 * Symbol for id attribute assignment.
	 * 
	 */
	const symbol_id ='#';

	/**
	 * Symbol for class attributes assignment.
	 * 
	 */
	const symbol_class ='.';

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
	 * Output container.
	 * 
	 * @var type 
	 */
	public $output = array();

	/**
	 * Options 
	 *
	 * doctype                  Choose your doctype
	 * process                  Clean up DOMDocument tardedness.
	 * process.doctype          Doctype depending on doctype option.
	 * process.body.image       Image tags valid depending on doctype option.
	 * process.body.br          br tags to be valid depending on doctype option.
	 * process.body.hr          hr tags to be valid depending on doctype option.
	 * process.body.a           A tags and match a tags with #name to #anchors.
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
	 * Current html object.
	 * 
	 * @var string
	 */
	public $html;

	/**
	 * Control for automatic fixing of DOMDocument bugs before output.
	 *
	 * @var boolean		True process and clean output, False do nothing. 
	 */
	private $outputProcess = false;
	
	// house keeping needed for these.
	public $head;
	public $title = 'FABRIC';
	public $body;
	private $styles = array();
	private $scripts = array();
	private $metas;
	private $document;

	/**
	 * Main setup function for the Fabrication Engine.
	 * 
	 * Examples from http://www.w3.org/QA/2002/04/valid-dtd-list.html
	 */
	public function __construct($version = '1.0', $encoding = 'utf-8') {

		parent::__construct($version, $encoding);

		//spl_autoload_register(array('\Fabrication\Library\FabricationEngine', 'autoloader'));
		spl_autoload_register(array($this, 'autoloader'));

		// default pattern html, this can be changed at runtime, xml, javascript ...
		$this->pattern = $this->pattern('Html');
	}


	// TODO Experimental 
	private function autoloader($class = NULL) {

		// this array will be pulled from cache/autoload.fabric
		$cache = array(
			'Fabrication\Library\Pattern\Xml'			=> 'xml/xml.php',
			'Fabrication\Library\Pattern\XmlTest'		=> 'xml/test.php',
			'Fabrication\Library\Pattern\Html'			=> 'html/html.php',
			'Fabrication\Library\Pattern\HtmlTable'		=> 'html/table.php',
			'Fabrication\Library\Pattern\HtmlForm'		=> 'html/form.php',
			'Fabrication\Library\Pattern\HtmlCanvas'	=> 'html/canvas.php'
		);

		if (isset($cache[$class])) {
			include_once('pattern/' . $cache[$class]);

			return true;
		}
		return false;
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
		return $this->options[$key];
	}

	/**
	 * Getter for returning the current doctype output <DOCTYPE...
	 *
	 * @return string
	 */
	public function getDoctype() {

		return $this->pattern->doctypes[$this->getOption('doctype')];
		//return $this->pattern->doctypes[$this->pattern->doctype];
	}


	// Experimental, signature will change!
	public function getSpecification($element = '') {

		if (isset($element) && $element !== '') {
			$spec = $this->pattern->specification[$this->getOption('doctype')][$element];
		}  else {
			$spec = $this->pattern->specification[$this->getOption('doctype')];

		}
		//array_flip($spec);
		//var_dump($spec);
		return $spec;
	}


	/**
	 * Register a prefix and uri to the xpath namespace.
	 * 
	 * @param type $prefix
	 * @param type $uri
	 */
	public function registerNamespace($prefix, $uri) {

		$this->setUp();
		$this->xpath->registerNamespace($prefix, $uri);
	}

	/**
	 * Setup DOMDocument and XPath and stuff.
	 * 
	 */
	public function setUp($options = array()) {

		//libxml_clear_errors();
		// 
		// DOMDocument options.
		//$this->formatOutput = true;			// TESTING make optional.
		//$this->preserveWhiteSpace = false;	// TESTING
		//$this->resolveExternals = true;		// TESTING
		//$this->substituteEntities = false;	// TESTING
		$this->recover = true;					// TESTING
		$this->strictErrorChecking = false;		// TESTING

		$this->xpath = new \DomXpath($this);
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
	public function run($data = '', $load = 'string', $type = 'html') {

		//libxml_use_internal_errors(true);

		if (!empty($data)) {

			switch ($type . '.' . $load) {

				case 'html.string':
					// make error suppression optional.
					if (@$this->loadHTML($data)) {
						// success.
						$this->pattern = $this->pattern('Html');
					} else {
						//print "[FAIL] problem loading html string.\n";
						return false;
					}
					break;

				case 'html.file':
					// make error suppression optional.
					if (@$this->loadHTMLFile($data)) {
						// success.
						//$this->pattern = $this->pattern('HtmlFile');
					} else {
						return false;
					}
					break;

				case 'xml.string':
					// make error suppression optional.
					if (@$this->loadXML($data)) {
						// success.
						$this->pattern = $this->pattern('Xml');
					} else {
						return false;
					}
					break;
			}
		}

		// TODO extend symbol mapping loop.
		foreach ($this->input as $key => $value) {

			$id = substr($key, 0, 1);
			if ($id == self::symbol_id) {
				$this->setElementById(
					str_replace(self::symbol_id, '', $key)
					, $value
				);
			}

			$class = substr($key, 0, 1);
			if ($class == self::symbol_class) {
				$this->setElementBy('class'
					, str_replace(self::symbol_class, '', $key)
					, $value
				);
			}
		}

		return true;
	}

	/**
	 * Return the engine output html view.
	 * 
	 * @deprecated
	 */
	public function outputHTML() {

		$this->output['raw'] = parent::saveHTML();
		$this->outputProcess();

		return $this->getDoctype('doctype') . $this->output['raw'];
	}

	/**
	 * Return the engine output xml view.
	 * 
	 * @deprecated
	 */
	public function outputXML() {

		$this->output['raw'] = $this->saveXML();
		$this->outputProcess();

		return $this->output['raw'];
	}

	/**
	 * Extend the native saveHTML method.
	 * Allow path search functionality.
	 * 
	 * @param	string	$path	Output file path.
	 * @param	boolean	$trim	Trim the output of surrounding space.
	 * @return	string
	 */
	public function saveHTML($path = '', $trim = true) {

		if ($path !=='') {
			// no processing as just return xpath html result no adding doctype.
			$this->output['raw'] = $this->view($path);
		} else {
			$this->output['raw'] = parent::saveHTML();
		}
		
		$this->outputProcess();
		
		$raw = $this->output['raw'];
		//$raw = $this->getDoctype('doctype') . $this->output['raw'];
		
		return $trim ? trim($raw) : $raw;
	}

	/**
	 * Return the engine output html view.
	 * 
	 * styles
	 * scripts 
	 * 
	 */
	public function saveFabric($type = 'html') {

		switch($type) {
			case 'html':
				$this->output['raw'] = parent::saveHTML();
				break;

			case 'xml':
				$this->output['raw'] = parent::saveXML();
				break;
		}

		// default output process.
		$this->outputProcess();

		// TODO style the output, bundle all the elements styles to fabric.css
		$this->bundleElementStyles();

		// TODO script the output, bundle all the elements scripts, to fabric.js
		$this->bundleElementScripts();

		return $this->getDoctype() . $this->output['raw'];
	}

	
	public function bundleElementStyles() {
		return;
	}

	public function bundleElementScripts() {
		return;
	}

	/**
	 * Time to sort out the plethora of DOMDocument bugs.
	 * 
	 * TODO Styles create compiled CSS file, insert into reference into the head.
	 * TODO Script create compiled JS file, insert into the page footer.
	 * TODO Both should be configurable using options.
	 */
	public function outputProcess() {

		// remove doctype.
		$this->output['raw'] = preg_replace(
			'/^<!DOCTYPE[^>]+>/U', 
			'', 
			$this->output['raw']
		);
		
		if ($this->outputProcess && $this->getOption('process')) {
			
			// Remove doctype added by DOMDocument (hacky)
			$this->output['raw'] = $this->getDoctype().$this->output['raw'];
		
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
				$this->output['raw'] = preg_replace(
					'/<img(.*)>/sU', 
					'<img\\1 />', $this->output['raw']
				);
			}

			if ($this->getOption('process.body.br')) {
				$this->output['raw'] = preg_replace(
					'/<br(.*)>/sU', 
					'<br\\1 />', 
					$this->output['raw']
				);
			}

			if ($this->getOption('process.body.hr')) {
				$this->output['raw'] = preg_replace(
					'/<hr(.*)>/sU', 
					'<hr\\1 />', 
					$this->output['raw']
				);
			}

			// TODO Styles add configurable reference into the header.
			if ($this->getOption('process.style')) {
			}
			// TODO Scripts add configurable refernce into the footer.
			if ($this->getOption('process.script')) {
			}

			// Trim whitespace need this to get exactly the wanted data back for test cases, mmm.
			$this->output['raw'] = trim($this->output['raw']);
		
			return $this->outputProcess;
		}
		return $this->outputProcess;
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

		$result = '';
		
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
					$result.="Method:\t" . $method . $line_end;
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

				default: $result.= var_export($data, true);
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
	 * Create elements with attributes and children.
	 * 
	 * @param type $name
	 * @param type $value
	 * @param type $attributes
	 * @param type $children
	 * @return type 
	 */
	public function create($name, $value = '', $attributes = array() , $children = array(), 
		$styles = array(), $scripts = array()) {

		try {

			$element = $this->createElement($name, $value);

			if (count($attributes) > 0) {
				foreach ($attributes as $key => $value) {
					$element->setAttribute($key, $value);
				}
			}		

			if (count($children) > 0) {
				foreach ($children as $child) {
					$element->appendChild(
						$this->create($child['name'],	
							@$child['value'], 
							@$child['attributes'], 
							@$child['children']
						)
					);
				}
			}

			/**
			 * Styles and Scripts.
			 * When page is rendered the default behaviour is to load the CSS at
			 * the top of the page and any javascript at the bottom of the page.
			 * This behaviour should be configurable.
			 * Styles and Scripts will be compiled into fabric.css, fabric.js 
			 * on save. 
			 *
			 * Basic template concept.
			 * <html>
			 *   <head>
			 *   <!-- content -->
			 *   <link type="text/css" rel="stylesheet" href="fabric.css" media="all" />
			 *   </head>
			 *
			 *   <body>
			 *   <!-- content -->
			 *   <script type="text/javascript" src="fabric.js"></script>
			 *   </body>
			 * </html>
			 */
			if (count($styles) > 0) {
				foreach($styles as $style) {
					$this->styles[] = $style;
				}
			}

			if (count($scripts) > 0) {
				foreach($scripts as $script) {
					$this->scripts[] = $script;
				}
			}

			return $element;

		} catch(\Exception $e) {
			die('Exception: ' . $e->getMessage());
		}
	}

	// TODO
	public function getStyles() {

		return $this->styles;
	}

	// TODO
	public function getScripts() {

		return $this->scripts;
	}

	// TODO 
	public function checkElementAttribute($nodeName, $attribute) {

		$specification = $this->engine->getSpecification();
	}

	/**
	 * Recursion method for creating and appending elements from a configuration 
	 * array.
	 * 
	 *
	 * @param \DOMElement $element
	 * @param type $name
	 * @param FabricationElement $value 
	 */
	public function createElementRecursion(\DOMElement $element, $name, $value) {

		if (is_string($value)) {
			$element->appendChild($this->createElement($name, $value));
		}

		if (is_array($value)) {

			if (array_key_exists('name', $value)) {
				$newElement = $this->create($value['name'], 
					@$value['value'],
					@$value['attributes'],
					@$value['children']
				);

				$element->appendChild($newElement);
			}

			if (@is_array($value[0])) {
				foreach ($value as $k => $v) {
					$this->createElementRecursion($element, $k, $v);
				}
			}
		}

		if (is_object($value)) {
			if ($value instanceof FabricationElement) {
				$value->execute($this->getEngine(), $element);
			}
		}
	}


	/**
	 * Pattern method for 
	 * 
	 * @param  string	$name	Object name to instantiate.
	 * @return object			Product object.
	 */ 
	public function pattern($name = 'Html') {

		// create the object name with the namespace.
		$objectName = 'Fabrication\Library\Pattern\\' . $name;

		$product = new $objectName($this);
		// product map data.

		return $product;
	}


	/**
	 * Document specification pattern.
	 * 
	 * 
	 */
	public function specification($pattern = array(), $contract = 'html',  
		$elements = array('head', 'body')
		) {

		// create the root specification element.
		$assent = $this->createElement($contract, '');

		// create the root layout sections.
		$sections = array();
		foreach ($elements as $element) {

			// ensure each section
			if (!array_key_exists($element, $pattern)) {
				die('Specification pattern is missing a key section ' . $element);
			}

			$sections[$element] = $this->createElement($element, '');
		}

		// create the elements in the pattern.
		foreach ($pattern as $key => $container) {

			if (isset($sections[$key]) ) {

				foreach ($container as $name => $value) {

					$this->createElementRecursion($sections[$key], $name, $value);
				}
			}
		}

		// append all the sections to the root product.
		foreach (array_keys($sections) as $section) {
			$assent->appendChild($sections[$section]);
		}

		// append the product to the engine.
		$this->appendChild($assent);

		// current context.
		return $this;
	}


	/**
	 * Template method allows for an element and its children to be used as the 
	 * pattern for an array dataset, the default map for the element children 
	 * atrribute is 'id'
	 *
	 * @param mixed			$pattern	String or DOMElement
	 * @param array			$dataset	Dataset to template.
	 * @param string		$map		Identifier.
	 * @return DOMElement
	 */
	public function template($pattern, $dataset = array(), $map = 'id') {

		//$this->outputProcess = false;
		
		if (sizeof($dataset) == 0) { return false; }

		if (is_string($pattern)) {
			
			$engine = new FabricationEngine();
			$engine->loadXML($pattern);
			
			//$template = $engine->query('/*')->item(0);
			//$template = $engine->query('//*')->item(0);
			$template = $engine->getDiv()->item(0);
			
			if (!$template instanceof \DOMElement) {
				throw new \Exception(
					'First div item should be an instance of the DOMElement.'
				);
			}
		}

		if ($pattern instanceof \DOMElement) {
			$template = $pattern;
		}

		// create an empty container, from the template node details.
		$container = $this->create($template->nodeName, $template->nodeValue);

		foreach ($dataset as $key => $row) {

			// process the template child nodes.
			foreach ($template->childNodes as $child) {

				if ($child->nodeName == '#text') {	
					//var_dump($child->nodeValue); die;
					continue;
				}

				if (is_object($child->attributes->getNamedItem($map))) {

					$mappedName  = $child->attributes->getNamedItem($map)->nodeName;
					$mappedValue = $child->attributes->getNamedItem($map)->nodeValue;

					$nodeAttributes = array();
					foreach($child->attributes as $attribute) {
						$nodeAttributes[$attribute->nodeName] = $attribute->nodeValue;
					}

					if (in_array($mappedValue, array_keys($row))) {

						// create the mapped node attribute with updated numeric key.
						$nodeAttributes[$mappedName] = $mappedValue.'_'.($key + 1);
						
						// fabricate the new child nodes.
						$node = $this->create($child->nodeName,
							$row[$mappedValue], $nodeAttributes
						);
						
						$container->appendChild($node);
					}
				}
			}
		}
		
		return $container;
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
		
		$buffer = '';
		if (!empty($path)) {

			$results = $this->query($path);

			// create an empty template object for xpath query results.
			$template = new FabricationEngine();
			foreach ($results as $result) {
				
				$node = $template->importNode($result, true);
				$template->appendChild($node);
			}

			// string buffer.
			if ($trim) {
				$buffer = trim($template->saveHTML());
			} else {
				$buffer = $template->saveHTML();
			}
			
		} else {

			// string buffer
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
	 * Main XPath query method.
	 * 
	 */
	public function query($path) {

		$this->setUp();
		
		if ($path) {
			//var_dump($path); die;
			return $this->xpath->query($path);
		}
		return false;
	}

	/**
	 * Debug method for writing to disk.
	 *
	 * @param type $line 
	 */
	public function debug($line) {

		if (self::$debug) {
			file_put_contents('/tmp/fabric.txt', $line . "\n", FILE_APPEND);
		}
	}

	/**
	 * Input key pair value into the input array.
	 *
	 */
	public function input($key, $value, $meta=array()) {

		$this->input[$key] = $value;
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
	public function output($key = '', $query='', $options=array(), $output='') {

		//if ($key == '' || array_key_exists($key, $this->input)) {

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

		// Experimental
		$output = $this->templateTextElement($key, $query, $options);
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

		$helpers = array(
			'gethtml'					=> array('path' => '/html'),
			'getheadings'				=> array('path' => '//h1|//h2|//h3|//h4|//h5|//h6'),
			'getlinkwithimage'			=> array('path' => '//a//img'),
			'getlinkrelalternatehref'	=> array('path' => '//link[@rel="alternate"]/@href'),
			
			// div helper group.
			'getdivswith'				=> array('path' => '//div[@$element]'), // TESTING
			'getdivswithid'				=> array('path' => '//div[@id]'),
			'getdivswithclass'			=> array('path' => '//div[@class]'),
			'getdivswithstyle'			=> array('path' => '//div[@class]'),
			
			// image helper group.
			'getimagewithalttag'		=> array('path' => '//img[@alt]'),
			'getimagewithoutalttag'		=> array('path' => '//img[not(@alt)]'),
			
			// TESTING preg replace
			//'getdivswith'		=> array('pattern'=>'^(\w+)', 'replacement'=>'//div[@($1)]'),
			//'getallwith'		=> array('pattern'=>'^(\w+)', 'replacement'=>'//*[@($1)]'),
			//'getallwith'		=> array('pattern'=>'^(\w+)', 'replacement'=>'//*[@id="($1)"]'),
		);

		// process arguments.
		$arg_string = '';
		$arg_container = array();
		if (count($args) > 0) {
			
			// check the args and collect info for xpath conversion.
			foreach ($args as $key => $arg) {

				switch (true) {

					case is_string($arg):
						if (preg_match('/^([a-zA-Z]{1})/U', $arg, $matches)) {
							$arg_container[] = $arg;
						} else {
							$arg_string.=$arg;
						}
						break;

					case is_array($arg):
						// TODO
						break;
				}
			}
		}

		//
		// Change specification depending on doctype.
		//

		// GETTERS
		if (preg_match('/^get(.*)/U', $method, $matches)) {

			$method = strtolower($method);

			$find = preg_replace('/^get/U', '', $method);

			// return the 
			$doctype = $this->pattern->specification[$this->getOption('doctype')];
			if (array_key_exists($find, $doctype)) {
				$path = '//' . $find . $arg_string;
				return $this->query($path);
			}

			// getter helpers.
			$find = $method;
			if (array_key_exists($find, $helpers)) {

				if (array_key_exists('path', $helpers[$find])) {
					$path = $helpers[$find]['path'] . $arg_string;
				} else {
					$path = $find . $arg_string;
				}
				return $this->query($path);
			} else {

				die("\n[FAIL] __call Unknown method: $method Find:$find\n");
				return false;
			}
		}

		// SETTERS
		if (preg_match('/^set(.*)/U', $method, $matches)) {
			
			var_dump("Method: $method");
			var_dump("ARGS: ".var_export($args,true));
			
			die("__CALL TESTING\n");
		}
	}

	// TODO convert and element into html.
	public function innerHTML(DOMElement $element) {	
		//
	}
	
	// Setters eventually put in __call
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

		//$this->dump($this->query("//*[@$element='$value']"));
		return $this->query("//*[@$element='$value']")->item(0)->nodeValue = $nodeValue;
	}

	/**
	 * Setter for changing HTML element.
	 * 
	 */
	public function setHtml($q, $value) {

		$this->getHtml($q)->item(0)->nodeValue = "$value";
		return $this->getHtml($q)->item(0);
	}

	// TESTING
	public function templateTextElement($key, $query, $options) {

			$query_parts = (array)  explode('.', $query);
			$language    = (string) isset($query_parts[0]) ? $query_parts[0] : '';
			$template    = (string) isset($query_parts[1]) ? $query_parts[1] : '';
			$result      = (string) '';

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
								$result = '$data=new stdClass;' . "\n";
								foreach ($this->input as $k => $v) {
									if ($key !== '' && $key !== $k) {
										continue;
									}
									$result.='$data->'.$k."="."'".$v."';\n";
								}

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

						default:

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
					} // if 

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
					 * 
					 * Simplest example line by line ...
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

		return $output;

	}

}
