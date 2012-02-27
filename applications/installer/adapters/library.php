<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Library installer
 *
 * @package     Joomla.Platform
 * @subpackage  Installer
 * @since       11.1
 */
class MolajoAdapterLibrary extends MolajoAdapterInstance
{
    /**
     * Custom loadLanguage method
     *
     * @param   string  $path  The path where to find language files.
     *
     * @return  void
     *
     * @since   1.0
     */
    public function loadLanguage($path = null)
    {
        $source = $this->parent->getPath('source');
        if (!$source) {
            $this->parent->setPath('source', JPATH_PLATFORM . '/' . $this->parent->extension->element);
        }
        $this->manifest = $this->parent->getManifest();
        $extension = 'lib_' . strtolower(FilterInput::getInstance()->clean((string)$this->manifest->name, 'cmd'));
        $name = strtolower((string)$this->manifest->libraryname);
        $lang = Services::Language()->get('tag');
        $source = $path ? $path : JPATH_PLATFORM . "/$name";
        $lang->load($extension . '.sys', $source, null, false, false)
        || $lang->load($extension . '.sys', MOLAJO_BASE_FOLDER, null, false, false)
        || $lang->load($extension . '.sys', $source, $lang->getDefault(), false, false)
        || $lang->load($extension . '.sys', MOLAJO_BASE_FOLDER, $lang->getDefault(), false, false);
    }

