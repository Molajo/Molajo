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
 * Wrap View Helper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class WrapViewHelper
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
			self::$instance = new WrapViewHelper();
		}

		return self::$instance;
	}

	/**
	 * get
	 *
	 * Get Requested Wrap View data
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function get($wrap_view_id = 0, $no_extension = false)
	{
		if ($wrap_view_id == 0) {
			$wrap_view_id = $this->setDefaultWrapView();
		}

		Services::Registry()->set('Parameters', 'wrap_view_id', (int)$wrap_view_id);
		$title = Helpers::Extension()->getInstanceTitle((int)$wrap_view_id);
		Services::Registry()->set('Parameters', 'wrap_view_title', $title);
		Services::Registry()->set('Parameters', 'wrap_view_path', $this->getPath($title, $no_extension));
		Services::Registry()->set('Parameters', 'wrap_view_path_include', $this->getPath($title, $no_extension) . '/Manifest.xml');
		Services::Registry()->set('Parameters', 'wrap_view_path_url', $this->getPathURL($title, $no_extension));

		$row = Helpers::Extension()->get($wrap_view_id, 'WrapView');

		/** 500: Theme not found */
		if (count($row) == 0) {
			/** Try System Template */
			$wrap_view_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_WRAP_VIEW, 'None');

			/** Get new Title and path */
			$title = Helpers::Extension()->getInstanceTitle((int)$wrap_view_id);
			Services::Registry()->set('Parameters', 'wrap_view_title', $title);
			Services::Registry()->set('Parameters', 'wrap_view_path', $this->getPath($title, $no_extension));
			Services::Registry()->set('Parameters', 'wrap_view_path_include', $this->getPath($title, $no_extension) . '/Manifest.xml');
			Services::Registry()->set('Parameters', 'wrap_view_path_url', $this->getPathURL($title, $no_extension));

			$row = Helpers::Extension()->get($wrap_view_id);

			if (count($row) == 0) {
				Services::Error()->set(500, 'Wrap View not found');
				return false;
			}
		}

		Services::Registry()->set('Parameters', 'wrap_view_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Parameters', 'wrap_view_language', $row['language']);
		Services::Registry()->set('Parameters', 'wrap_view_view_group_id', $row['view_group_id']);
		Services::Registry()->set('Parameters', 'wrap_view_catalog_id', $row['catalog_id']);
		Services::Registry()->set('Parameters', 'wrap_view_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Parameters', 'wrap_view_catalog_type_title', $row['catalog_type_title']);

		return;
	}

	/**
	 *  Determine the default theme value, given system default sequence
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function setDefaultWrapView()
	{
		$wrap_view_id = Services::Registry()->get('Parameters', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		$wrap_view_id = Services::Registry()->get('UserParameters', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		$wrap_view_id = Services::Registry()->get('Configuration', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		return Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_WRAP_VIEW, 'Default'); //55
	}

	/**
	 * getPath
	 *
	 * Return path for selected Wrap View
	 *
	 * Expects known path for Theme and Extension
	 *
	 * @param $wrap_view_name
	 * @return bool|string
	 */
	public function getPath($wrap_view_name, $no_extension = false)
	{
		$plus = '/View/Wrap/' . ucfirst(strtolower($wrap_view_name));

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/Manifest.xml')) {
			return Services::Registry()->get('Parameters', 'theme_path') . $plus;
		}

		/** 2. Extension */
		if ($no_extension == true) {
		} else {
			if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . $plus . '/Manifest.xml')) {
				return Services::Registry()->get('Parameters', 'extension_path') . $plus;
			}
		}

		/** 3. View */
		if (file_exists(EXTENSIONS_VIEWS . '/Wrap/' . $wrap_view_name . '/Manifest.xml')) {
			return EXTENSIONS_VIEWS . '/Wrap/' . $wrap_view_name;
		}

		/** 4. MVC */
		if (file_exists(MVC . $plus . '/Manifest.xml')) {
			return MVC . $plus;
		}

		return;
	}

	/**
	 * getURLPath
	 *
	 * Return URL path for selected Wrap View
	 *
	 * @return bool|string
	 * @since 1.0
	 */
	public function getPathURL($wrap_view_name, $no_extension = false)
	{
		$plus = '/View/Wrap/' . ucfirst(strtolower($wrap_view_name));

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/Manifest.xml')) {
			return Services::Registry()->get('Parameters', 'theme_path_url') . $plus;
		}

		/** 2. Extension */
		if ($no_extension == true) {
		} else {
			if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . $plus . '/Manifest.xml')) {
				return Services::Registry()->get('Parameters', 'extension_path_url') . $plus;
			}
		}

		/** 3. View */
		if (file_exists(EXTENSIONS_VIEWS . '/Wrap/' . $wrap_view_name . '/Manifest.xml')) {
			return EXTENSIONS_VIEWS_URL . '/Wrap/' . $wrap_view_name;
		}

		/** 4. MVC */
		if (file_exists(MVC . $plus . '/Manifest.xml')) {
			return MVC_URL . $plus;
		}

		return;
	}
}
