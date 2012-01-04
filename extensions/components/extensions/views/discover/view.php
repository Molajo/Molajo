<?php
/**
 * @version        $Id: view.html.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

include_once dirname(__FILE__) . '/../default/view.php';

/**
 * Extension Manager Manage View
 *
 * @package        Joomla.Administrator
 * @subpackage    installer
 * * * @since        1.0
 */
class InstallerViewDiscover extends InstallerViewDefault
{
    /**
     * @since    1.0
     */
    function display($tpl = null)
    {
        // Get data from the model
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

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
        /*
           * Set toolbar items for the page
           */
        MolajoToolbarHelper::custom('discover.install', 'upload', 'upload', 'TOOLBAR_INSTALL', true, false);
        MolajoToolbarHelper::custom('discover.refresh', 'refresh', 'refresh', 'INSTALLER_TOOLBAR_DISCOVER', false, false);
        MolajoToolbarHelper::custom('discover.purge', 'purge', 'purge', 'TOOLBAR_PURGE_CACHE', false, false);
        MolajoToolbarHelper::divider();
        parent::addToolbar();
        MolajoToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_DISCOVER');
    }
}