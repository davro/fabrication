<?php
namespace Library;

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
		'html.5'					=> '<!DOCTYPE HTML>', // Experimental, not a standard yet.
		'html.4.01.strict'			=> "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"\n   \"http://www.w3.org/TR/html4/strict.dtd\">",
		'html.4.01.transitional'	=> "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\n   \"http://www.w3.org/TR/html4/loose.dtd\">",
		'html.4.01.frameset'		=> "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\"\n   \"http://www.w3.org/TR/html4/frameset.dtd\">",
		// HTML Historical doctypes.
		'html.3.2'					=> "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 3.2 Final//EN\">",		
		'html.2.0'					=> "<!DOCTYPE html PUBLIC \"-//IETF//DTD HTML 2.0//EN\">",
		
		// XHTML 
		'xhtml.1.0.strict'			=> "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">",
		'xhtml.1.0.transitional'	=> "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">",
		'xhtml.1.0.frameset'		=> "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"\n   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">",
		// XHTML Historical doctypes.
		'xhtml.basic.1.0'			=> "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML Basic 1.0//EN\"\n    \"http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd\">",
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
			'_head' => array(
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
			),
			
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
			'_body' => array(
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
				'form'		=> array(),
				// 4.10.4 The fieldset element
				'fieldset'	=> array(),
				// 4.10.5 The legend element
				'legend'	=> array(),
				// 4.10.6 The label element
				'label'		=> array(),
				// 4.10.7 The input element
				'input'		=> array(
					'type'=>array('hidden', 'text', 'search', 'tel', 'url', 'email', 'password','datetime', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'range', 'color', 'checkbox', 'radio', 'file', 'submit', 'image', 'reset', 'button'), 
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
				//
				//4.10.7.1 States of the type attribute
				//	4.10.7.1.1 Hidden state (type=hidden)
				//	4.10.7.1.2 Text (type=text) state and Search state (type=search)
				//	4.10.7.1.3 Telephone state (type=tel)4.10.7.1.4 URL state (type=url)
				//	4.10.7.1.5 E-mail state (type=email)
				//	4.10.7.1.6 Password state (type=password)
				//	4.10.7.1.7 Date and Time state (type=datetime)
				//	4.10.7.1.8 Date state (type=date)
				//	4.10.7.1.9 Month state (type=month)
				//	4.10.7.1.10 Week state (type=week)
				//	4.10.7.1.11 Time state (type=time)
				//	4.10.7.1.12 Local Date and Time state (type=datetime-local)
				//	4.10.7.1.13 Number state (type=number)
				//	4.10.7.1.14 Range state (type=range)
				//	4.10.7.1.15 Color state (type=color)
				//  4.10.7.1.16 Checkbox state (type=checkbox)
				//	4.10.7.1.17 Radio Button state (type=radio)
				//	4.10.7.1.18 File Upload state (type=file)
				//	4.10.7.1.19 Submit Button state (type=submit)
				//	4.10.7.1.20 Image Button state (type=image)
				//	4.10.7.1.21 Reset Button state (type=reset)
				//	4.10.7.1.22 Button state (type=button)
				//4.10.7.2 Implemention notes regarding localization of form controls
				//
				//4.10.7.3 Common input element attributes
				//
				//	4.10.7.3.1 The autocomplete attribute
				//	4.10.7.3.2 The dirname attribute
				//	4.10.7.3.3 The list attribute
				//	4.10.7.3.4 The readonly attribute
				//	4.10.7.3.5 The size attribute
				//	4.10.7.3.6 The required attribute
				//	4.10.7.3.7 The multiple attribute
				//	4.10.7.3.8 The maxlength attribute
				//	4.10.7.3.9 The pattern attribute
				//	4.10.7.3.10 The min and max attributes
				//	4.10.7.3.11 The step attribute
				//	4.10.7.3.12 The placeholder attribute
				//
				//	4.10.7.4 Common input element APIs
				//	4.10.7.5 Common event behaviors
				//
				//	4.10.8 The button element
				'button'		=> array(),
				//	4.10.9 The select element
				'select'		=> array(),
				//	4.10.10 The datalist element
				'datalist'		=> array(),
				//	4.10.11 The optgroup element
				'optgroup'		=> array(),
				//	4.10.12 The option element
				'option'		=> array(),
				//	4.10.13 The textarea element
				'textarea'		=> array(),
				//	4.10.14 The keygen element
				'keygen'		=> array(),
				//	4.10.15 The output element
				'output'		=> array(),
				//	4.10.16 The progress element
				'progress'		=> array(),
				//	4.10.17 The meter element
				'meter'			=> array(),
				//
				//	4.11 Interactive elements
				//	4.11.1 The details element
				'details'		=> array(),
				//	4.11.2 The summary element
				'summary'		=> array(),
				//	4.11.3 The command element
				'command'		=> array(),
				//	4.11.4 The menu element
				'menu'			=> array(),
			)
		),

		
		//
		// This HTML4 transitional specification is uncomplete !!
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
			'_global' => array('id', 'class', 'lang', 'title', 'style', 'align'
				, 'onclick', 'ondblclick', 'onmousedown', 'onmouseup'
				, 'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress'
				, 'onkeydown', 'onkeyup'
			),
			
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
			'_head' => array(
			   'title'		=> array('lang'),
			   'meta'		=> array('name', 'content', 'scheme', 'http-equiv', 'lang'),
			),
			
			// 7.5 The document body
			'body'		=> array(),
			'_body' => array(

				// 7.5.4 Grouping elements: the DIV and SPAN elements
				'div'		=> array(),
				'span'		=> array(),

				// 7.5.5 Headings: The H1, H2, H3, H4, H5, H6 elements
				'h1'		=> array(),
				'h2'		=> array(),
				'h3'		=> array(),
				'h4'		=> array(),
				'h5'		=> array(),
				'h6'		=> array(),

				// 7.5.6 The ADDRESS element
				'address'	=> array(),

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
				'em'		=> array(),
				'strong'	=> array(),
				'dfn'		=> array(),
				'code'		=> array(),
				'samp'		=> array(),
				'kbd'		=> array(),
				'var'		=> array(),
				'cite'		=> array(),
				'abbr'		=> array(),
				'acronym'	=> array(),
				// 9.2.2 Quotations: The BLOCKQUOTE and Q elements.
				'blockquote'=> array(),
				'q'			=> array(),
				/**
				 * 9.3 Lines and Paragraphs.
				 */
				'p'			=> array(),
				// 9.3.2 Controlling line breaks.
				'br'		=> array(),
				// 9.3.3 Hyphenation
				// 9.3.4 Preformatted text: The PRE element
				'pre'		=> array(),
				// 9.3.5 Visual rendering of paragraphs
				/**
				 * 9.4 Marking document changes: The INS and DEL elements
				 */
				'ins'		=> array(),
				'del'		=> array(),

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
			),
		),
	);
}