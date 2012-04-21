<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

use Molajo\Application\Services;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Redirect
 *
 * http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Cookie.html
 *
 * @package   Molajo
 * @subpackage  Services
 * @since           1.0
 */
Class CookieService
{
	/**
	 * Response instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new CookieService();
		}
		return self::$instance;
	}
}
