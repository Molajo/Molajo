<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Service\Services;
use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * PageView Helper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class PageViewHelper
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
			self::$instance = new PageViewHelper();
		}

		return self::$instance;
	}

	/**
	 * get
	 *
	 * Get requested page_view data
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function get($page_view_id = 0)
	{
		if ($page_view_id == 0) {
			$page_view_id = $this->setDefault();
		}

		Services::Registry()->set('Parameters', 'page_view_id', (int)$page_view_id);

		$node = Helpers::Extension()->getExtensionNode((int)$page_view_id);

		Services::Registry()->set('Parameters', 'page_view_path_node', $node);

		Services::Registry()->set('Parameters', 'page_view_path', $this->getPath($node));
		Services::Registry()->set('Parameters', 'page_view_path_include', $this->getPath($node) . '/index.php');
		Services::Registry()->set('Parameters', 'page_view_path_url', $this->getPathURL($node));

		/** Retrieve the query results */
		$row = Helpers::Extension()->get($page_view_id, 'PageViews', 'Table');

		/** 500: not found */
		if (count($row) == 0) {

			/** System Default */
			$page_view_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_PAGE_VIEW, 'Default');

			/** System default */
			Services::Registry()->set('Parameters', 'page_view_id', (int)$page_view_id);

			$node = Helpers::Extension()->getExtensionNode((int)$page_view_id);

			Services::Registry()->set('Parameters', 'page_view_path_node', $node);

			Services::Registry()->set('Parameters', 'page_view_path', $this->getPath($node));
			Services::Registry()->set('Parameters', 'page_view_path_include', $this->getPath($node) . '/index.php');
			Services::Registry()->set('Parameters', 'page_view_path_url', $this->getPathURL($node));

			$row = Helpers::Extension()->get($page_view_id, 'PageView');

			if (count($row) == 0) {
				Services::Error()->set(500, 'View not found');
				return false;
			}
		}

		Services::Registry()->set('Parameters', 'page_view_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Parameters', 'page_view_language', $row['language']);
		Services::Registry()->set('Parameters', 'page_view_view_group_id', $row['view_group_id']);
		Services::Registry()->set('Parameters', 'page_view_catalog_id', $row['catalog_id']);
		Services::Registry()->set('Parameters', 'page_view_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Parameters', 'page_view_catalog_type_title', $row['catalog_types_title']);

		return;
	}

	/**
	 *  setDefault
	 *
	 *  Determine the default page_view value, given system default sequence
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function setDefault()
	{
		$page_view_id = Services::Registry()->get('Parameters', 'page_view_id', 0);
		if ((int)$page_view_id == 0) {
		} else {
			return $page_view_id;
		}

		$page_view_id = Services::Registry()->get('UserParameters', 'page_view_id', 0);
		if ((int)$page_view_id == 0) {
		} else {
			return $page_view_id;
		}

		$page_view_id = Services::Registry()->get('Configuration', 'page_view_id', 0);
		if ((int)$page_view_id == 0) {
		} else {
			return $page_view_id;
		}

		return Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_PAGE_VIEW, 'System'); //99
	}

	/**
	 * getPath
	 *
	 * Return path for selected PageView
	 *
	 * @param $page_view_name
	 * @return bool|string
	 */
	public function getPath($node)
	{
		if (file_exists(EXTENSIONS_VIEWS . '/Page/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
			return EXTENSIONS_VIEWS . '/Page/' . ucfirst(strtolower($node));
		}

		return false;
	}

	/**
	 * getPathURL
	 *
	 * Return path for selected PageView
	 *
	 * @return bool|string
	 * @since 1.0
	 */
	public function getPathURL($node)
	{
		if (file_exists(EXTENSIONS_VIEWS . '/Page/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
			return EXTENSIONS_VIEWS_URL . '/Page/' . ucfirst(strtolower($node));
		}

		return false;
	}
}
