<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
	 * Initialise
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function Initialise()
	{
		Services::Registry()->createRegistry(DATA_OBJECT_ASSETS);

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'Links', array());
		Services::Registry()->set(DATA_OBJECT_ASSETS, 'LinksPriorities', array());

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'Css', array());
		Services::Registry()->set(DATA_OBJECT_ASSETS, 'CssPriorities', array());

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'CssDeclarations', array());
		Services::Registry()->set(DATA_OBJECT_ASSETS, 'CssDeclarationsPriorities', array());

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'Js', array());
		Services::Registry()->set(DATA_OBJECT_ASSETS, 'JsPriorities', array());

		Services::Registry()->set(DATA_OBJECT_ASSETS, JS_DEFER_LITERAL, array());
		Services::Registry()->set(DATA_OBJECT_ASSETS, 'JS_DEFER_LITERALPriorities', array());

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'JsDeclarations', array());
		Services::Registry()->set(DATA_OBJECT_ASSETS, 'JS_DEFER_LITERALPriorities', array());

		Services::Registry()->set(DATA_OBJECT_ASSETS, JS_DECLARATIONS_DEFER, array());
		Services::Registry()->set(DATA_OBJECT_ASSETS, 'JS_DECLARATIONS_DEFERPriorities', array());

		return;
	}

	/**
	 * addLink - Adds <link> tags to the head of the document
	 *
	 * Usage:
	 *
	 * Services::Asset()->addLink(
	 *   $url = EXTENSIONS_THEMES_URL
	 *      . '/' . Services::Registry()->get(DATA_OBJECT_PARAMETERS, 'theme_path_node')
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
		if (trim($url) == '') {
			return $this;
		}
		$links = Services::Registry()->get(DATA_OBJECT_ASSETS, 'Links', array());

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
		$row->priority = $priority;

		$links[] = $row;

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'Links', $links);

		/** Order priorities for later use in rendered links in head */
		$priorities = Services::Registry()->get(DATA_OBJECT_ASSETS, 'LinksPriorities');

		if (count($priorities) > 0) {
			if (in_array($priority, $priorities)) {
			} else {
				$priorities[] = $priority;
			}
		} else {
			$priorities[] = $priority;
		}

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'LinksPriorities', $priorities);

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
				$add = 0;
				if (substr($file, 0, 4) == 'ltr_') {
					if (Services::Language()->get('direction') == 'rtl') {
					} else {
						$add = 1;
					}

				} elseif (substr($file, 0, 4) == 'rtl_') {

					if (Services::Language()->get('direction') == 'rtl') {
						$add = 1;
					}

				} elseif (strtolower(substr($file, 0, 4)) == 'hold') {

				} else {
					$add = 1;
				}

				if ($add == 1) {
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
		$css = Services::Registry()->get(DATA_OBJECT_ASSETS, 'Css', array());

		/** Do not load the same file multiple times */
		foreach ($css as $item) {
			if ($item->url == $url
				&& $item->mimetype == $mimetype
				&& $item->media == $media
				&& $item->conditional == $conditional
			) {
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

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'Css', $css);

		/** Order priorities for later use in rendered links in head */
		$priorities = Services::Registry()->get(DATA_OBJECT_ASSETS, 'CssPriorities', array());

		if (in_array($priority, $priorities)) {
		} else {
			$priorities[] = $priority;
		}

		sort($priorities);

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'CssPriorities', $priorities);

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
	 * @param int    $priority
	 *
	 * @return object
	 * @since   1.0
	 */
	public function addCssDeclaration($content, $mimetype = 'text/css', $priority = 500)
	{
		$css = Services::Registry()->get(DATA_OBJECT_ASSETS, 'CssDeclarations');

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

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'CssDeclarations', $css);

		/** Order priorities for later use in rendered links in head */
		$priorities = Services::Registry()->get(DATA_OBJECT_ASSETS, 'CssDeclarationsPriorities', array());

		if (in_array($priority, $priorities)) {
		} else {
			$priorities[] = $priority;
		}

		sort($priorities);

		Services::Registry()->set(DATA_OBJECT_ASSETS, 'CssDeclarationsPriorities', $priorities);

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
				if (strtolower(substr($file, 0, 4)) == 'hold') {
				} else {
					$this->addJs(
						$url_path . $extra . '/' . $file,
						$priority,
						$defer,
						'text/javascript',
						0
					);
				}
			}
		}

		return;
	}

	/**
	 * addJs - Adds a linked script to the page
	 *
	 * Usage:
	 * Services::Asset()->addJs('http://example.com/test.js', 1000, 1);
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
			$js = Services::Registry()->get(DATA_OBJECT_ASSETS, JS_DEFER_LITERAL, array());
		} else {
			$js = Services::Registry()->get(DATA_OBJECT_ASSETS, 'Js', array());
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
			Services::Registry()->set(DATA_OBJECT_ASSETS, JS_DEFER_LITERAL, $js);
		} else {
			Services::Registry()->set(DATA_OBJECT_ASSETS, 'Js', $js);
		}

		/** Order priorities for rendering */
		if ($defer == 1) {
			$priorities = Services::Registry()->get(DATA_OBJECT_ASSETS, 'JS_DEFER_LITERALPriorities', $js);
		} else {
			$priorities = Services::Registry()->get(DATA_OBJECT_ASSETS, 'JsPriorities', $js);
		}

		if (in_array($priority, $priorities)) {
		} else {
			$priorities[] = $priority;
		}

		sort($priorities);

		if ($defer == 1) {
			Services::Registry()->set(DATA_OBJECT_ASSETS, 'JS_DEFER_LITERALPriorities', $priorities);
		} else {
			Services::Registry()->set(DATA_OBJECT_ASSETS, 'JsPriorities', $priorities);
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
	 * @param string $priority
	 * @param string $defer
	 * @param string $mimetype
	 *
	 * @return object
	 * @since  1.0
	 */
	public function addJSDeclarations($content, $priority = 500, $defer = 0, $mimetype = 'text/javascript')
	{
		if ($defer == 1) {
			$js = Services::Registry()->get(DATA_OBJECT_ASSETS, JS_DECLARATIONS_DEFER, array());
		} else {
			$js = Services::Registry()->get(DATA_OBJECT_ASSETS, 'JsDeclarations', array());
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
			Services::Registry()->set(DATA_OBJECT_ASSETS, JS_DECLARATIONS_DEFER, $js);
		} else {
			Services::Registry()->set(DATA_OBJECT_ASSETS, 'JsDeclarations', $js);
		}

		/** Order priorities for rendering */
		if ($defer == 1) {
			$priorities = Services::Registry()->get(DATA_OBJECT_ASSETS, 'JS_DECLARATIONS_DEFERPriorities', array());
		} else {
			$priorities = Services::Registry()->get(DATA_OBJECT_ASSETS, 'JsDeclarationsPriorities', array());
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
			Services::Registry()->set(DATA_OBJECT_ASSETS, 'JS_DECLARATIONS_DEFERPriorities', $priorities);
		} else {
			Services::Registry()->set(DATA_OBJECT_ASSETS, 'JsDeclarationsPriorities', $priorities);
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
		$rows = Services::Registry()->get(DATA_OBJECT_ASSETS, $type);
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

		if ($update === true) {
			Services::Registry()->set(DATA_OBJECT_ASSETS, $type, $query_results);

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

			Services::Registry()->set(DATA_OBJECT_ASSETS, $priorityType, $priorities);
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
		$rows = Services::Registry()->get(DATA_OBJECT_ASSETS, $type);
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

		if ($update === true) {
			Services::Registry()->set(DATA_OBJECT_ASSETS, $type, $query_results);

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
			Services::Registry()->set(DATA_OBJECT_ASSETS, $priorityType, $priorities);
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

		$rows = Services::Registry()->get(DATA_OBJECT_ASSETS, $type);

		if (is_array($rows) && count($rows) > 0) {
		} else {
			return array();
		}

		$application_html5 = Services::Registry()->get(CONFIGURATION_LITERAL, 'application_html5', 1);
		if ((int)Services::Registry()->get(CONFIGURATION_LITERAL, 'application_html5', 1) == 1) {
			$end = '>' . chr(10);
		} else {
			$end = '/>' . chr(10);
		}

		/** Retrieve unique array of $priorities  */
		$priorities = Services::Registry()->get(DATA_OBJECT_ASSETS, $priorityType);
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
				if ($include === false) {
				} else {
					$row->application_html5 = $application_html5;
					$row->end = $end;
					$row->page_mime_type = Services::Registry()->get('Metadata', 'mimetype');
					$query_results[] = $row;
				}
			}
		}

		return $query_results;
	}
}
