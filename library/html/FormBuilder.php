<?php
namespace Library\Html;

use Library\Html\Input;
use Library\Html\Table as HtmlTable;

class FormBuilder {
	
	// default input type or an alternate based on field size
	// 'enum' field default is SELECT, alternate is RADIO
	// 'set' field default is MULTISELECT, alternate is CHECKBOX
	// 'blob' or 'text' field default is TEXTAREA, alternate is TEXT
	private $enumFieldToggle = 11;
	private $setFieldToggle = 11;
	private $strFieldToggle = 70;

	// determine form input size attributes
	private $textInputLength = 50;
	private $textareaRows = 4;
//	private $textareaCols = 50;
	private $textareaCols = 40;
	private $multiSelectSize = 4;
	private $fileInputLength = 50;

	//these vars hold the string returned on success or fail of record INSERT/UPDATE
	private $messageInsertSuccess = '<label class="font">Form has been submitted successfully.</label>';
	private $msg_insertFail = '<label class="font">Form submission failed.</label>';
	private $msg_updateSuccess = '<label class="font">Record has been updated successfully.</label>';
	private $msg_updateFail = '<label class="font">Record update failed.</label>';

	//these vars hold the string outputted before and after error messages
	private $err_pre = '<br /><span class="err">';
	private $err_post = '</span>';

	//toggle JavaScript labels
	private $jsLabels = false;

	private $createdWidgets = array();
	private $layout;
	private $formMethod = 'post';
	
	private $setSubmitValue = 'Submit';
	
	private $action = '/';
	
	// Remove for an escaping stregy
	private $_magic_quotes;

	/**
	 * Create structural elements fundamental, tangible or intangible notion 
	 * referring to the recognition, observation, nature, and permanence of 
	 * patterns and relationships of entities. This notion may itself be an 
	 * object, such as a built structure, or an attribute, such as the structure 
	 * of society.
	 * 
	 * A data structure is a way of storing data in a computer so that it can 
	 * be used efficiently. Often a carefully chosen data structure will allow 
	 * the most efficient algorithm to be used. The choice of the data structure 
	 * often begins from the choice of an abstract data type. A well-designed 
	 * data structure allows a variety of critical operations to be performed, 
	 * using as few resources, both execution time and memory space, as possible. 
	 * Data structures are implemented in a programming language as data types 
	 * and the references (e.g. relationships, links and pointers) and 
	 * operations that are possible with them.
	 * 
	 */
	function __construct($connection, $database, $table) {
		
		$this->layout = new HtmlTable();
		$this->layout->setAttribute('table', 'border', 1);
		$this->layout->setAttribute('table', 'cellpadding', '0');
		$this->layout->setAttribute('table', 'cellspacing', '0');

		if( stristr(getenv("HTTP_USER_AGENT"), "Mozilla/4") && !stristr(getenv("HTTP_USER_AGENT"), "compatible" ) ) {
			$this->NS4 = true;
		} else {
			$this->NS4 = false;
		}

		if (get_magic_quotes_gpc()) {
			$_GET  = $this->stripFormSlashes($_GET);
			$_POST = $this->stripFormSlashes($_POST);
			//echo "Magic quotes are enabled";
		} else {
			//echo "Magic quotes are disabled";
		}

        $this->pkey = "";
        $this->fieldSets = true;
        $this->feedback = "both";
        $this->mysql_errors = true;
        $this->hasFiles = false;
        $this->submitted = 0;
        $this->skipFields(
            array(
                "form_signature", "form_multipage", "form_setcheck",
                "pkey", "submit", "x", "y", "MAX_FILE_SIZE"
            )
        );
		
		$this->connect($connection, $database, $table);
    }

    /**
     *    Connect to data source.
	 *
	 * @param	resource	$connection
	 * @param	string		$database
	 * @param	string		$table
	 * @return	type 
	 */
    function connect($connection, $database, $table) {
		
		$this->conn = $connection;
        $this->DB = $database;
        $this->formName = $this->table = $table;
		
		// get from framework configuration.
        $this->adaptor='mysql';

        switch($this->adaptor) {
            case 'mysql':
//			    $this->conn		= mysql_connect($hostname, $username, $password);
				$this->fields	= mysql_list_fields($database, $table, $this->conn);
				$this->columns  = mysql_num_fields($this->fields);
				
//			    $this->conn		= mysql_connect($hostname, $username, $password);
//				$this->fields	= mysql_list_fields($database, $table, $this->conn) or die("DB, table or connection error.");
//				$this->columns  = mysql_num_fields($this->fields);
				
				return true;
				break;
		
            case 'pgsql':
				die("Adaptor PgSQL :: not implemented.");
				
				return true;
				break;
        }
		
        return false;
    }

	public function action($action = '/') {
		
		$this->action = $action;
	}
	
