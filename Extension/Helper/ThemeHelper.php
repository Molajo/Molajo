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

		Services::Registry()->set('Parameters', 'theme_id', (int)$theme_id);
		$title = Helpers::Extension()->getInstanceTitle((int)$theme_id);
		Services::Registry()->set('Parameters', 'theme_title', $title);
		Services::Registry()->set('Parameters', 'theme_path', $this->getPath($title));
		Services::Registry()->set('Parameters', 'theme_path_include', $this->getPath($title) . '/index.php');
		Services::Registry()->set('Parameters', 'theme_path_url', $this->getPathURL($title));
		Services::Registry()->set('Parameters', 'favicon', $this->getFavicon($title));

		$row = Helpers::Extension()->get($theme_id, 'Theme');

		/** 500: Theme not found */
		if (count($row) == 0) {
			/** Try System Template */
			$theme_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_THEME, 'System');

			/** Get new Title and path */
			$title = Helpers::Extension()->getInstanceTitle((int)$theme_id);
			Services::Registry()->set('Parameters', 'theme_title', $title);
			Services::Registry()->set('Parameters', 'theme_path', $this->getPath($title));
			Services::Registry()->set('Parameters', 'theme_path_include', $this->getPath($title) . '/index.php');
			Services::Registry()->set('Parameters', 'theme_path_url', $this->getPathURL($title));
			Services::Registry()->set('Parameters', 'favicon', $this->getFavicon($title));

			$row = Helpers::Extension()->get($theme_id);

			if (count($row) == 0) {
				Services::Error()->set(500, 'Theme not found');
				return false;
			}
		}

		Services::Registry()->set('Parameters', 'theme_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Parameters', 'theme_language', $row['language']);
		Services::Registry()->set('Parameters', 'theme_view_group_id', $row['view_group_id']);
		Services::Registry()->set('Parameters', 'theme_catalog_id', $row['catalog_id']);
		Services::Registry()->set('Parameters', 'theme_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Parameters', 'theme_catalog_type_title', $row['catalog_type_title']);

		//todo: think about parameters.

		return;
	}

	/**
	 *  setDefaultTheme
	 *
	 *  Determine the default theme value, given system default sequence
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function setDefaultTheme()
	{
		$theme_id = Services::Registry()->get('Parameters', 'theme_id', 0);
		if ((int)$theme_id == 0) {
		} else {
			return $theme_id;
		}

		$theme_id = Services::Registry()->get('UserParameters', 'theme_id', 0);
		if ((int)$theme_id == 0) {
		} else {
			return $theme_id;
		}

		$theme_id = Services::Registry()->get('Configuration', 'theme_id', 0);
		if ((int)$theme_id == 0) {
		} else {
			return $theme_id;
		}

		return Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_THEME, 'System'); //99
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
		if (file_exists(EXTENSIONS_THEMES . '/' . ucfirst(strtolower($theme_name)) . '/' . 'index.php')) {
			return EXTENSIONS_THEMES . '/' . ucfirst(strtolower($theme_name)) ;
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
		if (file_exists(EXTENSIONS_THEMES . '/' . ucfirst(strtolower($theme_name))  . '/' . 'index.php')) {
			return EXTENSIONS_THEMES_URL . '/' . ucfirst(strtolower($theme_name)) ;
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
		$path = EXTENSIONS_THEMES . '/' . ucfirst(strtolower($theme_name))  . '/images/';
		if (file_exists($path . 'favicon.ico')) {
			return EXTENSIONS_THEMES_URL . '/' . ucfirst(strtolower($theme_name))  . '/images/favicon.ico';
		}

		$path = BASE_FOLDER;
		if (file_exists($path . 'favicon.ico')) {
			return BASE_URL . '/favicon.ico';
		}

		return false;
	}
}
