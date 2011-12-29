<?php
/**
 * @version        $Id: controller.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Installer Controller
 *
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @since        1.5
 */
class InstallerController extends JController
{
    /**
     * Method to display a view.
     *
     * @param    boolean            If true, the view output will be cached
     * @param    array            An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return    JController        This object to support chaining.
     * @since    1.0
     */
    public function display($cachable = false, $urlparameters = false)
    {
        require_once JPATH_COMPONENT . '/helpers/installer.php';

        // Set the default view name and format from the Request.
        $vName = JRequest::getCmd('view', 'install');
        $vFormat = MolajoFactory::getApplication()->getType();
        $lName = JRequest::getCmd('layout', 'default');

        // Get and render the view.
        if ($view = $this->getView($vName, $vFormat)) {
            $ftp = JClientHelper::setCredentialsFromRequest('ftp');
            $view->assignRef('ftp', $ftp);

            // Get the model for the view.
            $model = $this->getModel($vName);

            // Push the model into the view (as default).
            $view->setModel($model, true);
            $view->setLayout($lName);

            // Load the submenu.
            InstallerHelper::addSubmenu($vName);
            $view->display();
        }

        return $this;
    }
}