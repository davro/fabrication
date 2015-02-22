<?php
namespace Fabrication;

/**
 * Fabrication Engine
 * 
 * Document Object Model based template engine without placeholders.
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
 * @method getHtml()
 * @method getHead()
 * @method getBody()
 * @method getDiv()
 * @method getArticle()
 * 
 * @author David Stevens <davro@davro.net>
 * @license http://www.gnu.org/copyleft/lgpl.html
 * 
 */

class FabricationEngine extends \DOMDocument
{
    /**
     * Time the fabrication engine was started.
     */
    protected $timeStarted;

    /**
     * Symbol container for attributes assignment.
     * 
     * @var array
     */
    public $symbols = [
        'id' => '#',
        'class' => '.'
    ];

    /**
     * Input container.
     * 
     * @var	array
     */
    public $input = [];

    /**
     * Output container.
     * 
     * @var	array
     */
    public $output = [];

    /**
     * Document Type.
     * 
     * @var	string
     */
    public $type = '';

    /**
     * XPath object
     * 
     * @var	object
     */
    public $xpath;
    
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
     * @var array		Doctypes avaliable.
     */
    public $options = [
        'doctype' => 'html.4.01.transitional',
        'process' => true,
        'process.doctype' => true,
        'process.body.image' => true,
        'process.body.br' => true,
        'process.body.hr' => true,
        'process.body.anchor' => false,
    ];

    /**
     * Current pattern object.
     * 
     * @var object
     */
    public $pattern;

    /**
     * Control for automatic fixing of DOMDocument bugs before output.
     *
     * @var boolean True process and clean output, False do nothing. 
     */
    private $outputProcess = false;

    // house keeping needed for these variables.
    public $head;
    public $title    = 'FABRIC';
    public $body;
    public $styles   = [];
    private $scripts = [];
    private $views   = [];

    /**
     * Main setup function for the Fabrication Engine.
     * 
     * Examples from http://www.w3.org/QA/2002/04/valid-dtd-list.html
     */
    public function __construct($version = '1.0', $encoding = 'utf-8', $pattern = 'html')
    {
        $this->timeStarted = microtime(true);

        parent::__construct($version, $encoding);

        $objectName = 'Fabrication\\' . ucfirst($pattern);
        $this->pattern = new $objectName($this);
    }

    /**
     * Register a prefix and uri to the xpath namespace.
     * 
     * @param string $prefix The namespace prefix.
     * @param string $uri    The namespace uri.
     */
    public function registerNamespace($prefix, $uri)
    {
        $this->initializeXPath();
        $this->xpath->registerNamespace($prefix, $uri);
    }

    /**
     * DOMDocument and XPath.
     * 
     */
    public function initializeXPath()
    {
        $this->xpath = new \DomXpath($this);
    }

    /**
     * Direct access to the Fabric of the engine.
     *
     * @return object This current FabricationEngine object.
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Getter for accessing an option value by key from the options array.
     * 
     */
    public function getOption($key)
    {
        return $this->options[$key];
    }

    /**
     * Setter for inserting a key value pair into the options array.
     * Once a key value pair has been insert the updateOptions method is executed. 
     * 
     * @return boolean True on success.
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this->options[$key];
    }

    /**
     * Getter for returning the current doctype output <DOCTYPE...
     *
     * @return mixed
     */
    public function getDoctype()
    {
        // Disable any xml doctype for the time being.
        if ($this->type == 'xml') {
            return false;
        }

        return $this->pattern->doctypes[$this->getOption('doctype')];
    }

    /**
     * TESTING Getter for retriving the specification in the current context.
     * 
     * @param string $element
     * 
     * @return string
     */
    public function getSpecification($element = '')
    {
        if (isset($element) && $element !== '') {
            $spec = $this->pattern->specification[$this->getOption('doctype')][$element];
        } else {
            $spec = $this->pattern->specification[$this->getOption('doctype')];
        }
        return $spec;
    }

