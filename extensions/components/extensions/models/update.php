<?php
/**
 * @version        $Id: update.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.application.component.modellist');
jimport('joomla.installer.installer');
jimport('joomla.updater.updater');
jimport('joomla.updater.update');

/**
 * @package        Joomla.Administrator
 * @subpackage    installer
 * * * @since        1.0
 */
class InstallerModelUpdate extends JModelList
{
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.0
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'name',
                'application_id',
                'type',
                'folder',
                'extension_id',
                'update_id',
                'extension_site_id',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.0
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = MolajoFactory::getApplication('administrator');
        $this->setState('message', MolajoFactory::getApplication()->getUserState('installer.message'));
        $this->setState('extension_message', MolajoFactory::getApplication()->getUserState('installer.extension_message'));
        MolajoFactory::getApplication()->setUserState('installer.message', '');
        MolajoFactory::getApplication()->setUserState('installer.extension_message', '');
        parent::populateState('name', 'asc');
    }

    /**
     * Method to get the database query
     *
     * @return    JDatabaseQuery    The database query
     * @since    1.0
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        // grab updates ignoring new installs
        $query->select('*')->from('#__updates')->where('extension_id != 0');
        $query->order($this->getState('list.ordering') . ' ' . $this->getState('list.direction'));

        return $query;
    }

    /**
     * Finds updates for an extension.
     *
     * @param    int        Extension identifier to look for
     * @return    boolean Result
     * @since    1.0
     */
    public function findUpdates($eid = 0)
    {
        $updater = JUpdater::getInstance();
        $results = $updater->findUpdates($eid);
        return true;
    }

    /**
     * Removes all of the updates from the table.
     *
     * @return    boolean result of operation
     * @since    1.0
     */
    public function purge()
    {
        $db = MolajoFactory::getDbo();
        // Note: TRUNCATE is a DDL operation
        // This may or may not mean depending on your database
        $db->setQuery('TRUNCATE TABLE #__updates');
        if ($db->Query()) {
            $this->_message = MolajoTextHelper::_('INSTALLER_PURGED_UPDATES');
            return true;
        } else {
            $this->_message = MolajoTextHelper::_('INSTALLER_FAILED_TO_PURGE_UPDATES');
            return false;
        }
    }

    /**
     * Enables any disabled rows in #__extension_sites table
     *
     * @return    boolean result of operation
     * @since    1.0
     */
    public function enableSites()
    {
        $db = MolajoFactory::getDbo();
        $db->setQuery('UPDATE #__extension_sites SET enabled = 1 WHERE enabled = 0');
        if ($db->Query()) {
            if ($rows = $db->getAffectedRows()) {
                $this->_message .= MolajoTextHelper::plural('INSTALLER_ENABLED_UPDATES', $rows);
            }
            return true;
        } else {
            $this->_message .= MolajoTextHelper::_('INSTALLER_FAILED_TO_ENABLE_UPDATES');
            return false;
        }
    }

    /**
     * Update function.
     *
     * Sets the "result" state with the result of the operation.
     *
     * @param    Array[int] List of updates to apply
     * @since    1.0
     */
    public function update($uids)
    {
        $result = true;
        foreach ($uids as $uid) {
            $update = new JUpdate();
            $instance = JTable::getInstance('update');
            $instance->load($uid);
            $update->loadFromXML($instance->details_url);
            // install sets state and enqueues messages
            $res = $this->install($update);

            if ($res) {
                $this->purge();
            }

            $result = $res & $result;
        }

        // Set the final state
        $this->setState('result', $result);
    }

    /**
     * Handles the actual update installation.
     *
     * @param    JUpdate    An update definition
     * @return    boolean    Result of install
     * @since    1.0
     */
    private function install($update)
    {

        if (isset($update->get('downloadurl')->_data)) {
            $url = $update->downloadurl->_data;
        } else {
            MolajoError::raiseWarning('', MolajoTextHelper::_('INSTALLER_INVALID_EXTENSION_UPDATE'));
            return false;
        }

        jimport('joomla.installer.helper');
        $p_file = JInstallerHelper::downloadPackage($url);

        // Was the package downloaded?
        if (!$p_file) {
            MolajoError::raiseWarning('', MolajoTextHelper::sprintf('INSTALLER_PACKAGE_DOWNLOAD_FAILED', $url));
            return false;
        }

        $config = MolajoFactory::getApplication()->get();
        $tmp_dest = $config->get('temp_path');

        // Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest . '/' . $p_file);

        // Get an installer instance
        $installer = JInstaller::getInstance();
        $update->set('type', $package['type']);

        // Install the package
        if (!$installer->update($package['dir'])) {
            // There was an error updating the package
            $msg = MolajoTextHelper::sprintf('INSTALLER_MSG_UPDATE_ERROR', MolajoTextHelper::_('INSTALLER_TYPE_TYPE_' . strtoupper($package['type'])));
            $result = false;
        } else {
            // Package updated successfully
            $msg = MolajoTextHelper::sprintf('INSTALLER_MSG_UPDATE_SUCCESS', MolajoTextHelper::_('INSTALLER_TYPE_TYPE_' . strtoupper($package['type'])));
            $result = true;
        }

        // Quick change
        $this->type = $package['type'];

        // Set some model state values
        MolajoFactory::getApplication()->enqueueMessage($msg);

        // TODO: Reconfigure this code when you have more battery life left
        $this->setState('name', $installer->get('name'));
        $this->setState('result', $result);
        MolajoFactory::getApplication()->setUserState('installer.message', $installer->message);
        MolajoFactory::getApplication()->setUserState('installer.extension_message', $installer->get('extension_message'));

        // Cleanup the install files
        if (!is_file($package['packagefile'])) {
            $config = MolajoFactory::getApplication()->get();
            $package['packagefile'] = $config->get('temp_path') . '/' . $package['packagefile'];
        }

        JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

        return $result;
    }
}
