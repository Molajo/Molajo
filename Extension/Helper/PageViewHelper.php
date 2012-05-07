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
	 * Get requested theme data
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function get($page_view_id = 0)
	{
		if ($page_view_id == 0) {
			$page_view_id = $this->DefaultPageView();
		}

		$row = Helpers::Extension()->get($page_view_id);

		/** 500: Theme not found */
		if (count($row) == 0) {
			/** Try System Template */
			$page_view_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_THEME, 'System');
			$row = Helpers::Extension()->get($page_view_id);
			if (count($row) == 0) {
				Services::Error()->set(500, 'Theme not found');
				return false;
			}
		}

		Services::Registry()->set('PageView', 'id', (int)$row->id);
		Services::Registry()->set('PageView', 'title', $row->title);
		Services::Registry()->set('PageView', 'translation_of_id', $row->translation_of_id);
		Services::Registry()->set('PageView', 'language', $row->language);
		Services::Registry()->set('PageView', 'view_group_id', $row->view_group_id);
		Services::Registry()->set('PageView', 'catalog_id', $row->catalog_id);
		Services::Registry()->set('PageView', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('PageView', 'catalog_type_title', $row->catalog_type_title);
		Services::Registry()->set('PageView', 'path', $this->getPath($row->title));
		Services::Registry()->set('PageView', 'path_include', $this->getPath($row->title) . '/index.php');
		Services::Registry()->set('PageView', 'path_url', $this->getPathURL($row->title));

		/** Load special fields for specific extension */
		$xml = Services::Configuration()->loadFile('Manifest', Services::Registry()->get('PageView', 'path'));
		$row = Services::Configuration()->addSpecialFields($xml->config, $row, 1);

		return;
	}

	/**
	 *  Determine the default theme value, given system default sequence
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function DefaultPageView()
	{
		$page_view_id = Services::Registry()->get('ContentParameters', 'page_view_id', 0);
		if ((int)$page_view_id == 0) {
		} else {
			return $page_view_id;
		}

		$page_view_id = Services::Registry()->get('MenuItemParameters', 'page_view_id', 0);
		if ((int)$page_view_id == 0) {
		} else {
			return $page_view_id;
		}

		$page_view_id = Services::Registry()->get('CategoryParameters', 'page_view_id', 0);
		if ((int)$page_view_id == 0) {
		} else {
			return $page_view_id;
		}

		$page_view_id = Services::Registry()->get('ExtensionParameters', 'page_view_id', 0);
		if ((int)$page_view_id == 0) {
		} else {
			return $page_view_id;
		}

		$page_view_id = Services::Registry()->get('UserParameters', 'page_view_id', 0);
		if ((int)$page_view_id == 0) {
		} else {
			return $page_view_id;
		}

		$page_view_id = Services::Registry()->get('ApplicationParameters', 'page_view_id', 0);
		if ((int)$page_view_id == 0) {
		} else {
			return $page_view_id;
		}

		$page_view_id = Services::Registry()->get('SiteParameters', 'page_view_id', 0);
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
		$plus = '/View/Page/' . $page_view_name;

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Theme', 'path') . $plus . '/index.php')) {
			return Services::Registry()->get('Theme', 'path') . $plus;
		}

		/** 2. Extension */
		if (file_exists(Services::Registry()->get('Extension', 'path') . $plus . '/index.php')) {
			return Services::Registry()->get('Extension', 'path') . $plus;
		}

		/** 3. View */
		if (file_exists(EXTENSIONS_VIEWS . '/Page/' . $page_view_name . '/index.php')) {
			return EXTENSIONS_VIEWS . '/Page/' . $page_view_name;
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
		$plus = '/View/Page/' . $page_view_name;

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Theme', 'path') . $plus . '/index.php')) {
			return Services::Registry()->get('Theme', 'path_url') . $plus;
		}

		/** 2. Extension */
		if (file_exists(Services::Registry()->get('Extension', 'path') . $plus . '/index.php')) {
			return Services::Registry()->get('Extension', 'path_url') . $plus;
		}

		/** 3. View */
		if (file_exists(EXTENSIONS_VIEWS . '/Page/' . $page_view_name . '/index.php')) {
			return EXTENSIONS_VIEWS_URL . '/Page/' . $page_view_name;
		}

		/** 4. MVC */
		if (file_exists(MVC . $plus . '/index.php')) {
			return MVC_URL . $plus;
		}

		return;
	}
}
