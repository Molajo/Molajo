<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Request
 *
 * @url http://symfony.com/doc/current/components/http_foundation.html#accessing-request-data
 *
 * @package   Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class RequestService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Request Connection
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $connection;

	/**
	 * Request
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $request;

	/**
	 * Parameters
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $parameters;

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
			self::$instance = new RequestService();
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

		/** Request */
		$class = 'Symfony\\Component\\HttpFoundation\\Request';
		$this->connection = new $class();
		$this->request = $this->connection->createFromGlobals();

		/** Request */
		$class2 = 'Symfony\\Component\\HttpFoundation\\ParameterBag';
		$this->connection2 = new $class2();

		return $this;
	}

	public function get($key)
	{
		return $this->$key;
	}
}
