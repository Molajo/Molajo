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
 * Package installer
 *
 * @package     Joomla.Platform
 * @subpackage  Installer
 * @since       11.1
 */
class MolajoInstallerAdapterPackage extends MolajoAdapterInstance
{
    /**
     * Method of system
     *
     * @var    string
     *
     * @since  11.1
     */
    protected $route = 'install';

    /**
     * Load language from a path
     *
     * @param   string  $path  The path of the language.
     *
     * @return  void
     *
     * @since   1.0
     */
    public function loadLanguage($path)
    {
        $this->manifest = $this->parent->getManifest();
        $extension = 'pkg_' . strtolower(JFilterInput::getInstance()->clean((string)$this->manifest->packagename, 'cmd'));
        $lang = Molajo::Applicationlication()->getLanguage();
        $source = $path;
        $lang->load($extension . '.sys', $source, null, false, false)
        || $lang->load($extension . '.sys', MOLAJO_BASE_FOLDER, null, false, false)
        || $lang->load($extension . '.sys', $source, $lang->getDefault(), false, false)
        || $lang->load($extension . '.sys', MOLAJO_BASE_FOLDER, $lang->getDefault(), false, false);
    }

    /**
     * Custom install method
     *
     * @return  int  The extension id
     *
     * @since   1.0
     */
    public function install()
    {
        // Get the extension manifest object
        $this->manifest = $this->parent->getManifest();

        // Manifest Document Setup Section

        // Set the extensions name
        $filter = JFilterInput::getInstance();
        $name = (string)$this->manifest->packagename;
        $name = $filter->clean($name, 'cmd');
        $this->set('name', $name);

        $element = 'pkg_' . $filter->clean($this->manifest->packagename, 'cmd');
        $this->set('element', $element);

        // Get the component description
        $description = (string)$this->manifest->description;
        if ($description) {
            $this->parent->set('message', TextHelper::_($description));
        }
        else
        {
            $this->parent->set('message', '');
        }

        // Set the installation path
        $files = $this->manifest->files;
        $group = (string)$this->manifest->packagename;
        if (!empty($group)) {
            // TODO: Remark this location
            $this->parent->setPath('extension_root', MOLAJO_BASE_FOLDER . '/packages/' . implode(DS, explode('/', $group)));
        }
        else
        {
            $this->parent->abort(TextHelper::sprintf('JLIB_INSTALLER_ABORT_PACK_INSTALL_NO_PACK', TextHelper::_('JLIB_INSTALLER_' . strtoupper($this->route))));
            return false;
        }

        // Filesystem Processing Section

        if ($folder = $files->attributes()->folder) {
            $source = $this->parent->getPath('source') . '/' . $folder;
        }
        else
        {
            $source = $this->parent->getPath('source');
        }

        // Install all necessary files
        if (count($this->manifest->files->children())) {
            foreach ($this->manifest->files->children() as $child)
            {
                $file = $source . '/' . $child;
                jimport('joomla.installer.helper');
                if (is_dir($file)) {
                    // If it's actually a directory then fill it up
                    $package = array();
                    $package['dir'] = $file;
                    $package['type'] = MolajoInstallerHelper::detectType($file);
                }
                else
                {
                    // If it's an archive
                    $package = MolajoInstallerHelper::unpack($file);
                }
                $tmpInstaller = new MolajoInstaller;
                if (!$tmpInstaller->install($package['dir'])) {
                    $this->parent->abort(
                        TextHelper::sprintf(
                            'JLIB_INSTALLER_ABORT_PACK_INSTALL_ERROR_EXTENSION', TextHelper::_('JLIB_INSTALLER_' . strtoupper($this->route)),
                            basename($file)
                        )
                    );
                    return false;
                }
            }
        }
        else
        {
            $this->parent->abort(TextHelper::sprintf('JLIB_INSTALLER_ABORT_PACK_INSTALL_NO_FILES', TextHelper::_('JLIB_INSTALLER_' . strtoupper($this->route))));
            return false;
        }

        // Parse optional tags
        $this->parent->parseLanguages($this->manifest->languages);

        // Extension Registration

        $row = MolajoModel::getInstance('extension');
        $eid = $row->find(array('element' => strtolower($this->get('element')), 'type' => 'package'));
        if ($eid) {
            $row->load($eid);
        }
        else
        {
            $row->name = $this->get('name');
            $row->type = 'package';
            $row->element = $this->get('element');
            // There is no folder for modules
            $row->folder = '';
            $row->enabled = 1;
            $row->protected = 0;
            $row->access = 1;
            $row->application_id = 0;
            // custom data
            $row->custom_data = '';
            $row->parameters = $this->parent->getParameters();
        }
        // Update the manifest cache for the entry
        $row->manifest_cache = $this->parent->generateManifestCache();

        if (!$row->store()) {
            // Install failed, roll back changes
            $this->parent->abort(TextHelper::sprintf('JLIB_INSTALLER_ABORT_PACK_INSTALL_ROLLBACK', $row->getError()));
            return false;
        }

        // Finalization and Cleanup Section

        // Lastly, we will copy the manifest file to its appropriate place.
        $manifest = array();
        $manifest['src'] = $this->parent->getPath('manifest');
        $manifest['dest'] = MOLAJO_SITE_MANIFESTS . '/packages/' . basename($this->parent->getPath('manifest'));

        if (!$this->parent->copyFiles(array($manifest), true)) {
            // Install failed, rollback changes
            $this->parent->abort(
                TextHelper::sprintf('JLIB_INSTALLER_ABORT_PACK_INSTALL_COPY_SETUP', TextHelper::_('JLIB_INSTALLER_ABORT_PACK_INSTALL_NO_FILES'))
            );
            return false;
        }
        return $row->extension_id;
    }

