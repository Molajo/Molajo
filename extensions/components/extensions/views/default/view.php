<?php
/**
 * @version        $Id: view.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Extension Manager Default View
 *
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @since        1.5
 */
class InstallerViewDefault extends JView
{
    /**
     * @since    1.0
     */
    function __construct($config = null)
    {

        parent::__construct($config);
        $this->_addPath('template', $this->_basePath . '/views/default/views');
        $this->_addPath('template', JPATH_THEMES . '/' . MolajoController::getApplication()->getTemplate() . '/html/installer/default');
    }

    /**
     * @since    1.0
     */
    function display($tpl = null)
    {
        // Get data from the model
        $state = $this->get('State');

        // Are there messages to display ?
        $showMessage = false;
        if (is_object($state)) {
            $message1 = $state->get('message');
            $message2 = $state->get('extension_message');
            $showMessage = ($message1 || $message2);
        }

        $this->assign('showMessage', $showMessage);
        $this->assignRef('state', $state);

        MolajoHTML::_('behavior.tooltip');
        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since    1.0
     */
    protected function addToolbar()
    {
        $canDo = InstallerHelper::getActions();
        MolajoToolbarHelper::title(MolajoTextHelper::_('INSTALLER_HEADER_' . $this->getName()), 'install.png');

        if ($canDo->get('core.admin')) {
            MolajoToolbarHelper::preferences('installer');
            MolajoToolbarHelper::divider();
        }

        // Document
        MolajoController::getApplication()->setTitle(MolajoTextHelper::_('INSTALLER_TITLE_' . $this->getName()));
    }
}
