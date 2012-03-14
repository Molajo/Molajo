<?php
/**
 * @package	 Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license	 GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Joomla\registry\Registry;

defined('MOLAJO') or die;

/**
 * Request
 *
 * @package	 Molajo
 * @subpackage  Services
 * @since	   1.0
 */
Class RegistryService
{
	/**
	 * Static instance
	 *
	 * @var	object
	 * @since  1.0
	 */
	protected static $instance;

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
			self::$instance = new RegistryService();
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
		$this->parameters = array();
	}

	/**
	 * initialise
	 *
	 * Create new Registry object that can be used locally
	 *
	 * Usage:
	 * $local = Services::Registry()->initalise();
	 *
	 *
	 * @return \Joomla\registry\Registry
	 */
	public function initialise()
	{
		return new Registry();
	}

	/**
	 * create
	 *
	 * Create new parameter set that is stored within the RegistryService
	 * class and can accessed globally throughout the application
	 *
	 * Usage:
	 * Services::Registry()->create('request');
	 *
	 * @param   string  $name
	 *
	 * @return  null
	 * @since   1.0
	 */
	public function create($name)
	{
		$this->parameters[$name] = new Registry();
		return;
	}

	/**
	 * set
	 *
	 * Sets a Parameter property for a specific item and parameter set
	 *
	 * Usage:
	 * Services::Registry()->set('request\\parameter_name', $value);
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function set($key, $value = null)
	{
		$split = explode('\\', $key);
		if (isset($this->parameters[$split[0]])) {
		} else {
			$this->create($split[0]);
		}
		return $this->parameters[$split[0]]->set($split[1], $value);
	}

	/**
	 * get
	 *
	 * Returns a Parameter property for a specific item and parameter set
	 *
	 * Usage:
	 * Services::Registry()->get('request\\parameter_name');
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @param	string	$type
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function get($key, $default = null)
	{
		$split = explode('\\', $key);
		return $this->parameters[$split[0]]->get($split[1], $default);
	}

	/**
	 * getAll
	 *
	 * Returns an array containing the key and name pairs for a specified parameter set
	 *
	 * Usage:
	 * Services::Registry()->getArray('request');
	 *
	 * @param   string  $name
	 * @param   boolean @keyOnly set to true to retrieve keynames
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function getArray($name, $keyOnly = false)
	{
		$r = new Registry($this->parameters[$name]);
		$temp = $r->toArray();
		$newArray = array();
		foreach ($temp as $item) {
			foreach ($item as $key => $value) {
				if ($keyOnly === false) {
					$newArray[$key] = $value;
				} else {
					$newArray[] = $key;
				}
			}
		}
		return $newArray;
	}

	/**
	 * getKeys
	 *
	 * Returns all key names for the specified parameter set
	 *
	 * Usage:
	 * Services::Registry()->getKeys('request');
	 *
	 * @param   string  $name
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function getKeys($name)
	{
		return $this->getArray($name, true);
	}
}
