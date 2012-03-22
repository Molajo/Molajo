<?php
/**
 * @package	 	Molajo
 * @copyright	Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license	 	GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Molajo\Application\Services;

defined('MOLAJO') or die;

/**
 * Parameter
 *
 * @package	 Molajo
 * @subpackage  Services
 * @since	   1.0
 */
Class DebugService
{
	/**
	 * Static instance
	 *
	 * @var	object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * $on Switch
	 *
	 * @var	object
	 * @since  1.0
	 */
	public $on;

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
			self::$instance = new DebugService();
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
	public function __construct()
	{
		$config = Services::Registry()->initialise();
		$this->on = (int) Services::Configuration()->get('debug', 0);

		return $this;
	}

	/**
	 * get
	 *
	 * Returns a property of the Request Parameter object
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function get($key, $value)
	{
        if ($key == 'on') {
            return $this->on;
        }

	}

	/**
	 * set
	 *
	 * Modifies a property of the Request Parameter object
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function set($message)
	{
		if ((int) $this->on == 0) {
			return $this;
		}

		echo $message.'<br />';

		return $this;
	}
}
