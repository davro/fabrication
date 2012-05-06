<?php
namespace Fabrication\Library\Pattern\Html;

use Fabrication\Library\FabricationEngine as FabricationEngine;
use Fabrication\Library\Pattern\Html;

/**
 * HTML Form 
 * 
 * @author	David Stevens <mail.davro@gmail.com> 
 */
class Form extends Html {

	protected $name  = 'form';
	protected $value = '';

	public $engine;
	public $data = array();
	public $attributes = array();


	/**
	 * Constructor for building table structures.
	 * A table is a means of arranging data in rows and columns.
	 * 
	 * @param FabricationEngine	$engine	The engine for generating elements.
	 * @param string			$data	The data to use for tabular data.
	 */
	public function __construct(FabricationEngine $engine, 
			$attributes = array(), 
			$dataset = array()
		) {

		$this->engine     = $engine;
		$this->attributes = $attributes;
		$this->dataset    = $dataset;

		$this->execute(array('name'=>'div'), array('name'=>'div'));
	}
}