<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoExtensionConfiguration
 *
 * @package     Molajo
 * @subpackage  Configuration
 * @since       1.0
 */
class MolajoExtensionConfiguration
{

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct() {}

    /**
     * get
     *
     * Retrieves and combines site and application configuration objects
     *
     * Returns the global configuration object, creating it
     * if it doesn't already exist.
     *
     * @return configuration object
     * @throws RuntimeException
     * @since  1.0
     */
    public function getConfig()
    {
        $results = array();
        return $results;
    }
}
