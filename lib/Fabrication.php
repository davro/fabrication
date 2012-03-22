<?php
namespace Fabrication\Library;

use Fabrication\Library\FabricationEngine;
use Fabrication\Library\FabricationMessage;

require_once(dirname(dirname(__FILE__)).'/lib/FabricationEngine.php');
require_once(dirname(dirname(__FILE__)).'/lib/FabricationMessage.php');

class Fabrication {

    /**
     * Super global arrays.
     * 
     * @param type $data
     * @return FabricationMessage 
     */
    public static function super($data='GET') {
	
        $prefix='_';
        
        $engine = new FabricationEngine();
        $engine->setUp();
        
        switch ($data) {
            case 'SESSION' :
            $engine->input($prefix.$data, $_SESSION);
            break;
            
            case 'POST' :
            $engine->input($prefix.$data, $_POST);
            break;

            case 'GET' :
            $engine->input($prefix.$data, $_GET);
            break;
        }
        
        return new FabricationMessage($engine);
    }
    
    
    public static function W3C($uri, $service='xhtml', $options = array()) {
	
        // W3C Documentation http://validator.w3.org/feed/docs/soap
        if (!array_key_exists('W3CHTML', $options)) {
            $W3CHTML = "http://validator.w3.org/check?uri=%s&amp;output=soap12";
        }
        if (!array_key_exists('W3CCSS', $options)) {
            $W3CCSS = "http://jigsaw.w3.org/css-validator/validator?uri=%s&amp;output=soap12";
        }
        
        $engine = new FabricationEngine();
        $engine->setUp();
        $engine->registerNamespace('m', 'http://www.w3.org/2005/10/markup-validator');

        switch ($service) {
            case 'html':
            case 'xhtml':
            $engine->loadXML(self::fetch(sprintf($W3CHTML, $uri)));
            break;

            case 'css':
            $engine->loadXML(self::fetch(sprintf($W3CCSS, $uri)));
            break;
        }
        return new FabricationMessage($engine);
    }

    
    private function fetch($uri, $options=array()) {

        $options[CURLOPT_URL] = $uri;
        $options[CURLOPT_RETURNTRANSFER] = 1;
        $options[CURLOPT_TIMEOUT] = 100;
        $options[CURLOPT_FOLLOWLOCATION] = true;
        $options[CURLOPT_MAXREDIRS] = 5;

        return self::execute($options);
    }

    
    private function execute($options) {
        
        $resource = curl_init();
        curl_setopt_array($resource, $options);
        
        $response = curl_exec($resource);
        
        // clean up connection.
        curl_close($resource);
        
        return $response;
    }
}
