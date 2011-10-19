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

        /** check for configuration.php file - if exists redirect to error */
        if(JFile::exists(JPATH_SITE . '/configuration.php')) {
//            $this->setRedirect(MolajoRoute::_('index.php?option=com_installer&view=display&layout=error', false));
//            $this->redirect();
        }
        else if(!$this->getModel('display')->can_install) {
            $this->setRedirect(MolajoRoute::_('index.php?option=com_installer&view=display&layout=error', false));
            $this->redirect();
        }

        if(JRequest::getCmd('next_step', '', 'post')) {
            $this->setRedirect(MolajoRoute::_('index.php?option=com_installer&view=display&layout=' . JRequest::getCmd('next_step', 'post'), false));
            $this->redirect();
//            $this->getView('display')->setLayout('step2');
        }

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
