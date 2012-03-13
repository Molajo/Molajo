<?php
/**
 * @package	 Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license	 GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Molajo\Application\Services; //registry

defined('MOLAJO') or die;

/**
 * Parameter
 *
 * @package	 Molajo
 * @subpackage  Services
 * @since	   1.0
 */
Class ParameterService
{
	/**
	 * Static instance
	 *
	 * @var	object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * $page_parameters
	 *
	 * Parameters specific to the extension for the URL request
	 *
	 * @var	object
	 * @since  1.0
	 */
	public $page_parameters;

	/**
	 * $extension_parameters
	 *
	 * Parameters specific to the current extension
	 *
	 * @var	object
	 * @since  1.0
	 */
	public $extension_parameters;

	/**
	 * $theme_parameters
	 *
	 * Parameters specific to the current theme
	 *
	 * @var	object
	 * @since  1.0
	 */
	public $theme_parameters;

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
			self::$instance = new ParameterService();
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
		$this->request_parameters = Services::Registry()->initialise();
		$this->theme_parameters = Services::Registry()->initialise();
		$this->page_parameters = Services::Registry()->initialise();
	}

	/**
	 * get
	 *
	 * Returns a parameter property
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function get($key, $default = null, $type = 'request')
	{
		return $this->request_parameters->get($key, $default);
	}

	/**
	 * set
	 *
	 * Sets a Parameter property
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function set($key, $value = null, $type = 'request')
	{
		return $this->request_parameters->set($key, $value);
	}

	/**
	 * getAll
	 *
	 * Returns a parameter property
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function getAll($type = 'request')
	{
		//
	}

	/**
	 * getKeys
	 *
	 * Returns a parameter property
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function getKeys($type = 'request')
	{
		//
	}
}
