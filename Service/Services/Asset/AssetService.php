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
		Services::Registry()->set('Assets', 'LinksPriorities', array());

		Services::Registry()->set('Assets', 'Css', array());
		Services::Registry()->set('Assets', 'CssPriorities', array());

		Services::Registry()->set('Assets', 'CssDeclarations', array());
		Services::Registry()->set('Assets', 'CssDeclarationsPriorities', array());

		Services::Registry()->set('Assets', 'Js', array());
		Services::Registry()->set('Assets', 'JsPriorities', array());

		Services::Registry()->set('Assets', 'JsDefer', array());
		Services::Registry()->set('Assets', 'JsDeferPriorities', array());

		Services::Registry()->set('Assets', 'JsDeclarations', array());
		Services::Registry()->set('Assets', 'JsDeferPriorities', array());

		Services::Registry()->set('Assets', 'JsDeclarationsDefer', array());
		Services::Registry()->set('Assets', 'JsDeclarationsDeferPriorities', array());

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
	 * @param  $prioritu
	 *
	 * @return object
	 * @since  1.0
	 */
	public function addLink($url, $relation, $relation_type = 'rel', $attributes = array(), $priority = 500)
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
		$row->url = $url;

		$links[] = $priority;

		Services::Registry()->set('Assets', 'Links', $links);

		/** Order priorities for later use in rendered links in head */
		$priorities = Services::Registry()->get('Assets', 'LinksPriorities', array());

		if (in_array($priority, $priorities)) {
		} else {
			$priorities[] = $priority;
		}

		sort($priorities);

		Services::Registry()->set('Assets', 'LinksPriorities', $priorities);

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

		/** Do not load the same file multiple times */
		foreach ($css as $item) {
			if ($item->url == $url
				&& $item->mimetype == $mimetype
				&& $item->media == $media
				&& $item->conditional == $conditional) {
				return $this;
			}
		}

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

		sort($priorities);

		Services::Registry()->set('Assets', 'CssPriorities', $priorities);

		return $this;
	}

	/**
	 * addCssDeclaration - Adds a css declaration to the array for later rendering
	 *
	 * Usage:
	 * Services::Asset()->addCssDeclaration($css_in_here, 'text/css');
	 *
	 * @param $content
	 * @param string $mimetype
	 * @param int $priority
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function addCssDeclaration($content, $mimetype = 'text/css', $priority = 500)
	{
		$css = Services::Registry()->get('Assets', 'CssDeclarations');

		/** Do not load the same file multiple times */
		if (is_array($css) && count($css) > 0) {
			foreach ($css as $item) {
				if ($item->content == $content) {
					return $this;
				}
			}
		}

		/** Load new row */
		$row = new \stdClass();

		$row->mimetype = $mimetype;
		$row->content = $content;
		$row->priority = $priority;

		$css[] = $row;

		Services::Registry()->set('Assets', 'CssDeclarations', $css);

		/** Order priorities for later use in rendered links in head */
		$priorities = Services::Registry()->get('Assets', 'CssDeclarationsPriorities', array());

		if (in_array($priority, $priorities)) {
		} else {
			$priorities[] = $priority;
		}

		sort($priorities);

		Services::Registry()->set('Assets', 'CssDeclarationsPriorities', $priorities);

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

		/** Do not load the same file multiple times */
		foreach ($js as $item) {
			if ($item->url == $url) {
				return $this;
			}
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

		/** Order priorities for rendering */
		if ($defer == 1) {
			$priorities = Services::Registry()->get('Assets', 'JsDeferPriorities', $js);
		} else {
			$priorities = Services::Registry()->get('Assets', 'JsPriorities', $js);
		}

		if (in_array($priority, $priorities)) {
		} else {
			$priorities[] = $priority;
		}

		sort($priorities);

		if ($defer == 1) {
			Services::Registry()->set('Assets', 'JsDeferPriorities', $priorities);
		} else {
			Services::Registry()->set('Assets', 'JsPriorities', $priorities);
		}

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
	public function addJSDeclarations($content, $mimetype = 'text/javascript', $defer = 0, $priority = 500)
	{
		if ($defer == 1) {
			$js = Services::Registry()->get('Assets', 'JsDeclarationsDefer', array());
		} else {
			$js = Services::Registry()->get('Assets', 'JsDeclarations', array());
		}

		/** Do not load the same file multiple times */
		foreach ($js as $item) {
			if ($item->content == $content) {
				return $this;
			}
		}

		$row = new \stdClass();

		$row->content = $content;
		$row->mimetype = $mimetype;
		$row->defer = $defer;
		$row->priority = $priority;

		$js[] = $row;

		if ($defer == 1) {
			Services::Registry()->set('Assets', 'JsDeclarationsDefer', $js);
		} else {
			Services::Registry()->set('Assets', 'JsDeclarations', $js);
		}

		/** Order priorities for rendering */
		if ($defer == 1) {
			$priorities = Services::Registry()->get('Assets', 'JsDeclarationsDeferPriorities', array());
		} else {
			$priorities = Services::Registry()->get('Assets', 'JsDeclarationsPriorities', array());
		}

		if (is_array($priorities)) {
		} else {
			$priorities = array();
		}

		if (in_array($priority, $priorities)) {
		} else {
			$priorities[] = $priority;
		}

		sort($priorities);

		if ($defer == 1) {
			Services::Registry()->set('Assets', 'JsDeclarationsDeferPriorities', $priorities);
		} else {
			Services::Registry()->set('Assets', 'JsDeclarationsPriorities', $priorities);
		}

		return $this;
	}

	/**
	 * setPriority - use to override the priority of a specific file
	 *
	 * Usage:
	 * Services::Asset()->setPriority('Css', 'http://example.com/media/1236_grid.css', 1);
	 *
	 * @param $type
	 * @param $url
	 * @param $priority
	 *
	 * @return array
	 * @since  1.0
	 */
	public function setPriority($type, $url, $priority)
	{

		$rows = Services::Registry()->get('Assets', $type);
		if (is_array($rows) && count($rows) > 0) {
		} else {
			return array();
		}

		$update = false;
		$query_results = array();
		foreach ($rows as $row) {
			if (isset($row->url)) {
				if ($row->url == $url) {
					echo $priority;
					$row->priority = $priority;
					$update = true;
				}
			}
			$query_results[] = $row;
		}

		if ($update == true) {
			Services::Registry()->set('Assets', $type, $query_results);

			$priorityType = $type . 'Priorities';

			/** Reload priorities to (possibly) remove the replaced one and add the new one */
			$priorities = array();
			foreach ($rows as $row) {
				if (in_array($row->priority, $priorities)) {
				} else {
					$priorities[] = $row->priority;
				}
			}

			sort($priorities);
			Services::Registry()->set('Assets', $priorityType, $priorities);
		}

		return $this;
	}

	/**
	 * remove - use to remove a specific Asset
	 *
	 * Usage:
	 * Services::Asset()->remove('Css', 'http://example.com/media/1236_grid.css');
	 *
	 * @param $type
	 * @param $url
	 * @param $priority
	 *
	 * @return array
	 * @since  1.0
	 */
	public function remove($type, $url)
	{

		$rows = Services::Registry()->get('Assets', $type);
		if (is_array($rows) && count($rows) > 0) {
		} else {
			return array();
		}

		$update = false;
		$query_results = array();
		foreach ($rows as $row) {
			if (isset($row->url)) {
				if ($row->url == $url) {
					$update = true;
				} else {
					$query_results[] = $row;
				}
			}
		}

		if ($update == true) {
			Services::Registry()->set('Assets', $type, $query_results);

			$priorityType = $type . 'Priorities';

			/** Reload priorities to (possibly) remove the replaced one and add the new one */
			$priorities = array();
			foreach ($rows as $row) {
				if (in_array($row->priority, $priorities)) {
				} else {
					$priorities[] = $row->priority;
				}
			}

			sort($priorities);
			Services::Registry()->set('Assets', $priorityType, $priorities);
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

		$priorityType = $type . 'Priorities';

		$rows = Services::Registry()->get('Assets', $type);
		if (is_array($rows) && count($rows) > 0) {
		} else {
			return array();
		}

		$html5 = Services::Registry()->get('Configuration', 'html5', 1);
		if ((int)Services::Registry()->get('Configuration', 'html5', 1) == 1) {
			$end = '>' . chr(10);
		} else {
			$end = '/>' . chr(10);
		}

		/** Retrieve unique array of $priorities  */
		$priorities = Services::Registry()->get('Assets', $priorityType);
		sort($priorities);

		$query_results = array();

		foreach ($priorities as $priority) {

			foreach ($rows as $row) {
				$include = false;

				if (isset($row->priority)) {
					if ($row->priority == $priority) {
						$include = true;
					}
				}
				if ($include == false) {
				} else {
					$row->html5 = $html5;
					$row->end = $end;
					$row->page_mime_type = Services::Registry()->get('Metadata', 'mimetype');
					$query_results[] = $row;
				}
			}
		}

		return $query_results;
	}
}
