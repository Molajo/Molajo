<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Asset;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Asset
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class AssetService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new AssetService();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function __construct()
	{
		Services::Registry()->createRegistry('Assets');

		Services::Registry()->set('Assets', 'Links', array());

		Services::Registry()->set('Assets', 'Css', array());
		Services::Registry()->set('Assets', 'CssPriorities', array());
		Services::Registry()->set('Assets', 'CssDeclarations', array());

		Services::Registry()->set('Assets', 'Js', array());
		Services::Registry()->set('Assets', 'JsPriorities', array());
		Services::Registry()->set('Assets', 'JsPrioritiesDefer', array());
		Services::Registry()->set('Assets', 'JsDeclarations', array());
		Services::Registry()->set('Assets', 'JsDeclarationsDefer', array());

		return;
	}

	/**
	 * addLink - Adds <link> tags to the head of the document
	 *
	 * Usage:
	 *
	 * Services::Asset()->addLink(
	 *   $url = EXTENSIONS_THEMES_URL
	 *      . '/' . Services::Registry()->get('Parameters', 'theme_path_node')
	 *      . '/' . 'images/apple-touch-icon-114x114.png',
	 *   $relation = 'apple-touch-icon-precomposed',
	 *   $relation_type = 'rel',
	 *   $attributes = array('sizes,114x114')
	 *  );
	 *
	 * Produces:
	 * <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/apple-touch-icon-114x114.png" />
	 *
	 * @param  $url
	 * @param  $relation
	 * @param  $relation_type
	 * @param  $attributes
	 *
	 * @return object
	 * @since  1.0
	 */
	public function addLink($url, $relation, $relation_type = 'rel', $attributes = array())
	{
		$links = Services::Registry()->get('Assets', 'Links', array());

		$row = new \stdClass();

		$row->url = $url;
		$row->relation = Services::Filter()->escape_text($relation);
		$row->relation_type = Services::Filter()->escape_text($relation_type);
	    $row->attributes = '';
		$temp = trim(implode(' ', $attributes));
		if (trim($temp) == '') {
		} elseif (count($temp) == 1) {
			$temp = array($temp);
		}
		if (is_array($temp) && count($temp) > 0) {
			foreach ($temp as $pair) {
				$split = explode(',', $pair);
				$row->attributes .= ' ' . $split[0]
					. '="'
					. Services::Filter()->escape_text($split[1])
					. '"';
			}
		}

		$links[] = $row;

		Services::Registry()->set('Assets', 'Links', $links);

		return $this;
	}

	/**
	 * addCssFolder - Loads the CS located within the folder, as specified by the filepath
	 *
	 * Usage:
	 * Services::Asset()->addCssFolder($file_path, $url_path, $priority);
	 *
	 * @param string  $file_path
	 * @param string  $url_path
	 * @param integer $priority
	 *
	 * @return object
	 * @since  1.0
	 */
	public function addCssFolder($file_path, $url_path, $priority = 500)
	{
		if (Services::Filesystem()->folderExists($file_path . '/css')) {
		} else {
			return $this;
		}

		$files = Services::Filesystem()->folderFiles($file_path . '/css', '\.css$', false, false);

		if (count($files) > 0) {
			foreach ($files as $file) {
				if (substr($file, 0, 4) == 'rtl_') {
					if (Services::Language()->get('direction') == 'rtl') {
						$this->addCss($url_path . '/css/' . $file, $priority);
					}
				} else {
					$this->addCss($url_path . '/css/' . $file, $priority);
				}
			}
		}

		return $this;
	}

	/**
	 * addCss - Adds a linked stylesheet to the page
	 *
	 * Usage:
	 * Services::Asset()->addCss($url_path . '/template.css');
	 *
	 * @param string $url
	 * @param int    $priority
	 * @param string $mimetype
	 * @param string $media
	 * @param string $conditional
	 * @param array  $attributes
	 *
	 * @return mixed
	 * @since  1.0
	 */
	public function addCss($url, $priority = 500, $mimetype = 'text/css', $media = '',
						   $conditional = '', $attributes = array())
	{
		/** Save new CSS entry */
		$css = Services::Registry()->get('Assets', 'Css', array());

		$row = new \stdClass();

		$row->url = $url;
		$row->priority = $priority;
		$row->mimetype = $mimetype;
		$row->media = $media;
		$row->conditional = $conditional;
		$row->attributes = trim(implode(' ', $attributes));

		$css[] = $row;

		Services::Registry()->set('Assets', 'Css', $css);

		/** Order priorities for later use in rendered links in head */
		$priorities = Services::Registry()->get('Assets', 'CssPriorities', array());

		if (in_array($priority, $priorities)) {
		} else {
			$priorities[] = $priority;
		}

		ksort($priorities);

		Services::Registry()->set('Assets', 'CssPriorities', $priorities);

		return $this;
	}

	/**
	 * addCssDeclaration - Adds a css declaration to the array for later rendering
	 *
	 * Usage:
	 * Services::Asset()->addCssDeclaration($css_in_here, 'text/css');
	 *
	 * @param string $content
	 * @param string $mimetype
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function addCssDeclaration($content, $mimetype = 'text/css')
	{
		$css = Services::Registry()->get('Assets', 'CssDeclarations', array());

		$row = new \stdClass();

		$row->mimetype = $mimetype;
		$row->content = $content;

		$css[] = $row;

		Services::Registry()->set('Assets', 'CssDeclarations', $css);

		return $this;
	}

	/**
	 * addJsFolder - Loads the JS Files located within the folder specified by the filepath
	 *
	 * Usage:
	 * Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
	 *
	 * @param  $file_path
	 * @param  $url_path
	 * @return void
	 * @since  1.0
	 */
	public function addJsFolder($file_path, $url_path, $priority = 500, $defer = 0)
	{
		if ($defer == 1) {
			$extra = '/js/defer';
		} else {
			$extra = '/js';
			$defer = 0;
		}

		if (Services::Filesystem()->folderExists($file_path . $extra)) {
		} else {
			return;
		}

		$files = Services::Filesystem()->folderFiles($file_path . $extra, '\.js$', false, false);

		if (count($files) > 0) {
			foreach ($files as $file) {
				$this->addJs(
					$url_path . $extra . '/' . $file,
					$priority,
					$defer,
					'text/javascript',
					0
				);
			}
		}

		return;
	}

	/**
	 * addJs - Adds a linked script to the page
	 *
	 * Usage:
	 * Services::Asset()->addJs('http://html5shim.googlecode.com/svn/trunk/html5.js', 1000);
	 *
	 * @param string $url
	 * @param int    $priority
	 * @param bool   $defer
	 * @param string $mimetype
	 * @param bool   $async
	 *
	 * @return mixed
	 * @since  1.0
	 */
	public function addJs($url, $priority = 500, $defer = 0, $mimetype = "text/javascript", $async = false)
	{
		if ($defer == 1) {
			$js = Services::Registry()->get('Assets', 'JsDefer', array());
		} else {
			$js = Services::Registry()->get('Assets', 'Js', array());
		}

		/** Save new entry */
		$row = new \stdClass();

		$row->url = $url;
		$row->priority = $priority;
		$row->mimetype = $mimetype;
		$row->async = $async;
		$row->defer = $defer;

		$js[] = $row;

		if ($defer == 1) {
			Services::Registry()->set('Assets', 'JsDefer', $js);
		} else {
			Services::Registry()->set('Assets', 'Js', $js);
		}

		/** Order priorities for later use rendering links */
		$priorities = Services::Registry()->get('Assets', 'JsPriorities', array());

		if (in_array($priority, $priorities)) {
		} else {
			$priorities[] = $priority;
		}

		ksort($priorities);

		Services::Registry()->set('Assets', 'JsPriorities', $priorities);

		return $this;
	}

	/**
	 * addJSDeclarations - Adds a js declaration to an array for later rendering
	 *
	 * Usage:
	 * Services::Asset()->addJSDeclarations($fallback, 'text/javascript', 1000);
	 *
	 * @param string $content
	 * @param string $mimetype
	 * @param string $defer
	 *
	 * @return object
	 * @since  1.0
	 */
	public function addJSDeclarations($content, $mimetype = 'text/javascript', $defer = 0)
	{
		if ($defer == 1) {
			$js = Services::Registry()->get('Assets', 'JsDeclarationsDefer', array());
		} else {
			$js = Services::Registry()->get('Assets', 'JsDeclarations', array());
		}

		$row = new \stdClass();

		$row->content = $content;
		$row->mimetype = $mimetype;
		$row->defer = $defer;

		$js[] = $row;

		if ($defer == 1) {
			Services::Registry()->set('Assets', 'JsDeclarationsDefer', $js);
		} else {
			Services::Registry()->set('Assets', 'JsDeclarations', $js);
		}

		return $this;
	}

	/**
	 *     Dummy functions to use service as a DBO to interact with model
	 */
	public function get($option = null)
	{
		if ($option == 'db') {
			return $this;

		} elseif ($option == 'count') {
			return count($this);

		} else {
			return $this;
		}
	}

	public function getNullDate()
	{
		return $this;
	}

	public function getQuery()
	{
		return $this;
	}

	public function toSql()
	{
		return $this;
	}

	public function clear()
	{
		return $this;
	}

	/**
	 * getData
	 *
	 * @return array
	 *
	 * @since    1.0
	 */
	public function getAssets($type)
	{
		$rows = Services::Registry()->get('Assets', $type);

		if (is_array($rows)) {
		} else {
			return array();
		}

		if (count($rows) > 0) {
		} else {
			return array();
		}

		$html5 = Services::Registry()->get('Configuration', 'html5', 1);
		if ((int) Services::Registry()->get('Configuration', 'html5', 1) == 1) {
			$end = '>' . chr(10);
		} else {
			$end = '/>' . chr(10);
		}

		foreach ($rows as $row) {
			$row->html5 = $html5;
			$row->end = $end;
			$row->page_mime_type = Services::Registry()->get('Metadata', 'mimetype');
		}

		return $rows;
	}
}
