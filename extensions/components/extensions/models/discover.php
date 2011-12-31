<?php
/**
 * @version        $Id: discover.php 21320 2011-05-11 01:01:37Z dextercowley $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Import library dependencies
require_once dirname(__FILE__) . '/extension.php';
jimport('joomla.installer.installer');

/**
 * Installer Manage Model
 *
 * @package        Joomla.Administator
 * @subpackage    installer
 * * * @since        1.0
 */
class InstallerModelDiscover extends InstallerModel
{
    protected $_context = 'installer.discover';

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.0
     */
    protected function populateState($ordering = null, $direction = null)
    {

        $this->setState('message', MolajoController::getUser()->getUserState('installer.message'));
        $this->setState('extension_message', MolajoController::getUser()->getUserState('installer.extension_message'));
        MolajoController::getUser()->setUserState('installer.message', '');
        MolajoController::getUser()->setUserState('installer.extension_message', '');
        parent::populateState('name', 'asc');
    }

    /**
     * Method to get the database query.
     *
     * @return    JDatabaseQuery the database query
     * @since    1.0
     */
    protected function getListQuery()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__extensions');
        $query->where('state=-1');
        return $query;
    }

    /**
     * Discover extensions.
     *
     * Finds uninstalled extensions
     *
     * @since    1.0
     */
    function discover()
    {
        $installer = JInstaller::getInstance();
        $results = $installer->discover();

        // Get all templates, including discovered ones
        $query = 'SELECT extension_id, element, folder, application_id, type FROM #__extensions';
        $dbo = MolajoController::getDbo();
        $dbo->setQuery($query);
        $installedtmp = $dbo->loadObjectList();
        $extensions = Array();

        foreach ($installedtmp as $install)
        {
            $key = implode(':', Array($install->type, $install->element, $install->folder, $install->application_id));
            $extensions[$key] = $install;
        }
        unset($installedtmp);


        foreach ($results as $result) {
            // check if we have a match on the element
            $key = implode(':', Array($result->type, $result->element, $result->folder, $result->application_id));
            if (!array_key_exists($key, $extensions)) {
                $result->store(); // put it into the table
            }
        }
    }

    /**
     * Installs a discovered extension.
     *
     * @since    1.0
     */
    function discover_install()
    {

        $installer = JInstaller::getInstance();
        $eid = JRequest::getVar('cid', 0);
        if (is_array($eid) || $eid) {
            if (!is_array($eid)) {
                $eid = Array($eid);
            }
            JArrayHelper::toInteger($eid);

            $failed = false;
            foreach ($eid as $id) {
                $result = $installer->discover_install($id);
                if (!$result) {
                    $failed = true;
                    MolajoController::getApplication()->setMessage(MolajoTextHelper::_('INSTALLER_MSG_DISCOVER_INSTALLFAILED') . ': ' . $id);
                }
            }
            $this->setState('action', 'remove');
            $this->setState('name', $installer->get('name'));
            MolajoController::getUser()->setUserState('installer.message', $installer->message);
            MolajoController::getUser()->setUserState('installer.extension_message', $installer->get('extension_message'));
            if (!$failed) {
                MolajoController::getApplication()->setMessage(MolajoTextHelper::_('INSTALLER_MSG_DISCOVER_INSTALLSUCCESSFUL'));
            }
        } else {
            MolajoController::getApplication()->setMessage(MolajoTextHelper::_('INSTALLER_MSG_DISCOVER_NOEXTENSIONSELECTED'));
        }
    }

    /**
     * Cleans out the list of discovered extensions.
     *
     * @since    1.0
     */
    function purge()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);
        $query->delete();
        $query->from('#__extensions');
        $query->where('state = -1');
        $db->setQuery((string)$query);
        if ($db->Query()) {
            $this->_message = MolajoTextHelper::_('INSTALLER_MSG_DISCOVER_PURGEDDISCOVEREDEXTENSIONS');
            return true;
        } else {
            $this->_message = MolajoTextHelper::_('INSTALLER_MSG_DISCOVER_FAILEDTOPURGEEXTENSIONS');
            return false;
        }
    }
}
