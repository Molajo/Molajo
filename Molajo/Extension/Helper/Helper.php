<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Helper;

use Molajo\Application\Services;

defined('MOLAJO') or die;

/**
 * Helper
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
Class Helper
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
            self::$instance = new Helper();
        }
        return self::$instance;
    }

	/**
	 * processCall
	 *
	 * Magic methods __call acts as a proxy to Extension Helpers
	 *
	 * Usage
	 * Services::Extension()->asset
	 * Services::Extension()->component ... etc.
	 *
	 * @static
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function __call($name, $arguments)
	{
		return $this->processCall($name, $arguments);
	}
	public static function __callStatic($name, $arguments)
	{
		return $this->processCall($name, $arguments);
	}
	public function processCall($name, $arguments)
	{
		$class = 'Molajo\\Extension\\Helper\\';
		$method = $name;

		$arg = array();
		$i = 0;
		$count = count($arguments);
		if ($count > 0) {
			foreach ($arguments as $item) {
				if ($i == 0) {
					$class .= ucfirst(strtolower($arguments[$i])).'Helper';
				} else {
					$arg[] = $arguments[$i];
				}
				$i++;
			}
		}

		if (class_exists($class)) {
		} else {
			Services::Debug()->set('Invalid Extension Helper Class: '. $name);
			return false;
		}

		if (method_exists($class, $method)) {
			return call_user_func_array (array($class, $method), $arg);
		}

		Services::Debug()->set('Invalid Extension Helper Method: '. $name);
		return false;
	}
}
