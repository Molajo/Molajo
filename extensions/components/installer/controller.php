<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Molajo. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display Controller
 *
 * @package        Molajo
 * @subpackage    Controller
 * @since        1.0
 */
class InstallerController extends MolajoController
{

    /**
     * display
     *
     * @return void
     */
    public function display($cachable = false, $urlparameters = false)
    {

        /** form token check */

        /** check for configuration.php file - if exists redirect to error */
        if (JFile::exists(MOLAJO_BASE_FOLDER . '/configuration.php')) {
            //            $this->setRedirect(MolajoRouteHelper::_('index.php?option=installer&view=display&view=error', false));
            //            $this->redirect();
        }
        else if (!$this->getModel('display')->can_install) {
            $this->setRedirect(MolajoRouteHelper::_('index.php?option=installer&view=display&view=error', false));
            $this->redirect();
        }

        if (JRequest::getCmd('next_step', '', 'post')) {
            $this->setRedirect(MolajoRouteHelper::_('index.php?option=installer&view=display&view=' . JRequest::getCmd('next_step', 'post'), false));
            $this->redirect();
            //            $this->getView('display')->setView('step2');
        }

        parent::display($cachable, $urlparameters);
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

        $model = $this->getModel('database');
        $config = $this->getModel('database')->getSetup();

        /** filter all form fields - place into session objects */

        /** edit for data completeness -- redirect to appropriate page for errors */

        /** save configuration file (display FTP page with config file if necessary) */
        if (!$this->getModel('configuration')->setup($config)) {
            // Trow error
        }


        /** create database (base install + admin user) */
        /** populate sample data, if selected */
        if (!$model->install($config)) {
            // Trow error
        }


        /** install site and admin language files (must be connected to the Internet - what to do if not?) */
        // This needs to be decided in step 1, just resort to default (en-UK?) that must be shipped with installer

        /** save configuration file (display FTP page with config file if necessary) */

        /** success - redirect to administrator */

    }
}
