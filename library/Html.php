<?php
namespace Fabrication;

class Html
{
	/**
	 * List of supported doctypes.
	 * 
	 * http://www.w3.org/QA/2002/04/valid-dtd-list.html
	 * 
	 * @var	array
	 */
	public $doctypes = array(
		// HTML
		'html.5' => '<!DOCTYPE HTML>',
		'html.4.01.strict' => "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"\n   \"http://www.w3.org/TR/html4/strict.dtd\">",
		'html.4.01.transitional' => "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\n   \"http://www.w3.org/TR/html4/loose.dtd\">",
		'html.4.01.frameset' => "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\"\n   \"http://www.w3.org/TR/html4/frameset.dtd\">",
		// HTML Historical doctypes.
		'html.3.2' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 3.2 Final//EN\">",
		'html.2.0' => "<!DOCTYPE html PUBLIC \"-//IETF//DTD HTML 2.0//EN\">",
		// XHTML 
		'xhtml.1.0.strict' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">",
		'xhtml.1.0.transitional' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">",
		'xhtml.1.0.frameset' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"\n   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">",
		// XHTML Historical doctypes.
		'xhtml.basic.1.0' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML Basic 1.0//EN\"\n    \"http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd\">",
	);

	/**
	 * Html specification
	 * 
	 * @var array 
	 */
	public $specification = array(
		/**
		 * W3C HTML5 specification.
		 * 
		 * http://www.w3.org/TR/html5/
		 *
		 *  1 Introduction
		 *  Common infrastructure
		 */
		'html.5' => array(
			'_global' => array('accesskey', 'class', 'contenteditable',
				'contextmenu', 'dir', 'draggable', 'dropzone', 'hidden', 'id',
				'lang', 'spellcheck', 'style', 'tabindex', 'title'
			),
			'html' => array('manifest'),
			'head' => array(),
			'_head' => array(
				'title' => array(),
				'base' => array('href', 'target'),
				'link' => array('href', 'rel', 'media', 'hreflang', 'type', 'sizes'),
				'meta' => array('name', 'http-equiv', 'content', 'charset'),
				'style' => array('media', 'type', 'scoped', 'title'),
				'script' => array('src', 'async', 'defer', 'type', 'charset'),
				'noscript' => array(),
			),
			'body' => array('onafterprint', 'onb/eforeprint',
				'onbeforeunload', 'onblur', 'onerror', 'onfocus',
				'onhashchange', 'onload', 'onmessage', 'onoffline', 'ononline',
				'onpagehide', 'onpageshow', 'onpopstate', 'onredo', 'onresize',
				'onscroll', 'onstorage', 'onundo', 'onunload'
			),
			'_body' => array(
				'body' => array(),
				'section' => array(),
				'nav' => array(),
				'article' => array(),
				'aside' => array(),
				'h1' => array(),
				'h2' => array(),
				'h3' => array(),
				'h4' => array(),
				'h5' => array(),
				'h6' => array(),
				'hgroup' => array(),
				'header' => array(),
				'footer' => array(),
				'address' => array(),
				'p' => array(),
				'hr' => array(),
				'pre' => array(),
				'blockquote' => array(),
				'ol' => array(),
				'ul' => array(),
				'li' => array(),
				'dl' => array(),
				'dt' => array(),
				'dd' => array(),
				'figure' => array(),
				'figcaption' => array(),
				'div' => array(),
				'a' => array('href', 'target', 'rel', 'media', 'hreflang',
					'type'
				),
				'em' => array(),
				'strong' => array(),
				'small' => array(),
				's' => array(),
				'cite' => array(),
				'q' => array('cite'),
				'dfn' => array('title'),
				'abbr' => array('title'),
				'time' => array('datetime', 'pubdate'),
				'code' => array(),
				'var' => array(),
				'samp' => array(),
				'kbd' => array(),
				'sub' => array(),
				'sup' => array(),
				'i' => array(),
				'b' => array(),
				'u' => array(),
				'mark' => array(),
				'ruby' => array(),
				'rt' => array(),
				'rp' => array(),
				'bdi' => array('dir'),
				'bdo' => array('dir'),
				'span' => array(),
				'br' => array(),
				'wbr' => array(),
				'ins' => array('cite', 'datetime'),
				'del' => array('cite', 'datetime'),
				'img' => array('alt', 'src', 'usemap', 'ismap', 'width',
					'height'
				),
				'iframe' => array('src', 'srcdoc', 'name', 'sandbox', 'seamless',
					'width', 'height'
				),
				'embed' => array('src', 'type', 'width', 'height'),
				'object' => array('data', 'type', 'name', 'usemap', 'form',
					'width', 'height'
				),
				'param' => array('name', 'value'),
				'video' => array('src', 'poster', 'preload', 'autoplay',
					'mediagroup', 'loop', 'muted', 'controls', 'width', 'height'
				),
				'audio' => array('src', 'preload', 'autoplay', 'mediagroup',
					'loop', 'muted', 'controls'
				),
				'source' => array('src', 'type', 'media'),
				'track' => array('kind', 'src', 'srclang', 'label', 'default'),
				'canvas' => array('width', 'height'),
				'map' => array('name'),
				'area' => array('alt', 'coords', 'shape', 'href', 'target',
					'rel', 'media', 'hreflang', 'type'
				),
				'table' => array('border'),
				'caption' => array(),
				'colgroup' => array('span'),
				'col' => array('span'),
				'tbody' => array(),
				'thead' => array(),
				'tfoot' => array(),
				'tr' => array(),
				'td' => array('colspan', 'rowspan', 'headers'),
				'th' => array('colspan', 'rowspan', 'headers', 'scope'),
				'form' => array(),
				'fieldset' => array(),
				'legend' => array(),
				'label' => array(),
				'input' => array(
					'type' => array('hidden', 'text', 'search', 'tel', 'url', 'email', 'password', 'datetime', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'range', 'color', 'checkbox', 'radio', 'file', 'submit', 'image', 'reset', 'button'),
					'autocomplete',
					'dirname',
					'list',
					'readonly',
					'size',
					'required',
					'multiple',
					'maxlength',
					'pattern',
					'min',
					'max',
					'step',
					'placeholder'
				),
				'button' => array(),
				'select' => array(),
				'datalist' => array(),
				'optgroup' => array(),
				'option' => array(),
				'textarea' => array(),
				'keygen' => array(),
				'output' => array(),
				'progress' => array(),
				'meter' => array(),
				'details' => array(),
				'summary' => array(),
				'command' => array(),
				'menu' => array(),
			)
		),
		'html.4.01.transitional' => array(
			'_global' => array('id', 'class', 'lang', 'title', 'style', 'align'
				, 'onclick', 'ondblclick', 'onmousedown', 'onmouseup'
				, 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress'
				, 'onkeydown', 'onkeyup'
			),
			'html' => array('lang'),
			// 7.4 The document head
			'head' => array('profile', 'lang'),
			'_head' => array(
				'title' => array('lang'),
				'meta' => array('name', 'content', 'scheme', 'http-equiv', 'lang'),
			),
			// 7.5 The document body
			'body' => array(),
			'_body' => array(
				'body' => array(),
				'div' => array(),
				'span' => array(),
				'h1' => array(),
				'h2' => array(),
				'h3' => array(),
				'h4' => array(),
				'h5' => array(),
				'h6' => array(),
				'address' => array(),
				'em' => array(),
				'strong' => array(),
				'dfn' => array(),
				'code' => array(),
				'samp' => array(),
				'kbd' => array(),
				'var' => array(),
				'cite' => array(),
				'abbr' => array(),
				'acronym' => array(),
				'blockquote' => array(),
				'q' => array(),
				'p' => array(),
				'br' => array(),
				'pre' => array(),
				'ins' => array(),
				'del' => array(),
				'form' => array(),
				'base' => array(),
				'script' => array(),
				'noscript' => array(),
				'ul' => array(),
				'ol' => array(),
				'dl' => array(),
				'dt' => array(),
				'dd' => array(),
				'meta' => array('name', 'content', 'scheme', 'http-equiv'),
				'table' => array(),
				'caption' => array(),
				'thead' => array(),
				'tbody' => array(),
				'tfoot' => array(),
				'colgroup' => array(),
				'col' => array(),
				'tr' => array(),
				'th' => array(),
				'td' => array(),
				'a' => array(),
				'img' => array(),
				'object' => array(),
				'param' => array(),
				'map' => array(),
				'style' => array(),
				'tt' => array(),
				'i' => array(),
				'b' => array(),
				'big' => array(),
				'small' => array(),
				'strike' => array(),
				'u' => array(),
			),
		),
	);
}
