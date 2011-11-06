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
 * print $engine->output('hello', 'php.string');
 * print $engine->output('hello', 'php.array');
 * 
 * // Generates something like, class Hello { public $hello='world'; }
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
class FabricationEngine extends \DOMDocument {

    /**
     * Symbol for id attribute assignment.
     * 
     */
    const symbol_id     ='.';

    /**
     * Symbol for class attributes assignment.
     * 
     */
    const symbol_class  ='#';

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
        'doctype'               => 'html.4.01.transitional',
        'process'               => true,
        'process.doctype'       => true,
        'process.body.image'    => true,
        'process.body.br'       => true,
        'process.body.hr'       => true,
        'process.body.anchor'   => false,
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

    //public $xpath;

    /**
     * Main setup function for the Fabrication Engine.
     * 
     * Examples from http://www.w3.org/QA/2002/04/valid-dtd-list.html
     */
    public function __construct() {
        // Construct the parent else error DOMDocument::createElement() 
        parent::__construct();


        $this->doctypes['html'][5]                      = '<!DOCTYPE HTML>'; // HTML5 Not a standard.
        $this->doctypes['html']['4.01.strict']          = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"'."\n".'   "http://www.w3.org/TR/html4/strict.dtd">';
        $this->doctypes['html']['4.01.transitional']    = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"'."\n".'   "http://www.w3.org/TR/html4/loose.dtd">';
        $this->doctypes['html']['4.01.frameset']        = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"'."\n".'   "http://www.w3.org/TR/html4/frameset.dtd">';
        $this->doctypes['xhtml']['1.0.strict']          = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"'."\n".'   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
        $this->doctypes['xhtml']['1.0.transitional']    = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"'."\n".'   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $this->doctypes['xhtml']['1.0.frameset']        = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"'."\n".'   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';

        $this->updateOptions();
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
     * Update options, helps with this bottom up design.
     *
     */
    public function updateOptions() {
        //
        // NOTE
        // Prepend the new doctype to the output html no newline 
        // It seems to trigger DOMDocument to remove the appended doctype from output html.
        //
        // Fetch the correct doctype from the doctypes internal array, by parts.
        //$doctype_parts = preg_split('/[\s.]+/', $this->getOption('doctype'));
        $parts = explode('.', $this->getOption('doctype'));
        $document_type = $parts[0];
        array_shift($parts);
        $version_and_view =implode('.', $parts);

        $this->output['doctype']=$this->doctypes[$document_type][$version_and_view];
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

        $this->options[$key]=$value;
        $this->updateOptions();
        return true;
    }

    /**
     * Input key pair value into the input array.
     *
     */
    public function input($key, $value, $meta=array()) {

        $this->input[$key]=$value;
        $this->input_meta[$key]=$meta;
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

        if ($key=='' || array_key_exists($key, $this->input)) {
            
            // ensure empty query, key based retrieval are returned first/fast.
            if (empty($query)) {
                return $this->input[$key];
            }
      
            // setup standard options.
            if (empty($options)) {
                $options=array(
                    'return'=>true,
                    'tags'=>true,
                    'echo'=>false, // TODO echo of each variable, objects __toString.
                );
            }
            
            $query_parts=explode('.', $query);
            $language=$query_parts[0];
            $template=$query_parts[1];
            $result='';
            
            switch($language) {
                case 'php':
                    /**
                     * PHP 
                     * Language generation is all done in one switch based 
                     * loosely on php datatypes, template, class, array, string
                     * need to implement rest.
                     * 
                     */
                    switch($template) {
                        case 'template':
                            foreach ($this->input as $k => $v) {
                                if ($key !== '' && $key !== $k) { continue; }
                                $result.=$v.";\n";
                            }
                            break;
                        case 'class':
                            // construction.
                            if (array_key_exists('class', $options)) {
                                if  ($options['class']) {
                                    $stereotype='';
                                    if  (array_key_exists('class.stereotype', $options)) {
                                        $stereotype='extends '.$options['class.stereotype'].' ';
                                    }
                                    $class=$options['class'];
                                    $result.='class '.$class." $stereotype{\n";
                                    foreach($this->input as $k => $v) {
                                        if ($key !== '' && $key !== $k) { continue; }
                                        if  (array_key_exists('tabs', $options)) {
                                            $result.="\t";
                                        }
                                        $result.='public $'.$k."=".var_export($v,true).";\n";
                                    }
                                    if  (array_key_exists('class.methods', $options)) {
                                        foreach($options['class.methods'] as $k => $v) {
                                            $parameters='';
                                            foreach($v['parameters'] as $kk => $vv) {
                                                $parameters.='$'.$kk.'='.var_export($vv,true).',';
                                            }
                                            $parameters=trim($parameters, ',');
                                            if  (array_key_exists('tabs', $options)) {
                                                $result.="\t";
                                            }
                                            $result.='public function '.$k.'('.$parameters.') {' . "\n";
                                            foreach($v['code'] as $kk => $vv) {
                                                if  (array_key_exists('tabs', $options)) {
                                                    $result.="\t\t";
                                                }
                                                if (empty($vv)) {
                                                    $result.="\n";
                                                } else {
                                                    $result.=$vv.";\n";
                                                }
                                            }
                                            if  (array_key_exists('tabs', $options)) {
                                                $result.="\t";
                                            }
                                            $result.='}'."\n";
                                        }
                                    }
                                    $result.="}\n";
                                }
                            } else {
                                $result.='$data=new stdClass;'."\n";
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
                            $result.='$data=array('."\n";
                            foreach($this->input as $k => $v) {
                                if ($key !== '' && $key !== $k) { continue; }
                                $result.="'$k'=>".var_export($v,true).",\n";
                            }
                            $result.=");\n";
                            // echo, implementation.
                            if (array_key_exists('echo', $options) && array_key_exists('class', $options)) { 
                                if ($options['echo'] === true) {
                                    $result.='echo $data'.$class.';';
                                }
                            }
                            break;
                        case 'string':
                            // construction.
                            foreach($this->input as $k => $v) {
                                if ($key !== '' && $key !== $k) { continue; }
                                $result.='$'.$k.'="'.$v.'";'."\n";
                            }
                            // echo, implementation.
                            if (array_key_exists('echo', $options)) { 
                                if ($options['echo'] === true) {
                                    foreach($this->input as $kk=>$vv) {
                                        if ($key !== '' && $key !== $kk) { continue; }
                                        $result.='echo $'.$kk.';'."\n";
                                    }
                                }
                            }
                            break;
                    }
                    // option :: language tags.
                    if (array_key_exists('tags', $options)) { 
                        if  ($options['tags'] === true) {
                            $output="<?php\n$result?>";
                        } else {
                            $output=$result;
                        }
                    } else {
                        $output=$result;
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
                        if  ($options['header']===true) {
                            $result.="/**\n";
                            $result.=" * CSS Generated by the FabricationEngine.\n";
                            $result.=" */\n";
                        }
                    }
                    foreach ($this->input as $k => $v) {
                        if ($key !== '' && $key !== $k) { continue; }
                        $result.=$k." {\n";
                        if (is_array($v))
                        foreach ($v as $kk => $vv) {
                            $result.=$kk.': '.$vv.";\n";
                        }
                        $result.="}\n";
                    }
                    $output=$result;
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
                                $result.=$line.";\n";
                            }
                        }
                    }

                    // option :: language tags.
                    if (array_key_exists('tags', $options)) { 
                        if  ($options['tags']===true) {
                            $output="<script>\n$result</script>\n";
                        } else {
                            $output=$result;
                        }
                    } else {
                        $output=$result;
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
     * Setup DOMDocument and XPath and stuff.
     * 
     */ 
    public function setUp() {

        // DOMDocument options.
        //$document->formatOutput = true;           // WORKS make optional.
        //$this->preserveWhiteSpace = false;
        //$document->substituteEntities = false;    // TESTING
        //$document->resolveExternals = true;       // TESTING
        $this->recover = true;                      // TESTING
        $this->strictErrorChecking = false;         // TESTING

        $this->xpath= new \DomXpath($this);
    }
    
    /**
     * Run method once the all input have been set.
     * Then you will have a valid document with a searchable path.
     *
     */ 
    public function run($html='', $type='string') {

        //libxml_use_internal_errors(true);
        
        if (! empty($html)) {
            switch($type) {
                case 'string':
                    //if ($this->loadHTML($html)) { // make error suppression optional.
                    if (@$this->loadHTML($html)) {
                        // success.
                    } else {
                        //print "[FAIL] problem loading html string.\n";
                        return false;
                    }
                    break;

                case 'file':
                    //if ($this->loadHTMLFile($html)) { //if ($this->loadHTMLFile($html)) { 
                    if (@$this->loadHTMLFile($html)) {
                        // success.
                    } else {
                        return false;
                    }
                    break;
            }
        }

        // create a more flexable symbol loop, array based..
        // id assigner maps .keys with values.
        foreach($this->input as $key => $value) {
            $id=substr($key, 0, 1);
            if ($id == self::symbol_id) {
                $id=str_replace(self::symbol_id, '', $key);
                $this->setElementById($id, $value);
            }
        }

        // class assigner maps .keys with values.
        foreach($this->input as $key => $value) {
            $class=substr($key, 0, 1);
            if ($id == self::symbol_class) {
                $class=str_replace(self::symbol_class, '', $key);
                $this->setElementById($class, $value);
            }
        }
        
        //libxml_clear_errors();

        // XPath object and other setting.
        $this->setUp();

        //$html=mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        return true;
    }

    public function save() {
        // Need to save here to populate DOM for getter and setter methods.
        $this->output['html'] = $this->saveHTML();
        $this->output['xml']  = $this->saveXML();
        return true;
    }

    /**
     * Return the engine output html view.
     * 
     */
    public function outputHTML() {

        $this->save();
        $this->outputProcess();

        return $this->output['doctype'].$this->output['html'];
    }

    /**
     * Return the engine output xml view.
     * 
     */
    public function outputXML() {

        $this->save();
        $this->outputProcess();

        return $this->output['xml'];
    }
    
    /**
     * Time to sort out the plethora of DOMDocument bugs.
     * 
     */
    public function outputProcess() {


        // Remove doctype added by DOMDocument (hacky)
        $this->output['html'] = preg_replace('/<!DOCTYPE[^>]+>/', '', $this->output['html']);
        $this->output['xml']  = preg_replace('/<!DOCTYPE[^>]+>/', '', $this->output['xml']);

        if ($this->getOption('process')) {
            // Remove all whitespace between tags, for the extremist .
            //$this->output['html'] = preg_replace("/>\s+</", "><", $this->output['html']);

            /**
             * Process image tags, ensuring patterns like there are changed 
             * 
             * <img>
             * <img src="">
             * <img src="/image.png">
             * 
             * Are changed to valid xhtml
             * <img src="" />
             * <img src="/image.png" />
             */
            if ($this->getOption('process.body.image')) {
                //$this->output['html'] = preg_replace('<img(.+)">', '<img\1" />', $this->output['html']);
                $this->output['html'] = preg_replace('/<img(.*)>/sU', '<img\\1 />', $this->output['html']); 
            }

            if ($this->getOption('process.body.br')) {
                $this->output['html'] = preg_replace('/<br(.*)>/sU', '<br\\1 />', $this->output['html']); 
            }

            if ($this->getOption('process.body.hr')) {
                $this->output['html'] = preg_replace('/<hr(.*)>/sU', '<hr\\1 />', $this->output['html']); 
            }
        }
        // Trim whitespace need this to get exactly the wanted data back for test cases, mmm.
        $this->output['html']=trim($this->output['html']);

        return true;
    }

    /**
     * Create elements with attributes.
     * 
     */
    public function create($name, $value, $attributes=array()) {

        $element = $this->createElement($name, $value);
        
        if (count($attributes) > 0 ) {
            foreach($attributes as $key => $value) {
                $element->setAttribute($key, $value);
            }
        }
        return $element;
    }

    /**
     * Create controller element with options.
     * 
     */
    public function createController($name, $value='', $options=array()) {
        
        $element = $this->createElement($name, $value);
        
        switch($name) {
            case 'head':
                $form = $this->create('form','', array('action'=>'/?head', 'method'=>'post'));
                $input_submit = $this->create('input','URI', array('type'=>'submit', 'value'=>'+'));
                $form->appendChild($input_submit);
                $element->appendChild($form);
                break;
            
            case 'body':
                $form = $this->create('form','', array('action'=>'/?body', 'method'=>'post'));
                $input_test = $this->create('input','URI', array('type'=>'hidden', 'name'=>'test'));
                $input_submit = $this->create('input','URI', array('type'=>'submit', 'value'=>'+'));
                $form->appendChild($input_test);
                $form->appendChild($input_submit);
                $element->appendChild($form);
                break;
        }
        
        $this->appendChild($element);
    }

    /**
     * Main XPath query method with some basic sanity checking.
     * 
     */
    public function query($path) {

        if ($path) {
            return $this->xpath->query($path);
        }
        return false;
    }

    /**
     * Get all elements that have an id.
     * 
     */
    public function getAllElementsById() { return null; }

    /**
     * 
     * 
     */
    public function getElementsWith($element='div', $attribute='id') {

        return $this->query("//".$element."[@".$attribute."]");
    }
    
    /**
     * 
     * 
     */
    public function getAllWith($element) {

        return $this->query("//*[@$element]");
    }
    
    /**
     * 
     * 
     */
    public function getElementById($id) {

        return $this->query("//*[@id='$id']")->item(0)->nodeValue;
    }

    /**
     * 
     * 
     */
    public function setElementById($id, $value) {

        return $this->query("//*[@id='$id']")->item(0)->nodeValue="$value";
    }

    /**
     * 
     * 
     */
    public function getElementBy($element, $value) {

        return $this->query("//*[@$element='$value']")->item(0)->nodeValue;
    }

    /**
     * Setter for changing a element  
     * 
     */
    public function setElementBy($element, $value, $nodeValue) {
        return $this->query("//*[@$element='$value']")->item(0)->nodeValue="$nodeValue";
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
        
        if (empty($options)) {
            $options=array(
                //'show.methods'=>true
                'show.methods'=>false
            );
            //var_dump($options);
        }
        
        if (is_object($data)) {
            $classname = get_class($data);
            
            $result = "\n";
            $result.= str_repeat('-', 80)."\n";
            $result.= "\tDUMP Type:".gettype($data)."\tReturn:". var_export($return, true)."\n";
            $result.= str_repeat('-', 80)."\n";
            $result.= "\n";
            $result.= "ClassName: of $classname: \n";
            $result.= "\n";
            $result.= "MethodList\n";
            
            $class_methods=get_class_methods($data);
            if (count($class_methods) > 0) {
                foreach($class_methods as $method) {
                    $result.="Method:\t$method\n";
                }
            } else {
                $result.="No methods found.\n";
            }
            
            $result.="\n";
            $result.="NodeList:\n";
            $result.="Instance: ".$classname."\n";
            switch ($classname) {
                case 'DOMDocument':
                    $result.= "DOMDocument XPath: {$data->getNodePath()}\n";
                    $result.= $data->saveXML($data);
                    break;
                case 'DOMElement':
                    $result.= "DOMElement XPath: {$data->getNodePath()}\n";
                    $result.= $data->ownerDocument->saveXML($data);
                    break;
                case 'DOMNodeList':
                    for ($i = 0; $i < $data->length; $i++) {
                        $result.= "DOMNodeList Item #$i, XPath: {$data->item($i)->getNodePath()}\n";
                        $result.= "{$data->item($i)->ownerDocument->saveXML($data->item($i))}\n";
                    }
                    break;
                case 'DOMAttr':
                    $result.= "DOMAttr XPath: {$data->getNodePath()}\n";
                    $result.= $data->ownerDocument->saveXML($data);
                    break;
                default:
                    $result.= var_export($data, true);
            }
        }
        
        if (is_array($data)) {
            $result = "\n";
            $result = "\n";
            $result.= str_repeat('-', 80)."\n";
            $result.= "| DUMP Type:".gettype($data)."\tReturn:". var_export($return, true)."\n";
            $result.= str_repeat('-', 80)."\n";
            $result.= "\n";
            
            $result = var_export($data, true);
        }
        
        if (is_string($data)) {
            $result = var_export($data, true);
        }

        if (is_int($data)) {
            $result = var_export($data, true);
        }
        
        if ($return) { 
            return $result . "\n";
        } else {
            print $result . "\n";
        }
    } 

    /**
     * !!!!!!!!!!!!!!!!! UNSTABLE TESTING ONLY !!!!!!!!!!!!!!!!!!!!
     * 
     * Needs recursion element creator options ... mmm ...
     * 
     * element append.          Add to start of parent nodeValue.
     * element prepend          Add to end of parent nodeValue.
     * element overwrite.       Overwrite parent nodeValue.
     * 
     */
    public function specification($specification = array()) {

        $this->setUp();

        // doctype handled by the engine using option methods.
        $this->head = $this->createElement('head', '');
        $this->body = $this->createElement('body', '');

        foreach($specification as $key => $container) {

            switch($key) {

                case 'doctype':
                break;

                case 'html':
                break;

                case 'head':
                    foreach($container as $key => $value) {
                        if ($key =='title') {
                            $this->title = $value;
                        }
                        if ($key =='styles') {
                            foreach($value as $k => $v) {
                                $this->addStyle($v);
                            }
                        }
                        if ($key =='scripts') {
                            foreach($value as $k => $v) {
                                $this->addScript($v);
                            }
                        }
                    }
                break;

                case 'body':
                    //
                    // Free form specification for document elements.
                    //
                    // TODO Create a recursion function here for the body ... 
                    //
                    foreach($container as $spec) {

                        if (array_key_exists('type', $spec)) {
                            $element=$this->createElement($spec['type']);
                        }

                        if (array_key_exists('nodeValue', $spec)) {
                            switch(gettype($spec['nodeValue'])) {
                                case 'string':
                                $element->nodeValue=$spec['nodeValue'];
                                break;

                                case 'array':
                                $map=array();
                                foreach($spec['nodeValue'] as $key => $value) {

                                    if (is_string($value['nodeValue'])) {

                                        $map[$key]=$this->createElement($value['type'], $value['nodeValue']);

                                        foreach($value['attributes'] as $akey => $attribute) {
                                            $map[$key]->setAttribute($akey, $attribute);
                                        }

                                        $element->appendChild($map[$key]);

                                    } else {
                                        // recursive loop on nodeValue if array ?
                                        exit;
                                        //$this->recursiveElementCreator($document, $element, $value['nodeValue']);
                                    }
                                }
                                break;
                            }
                        }

                        if (array_key_exists('attributes', $spec)) {
                            foreach($spec['attributes'] as $key => $attribute) {
                                $element->setAttribute($key, $attribute);
                            }
                        }
                        $this->body->appendChild($element);
                    }
                break;

            }
        }
        $document = $this->create();
        return $document;
    }

    /**
     * Getter for returning the current doctype output.
     *
     * @return DOMDocument
     */
    public function getDoctype() {

        return $this->output['doctype'];
    }

    /**
     * Getter for returning the current doctype option. 
     *
     * @return type 
     */
    public function getDoctypeOption() {
        return $this->getOption('doctype');
    }

    /**
     * Setter for adding the selecting the doctype option. 
     * 
     * @param type $doctype
     * @return type 
     */
    public function setDoctypeOption($doctype) {
        if (array_key_exists($doctype, $this->doctypes)) {
            return $this->setOption($doctype);
        }
    }

    /**
     * Getter for returning complete html DOM structure.
     * 
     * @return type 
     */
    public function getHtml($q='') {

        return $this->query('/html'.$q);
    }
    
    /**
     * 
     * 
     */ 
    public function setHtml($q, $value) {

        $this->getHtml($q)->item(0)->nodeValue="$value";
        
        return $this;
    }

    /**
     * 
     * 
     */ 
    public function getBase($q='') {

        return $this->query('//base'.$q);
    }
    
    /**
     * 
     * 
     */ 
    public function getNoScript($q='') {

        return $this->query('//noscript'.$q);
    }
    
    /**
     * Getter for returning a complete list of div tags
     * 
     */ 
    public function getDiv() {

        return $this->query('//div');
    }

    /**
     * Getter for returning a complete list of span tags
     * 
     */ 
    public function getSpan() {

        return $this->query('//span');
    }

    /**
     * Getter for returning element division tags with attributes.
     * 
     */ 
    public function getDivsWith($element='id') {

        return $this->getElementsWith('div', $element);
    }
    
    /**
     * Getter for returning element division tags with an id attribute.
     * 
     */ 
    public function getDivsWithId() {

        return $this->getDivsWith();
    }
    
    /**
     * Getter for returning a complete list of heading tag.
     * 
     */ 
    public function getHeading() {

        return $this->query('//h1|//h2|//h3|//h4|//h5|//h6');
    }
    
    /**
     * 
     * 
     */ 
    public function getHeading1($q='') {

        return $this->query('//h1'.$q);
    }
    
    /**
     * 
     * 
     */ 
    public function getHeading2($q='') {

        return $this->query('//h2'.$q);
    }
    
    /**
     * 
     * 
     */ 
    public function getHeading3($q='') {

        return $this->query('//h3'.$q);
    }
    
    /**
     * 
     * 
     */ 
    public function getHeading4($q='') {

        return $this->query('//h4'.$q);
    }
    
    /**
     * 
     * 
     */ 
    public function getHeading5($q='') {

        return $this->query('//h5'.$q);
    }
    
    /**
     * 
     * 
     */ 
    public function getHeading6($q='') {

        return $this->query('//h6'.$q);
    }

    /**
     * 
     * 
     */ 
    public function getAddress($q='') {

        return $this->query('//address'.$q);
    }
    
    /**
     * Indicates emphasis.
     * 
     */ 
    public function getEmphasis($q='') { return $this->getEm($q); }
    public function getEm($q='') {

        return $this->query('//em'.$q);
    }
    
    /**
     * Indicates stronger emphasis.
     * 
     */ 
    public function getStrong($q='') {

        return $this->query('//strong'.$q);
    }
    
    /**
     * Contains a citation or a reference to other sources.
     * 
     */ 
    public function getCitation() { return $this->getCite(); }
    public function getCite() {

        return $this->query('//cite');
    }
    
    /**
     * Indicates that this is the defining instance of the enclosed term.
     * 
     */ 
    public function getDefinition($q='') { return $this->getDfn($q); }
    public function getDfn($q='') {

        return $this->query('//dfn'.$q);
    }
    
    /**
     * Designates a fragment of computer code.
     * 
     */ 
    public function getCode($q='') {

        return $this->query('//code'.$q);
    }
    
    /**
     * Designates sample output from programs, scripts, etc.
     * 
     */ 
    public function getSample($q='') {

        return $this->query('//samp'.$q);
    }
    
    /**
     * Indicates text to be entered by the user.
     * 
     */ 
    public function getKeyboard($q='') { return $this->getKbd($q); }
    public function getKbd($q='') {

        return $this->query('//kbd'.$q);
    }
    
    /**
     * Indicates an instance of a variable or program argument.
     * 
     */ 
    public function getVariable($q='') { return $this->getVar($q); }
    public function getVar($q='') {

        return $this->query('//var'.$q);
    }
    
    /**
     * Indicates an abbreviated form (e.g., WWW, HTTP, URI, Mass., etc.).
     * An abbreviation (from Latin brevis, meaning short) is a shortened form of
     * a word or phrase. Usually, but not always, it consists of a letter or 
     * group of letters taken from the word or phrase. For example, the word 
     * abbreviation can itself be represented by the abbreviation abbr., abbrv.
     * or abbrev.
     * 
     */ 
    public function getAbbreviation($q='') { return $this->getAbbr($q); }
    public function getAbbr($q='') {

        return $this->query('//abbr'.$q);
    }
    
    /**
     * Indicates an acronym (e.g., WAC, radar, etc.).
     * 
     */ 
    public function getAcronym($q='') {

        return $this->query('//acronym'.$q);
    }

    /**
     * The P element represents a paragraph. It cannot contain block-level 
     * elements (including P itself).
     * 
     */ 
    public function getParagraph($q='') { return $this->getP($q); }
    public function getP($q='') {

        return $this->query('//p'.$q);
    }
    
    /**
     * The BR element forcibly breaks (ends) the current line of text.
     * 
     */ 
    public function getBr($q='') {

        return $this->query('//br'.$q);
    }
    
    /**
     * The PRE element tells visual user agents that the enclosed text is 
     * "preformatted". When handling preformatted text, visual user agents:
     * 
     */ 
    public function getPre($q='') {

        return $this->query('//pre'.$q);
    }
    
    /**
     * INS and DEL are used to markup sections of the document that have been 
     * inserted or deleted with respect to a different version of a document 
     * (e.g., in draft legislation where lawmakers need to view the changes).
     * 
     */ 
    public function getInserted($q='') { return $this->getIns($q); }
    public function getIns($q='') {

        return $this->query('//ins'.$q);
    }

    /**
     * INS and DEL are used to markup sections of the document that have been 
     * inserted or deleted with respect to a different version of a document 
     * (e.g., in draft legislation where lawmakers need to view the changes).
     * 
     */ 
    public function getDeleted($q='') { return $this->getDel($q); }
    public function getDel($q='') {

        return $this->query('//del'.$q);
    }

    /**
     * Ordered and unordered lists are rendered in an identical manner except 
     * that visual user agents number ordered list items. User agents may 
     * present those numbers in a variety of ways. Unordered list items are not 
     * numbered.
     * 
     * Both types of lists are made up of sequences of list items defined by the
     * LI element (whose end tag may be omitted).
     * 
     */ 
    public function getUnordered($q='') { return $this->getUl($q); }
    public function getUl($q='') {

        return $this->query('//ul'.$q);
    }
    
    /**
     * Ordered and unordered lists are rendered in an identical manner except 
     * that visual user agents number ordered list items. User agents may 
     * present those numbers in a variety of ways. Unordered list items are not 
     * numbered.
     * 
     * Both types of lists are made up of sequences of list items defined by the
     * LI element (whose end tag may be omitted).
     * 
     */ 
    public function getOrdered($q='') { return $this->getOl($q); }
    public function getOl($q='') {

        return $this->query('//ol'.$q);
    }
    
    /**
     * Definition lists vary only slightly from other types of lists in that 
     * list items consist of two parts: a term and a description. The term is 
     * given by the DT element and is restricted to inline content. The 
     * description is given with a DD element that contains block-level content.
     * 
     */ 
    public function getDefinitionList($q='') { return $this->getDl($q); }
    public function getDl($q='') {

        return $this->query('//dl'.$q);
    }

    // 10.3 Definition lists: the DL, DT, and DD elements
    /**
     * Definition lists vary only slightly from other types of lists in that 
     * list items consist of two parts: a term and a description. The term is 
     * given by the DT element and is restricted to inline content. The 
     * description is given with a DD element that contains block-level content.
     * 
     */ 
    public function getDefinitionType($q='') { return $this->getDt($q); }
    public function getDt($q='') {

        return $this->query('//dt'.$q);
    }
    
    /**
     * Definition lists vary only slightly from other types of lists in that 
     * list items consist of two parts: a term and a description. The term is 
     * given by the DT element and is restricted to inline content. The 
     * description is given with a DD element that contains block-level content.
     * 
     */ 
    public function getDefinitionData($q='') { return $this->getDd($q); }
    public function getDd($q='') {

        return $this->query('//dd'.$q);
    }
    
    /**
     * The TABLE element contains all other elements that specify caption, rows, 
     * content, and formatting.
     * 
     */ 
    public function getTable($q='') {

        return $this->query('//table'.$q);
    }
    
    /**
     * The caption element contains all other elements that specify caption, rows, 
     * content, and formatting.
     * 
     */ 
    public function getTableCaption($q='') {

        return $this->query('//caption'.$q);
    }    
    
    /**
     * The thead element contains all other elements that specify caption, rows, 
     * content, and formatting.
     * 
     */ 
    public function getTableTHead($q='') {

        return $this->query('//thead'.$q);
    }
    
    /**
     * The tfoot element contains all other elements that specify caption, rows, 
     * content, and formatting.
     * 
     */ 
    public function getTableTFoot($q='') {

        return $this->query('//tfoot'.$q);
    }
    
    /**
     * The tbody element contains all other elements that specify caption, rows, 
     * content, and formatting.
     * 
     */ 
    public function getTableTBody($q='') {

        return $this->query('//tbody'.$q);
    }
    
    /**
     * The colgroup element contains all other elements that specify caption, rows, 
     * content, and formatting.
     * 
     */ 
    public function getTableColGroup($q='') {

        return $this->query('//colgroup'.$q);
    }
    
    /**
     * The col element contains all other elements that specify caption, rows, 
     * content, and formatting.
     * 
     */ 
    public function getTableCol($q='') {

        return $this->query('//col'.$q);
    }

    /**
     * The TR elements acts as a container for a row of table cells. 
     * The end tag may be omitted.
     * 
     */ 
    public function getTableTr($q='') {

        return $this->query('//tr'.$q);
    }

    /**
     * Table cells may contain two types of information: header information and 
     * data. This distinction enables user agents to render header and data 
     * cells distinctly, even in the absence of style sheets. For example, 
     * visual user agents may present header cell text with a bold font. 
     * Speech synthesizers may render header information with a distinct voice 
     * inflection.
     * 
     * Start tag: required, End tag: optional
     * 
     */ 
    public function getTableTh($q='') {

        return $this->query('//th'.$q);
    }

    /**
     * Table cells may contain two types of information: header information and 
     * data. This distinction enables user agents to render header and data 
     * cells distinctly, even in the absence of style sheets. For example, 
     * visual user agents may present header cell text with a bold font. 
     * Speech synthesizers may render header information with a distinct voice 
     * inflection.
     * 
     * Start tag: required, End tag: optional
     * 
     */ 
    public function getTableTd($q='') {

        return $this->query('//td'.$q);
    }

    /**
     * Each A element defines an anchor
     * 
     * Start tag: required, End tag: required
     * 
     */ 
    public function getA($q='') {

        return $this->query('//a'.$q);
    }
    
    
    
    
    
    
    //--------------------------------------------------------------------------
    // Above working from http://www.w3.org/TR/html401/
    //--------------------------------------------------------------------------
    
    /**
     * List all the images on a page.
     *
     * //img
     * 
     */ 
    public function getImage($q='') { return $this->getImg($q); }
    public function getImg($q='') {

        return $this->query('//img'.$q);
    }

    /**
     * List all the images on a page.
     *
     * //a//img
     * 
     */ 
    public function getImageInsideALink() {

        return $this->query('//a//img');
    }

    /**
     * List the images that have alt tags.
     * 
     * //img[@alt]
     * 
     */ 
    public function getImageWithAltTag() {

        return $this->query('//img[@alt]');
    }

    /**
     * List the images that don't have alt tags.
     * 
     * //img[not(@alt)]
     * 
     */ 
    public function getImageWithoutAltTag() {

        return $this->query('//img[not(@alt)]');
    }
    
    /**
     * Generic inclusion: the OBJECT element
     * 
     * Start tag: required, End tag: required
     * 
     */ 
    public function getObject($q='') {

        return $this->query('//object'.$q);
    }

    /**
     * Object initialization: the PARAM element
     * 
     * Start tag: required, End tag: forbidden
     * 
     */ 
    public function getParam($q='') {

        return $this->query('//param'.$q);
    }

    /**
     * Client-side image maps: the MAP and AREA elements
     * 
     * Start tag: required, End tag: forbidden
     * 
     */ 
    public function getMap($q='') {

        return $this->query('//map'.$q);
    }
    
    /**
     * TEST
     * Get rss feed from link.
     *
     * link[@rel='alternate']/@href
     */
    public function getLinkRelAlternateHref() { return null; }


}