    /**
     *    Submits the form to the database IF form 'pkey' value is set then
     *    UPDATE record ELSE INSERT a new record.
     *    @param string $echo			Echo output.
     *    @return
     *    @access private
     */
    function submitForm($echo = true) {
		
        // return saved value if already submitted avoids double submit if
        // called explicitly and then form is opened.
        //if($this->submitted) {
        //	return $this->submitted;
        //}
		
		$fields = '';
		$values = '';
		
        if(isset($_POST['pkey'])) {
            $this->pkeyID = $_POST['pkey'];
			
            // if($this->_checkValidation() == -1){ $this->submitted=-1; return -1; }
            //cycle through $_POST variables to form query assignments
            foreach($_POST as $key=>$value) {
                //ignore formitable specific variables and fields not in signature
                if( isset($this->skip[$key]) || strstr($key,"_verify") ||
                    (isset($this->_signature) && !in_array($key, $this->_signature)) ) continue;

                // assign comma seperated value if checkbox or multiselect, otherwise normal assignment
                if(is_array($value)) {
                    $fields.= ",`$key` = '".implode(",",$_POST[$key])."'";
                } else {
                    //@$fields .= ",`$key` = '".($this->_magic_quotes?$value:addslashes($value))."'";
                    $fields.= ",`$key` = '".mysql_real_escape_string($value)."'";
                }
            }
            // Remove first comma
            $fields = substr($fields, 1);
			
            // form and execute query, echoing results
            $SQLquery = "UPDATE $this->table SET $fields WHERE `$this->pkey` = '".$_POST['pkey']."'";
            mysql_select_db($this->DB,$this->conn);
            mysql_query($SQLquery,$this->conn);
			
            if( mysql_error()=="" ) {
                //return "<p>PMD $SQLquery</p>";
                $this->submitted=1;
            } else {
                return $this->msg_updateFail.($this->mysql_errors?"<br/>".mysql_error():"");
            }
        } else {
			
            // Submit via INSERT
            if($this->_checkValidation() == -1) {
				$this->submitted=-1; return -1; 
			}

            foreach($_POST as $key=>$value){
				
				if (in_array($key, array('module', 'action'))) { 
					continue;
				}
				
				//print "$key $value<br />";
				
                if (isset($this->skip[$key]) || strstr($key,"_verify") || (isset($this->_signature) && !in_array($key, $this->_signature)) ) {
					continue;
				}
				
                $fields.= ",`".$key."`";

                if(is_array($value)) $values .= ",'".implode(",",$value)."'";
                else $values .= ",'".($this->_magic_quotes?$value:addslashes($value))."'";

            }

            // Remove first comma
            $fields = substr($fields,1);
            $values = substr($values,1);

            // Form and execute query, eventually echoing results
            $SQLquery = "INSERT INTO $this->table ($fields) VALUES ($values)";
            //print $SQLquery;
            mysql_select_db($this->DB,$this->conn);
            if(mysql_query($SQLquery,$this->conn)) {

                // Multi page form, select last ID and set pkeyID
                if( isset($_POST['formitable_multipage']) && $_POST['formitable_multipage'] == "start" ){
                    //$lastID = @mysql_insert_id($this->conn);
                    $SQLquery = "SELECT `$this->pkey` FROM `$this->table` ORDER BY `$this->pkey` DESC LIMIT 1";
                    $this->pkeyID = mysql_result(mysql_query($SQLquery,$this->conn),0);
					
                } else if(!isset($_POST['formitable_multipage']) || $_POST['formitable_multipage'] == "end") {

					return $this->messageInsertSuccess == 1;
					
//                    if($echo) {
//						echo $this->messageInsertSuccess; 
//					} else {
//						return $this->messageInsertSuccess;
//					}
                }
				
                $this->submitted=1;
				
				return 1;
				
            } else {
				
                if($echo) {
					
					echo $this->msg_insertFail . ($this->mysql_errors ? "<br />" . mysql_error() : "");
					
				} else {
					
					return $this->msg_insertFail . ($this->mysql_errors ? "Test<br />" . mysql_error() : "");
				}
				
				return 0;
            }
        }
		
        unset($_POST['submit']);
    }
	
	/**
	 * Retrive a record with a primary key field value of argument $id
	 * 
	 * see: setPrimaryKey();
	 *
	 * @param	integer	$id
	 * @param	string	$primaryKey
	 * @return	boolean 
	 */
    function getRecord($id, $primaryKey = 'id') {
		
		$this->setPrimaryKey($primaryKey);
		
        $sql = "SELECT * FROM $this->table WHERE $this->pkey = '$id'";
//        print $sql;

        mysql_select_db($this->DB,$this->conn);
		
        $result = mysql_query($sql,$this->conn);

        if( mysql_num_rows($result) == 1 ) {
            $this->pkeyID = $id;
            $this->record = mysql_fetch_assoc($result);
			
            return true;
			
        } else {
			
            return false;
        }
    }

    //this function forces a form field to an explicit input type regardless of size
    //args are field name and input type, input types are as follows:
    //for enum field - "select" or "radio"
    //for set field- "multiselect" or "checkbox"
    //for string or blob field - "text" or "textarea"
    //string can also be forced as "password" or "file"
    function forceType($fieldName,$inputType) {
		
        if($inputType == "file") {
            $this->hasFiles = true;
        }
        $this->forced[$fieldName] = $inputType;
    }

    function forceTypes($fieldNames,$inputTypes) {
        if( sizeof($fieldNames) != sizeof($inputTypes) ) {
            return false;
        }
        for($i=0;$i<sizeof($fieldNames);$i++) {
            $this->forceType($fieldNames[$i],$inputTypes[$i]);
        }
        return true;
    }

    //this function sets a default value for the field
    function setDefaultValue($fieldName, $fieldValue="", $overrideRetrieved=false) {
        $this->defaultValues[$fieldName]['value'] = $fieldValue;
        if($overrideRetrieved) $this->defaultValues[$fieldName]['override'] = true;
    }

    //this function forces a form field to be skipped on INSERT or UPDATE
    //arg is field name
    function skipField($fieldName) {
        $this->skip[$fieldName] = true;
    }

    function skipFields($fieldNames) {
        if( !is_array($fieldNames) ) {
            return false;
        }
        for($i=0;$i<sizeof($fieldNames);$i++) {
            $this->skip[$fieldNames[$i]] = true;
        }
        return true;
    }

    //this function hides a field from HTML output
    //arg is field name, plural version below
    function hideField($fieldName) {
        $this->hidden[$fieldName] = "hide";
    }

    function hideFields($fieldNames) {
        for($i=0;$i<sizeof($fieldNames);$i++) {
            $this->hidden[$fieldNames[$i]] = "hide";
        }
    }

    //this function sets a field's label text
    //args are field name and label text, plural version below
    function labelField($fieldName,$fieldLabel) {
        $this->labels[$fieldName] = $fieldLabel;
    }

