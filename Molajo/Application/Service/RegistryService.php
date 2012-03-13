<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Joomla\registry\Registry;

defined('MOLAJO') or die;

/**
 * Request
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class RegistryService
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
            self::$instance = new RegistryService();
        }
        return self::$instance;
    }

	/**
	 * initialise
	 *
	 * Create new Registry object
	 *
	 * @return \Joomla\registry\Registry
	 */
	public function initialise()
	{
		return new Registry();
	}
}
