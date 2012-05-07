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
 * ThemeHelper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class ThemeHelper
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
			self::$instance = new ThemeHelper();
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
	public function get($theme_id = 0)
	{
		if ($theme_id == 0) {
			$theme_id = $this->setDefaultTheme();
		}

		$row = Helpers::Extension()->get($theme_id);

		/** 500: Theme not found */
		if (count($row) == 0) {
			/** Try System Template */
			$theme_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_THEME, 'System');
			$row = Helpers::Extension()->get($theme_id);
			if (count($row) == 0) {
				Services::Error()->set(500, 'Theme not found');
				return false;
			}
		}

		Services::Registry()->set('Theme', 'id', (int)$row->id);
		Services::Registry()->set('Theme', 'title', $row->title);
		Services::Registry()->set('Theme', 'translation_of_id', $row->translation_of_id);
		Services::Registry()->set('Theme', 'language', $row->language);
		Services::Registry()->set('Theme', 'view_group_id', $row->view_group_id);
		Services::Registry()->set('Theme', 'catalog_id', $row->catalog_id);
		Services::Registry()->set('Theme', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('Theme', 'catalog_type_title', $row->catalog_type_title);

		Services::Registry()->set('Theme', 'path', $this->getPath($row->title));
		Services::Registry()->set('Theme', 'path_url', $this->getPathURL($row->title));
		Services::Registry()->set('Theme', 'favicon', $this->getFavicon($row->title));

		/** Load special fields for specific extension */
		$xml = Services::Configuration()->loadFile('Manifest', Services::Registry()->get('Theme', 'path'));
		$row = Services::Configuration()->addSpecialFields($xml->config, $row, 1);

		return;
	}

	/**
	 * 	setDefaultTheme
	 *
	 *  Determine the default theme value, given system default sequence
	 *
	 *  @return  string
	 *  @since   1.0
	 */
	public function setDefaultTheme()
	{
		$theme_id = Services::Registry()->get('ContentParameters', 'theme_id', 0);
		if ((int) $theme_id == 0) {
		} else {
			return $theme_id;
		}

		$theme_id = Services::Registry()->get('MenuItemParameters', 'theme_id', 0);
		if ((int) $theme_id == 0) {
		} else {
			return $theme_id;
		}

		$theme_id = Services::Registry()->get('CategoryParameters', 'theme_id', 0);
		if ((int) $theme_id == 0) {
		} else {
			return $theme_id;
		}

		$theme_id = Services::Registry()->get('ExtensionParameters', 'theme_id', 0);
		if ((int) $theme_id == 0) {
		} else {
			return $theme_id;
		}

		$theme_id = Services::Registry()->get('UserParameters', 'theme_id', 0);
		if ((int) $theme_id == 0) {
		} else {
			return $theme_id;
		}

		$theme_id = Services::Registry()->get('ApplicationParameters', 'theme_id', 0);
		if ((int) $theme_id == 0) {
		} else {
			return $theme_id;
		}

		$theme_id = Services::Registry()->get('SiteParameters', 'theme_id', 0);
		if ((int) $theme_id == 0) {
		} else {
			return $theme_id;
		}

		return Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_THEME, 'System');     //99
	}


	/**
	 * getPath
	 *
	 * Return path for selected Theme
	 *
	 * @param $theme_name
	 * @return bool|string
	 */
	public function getPath($theme_name)
	{
		if (file_exists(EXTENSIONS_THEMES . '/' . $theme_name . '/' . 'index.php')) {
			return EXTENSIONS_THEMES . '/' . $theme_name;
		}

		return false;
	}

	/**
	 * getPath
	 *
	 * Return path for selected Theme
	 *
	 * @return bool|string
	 * @since 1.0
	 */
	public function getPathURL($theme_name)
	{
		if (file_exists(EXTENSIONS_THEMES . '/' . $theme_name . '/' . 'index.php')) {
			return EXTENSIONS_THEMES_URL . '/' . $theme_name;
		}

		return false;
	}

	/**
	 * getFavicon
	 *
	 * Retrieve Favicon Path
	 *
	 * Can be located in:
	 *  - Themes/images/ folder (priority 1)
	 *  - Root of the website (priority 2)
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function getFavicon($theme_name)
	{
		$path = EXTENSIONS_THEMES . '/' . $theme_name . '/images/';
		if (file_exists($path . 'favicon.ico')) {
			return EXTENSIONS_THEMES_URL . '/' . $theme_name . '/images/favicon.ico';
		}
		$path = BASE_FOLDER;
		if (file_exists($path . 'favicon.ico')) {
			return BASE_URL . $theme_name . '/images/favicon.ico';
		}

		return false;
	}
}