    function labelFields($fieldNames,$fieldLabels) {
        if( sizeof($fieldNames) != sizeof($fieldLabels) ) {
            return false;
        }
        for($i=0;$i<sizeof($fieldNames);$i++) {
            $this->labels[$fieldNames[$i]] = $fieldLabels[$i];
        }
        return true;
    }

    //this function sets the name of the table's primary key,
    //it necessary to retrieve/update a record or for multiPage functionality
    function setPrimaryKey($pkey_name){
        $this->pkey = $pkey_name;
    }

    //this function sets the fieldsets option, value is either true or false
    function toggleFieldSets($toggle){
        $this->fieldSets=$toggle;
    }

    //this function checks records for field value, arg is field name
    function uniqueField($fieldName,$msg="Already taken."){
        $this->unique[$fieldName]['msg'] = $msg;
    }

    //this function registers a new validation type
    //args are method name, regular expression, and optional error text
    function registerValidation($methodName, $regex, $errText = "Invalid input."){
        $this->validationExpression[$methodName]['regex'] = $regex;
        $this->validationExpression[$methodName]['err'] = $errText;
    }

    //this function sets a field's validation type
    //args are field name, method name, and optional custom error text
    function validateField($fieldName, $methodName, $errText = "*NONE*"){
        $this->validate[$fieldName]['method'] = $methodName;
        if($errText!="*NONE*") $this->validate[$fieldName]['err'] = $errText;
    }

    // this sets a key string for rc4 encryption of pkey
    function setEncryptionKey($key){
        if($key!=""){
            $this->rc4key=$key;
            return true;
        } else return false;
    }

    //This function returns a single field value. It is useful to test a field value without printing it
    //this is equivilent to accessing a field like so: $FormitableObj->record["fieldName"] but with some error checking
    function getFieldValue($fieldName) {
		
        if( isset($this->record[$fieldName]) ) {
			return $this->record[$fieldName];
        } else {
			return false;
		}
    }
	
	public function setFieldValue($fieldName, $value) {
		
//		$this->record[$fieldName] = $value;
	}

    // this function returns a single field label. It is useful to get a field label without printing it
    // this is equivilent to accessing a field like so: $FormitableObj->labels["fieldName"] but with some error checking
    function getFieldLabel($fieldName){
        if( isset($this->labels[$fieldName]) ) return $this->labels[$fieldName];
        else return ucwords( str_replace("_", " ", $fieldName) );
    }

    // this function enables the submission of an arbitrary field when encryption is enabled
    // and the field was not output in the form (therefore not included in the form signature)
    function allowField($fieldName){
        if( $fieldName ) $this->signature[] = $fieldName;
    }

    // this function sets a callback function
    function registerCallback($fieldName, $funcName, $mode = "post", $args = ""){
        if( @in_array(strtolower($mode), array("post","retrieve","both")) && is_callable($funcName) ){
            $this->callback[$fieldName]["args"] = $args;
            if($mode == "both"){
                $this->callback[$fieldName]["post"] = $this->callback[$fieldName]["retrieve"] = $funcName;
            } else {
                $this->callback[$fieldName][$mode] = $funcName;
            }
            return true;
        } else {
            return false;
        }
    }

	// sets the error feedback method
	function setFeedback($mode)
	{
		if (@in_array($mode, array("line","box","both"))) {
			$this->feedback = $mode;
			return true;
		} else return false;
	}

	function setFormName($name) {
		$this->formName=$name;
	}

	
	function setFormMethod($type) {
		$this->formMethod=$type;
	}

	function getFormName() {
		return $this->formName;
	}

	function getFormMethod() {
		return $this->formMethod;
	}

	function setSubmitValue($name) {
		$this->setSubmitValue = $name;
	}
	
	// opens the form tag and submits the form if pkey is set
	function openForm($attr="", $autoSubmit=true, $action="") {
		
		$output='';
		if( isset($_POST['submit']) && $autoSubmit ) {

			$submitStatus = $this->submitForm();
			
//			var_dump($_REQUEST);
//			var_dump($submitStatus);

			// outputs error text box if validation failed and $feedback set
			if($submitStatus==-1 && ($this->feedback=="box" || $this->feedback=="both") ) {
				
				$output.= '<center><div class="errbox">';
				
				foreach($this->errMsg as $key=>$value) {
					if(isset($this->labels[$key])) {
						$label = $this->labels[$key];
					} else {
						$label = ucwords( str_replace("_", " ", $key) );
					}
					$output.= '<span class="errBoxName">'.$label.":</span> ".$value."<br />";
				}
				$output.='</div></center>';
			}
		}
		
		// Create form tag.
		$output.= sprintf(
			'<form name="%s" id="formBuilder" action="%s" method="%s"' 
				. ($this->hasFiles ? ' enctype="multipart/form-data"' : '') 
				. ($attr!=""?" ".$attr:"") . '>' 
				
			, $this->getFormName()
			, $this->action
			, $this->getFormMethod()
		);
		
		//
        // output hidden MAX_FILE_SIZE field if files are present
        // to set the upload size smaller than the value in php.ini
        // create an .htaccess file with the following directive
        // php_value upload_max_filesize 1M
        // http://us3.php.net/manual/en/ini.core.php#ini.upload-max-filesize
		//
        if($this->hasFiles){
            $maxBytes = trim(ini_get('upload_max_filesize'));
            $lastChar = strtolower($maxBytes[strlen($maxBytes)-1]);
            if($lastChar=="k"){ $maxBytes=$maxBytes*1024; }
            else if($lastChar=="m"){ $maxBytes=$maxBytes*1024*1024; }
            $output.= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$maxBytes\" />";
        }

        //output hidden pkey field for update opertaions
        if( isset($this->pkeyID) ){
            $pkeyVal = $this->pkeyID;
            $output.= "<input type=\"hidden\" name=\"pkey\" value=\"$pkeyVal\" />";
        }

        //output hidden signature field for security check
        if( isset($this->rc4key) ){
            $sigVal = sha1($this->rc4key);
            $output.= "<input type=\"hidden\" name=\"form_signature\" value=\"$sigVal\" />";
        }

        if( isset($this->multiPageSubmitValue) ) {
            $submitValue=$this->multiPageSubmitValue;
        }

        //$output.= "<div class=\"button\">".($printReset?"<input type=\"reset\" value=\"$resetValue\" class=\"reset\"/>":"");
        //if(strstr($submitValue,"image:")) {
        //	$output.= "<input type=\"hidden\" name=\"submit\"><input type=\"image\" src=\"".str_replace("image:","",$submitValue)."\" class=\"img\"".($attr!=""?" ".$attr:"")."/>";
        //} else {
        //	$output.= "<input type=\"submit\" name=\"submit\" value=\"$submitValue\" class=\"submit\"".($attr!=""?" ".$attr:"")."/>";
        //}
        return $output;
    }

