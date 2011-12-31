<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Primary Controller
 *
 *  Initiates Site and Application Controllers
 *
 */
class MolajoController
{
    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   array  $config  A configuration array
     *
     * @since  1.0
     */
    public function __construct($config = null)
    {
        self::load();
    }

    /**
     * load
     *
     * Loads the Site and Application
     *
     * @param    array
     *
     * @since 1.0
     */
    public function load()
    {
        /**
         *  Get the Site
         */
        $site = MolajoFactory::getSite(MOLAJO_SITE_ID);

        /**
         *  Load the Site
         */
        $site->load();

        /**
         *  Get the Application
         */
        $app = MolajoFactory::getApplication(MOLAJO_APPLICATION);

        /**
         *  Load the Application
         */
        $app->load();
    }
}