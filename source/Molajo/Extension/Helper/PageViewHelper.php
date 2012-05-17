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
 * Page View Helper
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
	 * Get Requested Page View
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function get($page_view_id = 0)
	{
		if ($page_view_id == 0) {
			$page_view_id = $this->setDefaultPageView();
		}

		Services::Registry()->set('Parameters', 'page_view_id', (int)$page_view_id);
		$title = Helpers::Extension()->getInstanceTitle((int)$page_view_id);
		Services::Registry()->set('Parameters', 'page_view_title', $title);
		Services::Registry()->set('Parameters', 'page_view_path', $this->getPath($title));
		Services::Registry()->set('Parameters', 'page_view_path_include', $this->getPath($title) . '/index.php');
		Services::Registry()->set('Parameters', 'page_view_path_url', $this->getPathURL($title));

		$row = Helpers::Extension()->get($page_view_id, 'PageView');

		/** 500: Theme not found */
		if (count($row) == 0) {
			/** Try System Template */
			$page_view_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_THEME, 'System');

			/** Get new Title and path */
			$title = Helpers::Extension()->getInstanceTitle((int)$page_view_id);
			Services::Registry()->set('Parameters', 'page_view_title', $title);
			Services::Registry()->set('Parameters', 'page_view_path', $this->getPath($title));
			Services::Registry()->set('Parameters', 'page_view_path_include', $this->getPath($title) . '/index.php');
			Services::Registry()->set('Parameters', 'page_view_path_url', $this->getPathURL($title));

			$row = Helpers::Extension()->get($page_view_id);

			if (count($row) == 0) {
				Services::Error()->set(500, 'Theme not found');
				return false;
			}
		}

		Services::Registry()->set('Parameters', 'page_view_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Parameters', 'page_view_language', $row['language']);
		Services::Registry()->set('Parameters', 'page_view_view_group_id', $row['view_group_id']);
		Services::Registry()->set('Parameters', 'page_view_catalog_id', $row['catalog_id']);
		Services::Registry()->set('Parameters', 'page_view_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Parameters', 'page_view_catalog_type_title', $row['catalog_type_title']);

		return;
	}

	/**
	 *  Determine the default theme value, given system default sequence
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function setDefaultPageView()
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

		return Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_PAGE_VIEW, 'Default'); //55
	}

	/**
	 * getPath
	 *
	 * Return path for selected Page View
	 *
	 * Expects known path for Theme and Extension
	 *
	 * @param $page_view_name
	 * @return bool|string
	 */
	public function getPath($page_view_name)
	{
		$plus = '/View/Page/' . ucfirst(strtolower($page_view_name));

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/index.php')) {
			return Services::Registry()->get('Parameters', 'theme_path') . $plus;
		}

		/** 2. Extension */
		if (file_exists(Services::Registry()->get('Include', 'extension_path') . $plus . '/index.php')) {
			return Services::Registry()->get('Include', 'extension_path') . $plus;
		}

		/** 3. View */
		if (file_exists(EXTENSIONS_VIEWS . '/Page/' . ucfirst(strtolower($page_view_name)) . '/index.php')) {
			return EXTENSIONS_VIEWS . '/Page/' . ucfirst(strtolower($page_view_name));
		}

		/** 4. MVC */
		if (file_exists(MVC . $plus . '/index.php')) {
			return MVC . $plus;
		}

		return;
	}

	/**
	 * getURLPath
	 *
	 * Return URL path for selected Page View
	 *
	 * @return bool|string
	 * @since 1.0
	 */
	public function getPathURL($page_view_name)
	{
		$plus = '/View/Page/' . ucfirst(strtolower($page_view_name));


		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/index.php')) {
			return Services::Registry()->get('Parameters', 'theme_path_url') . $plus;
		}

		/** 2. Extension */
		if (file_exists(Services::Registry()->get('Include', 'extension_path') . $plus . '/index.php')) {
			return Services::Registry()->get('Include', 'extension_path_url') . $plus;
		}

		/** 3. View */
		if (file_exists(EXTENSIONS_VIEWS . '/Page/' . ucfirst(strtolower($page_view_name)) . '/index.php')) {
			return EXTENSIONS_VIEWS_URL . '/Page/' . ucfirst(strtolower($page_view_name));
		}

		/** 4. MVC */
		if (file_exists(MVC . $plus . '/index.php')) {
			return MVC_URL . $plus;
		}

		return;
	}
}