	/*
	 *
	 */
    function createInput($type='', $id='', $name='', $value='', $state='') {
		
        $input= new Input(
            array(
                'type'=> $type,
                'id'=> $id,
                'name'=> $name,
                'value'=> $value,
            )
        );
        //$this->createdWidgets[] = array($name, $Input);
        return $input;
    }
	
	/**
	 * Display the form by using standard output functions.
	 * print, echo object to render form.
	 * 
	 * @return	string		The representation of the model.
	 */
    function __toString() {

		$createdWidgets = array();

		//		$this->createdWidgets[] = array(
		//			'VIEWS'
		//			, '<button>&lt;</button>'
		//			. '<input type="text" size="10" />'
		//			. '<button>&gt;</button>'
		//		);

		if ($this->columns != '') {

			for ($n=0; $n < $this->columns; $n++) {
				$this->createdWidgets[]=$this->_outputField($n);
			}
		}

		$reset  = new Input(array('type'=>'reset'));
		$submit = new Input(array('type'=>'submit', 'id'=>'formSubmit', 'name'=>'submit', 'value'=>$this->setSubmitValue));
		$this->createdWidgets[] = array($reset, $submit);
		$this->layout->setData($this->createdWidgets);

		return $this->openForm() . $this->layout . $this->closeForm();
    }

    //this function closes the form tag & prints a hidden field 'pkey' if a record has been set either manually or through multiPage
    //optional argument is the <div> alignment of the Reset and Submit buttons, value should be "right" or "left", "center" is default
    public function closeForm() {
        return "</form>";
    }
	
	
	private function stripFormSlashes($arr) {
		
		if (!is_array($arr)) {
			return stripslashes($arr);
		} else {
			return array_map(array($this, 'stripFormSlashes'), $arr);
		}
	}
	
	public function mysqlEnumValues($tableName,$fieldName) {
		
		$result = mysql_query("DESCRIBE $tableName");
		while($row = @mysql_fetch_array($result)) {
			ereg('^([^ (]+)(\((.+)\))?([ ](.+))?$',$row['Type'],$fieldTypeSplit);
			$fieldType = $fieldTypeSplit[1];
			$fieldLen = $fieldTypeSplit[3];
			if ( ($fieldType=='enum' || $fieldType=='set') && ($row['Field']==$fieldName) ) {
				$fieldOptions = split("','",substr($fieldLen,1,-1));
				return $fieldOptions;
			}
		}
		return FALSE;
	}
	
	/**
	 * Retrieve a record from another table to be used as labels for enum/set fields
	 * it is used to supply descriptions for smaller names.
	 *
	 * @param	string	$field
	 * @param	string	$name
	 * @param	string	$key
	 * @param	string	$value 
	 */
	public function getLabels($field, $name, $key = "ID", $value = "name") {
		
		$this->labelValues[$field]['tableName']  = $name;
		$this->labelValues[$field]['tableKey']   = $key;
		$this->labelValues[$field]['tableValue'] = $value;
	}
	
	/**
	 * Retrieve a record from another table to be used as values for input
	 * it is used for lookup tables / normalized data.
	 *
	 * @param	string	$fieldName
	 * @param	string	$tableName
	 * @param	string	$tableKey
	 * @param	string	$tableValue
	 * @param	string	$orderBy
	 * @param	string	$whereClause 
	 */
	public function normalize($fieldName, $tableName, $tableKey = 'ID'
			, $tableValue = 'name', $orderBy = 'value ASC', $whereClause = '1'
			) {
		
		$this->normalized[$fieldName]['tableName']   = $tableName;
		$this->normalized[$fieldName]['tableKey']    = $tableKey;
		$this->normalized[$fieldName]['tableValue']  = $tableValue;
		$this->normalized[$fieldName]['orderBy']     = $orderBy;
		$this->normalized[$fieldName]['whereClause'] = $whereClause;
	}

	public function parseAndNormalize($fieldName, $tableName, $tableKey = 'ID'
			, $tableValue = 'name', $orderBy = 'value ASC', $whereClause = '1'
			) {

//		$this->normalized[$fieldName]['tableName']   = $tableName;
//		$this->normalized[$fieldName]['tableKey']    = $tableKey;
//		$this->normalized[$fieldName]['tableValue']  = $tableValue;
//		$this->normalized[$fieldName]['orderBy']     = $orderBy;
//		$this->normalized[$fieldName]['whereClause'] = $whereClause;
		
		$this->parsedAndNormalized[$fieldName]['tableName']   = $tableName;
		$this->parsedAndNormalized[$fieldName]['tableKey']    = $tableKey;
		$this->parsedAndNormalized[$fieldName]['tableValue']  = $tableValue;
		$this->parsedAndNormalized[$fieldName]['orderBy']     = $orderBy;
		$this->parsedAndNormalized[$fieldName]['whereClause'] = $whereClause;
	}
	