    /**
     * Getter for retriving the current views.
     * 
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Run method once the all input have been set.
     * Then you will have a valid document with a searchable path.
     *
     * @param string $data
     * @param string $load
     * @param string $type
     * 
     * @return	mixed
     */
    public function run($data = '', $load = 'string', $type = 'html')
    {
        if (!empty($data)) {
            $this->$type = $type;

            // Check if data is a path to a valid file then load into buffer.
            if (file_exists($data)) {
                $pathHash = md5($data);
                $this->views[$pathHash] = $data;
            }

            switch ($load . '.' . $type) {
                default:
                    return false;
                    
                // load string html
                case 'string.html':
                    if ($this->loadHTML($data)) {
                        $objectName = 'Fabrication\\Html';
                        $this->pattern = new $objectName($this);
                        
                        $this->mapSymbols();
                        return true;
                    } else {
                        return false;
                    }
                    
                // load file html 
                case 'file.html':
                    if (@$this->loadHTMLFile($data)) {
                        $this->mapSymbols();
                        return true;
                    } else {
                        return false;
                    }

                // load string xml
                case 'string.xml':
                    if ($this->loadXML($data)) {
                        $this->mapSymbols();
                        return true;
                    } else {
                        return false;
                    }
                    
                // load file xml
                case 'file.xml':
                    $contents = file_get_contents($data);
                    if (@$this->loadXML($contents)) {
                        $this->mapSymbols();
                        return true;
                    } else {
                        return false;
                    }
            }
        }

        return;
    }

    /**
     * Symbol mapper for engine input symbolic values to engine element 
     * attribute values for a basic mapping sub-system.
     * 
     * @todo add functionality for adding removing custom symbols.
     * 
     * @return void
     */
    public function mapSymbols()
    {
        foreach ($this->input as $key => $input) {
            foreach ($this->symbols as $skey => $svalue) {
                if (substr($key, 0, 1) == $svalue) {

                    $keyWithoutSymbol = str_replace($svalue, '', $key);

                    if (is_string($input)) {
                        $this->setElementBy($skey, $keyWithoutSymbol, $input);
                    }
                    if (is_array($input)) {
                        $this->setElementBy($skey, $keyWithoutSymbol, $input);
                    }
                    if (is_object($input)) {
                        $this->setElementBy($skey, $keyWithoutSymbol, $input);
                    }
                }
            }
        }
    }

    /**
     * Extend the native saveHTML method.
     * Allow path search functionality.
     * 
     * @param string  $path Output file path.
     * @param boolean $trim Trim the output of surrounding space.
     * 
     * @return	string
     */
    public function saveHTML($path = '', $trim = true)
    {
        if (is_string($path) && $path !== '') {
            // no processing as just return xpath html result no adding doctype.
            $this->output['raw'] = $this->view($path);
        } else {
            $this->output['raw'] = parent::saveHTML();
        }

        $this->outputProcess();

        $raw = $this->output['raw'];

        return $trim ? trim($raw) : $raw;
    }

    /**
     * Return the engine output html view.
     * 
     * @param string $type  The run type 
     * 
     * @return type
     */
    public function saveFabric($type = 'html')
    {
        switch ($type) {
            case 'html':
                $this->output['raw'] = parent::saveHTML();
                break;

            case 'xml':
                $this->output['raw'] = parent::saveXML();
                break;
        }

        // default output process.
        $this->outputProcess();

        return $this->getDoctype() . trim($this->output['raw']);
    }

