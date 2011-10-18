<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2011 Molajo. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display Controller
 *
 * @package	    Molajo
 * @subpackage	Controller
 * @since	    1.0
 */
class InstallerController extends MolajoController
{

    /**
     * display
     *
     * @return void
     */
    public function display($cachable = false, $urlparams = false)
    {
        /** form token check */

        /** first page - run system checks - auto detect language */

        /** check for configuration.php file - if exists redirect to error */

        /** filter all form fields - place into session objects */

        /** edit for data completeness for previous page -- redirect to appropriate page for errors */

        /** load unused fields into hidden form fields for display */

        parent::display($cachable, $urlparams);
    }

    /**
     * install
     *
     * install Molajo given the user selections
     *
     * @return bool
     */
    public function install()
    {
        /** form token check */

        /** check for configuration.php file - if exists redirect to error */

        /** filter all form fields - place into session objects */

        /** edit for data completeness -- redirect to appropriate page for errors */

        /** create database (base install + admin user) */

        /** populate sample data, if selected */

        /** install site and admin language files (must be connected to the Internet - what to do if not?) */

        /** save configuration file (display FTP page with config file if necessary) */

        /** success - redirect to administrator */
    }
}