    /**
     * Updates a package
     *
     * The only difference between an update and a full install
     * is how we handle the database
     *
     * @return  void
     *
     * @since   1.0
     */
    public function update()
    {
        $this->route = 'update';
        $this->install();
    }

    /**
     * Custom uninstall method
     *
     * @param   integer  $id  The id of the package to uninstall.
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    function uninstall($id)
    {
        // Initialise variables.
        $row = null;
        $retval = true;

        $row = MolajoModel::getInstance('extension');
        $row->load($id);

        if ($row->protected) {
            MolajoError::raiseWarning(100, TextHelper::_('JLIB_INSTALLER_ERROR_PACK_UNINSTALL_WARNCOREPACK'));
            return false;
        }

        $manifestFile = MOLAJO_SITE_MANIFESTS . '/packages/' . $row->get('element') . '.xml';
        $manifest = new JPackageManifest($manifestFile);

        // Set the package root path
        $this->parent->setPath('extension_root', MOLAJO_SITE_MANIFESTS . '/packages/' . $manifest->packagename);

        // Because packages may not have their own folders we cannot use the standard method of finding an installation manifest
        if (!file_exists($manifestFile)) {
            // TODO: Fail?
            MolajoError::raiseWarning(100, TextHelper::_('JLIB_INSTALLER_ERROR_PACK_UNINSTALL_MISSINGMANIFEST'));
            return false;

        }

        $xml = Molajo::XML($manifestFile);

        // If we cannot load the XML file return false
        if (!$xml) {
            MolajoError::raiseWarning(100, TextHelper::_('JLIB_INSTALLER_ERROR_PACK_UNINSTALL_LOAD_MANIFEST'));
            return false;
        }

        /*
           * Check for a valid XML root tag.
           * @todo: Remove backwards compatability in a future version
           * Should be 'extension', but for backward compatability we will accept 'install'.
           */
        if ($xml->getName() != 'install' && $xml->getName() != 'extension') {
            MolajoError::raiseWarning(100, TextHelper::_('JLIB_INSTALLER_ERROR_PACK_UNINSTALL_INVALID_MANIFEST'));
            return false;
        }

        $error = false;
        foreach ($manifest->filelist as $extension)
        {
            $tmpInstaller = new MolajoInstaller;
            $id = $this->_getExtensionID($extension->type, $extension->id, $extension->client, $extension->group);
            $client = ApplicationHelper::getApplicationInfo($extension->client, true);
            if ($id) {
                if (!$tmpInstaller->uninstall($extension->type, $id, $client->id)) {
                    $error = true;
                    MolajoError::raiseWarning(100, TextHelper::sprintf('JLIB_INSTALLER_ERROR_PACK_UNINSTALL_NOT_PROPER', basename($extension->filename)));
                }
            }
            else
            {
                MolajoError::raiseWarning(100, TextHelper::_('JLIB_INSTALLER_ERROR_PACK_UNINSTALL_UNKNOWN_EXTENSION'));
            }
        }

        // Remove any language files
        $this->parent->removeFiles($xml->languages);

        // clean up manifest file after we're done if there were no errors
        if (!$error) {
            JFile::delete($manifestFile);
            $row->delete();
        }
        else
        {
            MolajoError::raiseWarning(100, TextHelper::_('JLIB_INSTALLER_ERROR_PACK_UNINSTALL_MANIFEST_NOT_REMOVED'));
        }

        // Return the result up the line
        return $retval;
    }

    /**
     * Gets the extension id.
     *
     * @param   string   $type    The extension type.
     * @param   string   $id      The name of the extension (the element field).
     * @param   integer  $client  The appliaction id (0: Joomla CMS site; 1: Joomla CMS administrator).
     * @param   string   $group   The extension group (mainly for plugins).
     *
     * @return  integer
     *
     * @since   1.0
     */
    protected function _getExtensionID($type, $id, $client, $group)
    {
        $db = $this->parent->getDbo();
        $result = $id;

        $query = $db->getQuery(true);
        $query->select('extension_id');
        $query->from('#__extensions');
        $query->where('type = ' . $db->Quote($type));
        $query->where('element = ' . $db->Quote($id));

        switch ($type)
        {
            case 'plugin':
                // Plugins have a folder but not a client
                $query->where('folder = ' . $db->Quote($group));
                break;

            case 'library':
            case 'package':
            case 'component':
                // Components, packages and libraries don't have a folder or client.
                // Included for completeness.
                break;

            case 'language':
            case 'module':
            case 'theme':
                // Languages, modules and themes have a client but not a folder
                $client = ApplicationHelper::getApplicationInfo($client, true);
                $query->where('application_id = ' . (int)$client->id);
                break;
        }

        $db->setQuery($query->__toString());
        $result = $db->loadResult();

        // Note: For themes, libraries and packages their unique name is their key.
        // This means they come out the same way they came in.
        return $result;
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
        $manifestPath = MOLAJO_SITE_MANIFESTS . '/packages/' . $this->parent->extension->element . '.xml';
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
            MolajoError::raiseWarning(101, TextHelper::_('JLIB_INSTALLER_ERROR_PACK_REFRESH_MANIFEST_CACHE'));
            return false;
        }
    }
}