    /**
     * Time to sort out the plethora of DOMDocument bugs.
     * 
     * TODO Styles create compiled CSS file, insert into reference into the head.
     * TODO Script create compiled JS file, insert into the page footer.
     * TODO Both should be configurable using options.
     */
    public function outputProcess()
    {
        // remove doctype.
        $this->output['raw'] = preg_replace(
                '/^<!DOCTYPE[^>]+>/U', '', $this->output['raw']
        );

        if ($this->outputProcess && $this->getOption('process')) {

            // Remove doctype added by DOMDocument (hacky)
            $this->output['raw'] = $this->getDoctype() . $this->output['raw'];

            /**
             * Process img, br, hr, tags, change html to xhtml.
             * 
             * <img> <img src=""> <img src="/image.png">
             * Are changed to xhtml
             * <img /> <img src="" /> <img src="/image.png" />
             */
            if ($this->getOption('process.body.image')) {
                $this->output['raw'] = preg_replace(
                        '/<img(.*)>/sU', '<img\\1 />', $this->output['raw']
                );
            }

            if ($this->getOption('process.body.br')) {
                $this->output['raw'] = preg_replace(
                        '/<br(.*)>/sU', '<br\\1 />', $this->output['raw']
                );
            }

            if ($this->getOption('process.body.hr')) {
                $this->output['raw'] = preg_replace(
                        '/<hr(.*)>/sU', '<hr\\1 />', $this->output['raw']
                );
            }

            // Trim whitespace need this to get exactly the wanted data back for test cases, mmm.
            $this->output['raw'] = trim($this->output['raw']);

            return $this->outputProcess;
        }
        return $this->outputProcess;
    }

    /**
     * Return a string representation of the data.
     * 
     * @param mixed   $data    The data to dump
     * @param boolean $return  True return output, False print output
     * 
     * @return	string 
     */
    public static function dump($data, $return = false)
    {
        $result = '';

        if (Fabrication::isCli()) {
            $end = "\n";
        } else {
            $end = "<br />\n";
        }

        if (is_object($data)) {

            $classname = get_class($data);

            $result = str_repeat('-', 80) . $end .
                    "\t" . __METHOD__ . ' Type: ' . gettype($data) . "\tReturn:" . var_export($return, true) . $end .
                    str_repeat('-', 80) . $end .
                    "Object Instance: $classname: $end" .
                    "Object Methods $end";

            $class_methods = get_class_methods($data);
            if (count($class_methods) > 0) {
                foreach ($class_methods as $method) {
                    $result.="\t" . $method . $end;
                }
            } else {
                $result.="No methods found.$end";
            }

            $result.= $end;
            $result.= "Object XPath:$end";
            $result.= $end;

            switch ($classname) {

                case 'DOMAttr':
                    $result.= "DOMAttr XPath: {$data->getNodePath()}$end" .
                            $data->ownerDocument->saveXML($data);
                    break;

                case 'DOMDocument':
                    $result.= "DOMDocument XPath: {$data->getNodePath()}$end" .
                            $data->saveXML($data);
                    break;

                case 'DOMElement':
                    $result.= "DOMElement XPath: {$data->getNodePath()}$end" .
                            $data->ownerDocument->saveXML($data);
                    break;

                case 'DOMNodeList':
                    for ($i = 0; $i < $data->length; $i++) {
                        $result.= "DOMNodeList Item #$i, " .
                                "XPath: {$data->item($i)->getNodePath()}$end" .
                                "{$data->item($i)->ownerDocument->saveXML($data->item($i))}$end";
                    }
                    break;

                default: $result.= var_export($data, true);
            }
        }

        if (is_array($data)) {

            $result = $end .
                $end .
                str_repeat('-', 80) . $end .
                "| DUMP Type:" . gettype($data) . "\tReturn:" . var_export($return, true) . $end .
                str_repeat('-', 80) . $end .
                $end;
        }

        if (is_string($data)) {
            $result = var_export($data, true);
        }

        if (is_int($data)) {
            $result = var_export($data, true);
        }

        if ($return) {
            return $result . $end;
        } else {
            echo $result . $end;
        }
    }

    /**
     * Getter for retriving styles.
     * 
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * Getter for retriving scripts.
     * 
     * @return array
     */
    public function getScripts() 
    {
        return $this->scripts;
    }