    /**
     * Custom install method
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function install()
    {
        // Get the extension manifest object
        $this->manifest = $this->parent->getManifest();

        // Manifest Document Setup Section

        // Set the extensions name
        $name = FilterInput::getInstance()->clean((string)$this->manifest->name, 'string');
        $element = str_replace('.xml', '', basename($this->parent->getPath('manifest')));
        $this->set('name', $name);
        $this->set('element', $element);

        $db = $this->parent->getDb();
        $db->setQuery('SELECT extension_id FROM #__extensions WHERE type="library" AND element = "' . $element . '"');
        $result = $db->loadResult();
        if ($result) {
            // Already installed, can we upgrade?
            if ($this->parent->getOverwrite() || $this->parent->getUpgrade()) {
                // We can upgrade, so uninstall the old one
                $installer = new MolajoInstaller; // we don't want to compromise this instance!
                $installer->uninstall('library', $result);
            }
            else
            {
                // Abort the install, no upgrade possible
                $this->parent->abort(Services::Language()->translate('JLIB_INSTALLER_ABORT_LIB_INSTALL_ALREADY_INSTALLED'));
                return false;
            }
        }

        // Get the libraries description
        $description = (string)$this->manifest->description;
        if ($description) {
            $this->parent->set('message', Services::Language()->translate($description));
        }
        else
        {
            $this->parent->set('message', '');
        }

        // Set the installation path
        $group = (string)$this->manifest->libraryname;
        if (!$group) {
            $this->parent->abort(Services::Language()->translate('JLIB_INSTALLER_ABORT_LIB_INSTALL_NOFILE'));
            return false;
        }
        else
        {
            $this->parent->setPath('extension_root', JPATH_PLATFORM . '/' . implode(DS, explode('/', $group)));
        }

        // Filesystem Processing Section

        // If the plugin directory does not exist, let's create it
        $created = false;
        if (!file_exists($this->parent->getPath('extension_root'))) {
            if (!$created = Services::Folder()->create($this->parent->getPath('extension_root'))) {
                $this->parent->abort(
                    Services::Language()->sprintf('JLIB_INSTALLER_ABORT_LIB_INSTALL_FAILED_TO_CREATE_DIRECTORY', $this->parent->getPath('extension_root'))
                );
                return false;
            }
        }

        // If we created the plugin directory and will want to remove it if we
        // have to roll back the installation, let's add it to the installation
        // step stack

        if ($created) {
            $this->parent->pushStep(array('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
        }

        // Copy all necessary files
        if ($this->parent->parseFiles($this->manifest->files, -1) === false) {
            // Install failed, roll back changes
            $this->parent->abort();
            return false;
        }

        // Parse optional tags
        $this->parent->parseLanguages($this->manifest->languages);
        $this->parent->parseMedia($this->manifest->media);

        // Extension Registration
        $row = MolajoModel::getInstance('extension');
        $row->name = $this->get('name');
        $row->type = 'library';
        $row->element = $this->get('element');
        $row->folder = ''; // There is no folder for modules
        $row->enabled = 1;
        $row->protected = 0;
        $row->access = 1;
        $row->application_id = 0;
        $row->parameters = $this->parent->getParameters();
        $row->custom_data = ''; // custom data
        $row->manifest_cache = $this->parent->generateManifestCache();
        if (!$row->store()) {
            // Install failed, roll back changes
            $this->parent->abort(Services::Language()->sprintf('JLIB_INSTALLER_ABORT_LIB_INSTALL_ROLLBACK', $db->stderr(true)));
            return false;
        }

        // Finalization and Cleanup Section

        // Lastly, we will copy the manifest file to its appropriate place.
        $manifest = array();
        $manifest['src'] = $this->parent->getPath('manifest');
        $manifest['dest'] = SITE_MANIFESTS . '/libraries/' . basename($this->parent->getPath('manifest'));
        if (!$this->parent->copyFiles(array($manifest), true)) {
            // Install failed, rollback changes
            $this->parent->abort(Services::Language()->translate('JLIB_INSTALLER_ABORT_LIB_INSTALL_COPY_SETUP'));
            return false;
        }
        return $row->get('extension_id');
    }

    /**
     * Custom update method
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function update()
    {
        // Since this is just files, an update removes old files
        // Get the extension manifest object
        $this->manifest = $this->parent->getManifest();

        // Manifest Document Setup Section

        // Set the extensions name
        $name = (string)$this->manifest->name;
        $name = FilterInput::getInstance()->clean($name, 'string');
        $element = str_replace('.xml', '', basename($this->parent->getPath('manifest')));
        $this->set('name', $name);
        $this->set('element', $element);
        $installer = new MolajoInstaller; // we don't want to compromise this instance!
        $db = $this->parent->getDb();
        $db->setQuery('SELECT extension_id FROM #__extensions WHERE type="library" AND element = "' . $element . '"');
        $result = $db->loadResult();
        if ($result) {
            // Already installed, which would make sense
            $installer->uninstall('library', $result);
        }
        // Now create the new files
        return $this->install();
    }

    /**
     * Custom uninstall method
     *
     * @param   string  $id  The id of the library to uninstall.
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function uninstall($id)
    {
        // Initialise variables.
        $retval = true;

        // First order of business will be to load the module object table from the database.
        // This should give us the necessary information to proceed.
        $row = MolajoModel::getInstance('extension');
        if (!$row->load((int)$id) || !strlen($row->element)) {
            MolajoError::raiseWarning(100, Services::Language()->translate('ERRORUNKOWNEXTENSION'));
            return false;
        }

        // Is the library we are trying to uninstall a core one?
        // Because that is not a good idea...
        if ($row->protected) {
            MolajoError::raiseWarning(100, Services::Language()->translate('JLIB_INSTALLER_ERROR_LIB_UNINSTALL_WARNCORELIBRARY'));
            return false;
        }

        $manifestFile = SITE_MANIFESTS . '/libraries/' . $row->element . '.xml';

        // Because libraries may not have their own folders we cannot use the standard method of finding an installation manifest
        if (file_exists($manifestFile)) {
            $manifest = new MolajoInstallerLibrarymanifest($manifestFile);
            // Set the plugin root path
            $this->parent->setPath('extension_root', JPATH_PLATFORM . '/' . $manifest->libraryname);

            $xml = simplexml_load_file($manifestFile);

            // If we cannot load the XML file return null
            if (!$xml) {
                MolajoError::raiseWarning(100, Services::Language()->translate('JLIB_INSTALLER_ERROR_LIB_UNINSTALL_LOAD_MANIFEST'));
                return false;
            }

            // Check for a valid XML root tag.
            // TODO: Remove backwards compatability in a future version
            // Should be 'extension', but for backward compatability we will accept 'install'.

            if ($xml->getName() != 'extension') {
                MolajoError::raiseWarning(100, Services::Language()->translate('JLIB_INSTALLER_ERROR_LIB_UNINSTALL_INVALID_MANIFEST'));
                return false;
            }

            $this->parent->removeFiles($xml->files, -1);
            Services::File()->delete($manifestFile);

        }
        else
        {
            // Remove this row entry since its invalid
            $row->delete($row->extension_id);
            unset($row);
            MolajoError::raiseWarning(100, Services::Language()->translate('JLIB_INSTALLER_ERROR_LIB_UNINSTALL_INVALID_NOTFOUND_MANIFEST'));
            return false;
        }

        // TODO: Change this so it walked up the path backwards so we clobber multiple empties
        // If the folder is empty, let's delete it
        if (Services::Folder()->exists($this->parent->getPath('extension_root'))) {
            if (is_dir($this->parent->getPath('extension_root'))) {
                $files = Services::Folder()->files($this->parent->getPath('extension_root'));
                if (!count($files)) {
                    Services::Folder()->delete($this->parent->getPath('extension_root'));
                }
            }
        }

        $this->parent->removeFiles($xml->languages);

        $row->delete($row->extension_id);
        unset($row);

        return $retval;
    }

    /**
     * Custom discover method
     *
     * @return  array  JExtension  list of extensions available
     *
     * @since   1.0
     */
    public function discover()
    {
        $results = array();
        $file_list = Services::Folder()->files(SITE_MANIFESTS . '/libraries', '\.xml$');
        foreach ($file_list as $file)
        {
            $manifest_details = InstallHelper::parseManifestXML(SITE_MANIFESTS . '/libraries/' . $file);
            $file = Services::File()->no_extension($file);
            $extension = MolajoModel::getInstance('extension');
            $extension->set('type', 'library');
            $extension->set('application_id', 0);
            $extension->set('element', $file);
            $extension->set('name', $file);
            $extension->set('state', -1);
            $extension->set('manifest_cache', json_encode($manifest_details));
            $results[] = $extension;
        }
        return $results;
    }

