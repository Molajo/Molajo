<?php
/**
 * @version        $Id: install.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Import library dependencies

jimport('joomla.application.component.model');
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');

/**
 * Extension Manager Install Model
 *
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @since        1.5
 */
class InstallerModelInstall extends JModel
{
    /**
     * @var object JTable object
     */
    protected $_table = null;

    /**
     * @var object JTable object
     */
    protected $_url = null;

    /**
     * Model context string.
     *
     * @var        string
     */
    protected $_context = 'installer.install';

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.0
     */
    protected function populateState()
    {
        // Initialise variables.
        $app = MolajoFactory::getApplication('administrator');

        $this->setState('message', MolajoFactory::getApplication()->getUserState('installer.message'));
        $this->setState('extension_message', MolajoFactory::getApplication()->getUserState('installer.extension_message'));
        MolajoFactory::getApplication()->setUserState('installer.message', '');
        MolajoFactory::getApplication()->setUserState('installer.extension_message', '');

        // Recall the 'Install from Directory' path.
        $path = MolajoFactory::getApplication()->getUserStateFromRequest($this->_context . '.install_directory', 'install_directory', MolajoFactory::getApplication()->getConfig('temp_path'));
        $this->setState('install.directory', $path);
        parent::populateState();
    }

    /**
     * Install an extension from either folder, url or upload.
     *
     * @return    boolean result of install
     * @since    1.0
     */
    function install()
    {
        $this->setState('action', 'install');

        // Set FTP credentials, if given.
        JClientHelper::setCredentialsFromRequest('ftp');


        switch (JRequest::getWord('installtype')) {
            case 'folder':
                // Remember the 'Install from Directory' path.
                MolajoFactory::getApplication()->getUserStateFromRequest($this->_context . '.install_directory', 'install_directory');
                $package = $this->_getPackageFromFolder();
                break;

            case 'upload':
                $package = $this->_getPackageFromUpload();
                break;

            case 'url':
                $package = $this->_getPackageFromUrl();
                break;

            default:
                MolajoFactory::getApplication()->setUserState('installer.message', MolajoTextHelper::_('INSTALLER_NO_INSTALL_TYPE_FOUND'));
                return false;
                break;
        }

        // Was the package unpacked?
        if (!$package) {
            MolajoFactory::getApplication()->setUserState('installer.message', MolajoTextHelper::_('INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'));
            return false;
        }

        // Get an installer instance
        $installer = JInstaller::getInstance();

        // Install the package
        if (!$installer->install($package['dir'])) {
            // There was an error installing the package
            $msg = MolajoTextHelper::sprintf('INSTALLER_INSTALL_ERROR', MolajoTextHelper::_('INSTALLER_TYPE_TYPE_' . strtoupper($package['type'])));
            $result = false;
        } else {
            // Package installed sucessfully
            $msg = MolajoTextHelper::sprintf('INSTALLER_INSTALL_SUCCESS', MolajoTextHelper::_('INSTALLER_TYPE_TYPE_' . strtoupper($package['type'])));
            $result = true;
        }

        // Set some model state values

        MolajoFactory::getApplication()->enqueueMessage($msg);
        $this->setState('name', $installer->get('name'));
        $this->setState('result', $result);
        MolajoFactory::getApplication()->setUserState('installer.message', $installer->message);
        MolajoFactory::getApplication()->setUserState('installer.extension_message', $installer->get('extension_message'));
        MolajoFactory::getApplication()->setUserState('installer.redirect_url', $installer->get('redirect_url'));

        // Cleanup the install files
        if (!is_file($package['packagefile'])) {
            $config = MolajoFactory::getApplication()->getConfig();
            $package['packagefile'] = $config->get('temp_path') . '/' . $package['packagefile'];
        }

        JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);


        return $result;
    }

    /**
     * Works out an installation package from a HTTP upload
     *
     * @return package definition or false on failure
     */
    protected function _getPackageFromUpload()
    {
        // Get the uploaded file information
        $userfile = JRequest::getVar('install_package', null, 'files', 'array');

        // Make sure that file uploads are enabled in php
        if (!(bool)ini_get('file_uploads')) {
            MolajoError::raiseWarning('', MolajoTextHelper::_('INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
            return false;
        }

        // Make sure that zlib is loaded so that the package can be unpacked
        if (!extension_loaded('zlib')) {
            MolajoError::raiseWarning('', MolajoTextHelper::_('INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
            return false;
        }

        // If there is no uploaded file, we have a problem...
        if (!is_array($userfile)) {
            MolajoError::raiseWarning('', MolajoTextHelper::_('INSTALLER_MSG_INSTALL_NO_FILE_SELECTED'));
            return false;
        }

        // Check if there was a problem uploading the file.
        if ($userfile['error'] || $userfile['size'] < 1) {
            MolajoError::raiseWarning('', MolajoTextHelper::_('INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
            return false;
        }

        // Build the appropriate paths
        $config = MolajoFactory::getApplication()->getConfig();
        $tmp_dest = $config->get('temp_path') . '/' . $userfile['name'];
        $tmp_src = $userfile['tmp_name'];

        // Move uploaded file
        jimport('joomla.filesystem.file');
        $uploaded = JFile::upload($tmp_src, $tmp_dest);

        // Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest);

        return $package;
    }

    /**
     * Install an extension from a directory
     *
     * @return    Package details or false on failure
     * @since    1.0
     */
    protected function _getPackageFromFolder()
    {
        // Get the path to the package to install
        $p_dir = JRequest::getString('install_directory');
        $p_dir = JPath::clean($p_dir);

        // Did you give us a valid directory?
        if (!is_dir($p_dir)) {
            MolajoError::raiseWarning('', MolajoTextHelper::_('INSTALLER_MSG_INSTALL_PLEASE_ENTER_A_PACKAGE_DIRECTORY'));
            return false;
        }

        // Detect the package type
        $type = JInstallerHelper::detectType($p_dir);

        // Did you give us a valid package?
        if (!$type) {
            MolajoError::raiseWarning('', MolajoTextHelper::_('INSTALLER_MSG_INSTALL_PATH_DOES_NOT_HAVE_A_VALID_PACKAGE'));
            return false;
        }

        $package['packagefile'] = null;
        $package['extractdir'] = null;
        $package['dir'] = $p_dir;
        $package['type'] = $type;

        return $package;
    }

    /**
     * Install an extension from a URL
     *
     * @return    Package details or false on failure
     * @since    1.0
     */
    protected function _getPackageFromUrl()
    {
        // Get a database connector
        $db = MolajoFactory::getDbo();

        // Get the URL of the package to install
        $url = JRequest::getString('install_url');

        // Did you give us a URL?
        if (!$url) {
            MolajoError::raiseWarning('', MolajoTextHelper::_('INSTALLER_MSG_INSTALL_ENTER_A_URL'));
            return false;
        }

        // Download the package at the URL given
        $p_file = JInstallerHelper::downloadPackage($url);

        // Was the package downloaded?
        if (!$p_file) {
            MolajoError::raiseWarning('', MolajoTextHelper::_('INSTALLER_MSG_INSTALL_INVALID_URL'));
            return false;
        }

        $config = MolajoFactory::getApplication()->getConfig();
        $tmp_dest = $config->get('temp_path');

        // Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest . '/' . $p_file);

        return $package;
    }
}