    /**
     * Create an element with a value and attributes including a mechanism for 
     * creating children elements recursively.
     * 
     * @param string $name       The name of the element to create.
     * @param string $value      The value to assign to the element.
     * @param array  $attributes The attributes to place into the element.
     * @param array  $children   The children to create within the element.
     * 
     * @return	mixed 	DOMElement on success or boolean false.
     */
    public function create($name, $value = '', $attributes = array(), $children = array()) 
    {
        if ($name == '') {
            return false;
        }

        // Convert value object to a string.
        if (is_object($value)) {
            $value = (string) $value;
        }

        try {

            $doctype = $this->getOption('doctype');

            // Allowed internal types.
            $internalTypes = array(
                'fabric' => ''
                , 'fabrication' => ''
            );

            // Ensure the constant is defined.
            if (defined('TEMPLATE_ENGINE_W3C')) {

                // Only proceed if the constant is correctly set.
                if (TEMPLATE_ENGINE_W3C) {

                    // Check the name is allowed in the selected W3C doctype specification.
                    if (!isset($this->pattern->specification[$doctype]['_body'][$name]) &&
                            !isset($this->pattern->specification[$doctype]['_head'][$name])
                    ) {

                        if (isset($internalTypes[$name])) {
                            //
                            // @TODO Create objectCache subsystem for application unique model objects.
                            // 
                            // Create object from element id attribute name.
                            // Example Fabric{attributeID}
                            //
							if (isset($attributes['id'])) {

                                $uniqueComponent = 'Applications\\Components\\' . ucfirst($attributes['id']);

                                if (class_exists($uniqueComponent)) {

                                    $component = new $uniqueComponent($this, $name, $value, $attributes, $children);

                                    return $component->execute();
                                }
                            }
                        }
                    }
                }
            }

            // Create the DOM element.
            $element = self::createElement($name, $value);

            if (is_array($attributes)) {
                if (count($attributes) > 0) {
                    foreach ($attributes as $key => $value) {
                        if ($key == '') {
                            continue;
                        }
                        $element->setAttribute($key, $value);
                    }
                }
            }
            if (is_object($attributes)) {
                if (count($attributes) > 0) {
                    foreach ($attributes as $key => $domAttr) {
                        if ($key == '') {
                            continue;
                        }
                        $element->setAttribute($key, $domAttr->value);
                    }
                }
            }
            if (count($children) > 0) {
                if (is_array($children)) {
                    foreach ($children as $child) {
                        if (is_object($child) && get_class($child) == 'stdClass') {
                            // import stdClass.
                            $newChild = $this->create(
                                    isset($child->name) ? $child->name : '', isset($child->value) ? $child->value : '', isset($child->attributes) ? $child->attributes : array(), isset($child->children) ? $child->children : array()
                            );
                            $element->appendChild($newChild);
                        }

                        if (is_array($child)) {
                            $newChild = $this->create(
                                    isset($child['name']) ? $child['name'] : '', isset($child['value']) ? $child['value'] : '', isset($child['attributes']) ? $child['attributes'] : array(), isset($child['children']) ? $child['children'] : array()
                            );
                            if (!$newChild) {
                                return;
                            } else {
                                $element->appendChild($newChild);
                            }
                        }
                    }
                }
            }
            return $element;
        } catch (\Exception $e) {
            return('Create :: Exception : ' . $e->getMessage());
        }
    }

    /**
     * Create comment 
     * To prevent a parser error when the comment string contains this character 
     * sequence "--", This will insert a Soft Hyphen in between the two hyphens 
     * which will not cause the parser to error out.
     * 
     * @param string $value
     * 
     * @return object DOMComment
     */
    public function createComment($value)
    {
        // Keep a space either side of the comment.
        $value = ' ' . str_replace('--', '-' . chr(194) . chr(173) . '-', $value) . ' ';

        $comment = parent::createComment($value);

        return $comment;
    }

    /**
     * Pattern method for fabrication-framework standardized patterns.
     * 
     * @param	string	$name							Object $name to instantiate.
     * @param	type	$attributes						Object attributes.
     * @param	type	$data							Object data.
     * @return	object	\Library\Pattern\objectName 
     */
    public function createPattern($name = 'html', $attributes = array(), $data = array())
    {
        $patternName = ucfirst($name);

        $objectName = 'Library\Pattern\\' . $patternName;
        $pattern = new $objectName($this, $attributes, $data);

        return $pattern;
    }

