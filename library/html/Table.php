<?php
namespace Library\Html;


/**
 *  HtmlTable dynamically create html tables using providing methods.
 *
 *  <table>
 *  [0.0][0.1][0.2] th table header
 *  [1.0][1.1][1.2] td table data
 *  [2.0][2.1][2.2] td table data
 *  </table>
 * 
 * @author	David Stevens <davro@davro.net> 
 */
class Table {
	
    public $header = array();
	
    function __construct($data = array(), $layout = array()) {

        $this->api = 'table';
        $this->data = $data;
        $this->layout = $layout;
//        $this->newline = "\n";
    }
	
	
    function setData($data) {
		
        return $this->data = $data;
    }
    
	
    function addData($data) {
		
		$this->data = array_merge($this->data, $data);
        return $this->data;
    }

    function addHeader($data) {
		
		$this->header = array_merge($this->header, $data);
        return $this->header;
    }
	
    function setItem($x, $y, $value) {
		
        $this->data[$x][$y] = $value;
    }
	
	
    function addLayout($layout) {
		
        $this->layout['data'] = $layout;
    }
	
	
    function setAttribute($tag, $key, $value) {
		
        if ($tag == $this->api) {
			
            $this->layout[$this->api][$key] = $value;
        } else {
            $this->layout['data'][$tag][$key] = $value;
        }
		
        return true;
    }
	
	
    function createTag($tag, $id_row = '', $id_data = '') {
		
        return sprintf("<%s%s>", $tag, 
            $this->buildTagAttributes($tag, $id_row, $id_data)
        );
    }
	
	
    function closeTag($tag) {
		
        return sprintf("</%s>", $tag);
    }
	
	
    function getData() {
		
        return $this->data;
    }
	
	
    function getAttribute($key, $value) {
		
        return $this->layout['data'][$key][$value];
    }
	
	
    function buildTagAttributes($tag, $id_row, $id_data) {
		
        $string = '';
		
        if ($tag == 'table') {
			
            if (@count($this->layout[$tag]) >= 1) {
				
                foreach ($this->layout[$tag] as $key => $value) {
                    $string.=" $key=\"$value\"";
                }
            }
        }
		
        if ($tag == 'td') {
			
            $vector="{$id_row}.{$id_data}";
			
            if (isset($this->layout['data'][$vector])) {
				
                if (count($this->layout['data'][$vector]) >= 1) {
					
                    foreach ($this->layout['data'][$vector] as $id => $value) {
                        $string.=" $id=\"$value\"";
                    }
                }
            }
        }
		
        return ($string);
    }
	
	
    function countData() {
		
        return count($this->getData());
    }
	
	
    function build() {
		
//        $table = $this->createTag($this->api) . $this->newline;
        $table = $this->createTag($this->api);
		
		if (sizeof($this->header) > 0) {
			$table.= $this->createTag('tr');
			foreach($this->header as $head) {
				$table.= '<th>'.$head.'</th>';
			}
			$table.= $this->closeTag('tr');
		}
			
        foreach ($this->data as $id_row => $row) {
			
            if ($row=='') { continue; }
			
            $table.= $this->createTag('tr');
			
            // check for object type.
            if (is_object($row)) {
				
                $table.= $this->createTag('td', $id_row, 0);
                $table.= $row;
                $table.= $this->closeTag('td');
            }
			
            // check for array type.
            if (is_array($row) ) {
						
                foreach ($row as $id_data => $data) {
				
					//var_dump($data);
					
                    if (is_object($data)) {
						
                        $table.= $this->createTag('td', $id_row, $id_data);
                        $table.= $data;
                        $table.= $this->closeTag('td');
                    }
					
                    if (is_string($data)) {
						
                        $table.= $this->createTag('td', $id_row, $id_data);
						
						if (preg_match('/^http:\/\/(.*?)/', trim($data))) {
							$table.= '<a href="' . $data . '" target="_blank">' . $data . '</a>';
						} else {
							$table.= $data;
						}
                        $table.= $this->closeTag('td');
					}
					
                    if (is_int($data)) {
						
                        $table.= $this->createTag('td', $id_row, $id_data);
                        $table.= $data;
                        $table.= $this->closeTag('td');
					}
                }
            }
			
            // check for string type.
            if (is_string($row) && isset($id_data)) {
				
                $table.= $this->createTag('td', $id_row, $id_data); 
                $table.= $data;
                $table.= $this->closeTag('td');
            }
			
//            $table.= $this->closeTag('tr') . $this->newline;
            $table.= $this->closeTag('tr');
        }
		
        $table.= $this->closeTag($this->api);
        return $table;
    }
	
	
    function __toString() {
		
        return $this->build();
    }
}