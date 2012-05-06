<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Service\Services;

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
	 * $data
	 *
	 * Allows collection of any set of data for a single $item
	 *
	 * @var    array
	 * @since  1.0
	 */
	public $data = array();

	/**
	 * $rows
	 *
	 * Retains pointer to current row contained within the $data array
	 *
	 * @var    int
	 * @since  1.0
	 */
	protected $rows = 0;

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
	 * Class constructor.
	 *
	 * @since  1.0
	 */
	public function __construct()
	{

	}

	/**
	 * get
	 *
	 * Get requested theme data
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function get($theme)
	{
		return Application::Helper()
			->get('Extension',
			CATALOG_TYPE_EXTENSION_THEME,
			$theme
		);
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