	/**
	 * Retrieve normalized data from another field.
	 * 
	 * @param	string $fieldName
	 * @return	boolean 
	 */
    function _getFieldData($fieldName){

		if (isset($this->normalized[$fieldName]['tableName'])) {
			
			$sql = "SELECT `"
						.$this->normalized[$fieldName]['tableKey']."` AS pkey".
						", `"
						.$this->normalized[$fieldName]['tableValue']."` AS value ".
						"FROM `"
						.$this->normalized[$fieldName]['tableName']."` ".
						"WHERE "
						.$this->normalized[$fieldName]['whereClause']." " . 
						"ORDER BY "
						.$this->normalized[$fieldName]['orderBy'];
						;

//			print "[DEBUG] Query :: $sql\n";
			$retrievedData = mysql_query($sql,$this->conn);
			
			if(mysql_error()!=""){
				print "[ERROR] Unable to retrieve normalized data from '".$this->normalized[$fieldName]['tableName']."'".($this->mysql_errors?"<br/>".mysql_error():"");
				return false;
			}
			
			$numPairs = mysql_num_rows($retrievedData);
			$this->normalized[$fieldName]['pairs'] = $numPairs;
//			var_dump($numPairs);
			
			for($i=0; $i<$numPairs; $i++){

				$set = mysql_fetch_assoc($retrievedData);
				
//				var_dump($set);
				
				$this->normalized[$fieldName]['keys'][$i] = $set['pkey'];
				$this->normalized[$fieldName]['values'][$i] = $set['value'];
			}
		}
    }

    //retrieve field labels from another field
    function _getFieldLabels($fieldName,$fieldOptions){

        $fieldOptions= "'".implode("','",$fieldOptions)."'";
        $SQLquery = "SELECT `"
                    .$this->labelValues[$fieldName]['tableKey']."` AS pkey".
                    ", `"
                    .$this->labelValues[$fieldName]['tableValue']."` AS value ".
                    "FROM `"
                    .$this->labelValues[$fieldName]['tableName']."` ".
                    "WHERE `".$this->labelValues[$fieldName]['tableKey']."` IN(".$fieldOptions.")";

        $retrievedData = mysql_query($SQLquery,$this->conn);
        if(mysql_error()!=""){
            echo "ERROR: Unable to retrieve field labels from '".$this->labelValues[$fieldName]['tableName']."'.".($this->mysql_errors?"<br/>".mysql_error():"");
            return false;
        }

        $numPairs = mysql_num_rows($retrievedData);
		
        for($i=0; $i<$numPairs; $i++){
			
            $set = mysql_fetch_assoc($retrievedData);
            $this->labelValues[$fieldName][$set['pkey']] = $set['value'];
        }
    }

    //outputs a hidden field that gets checked on submit to
    //prevent empty set/enum fields from being overlooked when empty (i.e. no fields checked)
    function _putSetCheckField($name){
        if(!isset($this->pkeyID) || isset($this->rc4key)) return;
        echo "<input type=\"hidden\" name=\"formitable_setcheck[]\" value=\"$name\"/>\n\n";
    }

    //prevent empty set/enum fields from being overlooked when empty (i.e. no fields checked)
    //cycle through formitable_setcheck POST variable to assign empty values if necessary
    function _setCheck(){
        if( isset($_POST['formitable_setcheck']) )
        foreach($_POST['formitable_setcheck'] as $key){
            if(!isset($_POST[$key])) $_POST[$key]="";
        }
    }

    //validate field
    function _validateField($fieldName,$fieldValue,$methodName){

        //special case for verify fields
        if($methodName == "_verify"){

            if( $_POST[$fieldName] == $_POST[str_replace("_verify","",$fieldName)] ) return true;
            else{ $this->errMsg[$fieldName] = "Values do not match"; return false; }

        } else if( @ereg($this->validationExpression[$methodName]['regex'],$fieldValue) ){
            return true;
        } else {
            //test if custom error is set
            if( isset($this->validate[$fieldName]['err']) )
                $this->errMsg[$fieldName] = $this->validate[$fieldName]['err'];
            else //otherwise use default error
                $this->errMsg[$fieldName] = $this->validationExpression[$methodName]['err'];
            return false;
        }

    }

    //check validation
    function _checkValidation(){

            //cycle through $_POST variables to test for validation
            foreach($_POST as $key=>$value){

                //decrypt hidden values if encrypted
                if( isset($this->forced[$key]) && $this->forced[$key]=="hidden" && isset($this->rc4key) ){
                    $_POST[$key] = $value;
                }

                $validated = true;
                if( isset($this->validate[$key]) )
                    $validated = $this->_validateField($key,$value,$this->validate[$key]['method']);

                //run callback if set and is callable
                if( isset($this->callback[$key]["post"]) && $validated ){

                    $tmpValue = $this->callback[$key]["post"]($key,$value,$this->callback[$key]["args"]);
                    if( isset($tmpValue["status"]) && $tmpValue["status"] == "failed"){
                        $this->errMsg[$key] = $tmpValue["errMsg"];
                        $validated = false;
                    }
                    else $_POST[$key] = $tmpValue;

                }

                //special cases for unique and verify fields
                if( isset($this->unique[$key]) && $validated ) $this->queryUnique($key);
                if( strstr($key,"_verify") && $validated ) $this->_validateField($key,$value,"_verify");

            }

            //test if there are errors from validation
            if( isset($this->errMsg) ) return -1;

    }

