<?php
namespace Fabrication\Library\Pattern;

class Html {

	protected $name  = 'table';
	protected $value = '';

	public $engine;
	//public $data       = array();
	//public $attributes = array();
	
	// http://www.w3.org/QA/2002/04/valid-dtd-list.html
	public $doctypes = array(
		'html.5'					=> '<!DOCTYPE HTML>', // HTML5 Experimental, NOT a standard yet!.
		'html.4.01.strict'			=> "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"\n   \"http://www.w3.org/TR/html4/strict.dtd\">",
		'html.4.01.transitional'	=> "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\n   \"http://www.w3.org/TR/html4/loose.dtd\">",
		'html.4.01.frameset'		=> "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\"\n   \"http://www.w3.org/TR/html4/frameset.dtd\">",
		'xhtml.1.0.strict'			=> "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">",
		'xhtml.1.0.transitional'	=> "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">",
		'xhtml.1.0.frameset'		=> "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"\n   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">",
		// historical doctype declarations.
		'html.2.0'					=> "<!DOCTYPE html PUBLIC \"-//IETF//DTD HTML 2.0//EN\">",
		'html.3.2'					=> "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 3.2 Final//EN\">",
		'xhtml.basic.1.0'			=> "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML Basic 1.0//EN\"\n    \"http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd\">",
	);

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
			/**
			 * 3.2.3 Global attributes
			 * The following attributes are common to and may be specified on 
			 * all HTML elements (even those not defined in this specification):
			 *
			 * _global_attributes.
			 * accesskey, class, contenteditable, contextmenu, dir, draggable
			 * dropzone, hidden, id, lang, spellcheck, style, tabindex, title
			 */
			'_global' => array('accesskey', 'class', 'contenteditable', 
				'contextmenu', 'dir', 'draggable', 'dropzone', 'hidden', 'id', 
				'lang', 'spellcheck', 'style', 'tabindex', 'title'
			),
			//
			// 4.1.1 The root element.
			//
			'html' => array('manifest'),
			//
			// 4.2.* Document metadata
			// head, title, base, link, meta, style
			// 
			'head'	=> array(),
			'title'	=> array(),
			'base'	=> array('href', 'target'),
			'link'	=> array('href', 'rel', 'media', 'hreflang', 'type', 'sizes'),
			'meta'	=> array('name', 'http-equiv', 'content', 'charset'),
			'style'	=> array('media', 'type', 'scoped', 'title'),
			//
			// 4.3.* Scripting.
			// script, noscript
			//  
			'script'	=> array('src', 'async', 'defer', 'type', 'charset'),
			'noscript'	=> array(),
			//
			// 4.4 Sections
			// 4.4.* The body element.
			// body, section, nav, article, aside, h1, h2, h3, h4, h5, h6,
			// hgroup, header, footer, address
			//
			'body'		=> array('onafterprint', 'onb/eforeprint', 
				'onbeforeunload', 'onblur', 'onerror', 'onfocus', 
				'onhashchange', 'onload', 'onmessage', 'onoffline', 'ononline', 
				'onpagehide', 'onpageshow', 'onpopstate', 'onredo', 'onresize', 
				'onscroll', 'onstorage', 'onundo', 'onunload'
			),
			'section'	=> array(),
			'nav'		=> array(),
			'article'	=> array(),
			'aside'		=> array(),
			'h1'		=> array(),
			'h2'		=> array(),
			'h3'		=> array(),
			'h4'		=> array(),
			'h5'		=> array(),
			'h6'		=> array(),
			'hgroup'	=> array(),
			'header'	=> array(),
			'footer'	=> array(),
			'address'	=> array(),
			//
			// 4.5.* Grouping content
			// p, hr, pre, blockquote, ol, ul, li, dl, dt, dd
			// figure, figcaption, div
			// 
			'p'				=> array(),
			'hr'			=> array(),
			'pre'			=> array(),
			'blockquote'	=> array(),
			'ol'			=> array(),
			'ul'			=> array(),
			'li'			=> array(),
			'dl'			=> array(),
			'dt'			=> array(),
			'dd'			=> array(),
			'figure'		=> array(),
			'figcaption'	=> array(),
			'div'			=> array(),
			//
			// 4.6.* Text-level semantics
			// a, em, strong, small, s, cite, q, dfn, abbr, time, code, var
			// samp, kbd, sub, sup, i, b, u, mark, ruby, rt, rp, bdi ,bdo
			// span, br, wbr
			//
			'a'			=> array('href', 'target', 'rel', 'media', 'hreflang',
				'type'
			),
			'em'		=> array(),
			'strong'	=> array(),
			'small'		=> array(),
			's'			=> array(),
			'cite'		=> array(),
			'q'			=> array('cite'),
			'dfn'		=> array('title'),
			'abbr'		=> array('title'),
			'time'		=> array('datetime', 'pubdate'),
			'code'		=> array(),
			'var'		=> array(),
			'samp'		=> array(),
			'kbd'		=> array(),
			'sub'		=> array(),
			'sup'		=> array(),
			'i'			=> array(),
			'b'			=> array(),
			'u'			=> array(),
			'mark'		=> array(),
			'ruby'		=> array(),
			'rt'		=> array(),
			'rp'		=> array(),
			'bdi'		=> array('dir'),
			'bdo'		=> array('dir'),
			'span'		=> array(),
			'br'		=> array(),
			'wbr'		=> array(),
			//
			// 4.7 Edits
			// ins, del
			'ins' => array('cite', 'datetime'),
			'del' => array('cite', 'datetime' ),
			//
			// 4.8.* Embedded content
			// img, iframe, embed, object, param, video, audio, source, track
			//
			'img'		=> array('alt', 'src', 'usemap', 'ismap', 'width', 
				'height'
			),
			'iframe'	=> array('src', 'srcdoc', 'name', 'sandbox', 'seamless',
				'width', 'height'
			),
			'embed'		=> array('src', 'type', 'width', 'height'),
			'object'	=> array('data', 'type', 'name', 'usemap', 'form', 
				'width', 'height'
			),
			'param'		=> array('name', 'value'),
			'video'		=> array('src', 'poster', 'preload', 'autoplay', 
				'mediagroup', 'loop', 'muted', 'controls', 'width', 'height'
			),
			'audio'		=> array('src', 'preload', 'autoplay', 'mediagroup', 
				'loop', 'muted', 'controls'
			),
			'source'	=> array('src', 'type', 'media'),
			'track'		=> array('kind', 'src', 'srclang', 'label', 'default'),
			// 4.8.10 Media elements
			// 4.8.11 The canvas element
			// map, area
			'canvas'	=> array('width', 'height'),
			'map'		=> array('name'),
			'area'		=> array('alt', 'coords', 'shape', 'href', 'target', 
				'rel', 'media', 'hreflang', 'type'
			),
			//
			// 4.9.* Tabular data
			// table, caption, colgroup, col, tbody, thead, tfoot, tr, td, th
			//  
			'table'		=> array('border'),
			'caption'	=> array(),
			'colgroup'	=> array('span'),
			'col'		=> array('span'),
			'tbody'		=> array(),
			'thead'		=> array(),
			'tfoot'		=> array(),
			'tr'		=> array(),
			'td'		=> array('colspan', 'rowspan', 'headers'),
			'th'		=> array('colspan', 'rowspan', 'headers', 'scope'),
			//
			// 4.10 Forms
			//
			// 4.10.3 The form element
			//
			// TODO
			
		),

		
		//
		// This spec is uncomplete !!
		// 
		// TODO add _global tag for shared attributes.
		// TODO add every section from the w3c spec
		// TODO add html global and remove global repetition.
		//
		'html.4.01.transitional' => array(
			/**
			 * http://www.w3.org/TR/html401/about.html
			 * 
			 * 1 About the HTML 4 Specification
			 * 2 Introduction to HTML 4
			 * 3 On SGML and HTML
			 * 4 Conformance: requirements and recommendations
			 * 5 HTML Document Representation
			 * 6 Basic HTML data types
			 */
			'_global' => array(),
			
			/**
			 * http://www.w3.org/TR/html401/struct/global.html
			 * 
			 * 7 The global structure of an HTML document
			 * 7.1 Introduction to the structure of an HTML document
			 * 7.2 HTML version information
			 * 7.3 The HTML element
			 */
			'html'		=> array('lang'),
			 // 7.4 The document head
			'head'		=> array('profile', 'lang'),
			'title'		=> array('lang'),
			'meta'		=> array('name', 'content', 'scheme', 'http-equiv', 
				'lang'
			),
			// 7.5 The document body
			'body'		=> array('id', 'class', 'lang', 'title', 'style', 
				'bgcolor', 'onload', 'onunload', 'onclick', 'ondblclick', 
				'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 
				'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'
			),
			// 7.5.4 Grouping elements: the DIV and SPAN elements
			'div'		=> array('id', 'class', 'lang', 'title', 'style', 
				'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 
				'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 
				'onkeydown', 'onkeyup'
			),
			'span'		=> array('id', 'class', 'lang', 'title', 'style', 
				'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 
				'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 
				'onkeydown', 'onkeyup'
			),
			// 7.5.5 Headings: The H1, H2, H3, H4, H5, H6 elements
			'h1'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'h2'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'h3'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'h4'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'h5'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'h6'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			// 7.5.6 The ADDRESS element
			'address'	=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),			
			
			/**
			 * http://www.w3.org/TR/html401/struct/dirlang.html
			 * 
			 * 8 Language information and text direction
			 */

			/**
			 * http://www.w3.org/TR/html401/struct/text.html
			 * 
			 * 9 Text
			 * 9.2 Structured text
			 * 9.2.1 Phrase elements: EM, STRONG, DFN, CODE, SAMP, KBD, VAR, CITE, ABBR, and ACRONYM
			 */
			'em'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'strong'	=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'dfn'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'code'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'samp'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'kbd'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'var'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'cite'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'abbr'		=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'acronym'	=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			// 9.2.2 Quotations: The BLOCKQUOTE and Q elements.
			'blockquote'=> array( 'id', 'class', 'lang', 'title', 'style','onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'q'			=> array( 'id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			/**
			 * 9.3 Lines and Paragraphs.
			 */
			'p'			=> array('id', 'class', 'lang', 'title', 'style', 'align', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			// 9.3.2 Controlling line breaks.
			'br'		=> array('id', 'class', 'title', 'style', 'clear'),
			// 9.3.3 Hyphenation
			// 9.3.4 Preformatted text: The PRE element
			'pre'		=> array('id', 'class', 'title', 'style', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			// 9.3.5 Visual rendering of paragraphs
			/**
			 * 9.4 Marking document changes: The INS and DEL elements
			 */
			'ins'		=> array('cite', 'datetime', 'id', 'class', 'title', 'style', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
			'del'		=> array('cite', 'datetime', 'id', 'class', 'title', 'style', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'),
				
			// TODO
			'form'		=> array(),
			'base'		=> array(),
			'script'	=> array(),
			'noscript'	=> array(),

			'ul'		=> array(),
			'ol'		=> array(),
			'dl'		=> array(),
			'dt'		=> array(),
			'dd'		=> array(),
			'meta'		=> array('name', 'content', 'scheme', 'http-equiv'),
			'table'		=> array(),
			'caption'	=> array(),
			'thead'		=> array(),
			'tbody'		=> array(),
			'tfoot'		=> array(),
			'colgroup'	=> array(),
			'col'		=> array(),
			'tr'		=> array(),
			'th'		=> array(),
			'td'		=> array(),
			'a'			=> array(),
			'img'		=> array(),
			'object'	=> array(),
			'param'		=> array(),
			'map'		=> array(),
			
			/**
			 * 14	Style Sheets
			 * http://www.w3.org/TR/html401/present/styles.html
			 */
			'style'		=> array(),
			
			
			/**
			 * 15.2.1	Font style elements: the TT, I, B, BIG, SMALL, STRIKE, S, and U elements
			 * 
			 */
			'tt'		=> array(),
			'i'			=> array(),
			'b'			=> array(),
			'big'		=> array(),
			'small'		=> array(),			
			'strike'	=> array(),			
			'u'			=> array(),			
		)
	);

	
	/**
	 * Main construction method.
	 * 
	 * @param type $engine	FabricationEngine
	 */
	public function __construct($engine) {

		$this->engine = $engine;
	}


	/**
	 * Main execution method for generating a HTML DOM structure. 
	 * 
	 * @return	void
	 */
	public function execute($row  = array('name'=>'tr'), $data = array('name'=>'td')) {
		
		$contract = array();
		
		if (!isset($this->name)) {
			$this->name = strtolower(join('', array_slice(explode('\\', __CLASS__), -1)));
		}
		
		if (sizeof($this->dataset) > 0) {
			
			foreach($this->dataset as $did => $drow) {
				
				if (is_array($drow)) {

					$children = array();
					
					foreach($drow as $rid => $item) {

						$children[] = array(
							'name' => $data['name'], 
							'value' => $item
						);
					}
					
					$contract[] = array(
						'name' => $row['name'], 
						'children' => $children
					);
				}
			}
		}
	
		$this->fabric = $this->engine->create($this->name, $this->value, 
			$this->attributes, $contract
		);
	}


	/**
	 * HTML representation of the dataset
	 * 
	 * @return	string	The HTML structure.
	 */
	public function __toString() {
		
		if (isset($this->fabric)) {
			
			$this->engine->appendChild($this->fabric);
			
			return $this->engine->saveHTML();
		}

		return $this->engine->specification()->saveFabric();
	}
}