<?php
namespace Fabrication;

/**
 * Fabricator
 * 
 */
class Fabricator
{	
	public $name;
	
	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function getName($name)
	{
		return $this->name;
	}
	
	public function __toString() 
	{
		return $this->name;
	}
}
