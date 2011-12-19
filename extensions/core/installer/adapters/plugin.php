<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Plugin installer
 *
 * @package     Joomla.Platform
 * @subpackage  Installer
 * @since       11.1
 */
class MolajoInstallerAdapterPlugin extends MolajoAdapterInstance
{
    /**
     * Install function routing
     *
     * @var    string
     * @since  11.1
     * */
    var $route = 'install';

    /**
     * The installation manifest XML object
     *
     * @var
     * @since  11.1
     * */
    protected $manifest = null;

    /**
     *
     *
     * @var
     * @since  11.1
     * */

    protected $manifest_script = null;

    /**
     *
     *
     * @var
     * @since  11.1
     * */
    protected $name = null;

    /**
     *
     *
     * @var
     * @since  11.1
     * */
    protected $scriptElement = null;

    /**
     * @var
     * @since  11.1
     */
    protected $oldFiles = null;

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
            $this->parent->setPath('source', MOLAJO_EXTENSIONS_PLUGINS . '/' . $this->parent->extension->folder . '/' . $this->parent->extension->element);
        }
        $this->manifest = $this->parent->getManifest();
        $element = $this->manifest->files;
        if ($element) {
            $group = strtolower((string)$this->manifest->attributes()->group);
            $name = '';
            if (count($element->children())) {
                foreach ($element->children() as $file)
                {
                    if ((string)$file->attributes()->plugin) {
                        $name = strtolower((string)$file->attributes()->plugin);
                        break;
                    }
                }
            }
            if ($name) {
                $extension = "plg_${group}_${name}";
                $lang = MolajoFactory::getLanguage();
                $source = $path ? $path : MOLAJO_EXTENSIONS_PLUGINS . "/$group/$name";
                $folder = (string)$element->attributes()->folder;
                if ($folder && file_exists("$path/$folder")) {
                    $source = "$path/$folder";
                }
                $lang->load($extension . '.sys', $source, null, false, false)
                || $lang->load($extension . '.sys', MOLAJO_BASE_FOLDER, null, false, false)
                || $lang->load($extension . '.sys', $source, $lang->getDefault(), false, false)
                || $lang->load($extension . '.sys', MOLAJO_BASE_FOLDER, $lang->getDefault(), false, false);
            }
        }
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
        // Get a database connector object
        $db = $this->parent->getDbo();

        // Get the extension manifest object
        $this->manifest = $this->parent->getManifest();

        $xml = $this->manifest;

        // Manifest Document Setup Section

        // Set the extension name
        $name = (string)$xml->name;
        $name = JFilterInput::getInstance()->clean($name, 'string');
        $this->set('name', $name);

        // Get the component description
        $description = (string)$xml->description;
        if ($description) {
            $this->parent->set('message', MolajoTextHelper::_($description));
        }
        else
        {
            $this->parent->set('message', '');
        }

        /*
           * Backward Compatability
           * @todo Deprecate in future version
           */
        $type = (string)$xml->attributes()->type;

        // Set the installation path
        if (count($xml->files->children())) {
            foreach ($xml->files->children() as $file)
            {
                if ((string)$file->attributes()->$type) {
                    $element = (string)$file->attributes()->$type;
                    break;
                }
            }
        }
        $group = (string)$xml->attributes()->group;
        if (!empty($element) && !empty($group)) {
            $this->parent->setPath('extension_root', MOLAJO_EXTENSIONS_PLUGINS . '/' . $group . '/' . $element);
        }
        else
        {
            $this->parent->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT_PLG_INSTALL_NO_FILE', MolajoTextHelper::_('JLIB_INSTALLER_' . $this->route)));
            return false;
        }

        // Check if we should enable overwrite settings

        // Check to see if a plugin by the same name is already installed.
        $query = $db->getQuery(true);
        $query->select($query->qn('extension_id'))->from($query->qn('#__extensions'));
        $query->where($query->qn('folder') . ' = ' . $query->q($group));
        $query->where($query->qn('element') . ' = ' . $query->q($element));
        $db->setQuery($query);
        try
        {
            $db->Query();
        }
        catch (Exception $e)
        {
            // Install failed, roll back changes
            $this->parent
                    ->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT_PLG_INSTALL_ROLLBACK', MolajoTextHelper::_('JLIB_INSTALLER_' . $this->route), $db->stderr(true)));
            return false;
        }
        $id = $db->loadResult();

        // If it's on the fs...
        if (file_exists($this->parent->getPath('extension_root')) && (!$this->parent->getOverwrite() || $this->parent->getUpgrade())) {
            $updateElement = $xml->update;
            // Upgrade manually set or
            // Update function available or
            // Update tag detected
            if ($this->parent->getUpgrade() || ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'update'))
                || is_a($updateElement, 'SimpleXMLElement')
            ) {
                // Force this one
                $this->parent->setOverwrite(true);
                $this->parent->setUpgrade(true);
                if ($id) {
                    // If there is a matching extension mark this as an update; semantics really
                    $this->route = 'update';
                }
            }
            elseif (!$this->parent->getOverwrite())
            {
                // Overwrite is set
                // We didn't have overwrite set, find an udpate function or find an update tag so lets call it safe
                $this->parent
                        ->abort(
                    MolajoTextHelper::sprintf(
                        'JLIB_INSTALLER_ABORT_PLG_INSTALL_DIRECTORY', MolajoTextHelper::_('JLIB_INSTALLER_' . $this->route),
                        $this->parent->getPath('extension_root')
                    )
                );
                return false;
            }
        }

        // Installer Trigger Loading

        // If there is an manifest class file, let's load it; we'll copy it later (don't have destination yet).

        if ((string)$xml->scriptfile) {
            $manifestScript = (string)$xml->scriptfile;
            $manifestScriptFile = $this->parent->getPath('source') . '/' . $manifestScript;
            if (is_file($manifestScriptFile)) {
                // Load the file
                include_once $manifestScriptFile;
            }
            // If a dash is present in the group name, remove it
            $groupClass = str_replace('-', '', $group);
            // Set the class name
            $classname = 'plg' . $groupClass . $element . 'InstallerScript';
            if (class_exists($classname)) {
                // Create a new instance
                $this->parent->manifestClass = new $classname($this);
                // And set this so we can copy it later
                $this->set('manifest_script', $manifestScript);

                // Note: if we don't find the class, don't bother to copy the file
            }
        }

        // Run preflight if possible (since we know we're not an update)
        ob_start();
        ob_implicit_flush(false);
        if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'preflight')) {
            if ($this->parent->manifestClass->preflight($this->route, $this) === false) {
                // Install failed, rollback changes
                $this->parent->abort(MolajoTextHelper::_('JLIB_INSTALLER_ABORT_PLG_INSTALL_CUSTOM_INSTALL_FAILURE'));
                return false;
            }
        }
        $msg = ob_get_contents(); // create msg object; first use here
        ob_end_clean();

        // Filesystem Processing Section

        // If the plugin directory does not exist, lets create it
        $created = false;
        if (!file_exists($this->parent->getPath('extension_root'))) {
            if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
                $this->parent
                        ->abort(
                    MolajoTextHelper::sprintf(
                        'JLIB_INSTALLER_ABORT_PLG_INSTALL_CREATE_DIRECTORY', MolajoTextHelper::_('JLIB_INSTALLER_' . $this->route),
                        $this->parent->getPath('extension_root')
                    )
                );
                return false;
            }
        }

        // If we're updating at this point when there is always going to be an extension_root find the old XML files
        if ($this->route == 'update') {
            // Hunt for the original XML file
            $old_manifest = null;
            $tmpInstaller = new MolajoInstaller; // create a new installer because findManifest sets stuff; side effects!
            // Look in the extension root
            $tmpInstaller->setPath('source', $this->parent->getPath('extension_root'));
            if ($tmpInstaller->findManifest()) {
                $old_manifest = $tmpInstaller->getManifest();
                $this->oldFiles = $old_manifest->files;
            }
        }

        // If we created the plugin directory and will want to remove it if we
        // have to roll back the installation, let's add it to the installation
        // step stack

        if ($created) {
            $this->parent->pushStep(array('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
        }

        // Copy all necessary files
        if ($this->parent->parseFiles($xml->files, -1, $this->oldFiles) === false) {
            // Install failed, roll back changes
            $this->parent->abort();
            return false;
        }

        // Parse optional tags -- media and language files for plugins go in admin app
        $this->parent->parseMedia($xml->media, 1);
        $this->parent->parseLanguages($xml->languages, 1);

        // If there is a manifest script, lets copy it.
        if ($this->get('manifest_script')) {
            $path['src'] = $this->parent->getPath('source') . '/' . $this->get('manifest_script');
            $path['dest'] = $this->parent->getPath('extension_root') . '/' . $this->get('manifest_script');

            if (!file_exists($path['dest'])) {
                if (!$this->parent->copyFiles(array($path))) {
                    // Install failed, rollback changes
                    $this->parent
                            ->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT_PLG_INSTALL_MANIFEST', MolajoTextHelper::_('JLIB_INSTALLER_' . $this->route)));
                    return false;
                }
            }
        }

        // Database Processing Section

        $row = MolajoTable::getInstance('extension');
        // Was there a plugin with the same name already installed?
        if ($id) {
            if (!$this->parent->getOverwrite()) {
                // Install failed, roll back changes
                $this->parent
                        ->abort(
                    MolajoTextHelper::sprintf(
                        'JLIB_INSTALLER_ABORT_PLG_INSTALL_ALLREADY_EXISTS', MolajoTextHelper::_('JLIB_INSTALLER_' . $this->route),
                        $this->get('name')
                    )
                );
                return false;
            }
            $row->load($id);
            $row->name = $this->get('name');
            $row->manifest_cache = $this->parent->generateManifestCache();
            $row->store(); // update the manifest cache and name
        }
        else
        {
            // Store in the extensions table (1.6)
            $row->name = $this->get('name');
            $row->type = 'plugin';
            $row->ordering = 0;
            $row->element = $element;
            $row->folder = $group;
            $row->enabled = 0;
            $row->protected = 0;
            $row->access = 1;
            $row->application_id = 0;
            $row->parameters = $this->parent->getParameters();
            // Custom data
            $row->custom_data = '';
            // System data
            $row->system_data = '';
            $row->manifest_cache = $this->parent->generateManifestCache();

            // Editor plugins are published by default
            if ($group == 'editors') {
                $row->enabled = 1;
            }

            if (!$row->store()) {
                // Install failed, roll back changes
                $this->parent
                        ->abort(
                    MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT_PLG_INSTALL_ROLLBACK', MolajoTextHelper::_('JLIB_INSTALLER_' . $this->route), $db->stderr(true))
                );
                return false;
            }

            // Since we have created a plugin item, we add it to the installation step stack
            // so that if we have to rollback the changes we can undo it.
            $this->parent->pushStep(array('type' => 'extension', 'id' => $row->extension_id));
            $id = $row->extension_id;
        }

        // Let's run the queries for the module
        //	If Joomla 1.5 compatible, with discreet sql files - execute appropriate
        //	file for utf-8 support or non-utf-8 support

        // Try for Joomla 1.5 type queries
        // Second argument is the utf compatible version attribute
        if (strtolower($this->route) == 'install') {
            $utfresult = $this->parent->parseSQLFiles($this->manifest->install->sql);
            if ($utfresult === false) {
                // Install failed, rollback changes
                $this->parent
                        ->abort(
                    MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT_PLG_INSTALL_SQL_ERROR', MolajoTextHelper::_('JLIB_INSTALLER_' . $this->route), $db->stderr(true))
                );
                return false;
            }

            // Set the schema version to be the latest update version
            if ($this->manifest->update) {
                $this->parent->setSchemaVersion($this->manifest->update->schemas, $row->extension_id);
            }
        }
        elseif (strtolower($this->route) == 'update')
        {
            if ($this->manifest->update) {
                $result = $this->parent->parseSchemaUpdates($this->manifest->update->schemas, $row->extension_id);
                if ($result === false) {
                    // Install failed, rollback changes
                    $this->parent->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT_PLG_UPDATE_SQL_ERROR', $db->stderr(true)));
                    return false;
                }
            }
        }

        // Start Joomla! 1.6
        ob_start();
        ob_implicit_flush(false);
        if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, $this->route)) {
            if ($this->parent->manifestClass->{
                $this->route
                }($this) === false
            ) {
                // Install failed, rollback changes
                $this->parent->abort(MolajoTextHelper::_('JLIB_INSTALLER_ABORT_PLG_INSTALL_CUSTOM_INSTALL_FAILURE'));
                return false;
            }
        }
        // Append messages
        $msg .= ob_get_contents();
        ob_end_clean();

        // Finalization and Cleanup Section

        // Lastly, we will copy the manifest file to its appropriate place.
        if (!$this->parent->copyManifest(-1)) {
            // Install failed, rollback changes
            $this->parent->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT_PLG_INSTALL_COPY_SETUP', MolajoTextHelper::_('JLIB_INSTALLER_' . $this->route)));
            return false;
        }
        // And now we run the postflight
        ob_start();
        ob_implicit_flush(false);
        if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'postflight')) {
            $this->parent->manifestClass->postflight($this->route, $this);
        }
        // Append messages
        $msg .= ob_get_contents();
        ob_end_clean();
        if ($msg != '') {
            $this->parent->set('extension_message', $msg);
        }
        return $id;
    }

    /**
     * Custom update method
     *
     * @return   boolean  True on success
     *
     * @since    1.0
     */
    function update()
    {
        // Set the overwrite setting
        $this->parent->setOverwrite(true);
        $this->parent->setUpgrade(true);
        // Set the route for the install
        $this->route = 'update';
        // Go to install which handles updates properly
        return $this->install();
    }

    /**
     * Custom uninstall method
     *
     * @param   integer  $id  The id of the plugin to uninstall
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function uninstall($id)
    {
        $this->route = 'uninstall';

        // Initialise variables.
        $row = null;
        $retval = true;
        $db = $this->parent->getDbo();

        // First order of business will be to load the plugin object table from the database.
        // This should give us the necessary information to proceed.
        $row = MolajoTable::getInstance('extension');
        if (!$row->load((int)$id)) {
            MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_ERRORUNKOWNEXTENSION'));
            return false;
        }

        // Is the plugin we are trying to uninstall a core one?
        // Because that is not a good idea...
        if ($row->protected) {
            MolajoError::raiseWarning(100, MolajoTextHelper::sprintf('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_WARNCOREPLUGIN', $row->name));
            return false;
        }

        // Get the plugin folder so we can properly build the plugin path
        if (trim($row->folder) == '') {
            MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_FOLDER_FIELD_EMPTY'));
            return false;
        }

        // Set the plugin root path
        if (is_dir(MOLAJO_EXTENSIONS_PLUGINS . '/' . $row->folder . '/' . $row->element)) {
            // Use 1.6 plugins
            $this->parent->setPath('extension_root', MOLAJO_EXTENSIONS_PLUGINS . '/' . $row->folder . '/' . $row->element);
        }
        else
        {
            // Use Legacy 1.5 plugins
            $this->parent->setPath('extension_root', MOLAJO_EXTENSIONS_PLUGINS . '/' . $row->folder);
        }

        // Because 1.5 plugins don't have their own folders we cannot use the standard method of finding an installation manifest
        // Since 1.6 they do, however until we move to 1.7 and remove 1.6 legacy we still need to use this method.
        // When we get there it'll be something like "$this->parent->findManifest();$manifest = $this->parent->getManifest();"
        $manifestFile = $this->parent->getPath('extension_root') . '/' . $row->element . '.xml';

        if (!file_exists($manifestFile)) {
            MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_INVALID_NOTFOUND_MANIFEST'));
            return false;
        }

        $xml = MolajoFactory::getXML($manifestFile);

        $this->manifest = $xml;

        // If we cannot load the XML file return null
        if (!$xml) {
            MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_LOAD_MANIFEST'));
            return false;
        }

        /*
           * Check for a valid XML root tag.
           * @todo: Remove backwards compatability in a future version
           * Should be 'extension', but for backward compatability we will accept 'install'.
           */
        if ($xml->getName() != 'install' && $xml->getName() != 'extension') {
            MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_INVALID_MANIFEST'));
            return false;
        }

        // Attempt to load the language file; might have uninstall strings
        $this->parent->setPath('source', MOLAJO_EXTENSIONS_PLUGINS . '/' . $row->folder . '/' . $row->element);
        $this->loadLanguage(MOLAJO_EXTENSIONS_PLUGINS . '/' . $row->folder . '/' . $row->element);

        // Installer Trigger Loading

        // If there is an manifest class file, let's load it; we'll copy it later (don't have dest yet)
        $manifestScript = (string)$xml->scriptfile;
        if ($manifestScript) {
            $manifestScriptFile = $this->parent->getPath('source') . '/' . $manifestScript;
            if (is_file($manifestScriptFile)) {
                // Load the file
                include_once $manifestScriptFile;
            }
            // If a dash is present in the folder, remove it
            $folderClass = str_replace('-', '', $row->folder);
            // Set the class name
            $classname = 'plg' . $folderClass . $row->element . 'InstallerScript';
            if (class_exists($classname)) {
                // Create a new instance
                $this->parent->manifestClass = new $classname($this);
                // And set this so we can copy it later
                $this->set('manifest_script', $manifestScript);

                // Note: if we don't find the class, don't bother to copy the file
            }
        }

        // Run preflight if possible (since we know we're not an update)
        ob_start();
        ob_implicit_flush(false);
        if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'preflight')) {
            if ($this->parent->manifestClass->preflight($this->route, $this) === false) {
                // Install failed, rollback changes
                $this->parent->abort(MolajoTextHelper::_('JLIB_INSTALLER_ABORT_PLG_INSTALL_CUSTOM_INSTALL_FAILURE'));
                return false;
            }
        }
        // Create msg object; first use here
        $msg = ob_get_contents();
        ob_end_clean();

        // Let's run the queries for the module
        // If Joomla 1.5 compatible, with discreet sql files - execute appropriate
        // file for utf-8 support or non-utf-8 support

        // Try for Joomla 1.5 type queries
        // Second argument is the utf compatible version attribute
        $utfresult = $this->parent->parseSQLFiles($xml->{strtolower($this->route)}->sql);
        if ($utfresult === false) {
            // Install failed, rollback changes
            $this->parent->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT_PLG_UNINSTALL_SQL_ERROR', $db->stderr(true)));
            return false;
        }

        // Start Joomla! 1.6
        ob_start();
        ob_implicit_flush(false);
        if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'uninstall')) {
            $this->parent->manifestClass->uninstall($this);
        }
        // Append messages
        $msg = ob_get_contents();
        ob_end_clean();

        // Remove the plugin files
        $this->parent->removeFiles($xml->images, -1);
        $this->parent->removeFiles($xml->files, -1);
        JFile::delete($manifestFile);

        // Remove all media and languages as well
        $this->parent->removeFiles($xml->media);
        $this->parent->removeFiles($xml->languages, 1);

        // Remove the schema version
        $query = $db->getQuery(true);
        $query->delete()->from('#__schemas')->where('extension_id = ' . $row->extension_id);
        $db->setQuery($query);
        $db->Query();

        // Now we will no longer need the plugin object, so let's delete it
        $row->delete($row->extension_id);
        unset($row);

        // If the folder is empty, let's delete it
        $files = JFolder::files($this->parent->getPath('extension_root'));

        JFolder::delete($this->parent->getPath('extension_root'));

        if ($msg) {
            $this->parent->set('extension_message', $msg);
        }

        return $retval;
    }

    /**
     * Custom discover method
     *
     * @return  array  JExtension) list of extensions available
     *
     * @since   1.0
     */
    function discover()
    {
        $results = array();
        $folder_list = JFolder::folders(MOLAJO_BASE_FOLDER . '/plugins');

        foreach ($folder_list as $folder)
        {
            $file_list = JFolder::files(MOLAJO_BASE_FOLDER . '/plugins/' . $folder, '\.xml$');
            foreach ($file_list as $file)
            {
                $manifest_details = MolajoApplicationHelper::parseXMLInstallFile(MOLAJO_BASE_FOLDER . '/plugins/' . $folder . '/' . $file);
                $file = JFile::stripExt($file);
                // Ignore example plugins
                if ($file == 'example') {
                    continue;
                }

                $extension = MolajoTable::getInstance('extension');
                $extension->set('type', 'plugin');
                $extension->set('application_id', 0);
                $extension->set('element', $file);
                $extension->set('folder', $folder);
                $extension->set('name', $file);
                $extension->set('state', -1);
                $extension->set('manifest_cache', json_encode($manifest_details));
                $results[] = $extension;
            }
            $folder_list = JFolder::folders(MOLAJO_BASE_FOLDER . '/plugins/' . $folder);
            foreach ($folder_list as $plugin_folder)
            {
                $file_list = JFolder::files(MOLAJO_BASE_FOLDER . '/plugins/' . $folder . '/' . $plugin_folder, '\.xml$');
                foreach ($file_list as $file)
                {
                    $manifest_details = MolajoApplicationHelper::parseXMLInstallFile(
                        MOLAJO_BASE_FOLDER . '/plugins/' . $folder . '/' . $plugin_folder . '/' . $file
                    );
                    $file = JFile::stripExt($file);

                    if ($file == 'example') {
                        continue;
                    }

                    // ignore example plugins
                    $extension = MolajoTable::getInstance('extension');
                    $extension->set('type', 'plugin');
                    $extension->set('application_id', 0);
                    $extension->set('element', $file);
                    $extension->set('folder', $folder);
                    $extension->set('name', $file);
                    $extension->set('state', -1);
                    $extension->set('manifest_cache', json_encode($manifest_details));
                    $results[] = $extension;
                }
            }
        }
        return $results;
    }

    /**
     * Custom discover_install method.
     *
     * @return  mixed
     *
     * @since   1.0
     */
    public function discover_install()
    {
        // Plugins use the extensions table as their primary store
        // Similar to modules and templates, rather easy
        // If it's not in the extensions table we just add it
        $client = MolajoApplicationHelper::getApplicationInfo($this->parent->extension->application_id);
        if (is_dir($client->path . '/plugins/' . $this->parent->extension->folder . '/' . $this->parent->extension->element)) {
            $manifestPath = $client->path . '/plugins/' . $this->parent->extension->folder . '/' . $this->parent->extension->element . '/'
                            . $this->parent->extension->element . '.xml';
        }
        else
        {
            $manifestPath = $client->path . '/plugins/' . $this->parent->extension->folder . '/' . $this->parent->extension->element . '.xml';
        }
        $this->parent->manifest = $this->parent->isManifest($manifestPath);
        $description = (string)$this->parent->manifest->description;
        if ($description) {
            $this->parent->set('message', MolajoTextHelper::_($description));
        }
        else
        {
            $this->parent->set('message', '');
        }
        $this->parent->setPath('manifest', $manifestPath);
        $manifest_details = MolajoApplicationHelper::parseXMLInstallFile($manifestPath);
        $this->parent->extension->manifest_cache = json_encode($manifest_details);
        $this->parent->extension->state = 0;
        $this->parent->extension->name = $manifest_details['name'];
        $this->parent->extension->enabled = ('editors' == $this->parent->extension->folder) ? 1 : 0;
        $this->parent->extension->parameters = $this->parent->getParameters();
        if ($this->parent->extension->store()) {
            return $this->parent->extension->get('extension_id');
        }
        else
        {
            MolajoError::raiseWarning(101, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_PLG_DISCOVER_STORE_DETAILS'));
            return false;
        }
    }

    /**
     * Refreshes the extension table cache.
     *
     * @return  boolean  Result of operation, true if updated, false on failure.
     *
     * @since   1.0
     */
    public function refreshManifestCache()
    {
        // Plugins use the extensions table as their primary store
        // Similar to modules and templates, rather easy
        // If it's not in the extensions table we just add it
        $client = MolajoApplicationHelper::getApplicationInfo($this->parent->extension->application_id);
        $manifestPath = $client->path . '/plugins/' . $this->parent->extension->folder . '/' . $this->parent->extension->element . '/'
                        . $this->parent->extension->element . '.xml';
        $this->parent->manifest = $this->parent->isManifest($manifestPath);
        $this->parent->setPath('manifest', $manifestPath);
        $manifest_details = MolajoApplicationHelper::parseXMLInstallFile($this->parent->getPath('manifest'));
        $this->parent->extension->manifest_cache = json_encode($manifest_details);

        $this->parent->extension->name = $manifest_details['name'];
        if ($this->parent->extension->store()) {
            return true;
        }
        else
        {
            MolajoError::raiseWarning(101, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_PLG_REFRESH_MANIFEST_CACHE'));
            return false;
        }
    }
}
