<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

use Molajo\Service\RequestService;
use Molajo\Extension\Helper;

defined('MOLAJO') or die;

/**
 * Molajo
 *
 * Creates instances of base classes
 */
class Molajo
{

	/**
	 * Molajo::Application
	 *
	 * @static
	 * @return  Application
	 * @since   1.0
	 */
	public static function Application()
	{
		if (self::$application) {
		} else {
			try {
				self::$application = Application::getInstance();
			}
			catch (\Exception $e) {
				echo 'Instantiate Application Exception : ', $e->getMessage(), "\n";
				die;
			}
		}

		return self::$application;
	}

	/**
	 * Application::Request
	 *
	 * @static
	 * @return  Parse
	 * @since   1.0
	 */
	public static function Request()
	{
		if (self::$request) {
		} else {
			try {
				self::$request = RequestServices::getInstance();
			}
			catch (\Exception $e) {
				echo 'Instantiate RequestService Exception : ', $e->getMessage(), "\n";
				die;
			}
		}

		return self::$request;
	}

	/**
	 * Molajo::Route
	 *
	 * @static
	 * @param string $override_request_url
	 * @param string $override_catalog_id
	 *
	 * @return Route
	 * @since 1.0
	 */
	public static function Route()
	{
		if (self::$route) {
		} else {
			try {
				self::$route = Route::getInstance();
			}
			catch (\Exception $e) {
				echo 'Instantiate Route Exception : ', $e->getMessage(), "\n";
				die;
			}
		}
		return self::$route;
	}

	/**
	 * Molajo::Parse
	 *
	 * @static
	 * @return  Parse
	 * @since   1.0
	 */
	public static function Parse()
	{
		if (self::$parse) {
		} else {
			try {
				self::$parse = Parse::getInstance();
			}
			catch (\Exception $e) {
				echo 'Instantiate Parse Exception : ', $e->getMessage(), "\n";
				die;
			}
		}

		return self::$parse;
	}

}