    //this function checks if a field value is unique (not already stored in a record)
    function queryUnique($fieldName){

        $SQLquery = "SELECT `".$fieldName."` FROM ".$this->table." WHERE `".$fieldName."` ='".$_POST[$fieldName]."'";
        //if updating make sure it doesn't select self
        if( isset($_POST['pkey']) ) $SQLquery .= " AND ".$this->pkey." != '".$_POST['pkey']."'";
        if( @mysql_num_rows(@mysql_query($SQLquery)) ) $this->errMsg[$fieldName] = $this->unique[$fieldName]['msg'];

    }

    // this function is used by printForm to write the HTML for all label tags
    // args are field name and label text with optional css class, focus value and fieldset
    function _putLabel($fieldName, $fieldLabel, $css="text", $focus=true, $fieldSet=false) {
        if($focus && $this->jsLabels) {
            $onclick = " onClick=\"forms['$this->formName']['$fieldName'].select();\"";
        } else {
            $onclick = "";
        }
        if( !$this->NS4 && !strstr($fieldName," ")) {
            return "<label class=\"".$css."label\" for=\"".$fieldName."\"$onclick>".$fieldLabel."</label>";
        } else {
            return "<label class=\"".$css."label\" for=\"".$fieldName."\">".$fieldLabel."</label>";
        }
    }

    //this function is called by _outputField. it returns the correct field value by
    //testing if a record has been retrieved using getRecord(), the form is posted
    //or a default value has been set.
    function _putValue($fieldName,$fieldType="text",$fieldValue="*NONE*"){

        $retrieved = isset($this->record);
        if($retrieved){
            $recordValue = isset($this->defaultValues[$fieldName]['override']) ?
                $this->defaultValues[$fieldName]['value'] : $this->record[$fieldName];
        }

        $posted = isset($_POST[$fieldName]);
        if($posted) $postValue = $_POST[$fieldName];

        $default = isset($this->defaultValues[$fieldName]);
        if($default) $defaultValue = $this->defaultValues[$fieldName]['value'];

        switch($fieldType) {
            case "textarea":
                if( $posted && isset($postValue) ) {
                    return $postValue;
                } else if( $retrieved ) {
                    return isset($this->callback[$fieldName]["retrieve"]) ?
                        $this->callback[$fieldName]["retrieve"]($fieldName,$recordValue,$this->callback[$fieldName]["args"])
                            : $recordValue;
                } else if( isset($defaultValue) ) {
                    return $defaultValue;
                }
            break;

            case "hidden":
            case "text":
                if( isset($postValue) ){
                    if( $fieldType=="hidden" && isset($this->rc4key) )
                        $postValue = $this->rc4->_encrypt($this->rc4key, $postValue);
                    return " value=\"$postValue\"";
                }
                else if( isset($recordValue) ){
                    $value = isset($this->callback[$fieldName]["retrieve"]) ?
                        $this->callback[$fieldName]["retrieve"]($fieldName,$recordValue,$this->callback[$fieldName]["args"])
                            : $recordValue;
                    if( $fieldType=="hidden" && isset($this->rc4key) )
                        $value = $this->rc4->_encrypt($this->rc4key, $value);
                    return " value=\"$value\"";
                }
                else if( isset($defaultValue) ){
                    if( $fieldType=="hidden" && isset($this->rc4key) )
                        $defaultValue = $this->rc4->_encrypt($this->rc4key, $defaultValue);
                    return " value=\"$defaultValue\"";
                }
                //accounts for default date & time formats
                else if( $fieldValue != "*NONE*" )
                    return " value=\"$fieldValue\"";
            break;

            case "radio":
                $selectedText = " checked";
            case "select":
                if(!isset($selectedText)) $selectedText = " selected";
                if( ($posted && $postValue == $fieldValue) ||
                    (!$posted && $retrieved && $recordValue == $fieldValue) ||
                    (!$posted && !$retrieved && $default && $defaultValue == $fieldValue)
                ) return $selectedText;
            break;

            case "checkbox":
                $selectedText = " checked";
            case "multi":
                if(!isset($selectedText)) $selectedText = " selected";
                if(
                    ($posted && $postValue && preg_match( '/\b'.$fieldValue.'\b/', implode(",",$postValue) )) ||
                    (!$posted && $retrieved && preg_match('/\b'.$fieldValue.'\b/', $recordValue)) ||
                    (!$posted && !$retrieved && $default && preg_match('/\b'.$fieldValue.'\b/', $defaultValue))
                ){ return $selectedText; }
            break;
        }
        return "";
    }

