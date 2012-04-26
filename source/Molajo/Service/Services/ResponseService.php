<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

defined('MOLAJO') or die;

use Molajo\Service\Services;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response
 *
 * http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Response.html
 *
 * @package   Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class ResponseService extends Response
{
	/**
	 * Response instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * $response
	 *
	 * @var        object
	 * @since      1.0
	 */
	protected $response;

	/**
	 * getInstance
	 *
	 * @static
	 * @return object
	 * @since  1.0
	 */
	public static function getInstance($content = '', $status = 200, $headers = array())
	{
		if (empty(self::$instance)) {
			self::$instance = new ResponseService($content, $status, $headers);
		}
		return self::$instance;
	}

	/**
	 * __construct
	 *
	 * Class constructor.
	 *
	 * @since  1.0
	 */
	public function __construct($content, $status, $headers)
	{
		$this->response = new Response();
		parent::__construct($content, $status, $headers);
	}
}