    /**
     * Custom discover_install method
     *
     * @return  void
     *
     * @since   1.0
     */
    public function discover_install()
    {
        /* Libraries are a strange beast; they are actually references to files
           * There are two parts to a library which are disjunct in their locations
           * 1) The manifest file (stored in /SITE_MANIFESTS/libraries)
           * 2) The actual files (stored in /JPATH_PLATFORM/libraryname)
           * Thus installation of a library is the process of dumping files
           * in two different places. As such it is impossible to perform
           * any operation beyond mere registration of a library under the presumption
           * that the files exist in the appropriate location so that come uninstall
           * time they can be adequately removed.
           */

        $manifestPath = SITE_MANIFESTS . '/libraries/' . $this->parent->extension->element . '.xml';
        $this->parent->manifest = $this->parent->isManifest($manifestPath);
        $this->parent->setPath('manifest', $manifestPath);
        $manifest_details = InstallHelper::parseManifestXML($this->parent->getPath('manifest'));
        $this->parent->extension->manifest_cache = json_encode($manifest_details);
        $this->parent->extension->state = 0;
        $this->parent->extension->name = $manifest_details['name'];
        $this->parent->extension->enabled = 1;
        $this->parent->extension->parameters = $this->parent->getParameters();
        if ($this->parent->extension->store()) {
            return true;
        }
        else
        {
            MolajoError::raiseWarning(101, Services::Language()->translate('JLIB_INSTALLER_ERROR_LIB_DISCOVER_STORE_DETAILS'));
            return false;
        }
    }

    /**
     * Refreshes the extension table cache
     *
     * @return  boolean  Result of operation, true if updated, false on failure
     *
     * @since   1.0
     */
    public function refreshManifestCache()
    {
        // Need to find to find where the XML file is since we don't store this normally
        $manifestPath = SITE_MANIFESTS . '/libraries/' . $this->parent->extension->element . '.xml';
        $this->parent->manifest = $this->parent->isManifest($manifestPath);
        $this->parent->setPath('manifest', $manifestPath);

        $manifest_details = InstallHelper::parseManifestXML($this->parent->getPath('manifest'));
        $this->parent->extension->manifest_cache = json_encode($manifest_details);
        $this->parent->extension->name = $manifest_details['name'];

        try
        {
            return $this->parent->extension->store();
        }
        catch (Exception $e)
        {
            MolajoError::raiseWarning(101, Services::Language()->translate('JLIB_INSTALLER_ERROR_LIB_REFRESH_MANIFEST_CACHE'));
            return false;
        }
    }
}
