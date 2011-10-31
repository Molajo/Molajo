<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright	Copyright (C) Luvue, LLC. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Plug-in to handle JavaScript in the document head.
 *
 * @package		Molajo
 * @subpackage	Compress
 */
class plgSystemCompress extends MolajoPlugin
{
	/**
	 * @access  protected
	 * @var     array
	 */
	var $scripts = array();

	/**
	 * @access  protected
	 * @var     array
	 */
	var $styleSheets = array();

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args(void) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	public
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemCompress(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Method to listen to the custom 'onBeforeCompileHead' event fired in JDocumentRendererHead
	 * which is found in /libraries/joomla/document/html/renderer/head.php
	 *
	 * Note: The libpatch JParameterElement class packaged with this plugin patches the above
	 * library so that it fires this event.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onBeforeCompileHead()
	{
		// Do nothing in administration console.

		// Get the system document object.
		$document = & MolajoFactory::getDocument();

		// Get the document head data.
		$head = $document->getHeadData();

		// Reset the document scripts to empty.
		$inputStyleSheets = $head['styleSheets'];
		$head['styleSheets'] = array();
		$styleSheets = array();

		// Reset the document scripts to empty.
		$inputScripts = $head['scripts'];
		$head['scripts'] = array();
		$scripts = array();

		// Replace the document head data.
		$document->setHeadData($head);

		// If the manual overload for scripts is set use the ones set in the plugin options.
		if ($this->params->get('scripts_overload')) {
			$scripts = explode("\n", trim($this->params->get('scripts')));
		} else {
			foreach ($inputScripts as $k => $v)
			{
				// If we are talking about basic scripts then add to concatenate list.
				if ($v == 'text/javascript') {
					$scripts[] = $k;
				}
				// Non-standard stuff we just pass through.
				else {
					$this->scripts[] = array('url' => $k, 'type' => $v);
				}
			}
		}

		// Get the JavaScript scripts to include.
		if (!empty($scripts)) {
			$this->processJavaScript($scripts);

			// Add the scripts to the document head.
			foreach ($this->scripts as $script)
			{
				$document->addScript($script['url'], @$script['type']);
			}
		}

		// If the manual overload for stylesheets is set use the ones set in the plugin options.
		if ($this->params->get('stylesheets_overload')) {
			$styleSheets = explode("\n", trim($this->params->get('stylesheets')));
		} else {
			foreach ($inputStyleSheets as $k => $v)
			{
				// If we are talking about basic stylesheets then add to concatenate list.
				if (($v['mime'] == 'text/css') && empty($v['media']) && empty($v['attribs'])) {
					$styleSheets[] = $k;
				}
				// Non-standard stuff we just pass through.
				else {
					$this->styleSheets[] = array('url' => $k, 'type' => $v['mime'], 'media' => $v['media'], 'attribs' => $v['attribs']);
				}
			}
		}

		// Get the CSS stylesheets to include.
		if (!empty($styleSheets)) {
			$this->processCSS($styleSheets);

			// Add the stylesheets to the document head.
			foreach ($this->styleSheets as $sheet)
			{
				$document->addStyleSheet($sheet['url'], @$sheet['type'], @$sheet['media'], @$sheet['attribs']);
			}
		}
	}

	/**
	 * Method to concatenate a set of JavaScript files into one file and apply compression if available.
	 *
	 * @param   array   List of files to concatenate.
	 *
	 * @return  array  List of files to add to the document head.
	 *
	 * @since   1.0
	 */
	function processJavaScript($files)
	{
		// Define the root path (filesystem) and base path (URI) for the caching folder.
		$root = JPATH_SITE.$this->params->get('root', '/media/tmp').'/js';
		$base = $this->params->get('root', '/media/tmp').'/js';

		// Get the compresion setting for the plugin.
		$compress = ($this->params->get('minify') && (version_compare(phpversion(), '5.0') > 0));

		// Initialize variables.
		$buffer = '';
		$filesToConcat = array();
		$hashCheck = '';
		$sitePath = JURI::base(true);

		foreach ($files as $k => $file)
		{
			// Ignore absolute URL based files.
			if (strpos($file, 'http') === 0) {
				$this->scripts[] = array(
					'url' => $file,
					'type' => 'text/javascript'
				);
				continue;
			}

			// Strip off site base URL path.
			$file = JPath::clean((strlen($sitePath) && (strpos($file, $sitePath) === 0)) ? substr($file, strlen($sitePath)) : $file);

			if (is_file(JPATH_SITE.$file)) {
				$hashCheck .= ':'.sha1_file(JPATH_SITE.$file);
				$filesToConcat[] = $file;
				unset($files[$k]);
			}
			else {
				// TODO: Throw error.
			}
		}

		// Get a unique hash representation of the files for caching and derive the file/uri paths.
		$hash	= md5($hashCheck);
		$path	= $root.'/'.$hash.'.js';
		$uri	= $base.'/'.$hash.'.js';

		// If the cached file already exists add it to the queue and return.
		if (is_file($path)) {
			array_unshift($this->scripts, array(
				'url' => $sitePath.trim($uri),
				'type' => 'text/javascript'
			));
			return true;
		}

		// First make sure the root path exists.
		JFolder::create($root);

		// Concatenate the files into a buffer.
		foreach ($filesToConcat as $file)
		{
			// Read in the file to a temporary buffer.
			$tmp = JFile::read(JPATH_SITE.$file);

			// Append the temporary buffer to the buffer.
			$buffer .= $tmp;
		}

		if ($compress) {

			// Import the JavaScript minification class.
			require_once JPATH_PLUGINS.'/system/documenttweak/jsmin.php';

			// Minify the JavaScript file buffer.
			$buffer = JSMin::minify($buffer);
		}

		// Write the concatenated file buffer out to the cached file path.
		JFile::write($path, $buffer);
		array_unshift($this->scripts, array(
			'url' => $sitePath.trim($uri),
			'type' => 'text/javascript'
		));

		return true;
	}

	/**
	 * Method to concatenate a set of files into one file and apply compression if available.
	 *
	 * @param   array   List of files to concatenate.
	 *
	 * @return  array  List of files to add to the document head.
	 *
	 * @since   1.0
	 */
	function processCSS($files)
	{
		// Define the root path (filesystem) and base path (URI) for the caching folder.
		$root = JPATH_SITE.$this->params->get('root', '/media/tmp').'/css';
		$base = $this->params->get('root', '/media/tmp').'/css';

		// Get the compresion setting for the plugin.
		$compress = ($this->params->get('minify') && (version_compare(phpversion(), '5.0') > 0));

		// Initialize variables.
		$buffer = '';
		$filesToConcat = array();
		$hashCheck = '';
		$sitePath = JURI::base(true);

		foreach ($files as $k => $file)
		{
			// Ignore absolute URL based files.
			if (strpos($file, 'http') === 0) {
				$this->styleSheets[] = array(
					'url' => $file,
					'type' => 'text/css'
				);
				continue;
			}

			// Strip off site base URL path.
			$file = JPath::clean((strlen($sitePath) && (strpos($file, $sitePath) === 0)) ? substr($file, strlen($sitePath)) : $file);

			if (is_file(JPATH_SITE.$file)) {
				$hashCheck .= ':'.sha1_file(JPATH_SITE.$file);
				$filesToConcat[] = $file;
				unset($files[$k]);
			}
			else {
				// TODO: Throw error.
			}
		}

		// Get a unique hash representation of the files for caching and derive the file/uri paths.
		$hash	= md5($hashCheck);
		$path	= $root.'/'.$hash.'.css';
		$uri	= $base.'/'.$hash.'.css';

		// If the cached file already exists add it to the queue and return.
		if (is_file($path)) {
			$this->styleSheets[] = array(
				'url' => $sitePath.trim($uri),
				'type' => 'text/css'
			);
			return true;
		}

		// First make sure the root path exists.
		JFolder::create($root);

		// Concatenate the files into a buffer.
		foreach ($filesToConcat as $file)
		{
			// Read in the file to a temporary buffer.
			$tmp = JFile::read(JPATH_SITE.$file);

			// Get any URLs in the CSS file for transformation.
			$urls = $this->extractCssUrls($tmp);

			// Transform and clean up any property based URLs found in the CSS file.
			foreach ($urls['property'] as $url)
			{
				if ((strpos($url, 'http://') !== 0) && (strpos($url, 'https://') !== 0)) {
//					$tmp = str_replace($url, '../../..'.JURI::_cleanPath(dirname($file).'/'.$url), $tmp);
				}
			}

			// Transform and clean up any import based URLs found in the CSS file.
			foreach ($urls['import'] as $full => $url)
			{
				if ((strpos($url, 'http://') !== 0) && (strpos($url, 'https://') !== 0)) {

					// Get the imported CSS text from the file.
					$text = $this->getImportedCSS($url, dirname($file));

					// Replace the import statement with the imported CSS text.
					$tmp = str_replace($full, $text, $tmp);
				}
			}

			// Append the temporary buffer to the buffer.
			$buffer .= $tmp;
		}

		if ($compress) {

			// Import the CSS minification class.
			require_once JPATH_PLUGINS.'/system/documenttweak/cssmin.php';

			// Minify the CSS file buffer.
			$buffer = CssMin::minify($buffer);
		}

		// Write the concatenated file buffer out to the cached file path.
		JFile::write($path, $buffer);
			$this->styleSheets[] = array(
			'url' => $sitePath.trim($uri),
			'type' => 'text/css'
		);

		return true;
	}

	function getImportedCSS($file, $path)
	{
		// Read in the file to a temporary buffer.
		$tmp = JFile::read(JPATH_SITE.$path.'/'.$file);

		// Get any URLs in the CSS file for transformation.
		$urls = $this->extractCssUrls($tmp);

		// Transform and clean up any property based URLs found in the CSS file.
		foreach ($urls['property'] as $url)
		{
			if ((strpos($url, 'http://') !== 0) && (strpos($url, 'https://') !== 0)) {
//				$tmp = str_replace($url, '../../..'.JURI::_cleanPath(dirname($path.'/'.$file).'/'.$url), $tmp);
			}
		}

		// Transform and clean up any import based URLs found in the CSS file.
		foreach ($urls['import'] as $full => $url)
		{
			if ((strpos($url, 'http://') !== 0) && (strpos($url, 'https://') !== 0)) {

				// Get the imported CSS text from the file.
				$text = $this->getImportedCSS($url, dirname($file));

				// Replace the import statement with the imported CSS text.
				$tmp = str_replace($full, $text, $tmp);
			}
		}

		return $tmp;
	}

	/**
	 * Extract URLs from UTF-8 CSS text.
	 *
	 * URLs within @import statements and url() property functions are extracted
	 * and returned in an associative array of arrays.  Array keys indicate
	 * the use context for the URL, including:
	 *
	 * 	"import"
	 * 	"property"
	 *
	 * Each value in the associative array is an array of URLs.
	 *
	 * Parameters:
	 * 	text		the UTF-8 text to scan
	 *
	 * Return values:
	 * 	an associative array of arrays of URLs.
	 *
	 * See:
	 * 	http://nadeausoftware.com/articles/2008/01/php_tip_how_extract_urls_css_file
	 *
	 * Copyright (c) 2008, David R. Nadeau, NadeauSoftware.com.
	 * All rights reserved.
	 *
	 */
	function extractCssUrls($text)
	{
		// Initialize variables.
		$urls = array('import' => array(), 'property' => array());

		// Setup the patterns for matching.
		$url_pattern     = '(([^\\\\\'", \(\)]*(\\\\.)?)+)';
		$urlfunc_pattern = 'url\(\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
		$pattern         = '/(' .
			 '(@import\s*[\'"]' . $url_pattern     . '[\'"]\s*\;?)' .
			'|(@import\s*'      . $urlfunc_pattern . '\s*\;?)'      .
			'|('                . $urlfunc_pattern . ')'      .  ')/iu';

		// If there are no matches just return the empty array.
		if (!preg_match_all($pattern, $text, $matches)) {
			return $urls;
		}

		// @import '...'
		// @import "..."
		foreach ($matches[3] as $i => $match)
		{
			if ( !empty($match) ) {
				$urls['import'][$matches[2][$i]] = preg_replace('/\\\\(.)/u', '\\1', $match);
			}
		}

		// @import url(...)
		// @import url('...')
		// @import url("...")
		foreach ($matches[7] as $i => $match)
		{
			if (!empty($match)) {
				$urls['import'][$matches[6][$i]] = preg_replace('/\\\\(.)/u', '\\1', $match);
			}
		}

		$urls['import'] = array_unique($urls['import']);

		// url(...)
		// url('...')
		// url("...")
		foreach ($matches[11] as $i => $match)
		{
			if (!empty($match)) {
				$urls['property'][] = preg_replace('/\\\\(.)/u', '\\1', $match);
			}
		}

		$urls['property'] = array_unique($urls['property']);

		return $urls;
	}
}