    /**
     * Document specification pattern.
     * 
     * This method is an extension of the create method but for building
     * structures.
     * 
     * Experiment note signature will change
     *
     * @param string $pattern    Html Xml structure pattern.
     * @param string $value      Pattern value.
     * @param array  $attributes Pattern attributes.
     * @param array  $contract	 Pattern children recursion.
     * 
     * @return FabricationEngine 
     */
    public function specification($pattern = 'html', $value = '', $attributes = [], $contract = [])
    {
        if (!is_array($contract)) {
            return;
        }

        // create the root specification element.
        $this->appendChild(
                $this->create($pattern, $value, $attributes, $contract)
        );

        return $this;
    }

    /**
     * Template method allows for an element and its children to be used as the 
     * pattern for an array dataset, the default map for the element children 
     * atrribute is 'id'
     *
     * @param mixed  $pattern String or DOMElement
     * @param array  $dataset Dataset to template.
     * @param string $map     Identifier.
     * 
     * @return mixed
     */
    public function template($pattern, $dataset = array(), $map = 'id')
    {
        if (count($dataset) == 0) {
            return false;
        }

        try {

            $template = '';
                            
            if (is_string($pattern)) {
                $engine = new FabricationEngine();
                $engine->setOption('doctype', 'html.5');
                $engine->loadHTML($pattern);

                $templateDiv = $engine->getDiv();

                if ($templateDiv) {
                    $template = $templateDiv->item(0);
                }
            }

            if (is_object($pattern)) {
                $template = $pattern;
            }

            // Create an empty container, from the template node details.
            if (is_object($template)) {

                $container = $this->create($template->nodeName, $template->nodeValue);

                foreach ($dataset as $key => $row) {

                    // process the template child nodes.
                    foreach ($template->childNodes as $child) {

                        if ($child->nodeName == '#text') {
                            continue;
                        }

                        if (is_object($child->attributes->getNamedItem($map))) {

                            $mappedName = $child->attributes->getNamedItem($map)->nodeName;
                            $mappedValue = $child->attributes->getNamedItem($map)->nodeValue;

                            $nodeAttributes = array();
                            foreach ($child->attributes as $attribute) {
                                $nodeAttributes[$attribute->nodeName] = $attribute->nodeValue;
                            }

                            if (in_array($mappedValue, array_keys($row))) {

                                // create the mapped node attribute with updated numeric key.
                                $nodeAttributes[$mappedName] = $mappedValue . '_' . ($key + 1);

                                // fabricate the new child nodes.
                                $node = $this->create($child->nodeName, $row[$mappedValue], $nodeAttributes
                                );

                                $container->appendChild($node);
                            }
                        }
                    }
                }
                return $container;
            }

            return false;
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * View the DOMTree in HTML either in full or search using XPath for the 
     * first argument, also trim, return and change the output type, html, xml.
     * 
     * @param string  $path   The xpath to the element to view.
     * @param boolean $trim   Trim the returned output string.
     * @param boolean $return Return or Print the output string.
     * 
     * @return	string
     */
    public function view($path = '', $trim = true, $return = true)
    {
        if (!empty($path)) {

            $results = $this->query($path);

            // create an empty template object for xpath query results.
            $template = new FabricationEngine();
            foreach ($results as $result) {
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

        echo $buffer;
    }

    /**
     * Main XPath query method.
     * 
     * @param string $path The xpath query to run on the current DOM
     * 
     * @return mixed 
     */
    public function query($path)
    {
        $this->initializeXPath();

        if ($path) {
            return $this->xpath->query($path);
        }
        return false;
    }

    /**
     * Input key pair value into the input array.
     *
     * @param mixed $key   The key to associate the value with.
     * @param mixed $value The value associated with the key.
     * 
     * @return boolean 
     */
    public function input($key, $value)
    {
        $this->input[$key] = $value;

        return true;
    }

    /**
     * Output key value from the input array.
     * 
     * @param mixed  $key     The key=>value to retrive.
     * @param string $query   Example php.array 
     * @param array  $options Options for the template text element.
     * 
     * @return	mixed
     */
    public function output($key = '', $query = '', $options = array())
    {
        // ensure key based retrievals are returned first/fast if empty query.
        if (empty($query) && isset($this->input[$key])) {
            return $this->input[$key];
        }

        // setup standard options.
        if (empty($options)) {
            $options = array('return' => true, 'tags' => true, 'echo' => false);
        }

        // Experimental, change for the createPattern method.
        $output = $this->templateTextElement($key, $query, $options);

        if (array_key_exists('return', $options)) {
            if ($options['return']) {
                return $output;
            }
        } else {
            return false;
        }
    }

    /**
     * Append element to the html head element of the document.
     * 
     * @param mixed   $element The element to append
     * @param boolean $debug   Optional debug
     */
    public function appendHead($element)
    {
        $this->query('/html/head')->item(0)->appendChild($element);
    }

    /**
     * Helper to allow the import of a html string into the current engine, 
     * without causing DOM hierarchy errors.
     * 
     * This method generate's a temporary engine DOM structure from the data 
     * Will return the body first child, if null the head first child.
     * Then the engine will call importNode using the found node and return the
     * DOM structure.
     * 
     * @param string $data
     * 
     * @return mixed
     */
    public function convert($data)
    {
        $data = trim($data);

        try {

            // Buffer engine used to convert the html string into DOMElements,
            $engine = new FabricationEngine;
            $engine->run($data);

            // Check if the body is null, so use the head if avaliable.
            if ($engine->getBody()->item(0) == null) {

                $node = $engine->getHead()->item(0)->childNodes->item(0);

                return $this->importNode($node, true);
            }

            if ($engine->getBody()->item(0) !== null) {

                // body first item.
                $node = $engine->getBody()->item(0)->childNodes->item(0);

                return $this->importNode($node, true);
            }

            return false;
        } catch (\Exception $e) {

            return('FabricationEngine :: convert : ' . $e->getMessage());
        }
    }
    
    /**
     * Convert number
     * 
     * @staticvar array $unit
     * @param integer $value
     * @param integer $precision
     * @return mixed
     */
    public function convertNumber($value, $precision = 2)
    {
        // Every days a school day!
        // In binary notation, 1024 is represented as 10000000000, making it a 
        // simple round number. So 1024 is the maximum number of computer memory 
        // addresses that can be referenced with ten binary switches.
        $memoryAddresses = 1024;

        static $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        $unitIndex = floor(log($value, $memoryAddresses));
        $unitResult = $value / pow($memoryAddresses, $unitIndex);

        return round($unitResult, $precision) . ' ' . $unit[$unitIndex];
    }

    /**
     * Import a html string into the current engine, without causing DOM
     * hierarchy errors.
     * 
     * @param string $html
     * 
     * @return mixed
     */
    public function htmlToElement($html)
    {
        // Buffer engine used to convert the html string into DOMElements,
        $fabrication = new FabricationEngine;
        $fabrication->run($html);

        // Retrive xpath element from the FabricationEngine.
        $element = $this->query('//html/body')->item(0);

        // Append the new DOM Element(s) to the found DOMElement.
        $element->appendChild(
                $this->importNode(
                        $fabrication->query($this->xpath . '/*')->item(0)
                        , true
                )
        );

        return $element;
    }

    /**
     * Magic method for handling specification and helper based method these 
     * each method has a configuration array for the helper xpath query. 

     * @param string $method The method name called.
     * @param array  $args   The arguments passed with the method call.
     * 
     * @return mixed
     */
    public function __call($method, $args)
    {
        // process arguments.
        $argString = '';
        $argContainer = array();

        if (count($args) > 0) {

            // check the args and collect info for xpath conversion.
            foreach ($args as $key => $arg) {
                if (is_string($arg)) {
                    if (preg_match('/^([a-zA-Z]{1})/U', $arg, $matches)) {
                        $argContainer[] = $arg;
                    } else {

                        $argString.= $arg;
                    }
                }
            }
        }

        // GETTERS.
        // @todo move the helper array to the pattern objects eg Html, Xml.
        $helpers = array(
            // helpers general
            'gethtml' => array(
                'path' => '/html'
            ),
            'getheadings' => array(
                'path' => '//h1|//h2|//h3|//h4|//h5|//h6'
            ),
            'getlinkwithimage' => array(
                'path' => '//a//img'
            ),
            'getlinkrelalternatehref' => array(
                'path' => '//link[@rel="alternate"]/@href'
            ),
            // helpers div.
            'getdivswith' => array('path' => '//div[@$element]'), // TESTING
            'getdivswithid' => array('path' => '//div[@id]'),
            'getdivswithclass' => array('path' => '//div[@class]'),
            'getdivswithstyle' => array('path' => '//div[@class]'),
            // helpers image.
            'getimagewithalttag' => array('path' => '//img[@alt]'),
            'getimagewithoutalttag' => array('path' => '//img[not(@alt)]'),
        );

        if (preg_match('/^get(.*)/U', $method, $matches)) {

            $method = strtolower($method);

            // Change specification depending on doctype.
            $doctype = (
                    isset($this->pattern->specification[$this->getOption('doctype')]) ?
                            $this->pattern->specification[$this->getOption('doctype')] : array()
                    );

            // Attempt to find the doctype.
            $nodeName = preg_replace('/^get/U', '', $method);
            $xpath = '//' . $nodeName . $argString;

            if (array_key_exists($nodeName, $doctype['_body'])) {
                return $this->query($xpath);
            } else {

                echo "\n";
                echo "The 'FabricationEngine' misfired, failed to find an expected element. \n";
                echo "The means the request element name is not in the current doctype specification.\n";
                echo "Doctype:  " . $this->getOption('doctype') . "\n";
                echo "Method:   $method \n";
                echo "NodeName: $nodeName \n";
                echo "XPath:    $xpath \n";
            }

            // @todo move the helpers to the pattern objects eg Html, Xml.
            if (array_key_exists($method, $helpers)) {
                if (array_key_exists('path', $helpers[$method])) {
                    $path = $helpers[$method]['path'] . $argString;
                } else {
                    $path = $method . $argString;
                }
                return $this->query($path);
            }

            return false;
        }
        
        if (preg_match('/^set(.*)/U', $method, $matches)) {
            return false;
        }
    }

    /**
     * Setter for changing a element  
     * 
     * TODO put in __call
     */
    public function setElementBy($element, $value, $nodeValue)
    {
        $xql = "//*[@$element='$value']";

        if (is_object($nodeValue)) {
            $result = $this->query($xql)->item(0)->appendChild($this->importNode($nodeValue, true));
        } else {
            $result = $this->query($xql)->item(0)->nodeValue = $nodeValue;
        }

        return $result;
    }

    /**
     * Setter for changing a element  
     * 
     * TODO put in __call
     */
    public function getElementBy($element, $value)
    {
        $xql = "//*[@$element='$value']";

        return $this->query($xql)->item(0);
    }
    
    /**
     * Setter for changing HTML element.
     * 
     * TODO put in __call
     */
    public function setHtml($q, $value)
    {
        $this->getHtml($q)->item(0)->nodeValue = "$value";
        return $this->getHtml($q)->item(0);
    }
    
    /**
     * Generate the amount of time taken since the object was contructed.
     * 
     * @return string
     */
    public function timeTaken()
    {
        $timeStop = microtime(true);
        $timeTaken = (float) substr(-($this->timeStarted - $timeStop), 0, 5);
        return $timeTaken;
    }
    
    /**
     * TemplateTextElement
     * 
     * @param mixed  $key
     * @param string $query
     * @param array  $options
     * 
     * @return string
     */
    public function templateTextElement($key, $query, $options)
    {
        $output = '';

        $parts = (array) explode('.', $query);
        $language = (string) isset($parts[0]) ? $parts[0] : '';
        $template = (string) isset($parts[1]) ? $parts[1] : '';
        $result = (string) '';

        switch ($language) {
            case 'php':
                /**
                 * PHP 
                 * Language generation is all done in one switch based 
                 * loosely on php datatypes, template, class, array, string
                 * need to implement rest.
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
                                $result.='echo $data' . $options['class'] . ';';
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
                                $result.='$data->' . $k . "=" . "'" . $v . "';\n";
                            }
                        }
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
        }
        return $output;
    }
}
