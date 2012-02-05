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
 * Language installer
 *
 * @package     Joomla.Platform
 * @subpackage  Installer
 * @since       11.1
 */
class MolajoInstallerAdapterLanguage extends MolajoAdapterInstance
{
    /**
     * Core language pack flag
     *
     * @var    boolean
     * @since  11.1
     */
    protected $_core = false;

    /**
     * Custom install method
     *
     * Note: This behaves badly due to hacks made in the middle of 1.5.x to add
     * the ability to install multiple distinct packs in one install. The
     * preferred method is to use a package to install multiple language packs.
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function install()
    {
        $source = $this->parent->getPath('source');
        if (!$source) {
            $this->parent
                    ->setPath(
                'source',
                ($this->parent->extension->application_id ? MOLAJO_BASE_FOLDER
                        : MOLAJO_BASE_FOLDER) . '/language/' . $this->parent->extension->element
            );
        }
        $this->manifest = $this->parent->getManifest();
        $root = $this->manifest->document;

        // Get the client application target
        if ((string)$this->manifest->attributes()->client == 'both') {
            MolajoError::raiseWarning(42, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_DEPRECATED_FORMAT'));
            $element = $this->manifest->site->files;
            if (!$this->_install('site', MOLAJO_BASE_FOLDER, 0, $element)) {
                return false;
            }

            $element = $this->manifest->administration->files;
            if (!$this->_install('administrator', MOLAJO_BASE_FOLDER, 1, $element)) {
                return false;
            }
            // This causes an issue because we have two eid's, *sigh* nasty hacks!
            return true;
        }
        elseif ($cname = (string)$this->manifest->attributes()->client)
        {
            // Attempt to map the client to a base path
            jimport('joomla.application.helper');
            $client = MolajoApplicationHelper::getApplicationInfo($cname, true);
            if ($client === null) {
                $this->parent->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT', MolajoTextHelper::sprintf('JLIB_INSTALLER_ERROR_UNKNOWN_CLIENT_TYPE', $cname)));
                return false;
            }
            $basePath = $client->path;
            $clientId = $client->id;
            $element = $this->manifest->files;

            return $this->_install($cname, $basePath, $clientId, $element);
        }
        else
        {
            // No client attribute was found so we assume the site as the client
            $cname = 'site';
            $basePath = MOLAJO_BASE_FOLDER;
            $clientId = 0;
            $element = $this->manifest->files;

            return $this->_install($cname, $basePath, $clientId, $element);
        }
    }

    /**
     * Install function that is designed to handle individual clients
     *
     * @param   string   $cname     Cname @todo: not used
     * @param   string   $basePath  The base name.
     * @param   integer  $clientId  The client id.
     * @param   object   &$element  The XML element.
     *
     * @return  boolean
     *
     * @since  11.1
     */
    protected function _install($cname, $basePath, $clientId, &$element)
    {
        $this->manifest = $this->parent->getManifest();

        // Get the language name
        // Set the extensions name
        $name = JFilterInput::getInstance()->clean((string)$this->manifest->name, 'cmd');
        $this->set('name', $name);

        // Get the Language tag [ISO tag, eg. en-GB]
        $tag = (string)$this->manifest->tag;

        // Check if we found the tag - if we didn't, we may be trying to install from an older language package
        if (!$tag) {
            $this->parent->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT', MolajoTextHelper::_('JLIB_INSTALLER_ERROR_NO_LANGUAGE_TAG')));
            return false;
        }

        $this->set('tag', $tag);

        // Set the language installation path
        $this->parent->setPath('extension_site', $basePath . '/language/' . $tag);

        // Do we have a meta file in the file list?  In other words... is this a core language pack?
        if ($element && count($element->children())) {
            $files = $element->children();
            foreach ($files as $file)
            {
                if ((string)$file->attributes()->file == 'meta') {
                    $this->_core = true;
                    break;
                }
            }
        }

        // Either we are installing a core pack or a core pack must exist for the language we are installing.
        if (!$this->_core) {
            if (!JFile::exists($this->parent->getPath('extension_site') . '/' . $this->get('tag') . '.xml')) {
                $this->parent
                        ->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT', MolajoTextHelper::sprintf('JLIB_INSTALLER_ERROR_NO_CORE_LANGUAGE', $this->get('tag'))));
                return false;
            }
        }

        // If the language directory does not exist, let's create it
        $created = false;
        if (!file_exists($this->parent->getPath('extension_site'))) {
            if (!$created = JFolder::create($this->parent->getPath('extension_site'))) {
                $this->parent
                        ->abort(
                    MolajoTextHelper::sprintf(
                        'JLIB_INSTALLER_ABORT',
                        MolajoTextHelper::sprintf('JLIB_INSTALLER_ERROR_CREATE_FOLDER_FAILED', $this->parent->getPath('extension_site'))
                    )
                );
                return false;
            }
        }
        else
        {
            // Look for an update function or update tag
            $updateElement = $this->manifest->update;
            // Upgrade manually set or
            // Update function available or
            // Update tag detected
            if ($this->parent->getUpgrade() || ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'update'))
                || is_a($updateElement, 'SimpleXMLElement')
            ) {
                return $this->update(); // transfer control to the update function
            }
            elseif (!$this->parent->getOverwrite())
            {
                // Overwrite is set
                // We didn't have overwrite set, find an update function or find an update tag so lets call it safe
                if (file_exists($this->parent->getPath('extension_site'))) {
                    // If the site exists say so.
                    MolajoError::raiseWarning(
                        1,
                        MolajoTextHelper::sprintf(
                            'JLIB_INSTALLER_ABORT',
                            MolajoTextHelper::sprintf('JLIB_INSTALLER_ERROR_FOLDER_IN_USE', $this->parent->getPath('extension_site'))
                        )
                    );
                }
                else
                {
                    // If the admin exists say so.
                    MolajoError::raiseWarning(
                        1,
                        MolajoTextHelper::sprintf(
                            'JLIB_INSTALLER_ABORT',
                            MolajoTextHelper::sprintf('JLIB_INSTALLER_ERROR_FOLDER_IN_USE', $this->parent->getPath('extension_administrator'))
                        )
                    );
                }
                return false;
            }
        }

        /*
           * If we created the language directory we will want to remove it if we
           * have to roll back the installation, so let's add it to the installation
           * step stack
           */
        if ($created) {
            $this->parent->pushStep(array('type' => 'folder', 'path' => $this->parent->getPath('extension_site')));
        }

        // Copy all the necessary files
        if ($this->parent->parseFiles($element) === false) {
            // Install failed, rollback changes
            $this->parent->abort();
            return false;
        }

        // Parse optional tags
        $this->parent->parseMedia($this->manifest->media);

        // Copy all the necessary font files to the common pdf_fonts directory
        $this->parent->setPath('extension_site', $basePath . '/language/pdf_fonts');
        $overwrite = $this->parent->setOverwrite(true);
        if ($this->parent->parseFiles($this->manifest->fonts) === false) {
            // Install failed, rollback changes
            $this->parent->abort();
            return false;
        }
        $this->parent->setOverwrite($overwrite);

        // Get the language description
        $description = (string)$this->manifest->description;
        if ($description) {
            $this->parent->set('message', MolajoTextHelper::_($description));
        }
        else
        {
            $this->parent->set('message', '');
        }

        // Add an entry to the extension table with a whole heap of defaults
        $row = MolajoModel::getInstance('extension');
        $row->set('name', $this->get('name'));
        $row->set('type', 'language');
        $row->set('element', $this->get('tag'));
        // There is no folder for languages
        $row->set('folder', '');
        $row->set('enabled', 1);
        $row->set('protected', 0);
        $row->set('access', 0);
        $row->set('application_id', $clientId);
        $row->set('parameters', $this->parent->getParameters());
        $row->set('manifest_cache', $this->parent->generateManifestCache());

        if (!$row->store()) {
            // Install failed, roll back changes
            $this->parent->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT', $row->getError()));
            return false;
        }

        // Clobber any possible pending updates
        $update = MolajoModel::getInstance('update');
        $uid = $update->find(array('element' => $this->get('tag'), 'type' => 'language', 'application_id' => '', 'folder' => ''));
        if ($uid) {
            $update->delete($uid);
        }

        return $row->get('extension_id');
    }

    /**
     * Custom update method
     *
     * @return  boolean  True on success, false on failure
     *
     * @since   1.0
     */
    public function update()
    {
        $xml = $this->parent->getManifest();

        $this->manifest = $xml;

        $cname = $xml->attributes()->client;

        // Attempt to map the client to a base path
        jimport('joomla.application.helper');
        $client = MolajoApplicationHelper::getApplicationInfo($cname, true);
        if ($client === null || (empty($cname) && $cname !== 0)) {
            $this->parent->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT', MolajoTextHelper::sprintf('JLIB_INSTALLER_ERROR_UNKNOWN_CLIENT_TYPE', $cname)));
            return false;
        }
        $basePath = $client->path;
        $clientId = $client->id;

        // Get the language name
        // Set the extensions name
        $name = (string)$this->manifest->name;
        $name = JFilterInput::getInstance()->clean($name, 'cmd');
        $this->set('name', $name);

        // Get the Language tag [ISO tag, eg. en-GB]
        $tag = (string)$xml->tag;

        // Check if we found the tag - if we didn't, we may be trying to install from an older language package
        if (!$tag) {
            $this->parent->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT', MolajoTextHelper::_('JLIB_INSTALLER_ERROR_NO_LANGUAGE_TAG')));
            return false;
        }

        $this->set('tag', $tag);
        $folder = $tag;

        // Set the language installation path
        $this->parent->setPath('extension_site', $basePath . '/language/' . $this->get('tag'));

        // Do we have a meta file in the file list?  In other words... is this a core language pack?
        if (count($xml->files->children())) {
            foreach ($xml->files->children() as $file)
            {
                if ((string)$file->attributes()->file == 'meta') {
                    $this->_core = true;
                    break;
                }
            }
        }

        // Either we are installing a core pack or a core pack must exist for the language we are installing.
        if (!$this->_core) {
            if (!JFile::exists($this->parent->getPath('extension_site') . '/' . $this->get('tag') . '.xml')) {
                $this->parent
                        ->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT', MolajoTextHelper::sprintf('JLIB_INSTALLER_ERROR_NO_CORE_LANGUAGE', $this->get('tag'))));
                return false;
            }
        }

        // Copy all the necessary files
        if ($this->parent->parseFiles($xml->files) === false) {
            // Install failed, rollback changes
            $this->parent->abort();
            return false;
        }

        // Parse optional tags
        $this->parent->parseMedia($xml->media);

        // Copy all the necessary font files to the common pdf_fonts directory
        $this->parent->setPath('extension_site', $basePath . '/language/pdf_fonts');
        $overwrite = $this->parent->setOverwrite(true);
        if ($this->parent->parseFiles($xml->fonts) === false) {
            // Install failed, rollback changes
            $this->parent->abort();
            return false;
        }
        $this->parent->setOverwrite($overwrite);

        // Get the language description and set it as message
        $this->parent->set('message', (string)$xml->description);

        // Finalization and Cleanup Section

        // Clobber any possible pending updates
        $update = MolajoModel::getInstance('update');
        $uid = $update->find(array('element' => $this->get('tag'), 'type' => 'language', 'application_id' => $clientId));
        if ($uid) {
            $update->delete($uid);
        }

        // Update an entry to the extension table
        $row = MolajoModel::getInstance('extension');
        $eid = $row->find(array('element' => strtolower($this->get('tag')), 'type' => 'language', 'application_id' => $clientId));
        if ($eid) {
            $row->load($eid);
        }
        else
        {
            // set the defaults
            $row->set('folder', ''); // There is no folder for language
            $row->set('enabled', 1);
            $row->set('protected', 0);
            $row->set('access', 0);
            $row->set('application_id', $clientId);
            $row->set('parameters', $this->parent->getParameters());
        }
        $row->set('name', $this->get('name'));
        $row->set('type', 'language');
        $row->set('element', $this->get('tag'));
        $row->set('manifest_cache', $this->parent->generateManifestCache());

        if (!$row->store()) {
            // Install failed, roll back changes
            $this->parent->abort(MolajoTextHelper::sprintf('JLIB_INSTALLER_ABORT', $row->getError()));
            return false;
        }

        // And now we run the postflight
        ob_start();
        ob_implicit_flush(false);
        if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'postflight')) {
            $this->parent->manifestClass->postflight('update', $this);
        }
        $msg .= ob_get_contents(); // append messages
        ob_end_clean();
        if ($msg != '') {
            $this->parent->set('extension_message', $msg);
        }

        return $row->get('extension_id');
    }

    /**
     * Custom uninstall method
     *
     * @param   string  $eid  The tag of the language to uninstall
     *
     * @return  mixed  Return value for uninstall method in component uninstall file
     *
     * @since   1.0
     */
    public function uninstall($eid)
    {
        // Load up the extension details
        $extension = MolajoModel::getInstance('extension');
        $extension->load($eid);
        // Grab a copy of the client details
        $client = MolajoApplicationHelper::getApplicationInfo($extension->get('application_id'));

        // Check the element isn't blank to prevent nuking the languages directory...just in case
        $element = $extension->get('element');
        if (empty($element)) {
            MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_LANG_UNINSTALL_ELEMENT_EMPTY'));
            return false;
        }

        // Check that the language is not protected, Normally en-GB.
        $protected = $extension->get('protected');
        if ($protected == 1) {
            MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_LANG_UNINSTALL_PROTECTED'));
            return false;
        }

        // Verify that it's not the default language for that client
        $parameters = MolajoComponent::getParameters('languages');
        if ($parameters->get($client->name) == $element) {
            MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_LANG_UNINSTALL_DEFAULT'));
            return false;
        }

        // Construct the path from the client, the language and the extension element name
        $path = $client->path . '/language/' . $element;

        // Get the package manifest object and remove media
        $this->parent->setPath('source', $path);
        // We do findManifest to avoid problem when uninstalling a list of extension: getManifest cache its manifest file
        $this->parent->findManifest();
        $this->manifest = $this->parent->getManifest();
        $this->parent->removeFiles($this->manifest->media);

        // Check it exists
        if (!JFolder::exists($path)) {
            // If the folder doesn't exist lets just nuke the row as well and presume the user killed it for us
            $extension->delete();
            MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_LANG_UNINSTALL_PATH_EMPTY'));
            return false;
        }

        if (!JFolder::delete($path)) {
            // If deleting failed we'll leave the extension entry in tact just in case
            MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_LANG_UNINSTALL_DIRECTORY'));
            return false;
        }

        // Remove the extension table entry
        $extension->delete();

        // Setting the language of users which have this language as the default language
        $db = Molajo::DB();
        $query = $db->getQuery(true);
        $query->from('#__users');
        $query->select('*');
        $db->setQuery($query->__toString());
        $users = $db->loadObjectList();
        if ($client->name == 'administrator') {
            $param_name = 'admin_language';
        }
        else
        {
            $param_name = 'language';
        }

        $count = 0;
        foreach ($users as $user)
        {
            $registry = new JRegistry;
            $registry->loadString($user->parameters);
            if ($registry->get($param_name) == $element) {
                $registry->set($param_name, '');
                $query = $db->getQuery(true);
                $query->update('#__users');
                $query->set('parameters=' . $db->quote($registry));
                $query->where('id=' . (int)$user->id);
                $db->setQuery($query->__toString());
                $db->query();
                $count = $count + 1;
            }
        }
        if (!empty($count)) {
            MolajoError::raiseNotice(500, MolajoTextHelper::plural('JLIB_INSTALLER_NOTICE_LANG_RESET_USERS', $count));
        }

        // All done!
        return true;
    }

    /**
     * Custom discover method
     * Finds language files
     *
     * @return  void
     *
     * @since  11.1
     */
    public function discover()
    {
        $results = array();
        $site_languages = JFolder::folders(MOLAJO_BASE_FOLDER . '/language');
        $admin_languages = JFolder::folders(MOLAJO_BASE_FOLDER . '/language');
        foreach ($site_languages as $language)
        {
            if (file_exists(MOLAJO_BASE_FOLDER . '/language/' . $language . '/' . $language . '.xml')) {
                $manifest_details = MolajoInstallHelper::parseManifestXML(MOLAJO_BASE_FOLDER . '/language/' . $language . '/' . $language . '.xml');
                $extension = MolajoModel::getInstance('extension');
                $extension->set('type', 'language');
                $extension->set('application_id', 0);
                $extension->set('element', $language);
                $extension->set('name', $language);
                $extension->set('state', -1);
                $extension->set('manifest_cache', json_encode($manifest_details));
                $results[] = $extension;
            }
        }
        foreach ($admin_languages as $language)
        {
            if (file_exists(MOLAJO_BASE_FOLDER . '/language/' . $language . '/' . $language . '.xml')) {
                $manifest_details = MolajoInstallHelper::parseManifestXML(MOLAJO_BASE_FOLDER . '/language/' . $language . '/' . $language . '.xml');
                $extension = MolajoModel::getInstance('extension');
                $extension->set('type', 'language');
                $extension->set('application_id', 1);
                $extension->set('element', $language);
                $extension->set('name', $language);
                $extension->set('state', -1);
                $extension->set('manifest_cache', json_encode($manifest_details));
                $results[] = $extension;
            }
        }
        return $results;
    }

    /**
     * Custom discover install method
     * Basically updates the manifest cache and leaves everything alone
     *
     * @return  integer  The extrension id
     *
     * @since   1.0
     */
    public function discover_install()
    {
        // Need to find to find where the XML file is since we don't store this normally
        $client = MolajoApplicationHelper::getApplicationInfo($this->parent->extension->application_id);
        $short_element = $this->parent->extension->element;
        $manifestPath = $client->path . '/language/' . $short_element . '/' . $short_element . '.xml';
        $this->parent->manifest = $this->parent->isManifest($manifestPath);
        $this->parent->setPath('manifest', $manifestPath);
        $this->parent->setPath('source', $client->path . '/language/' . $short_element);
        $this->parent->setPath('extension_root', $this->parent->getPath('source'));
        $manifest_details = MolajoInstallHelper::parseManifestXML($this->parent->getPath('manifest'));
        $this->parent->extension->manifest_cache = json_encode($manifest_details);
        $this->parent->extension->state = 0;
        $this->parent->extension->name = $manifest_details['name'];
        $this->parent->extension->enabled = 1;
        //$this->parent->extension->parameters = $this->parent->getParameters();
        try
        {
            $this->parent->extension->store();
        }
        catch (Exception $e)
        {
            MolajoError::raiseWarning(101, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_LANG_DISCOVER_STORE_DETAILS'));
            return false;
        }
        return $this->parent->extension->get('extension_id');
    }

    /**
     * Refreshes the extension table cache
     *
     * @return  boolean result of operation, true if updated, false on failure
     *
     * @since   1.0
     */
    public function refreshManifestCache()
    {
        $client = MolajoApplicationHelper::getApplicationInfo($this->parent->extension->application_id);
        $manifestPath = $client->path . '/language/' . $this->parent->extension->element . '/' . $this->parent->extension->element . '.xml';
        $this->parent->manifest = $this->parent->isManifest($manifestPath);
        $this->parent->setPath('manifest', $manifestPath);
        $manifest_details = MolajoInstallHelper::parseManifestXML($this->parent->getPath('manifest'));
        $this->parent->extension->manifest_cache = json_encode($manifest_details);
        $this->parent->extension->name = $manifest_details['name'];

        if ($this->parent->extension->store()) {
            return true;
        }
        else
        {
            MolajoError::raiseWarning(101, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_REFRESH_MANIFEST_CACHE'));

            return false;
        }
    }
}
