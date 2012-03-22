<?php
namespace Fabrication\Library\Pattern;

use Fabrication\Library\Pattern\Html;

class HtmlForm extends Html {

	public function __toString() {
		return '<form></form>';
	}

}
