<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * ModuleHelper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class ModuleHelper
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
			self::$instance = new ModuleHelper();
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
	 * Retrieve module data
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function get($name)
	{
		$row = Helpers::Extension()->get(
			CATALOG_TYPE_EXTENSION_MODULE,
			$name
		);
		if (count($row) == 0) {
			return array();
		}
		return $row;
	}

	/**
	 * getPath
	 *
	 * Return path for selected Module
	 *
	 * @return bool|string
	 * @since 1.0
	 */
	public function getPath($name)
	{
		return EXTENSIONS_MODULES . '/' . $name;
	}
}