	// this function forms the core of the class;
	// it is called by public function printField and outputs a single field using a record offset
	function _outputField($n,$attr="",$verify=false)
	{
		$name = @mysql_field_name($this->fields,$n);
		$type = @mysql_field_type($this->fields,$n);
		$len  = @mysql_field_len($this->fields,$n);
		$flag = @mysql_field_flags($this->fields,$n);

		$byForce = false;

		// detect primary key.
		if (strstr($flag,"primary_key")) {
			$this->setPrimaryKey($name);
		}

		// check type is forced, set accordingly.
		if (isset($this->forced[$name])) {
			$byForce = $this->forced[$name];
		}

		// if hidden, set type to skip
		if (isset($this->hidden[$name])) {
			$type = "skip";
		} else {
			$this->signature[] = $name;
		}

		// handle hidden type
		if ($byForce == "hidden" ) {
			return array("<input type=\"hidden\" name=\"$name\"".$this->_putValue($name,"hidden").($attr!=""?" ".$attr:"")." />");
		}

		// set custom label or uppercased-spaced field name
		if ($verify) {
			$verified="_verify";
		} else {
			$verified="";
		}

		if (isset($this->labels[$name.$verified]) ) {
			$label=$this->labels[$name.$verified];
		} else {
			$label=ucwords( str_replace("_", " ", $name.$verified) );
		}

		// error text to label if validation failed
		if ($this->feedback=="line" || $this->feedback=="both" ) {
			//test if verify field and validation failed
			if( $verify && isset($this->errMsg[$name."_verify"]) ) $label .= $this->err_pre.$this->errMsg[$name."_verify"].$this->err_post;
			//else test if regular field validation failed
			else if( isset($this->errMsg[$name]) && $byForce != "button" ) $label .= $this->err_pre.$this->errMsg[$name].$this->err_post;
		}

		// set vars if normalized data was retrieved
		if (isset($this->normalized[$name]) ) $valuePairs = true;	else $valuePairs = false;

		// set vars if enum labels were retrieved
		if (isset($this->labelValues[$name]) ) $labelPairs = true;	else $labelPairs = false;

		switch($type) {
			case "real": case "int":
				if ($valuePairs) {
					$this->_getFieldData($name);
					$select="<select name=\"$name\" id=\"$name\" size=\"1\" class=\"select\"".($attr!=""?" ".$attr:"").">\n";
					for($i=0;$i<$this->normalized[$name]['pairs'];$i++) {
						$select.="<option value=\"".$this->normalized[$name]['keys'][$i]."\"".$this->_putValue($name,"select",$this->normalized[$name]['keys'][$i]).">".$this->normalized[$name]['values'][$i]."</option>\n";
					}
					$select.="</select>";
					return array($this->_putLabel($name,$label,"select",false), $select);
				} else {
					if ($len<$this->textInputLength) {
						$length=$len;
					} else {
						$length=$this->textInputLength;
					}
					return array($this->_putLabel($name,$label), "<input type=\"text\" name=\"$name\" id=\"$name\" size=\"$length\" MAXLENGTH=\"$len\" class=\"text\"".$this->_putValue($name).($attr!=""?" ".$attr:"")." />");
				}
				break;
				
			case "blob":
				if( $byForce == "file" ) {
					
					return array($this->_putLabel($name,$label), "<input type=\"file\" name=\"$name\" id=\"$name\" size=\"$this->fileInputLength\" class=\"file\"".($attr!=""?" ".$attr:"")." />");
					
				} else if( ($len>$this->strFieldToggle || $byForce == "textarea") && $byForce != "text" ) {
					
					// 
					// Fabrication 
					// 
					//print "TEST $name ";
					$this->_getFieldData($name);
					
					return array($this->_putLabel($name,$label), "<textarea name=\"$name\" id=\"$name\" rows=\"$this->textareaRows\" cols=\"$this->textareaCols\" class=\"textarea\"".($attr!=""?" ".$attr:"").">".$this->_putValue($name,"textarea")."</textarea>");
					
				} else {
					
                    return array($this->_putLabel($name,$label), "<input type=\"text\" name=\"$name\" id=\"$name\" size=\"$this->textInputLength\" MAXLENGTH=\"$len\" class=\"text\"".$this->_putValue($name).($attr!=""?" ".$attr:"")." />");
                }
				break;
				
            case "string":
                if( strstr($flag,"enum") ) {
                    if($valuePairs){
                        $this->_getFieldData($name);
                        $len=sizeof($this->normalized[$name]);
                    } else {
                        $options = $this->mysqlEnumValues($this->table,$name);
                        if($labelPairs) $this->_getFieldLabels($name,$options);
                        $len=sizeof($options);
                    }
                    if( ($len > $this->enumFieldToggle || $byForce == "select") && $byForce != "radio"){
                        $select = "<select name=\"$name\" id=\"$name\" size=\"1\" class=\"select\"".($attr!=""?" ".$attr:"").">";
                        if( $valuePairs )
                            for($i=0;$i<$this->normalized[$name]['pairs'];$i++)
                                $select.= "<option value=\"".$this->normalized[$name]['keys'][$i]."\"".$this->_putValue($name,"select",$this->normalized[$name]['keys'][$i]).">".$this->normalized[$name]['values'][$i]."</option>";
                        else
                            foreach($options as $opt){
                                if( isset($this->labelValues[$name][$opt]) ) $optionLabel=$this->labelValues[$name][$opt]; else $optionLabel=$opt;
                                $select.= "<option value=\"$opt\"".$this->_putValue($name,"select",$opt).">$optionLabel</option>";
                            }
                        $select.= "</select>";
                        return array($this->_putLabel($name,$label,"",false), $select);
                    } else {
                        if($this->fieldSets) {
                            $data = "<fieldset class=\"fieldset\">";
                            //$data.= "<legend class=\"legend\">$label</legend>"; // add option for legend
                        }
                        if( $valuePairs ) {
                            for($i=0;$i<$this->normalized[$name]['pairs'];$i++){
                                $data.= "<input type=\"radio\" name=\"$name\" id=\"{$name}_".$this->normalized[$name]['keys'][$i]."\" value=\"".$this->normalized[$name]['keys'][$i]."\" class=\"radio\"".$this->_putValue($name,"radio",$this->normalized[$name]['keys'][$i]).($attr!=""?" ".$attr:"")."/>";
                                $data.= $this->_putLabel($name."_".$this->normalized[$name]['keys'][$i],$this->normalized[$name]['values'][$i],"radio",true,true);
                            }
                        } else {
                            foreach($options as $opt) {
                                if( isset($this->labelValues[$name][$opt]) ) {
                                    $optionLabel=$this->labelValues[$name][$opt];
                                } else {
                                    $optionLabel=$opt;
                                }
                                $data.= "	<input type=\"radio\" name=\"$name\" id=\"{$name}_{$opt}\" value=\"$opt\" class=\"radio\"".$this->_putValue($name,"radio",$opt).($attr!=""?" ".$attr:"")." />";
                                $data.= $this->_putLabel($name."_".$opt,$optionLabel,"radio",true,true);
                            }
                        }
                        if($this->fieldSets)  {
                            $data.= "</fieldset>";
                        }
                        return array($this->_putLabel($name,$label,"",false), $data);
                    }

                } else if( strstr($flag,"set") ) {
                    if( $valuePairs ){
                        $this->_getFieldData($name);
                        $len=sizeof($this->normalized[$name]);
                    }
                    else {
                        $options = $this->mysqlEnumValues($this->table,$name);
                        if($labelPairs) $this->_getFieldLabels($name,$options);
                        $len=sizeof($options);
                    }
                    if( ($len > $this->enumFieldToggle || $byForce == "multiselect") && $byForce != "checkbox" ) {
                        $selectMulti = "<select name=\"".$name."[]\" id=\"$name\" size=\"$this->multiSelectSize\" multiple=\"multiple\" class=\"multiselect\"".($attr!=""?" ".$attr:"").">";
                        if( $valuePairs )
                            for($i=0;$i<$this->normalized[$name]['pairs'];$i++)
                                $selectMulti.= "<option value=\"".$this->normalized[$name]['keys'][$i]."\"".$this->_putValue($name,"multi",$this->normalized[$name]['keys'][$i]).">".$this->normalized[$name]['values'][$i]."</option>";
                        else
                            foreach($options as $opt){
                                if( isset($this->labelValues[$name][$opt]) ) $optionLabel=$this->labelValues[$name][$opt]; else $optionLabel=$opt;
                                $selectMulti.= "<option value=\"$opt\"".$this->_putValue($name,"multi",$opt).">$optionLabel</option>";
                        }
                        $selectMulti.= "</select>";
                        return array($this->_putLabel($name,$label,"",false), $selectMulti);
                    } else {
                        if($this->fieldSets){
                            $checkbox = "<fieldset class=\"fieldset\">";
                            $checkbox.= "<legend class=\"legend\">$label</legend>";
                        } else {
                            $checkbox.= $this->_putLabel($name,$label,"",false);
                        }
                        $cb=0;
                        if( $valuePairs ) {
                            for($i=0;$i<$this->normalized[$name]['pairs'];$i++){
                                $checkbox.= "<input type=\"checkbox\" name=\"".$name."[]\" id=\"{$name}_{$cb}\" value=\"".$this->normalized[$name]['keys'][$i]."\"".$this->_putValue($name,"checkbox",$this->normalized[$name]['keys'][$i]).($attr!=""?" ".$attr:"")." />";
                                $checkbox.= $this->_putLabel($name."_".$cb,$this->normalized[$name]['values'][$i],"checkbox",true,true);
                                $cb++;
                            }
                        } else {
                            foreach($options as $opt){
                                if( isset($this->labelValues[$name][$opt]) ) $optionLabel=$this->labelValues[$name][$opt]; else $optionLabel=$opt;
                                $checkbox.= "<input type=\"checkbox\" name=\"".$name."[]\" id=\"{$name}_{$cb}\" value=\"$opt\"".$this->_putValue($name,"checkbox",$opt).($attr!=""?" ".$attr:"")." />";
                                $checkbox.= $this->_putLabel($name."_".$cb,$optionLabel,"checkbox",true,true);
                                $cb++;
                            }
                        }
                        if($this->fieldSets) {
                            $checkbox.= "</fieldset>";
                        }
                        return array($this->_putLabel($name,$label,"",false), $checkbox);
                    }
                    $this->_putSetCheckField($name);
                } else {
                    // plain text.
                    if($verify) $name = $name."_verify";
                    if( $byForce != "button" ){ $fieldLabel = $this->_putLabel($name,$label); }
                    if($len < $this->textInputLength) $length = $len; else $length=$this->textInputLength;
                    if( ($len>$this->strFieldToggle || $byForce == "textarea") && $byForce != "text" && $byForce != "file" ) {
                        return array($this->_putLabel($name,$label), "<textarea name=\"$name\" id=\"$name\" rows=\"$this->textareaRows\" cols=\"$this->textareaCols\" class=\"textarea\"".($attr!=""?" ".$attr:"").">".$this->_putValue(str_replace("_verify","",$name),"textarea")."</textarea>");
                    } else {
                        if( $byForce == "file" ) {
                            return array("$name $label", "<input type=\"file\" name=\"$name\" id=\"$name\" size=\"$this->fileInputLength\" class=\"file\"".($attr!=""?" ".$attr:"")." />");
                        } else if( $byForce == "button" ) {
                            return array("$name $label", "<input type=\"button\" name=\"$name\" id=\"$name\" value=\"$label\" class=\"button\"".($attr!=""?" ".$attr:"")." />");
                        } else {
                            $fieldType = ($byForce=="password" ? "password" : "text");
                            return array($this->_putLabel($name,$label), "<input type=\"$fieldType\" name=\"$name\" id=\"$name\" size=\"$length\" MAXLENGTH=\"$len\" class=\"text\"".$this->_putValue(str_replace("_verify","",$name)).($attr!=""?" ".$attr:"")." />");
                        }
                    }
                }
				break;
			
            case "date":
                $fieldVals["date"]		= array('size'=>"10",	'default'=>date("Y-m-d"));
				
            case "datetime":
                $fieldVals["datetime"]	= array('size'=>"19",	'default'=>date("Y-m-d H:i:s"));
				
            case "timestamp":
                $fieldVals["timestamp"]	= array('size'=>$len,	'default'=>time());
				
            case "time":
                $fieldVals["time"]		= array('size'=>"8",	'default'=>date("H:i:s"));
				
            case "year":
                $fieldVals["year"]		= array('size'=>"4",	'default'=>date("Y"));
                return array($this->_putLabel($name,$label), "<input type=\"text\" name=\"$name\" id=\"$name\" size=\"".$fieldVals[$type]['size']."\" MAXLENGTH=\"".$fieldVals[$type]['size']."\" ".$this->_putValue($name,"text",$fieldVals[$type]['default'])." class=\"text\"".($attr!=""?" ".$attr:"")." />");
				break;

            case "skip": break;
        }
    }
}
