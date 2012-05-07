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
	 * Get requested theme data
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function get($wrap_view_id = 0)
	{
		if ($wrap_view_id == 0) {
			$wrap_view_id = $this->DefaultWrapView();
		}

		$row = Helpers::Extension()->get($wrap_view_id);

		/** 500: Wrap not found */
		if (count($row) == 0) {
			/** Try Default 'None' Wrap */
			$wrap_view_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_WRAP_VIEW, 'None');
			$row = Helpers::Extension()->get($wrap_view_id);
			if (count($row) == 0) {
				Services::Error()->set(500, 'Theme not found');
				return false;
			}
		}

		Services::Registry()->set('WrapView', 'id', (int)$row->id);
		Services::Registry()->set('WrapView', 'title', $row->title);
		Services::Registry()->set('WrapView', 'translation_of_id', $row->translation_of_id);
		Services::Registry()->set('WrapView', 'language', $row->language);
		Services::Registry()->set('WrapView', 'view_group_id', $row->view_group_id);
		Services::Registry()->set('WrapView', 'catalog_id', $row->catalog_id);
		Services::Registry()->set('WrapView', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('WrapView', 'catalog_type_title', $row->catalog_type_title);
		Services::Registry()->set('WrapView', 'path', $this->getPath($row->title));
		Services::Registry()->set('WrapView', 'path_url', $this->getPathURL($row->title));

		/** Load special fields for specific extension */
		$xml = Services::Configuration()->loadFile('Manifest', Services::Registry()->get('WrapView', 'path'));
		$row = Services::Configuration()->addSpecialFields($xml->config, $row, 1);

		return;
	}

	/**
	 *  Determine the default theme value, given system default sequence
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function DefaultWrapView()
	{
		$wrap_view_id = Services::Registry()->get('ContentParameters', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		$wrap_view_id = Services::Registry()->get('MenuItemParameters', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		$wrap_view_id = Services::Registry()->get('CategoryParameters', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		$wrap_view_id = Services::Registry()->get('ExtensionParameters', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		$wrap_view_id = Services::Registry()->get('UserParameters', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		$wrap_view_id = Services::Registry()->get('ApplicationParameters', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		$wrap_view_id = Services::Registry()->get('SiteParameters', 'wrap_view_id', 0);
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
	public function getPath($wrap_view_name)
	{
		$plus = '/View/Wrap/' . ucfirst(strtolower($wrap_view_name));

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Theme', 'path') . $plus . '/Manifest.xml')) {
			return Services::Registry()->get('Theme', 'path') . $plus;
		}

		/** 2. Extension */
		if (file_exists(Services::Registry()->get('Extension', 'path') . $plus . '/Manifest.xml')) {
			return Services::Registry()->get('Extension', 'path') . $plus;
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
	public function getPathURL($wrap_view_name)
	{
		$plus = '/View/Wrap/' . ucfirst(strtolower($wrap_view_name));

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Theme', 'path') . $plus . '/Manifest.xml')) {
			return Services::Registry()->get('Theme', 'path_url') . $plus;
		}

		/** 2. Extension */
		if (file_exists(Services::Registry()->get('Extension', 'path') . $plus . '/Manifest.xml')) {
			return Services::Registry()->get('Extension', 'path_url') . $plus;
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
