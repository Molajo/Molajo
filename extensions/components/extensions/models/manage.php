<?php
/**
 * @version        $Id: manage.php 21650 2011-06-23 05:29:17Z chdemko $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Import library dependencies
require_once dirname(__FILE__) . '/extension.php';

/**
 * Installer Manage Model
 *
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @since        1.5
 */
class InstallerModelManage extends InstallerModel
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
                'enabled',
                'type',
                'folder',
                'extension_id',
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
        // Initialise variables.

        $filters = JRequest::getVar('filters');
        if (empty($filters)) {
            $data = MolajoFactory::getUser()->getUserState($this->context . '.data');
            $filters = $data['filters'];
        }
        else {
            MolajoFactory::getUser()->setUserState($this->context . '.data', array('filters' => $filters));
        }

        $this->setState('message', MolajoFactory::getUser()->getUserState('installer.message'));
        $this->setState('extension_message', MolajoFactory::getUser()->getUserState('installer.extension_message'));
        MolajoFactory::getUser()->setUserState('installer.message', '');
        MolajoFactory::getUser()->setUserState('installer.extension_message', '');

        $this->setState('filter.search', isset($filters['search']) ? $filters['search'] : '');
        $this->setState('filter.hideprotected', isset($filters['hideprotected']) ? $filters['hideprotected'] : 0);
        $this->setState('filter.enabled', isset($filters['enabled']) ? $filters['enabled'] : '');
        $this->setState('filter.type', isset($filters['type']) ? $filters['type'] : '');
        $this->setState('filter.group', isset($filters['group']) ? $filters['group'] : '');
        $this->setState('filter.application_id', isset($filters['application_id']) ? $filters['application_id'] : '');
        parent::populateState('name', 'asc');
    }

    /**
     * Enable/Disable an extension.
     *
     * @return    boolean True on success
     * @since    1.0
     */
    function publish(&$eid = array(), $value = 1)
    {
        // Initialise variables.
        $user = MolajoFactory::getUser();
        if ($user->authorise('core.edit.state', 'installer')) {
            $result = true;

            /*
               * Ensure eid is an array of extension ids
               * TODO: If it isn't an array do we want to set an error and fail?
               */
            if (!is_array($eid)) {
                $eid = array($eid);
            }

            // Get a database connector
            $db = MolajoFactory::getDbo();

            // Get a table object for the extension type
            $table = JTable::getInstance('Extension');
            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/templates/tables');
            // Enable the extension in the table and store it in the database
            foreach ($eid as $i => $id) {
                $table->load($id);
                if ($table->type == 'template') {
                    $style = JTable::getInstance('Style', 'TemplatesTable');
                    if ($style->load(array('template' => $table->element, 'application_id' => $table->application_id, 'home' => 1))) {
                        MolajoError::raiseNotice(403, MolajoTextHelper::_('INSTALLER_ERROR_DISABLE_DEFAULT_TEMPLATE_NOT_PERMITTED'));
                        unset($eid[$i]);
                        continue;
                    }
                }
                $table->enabled = $value;
                if (!$table->store()) {
                    $this->setError($table->getError());
                    $result = false;
                }
            }
        } else {
            $result = false;
            MolajoError::raiseWarning(403, MolajoTextHelper::_('MOLAJO_APPLICATION_ERROR_EDITSTATUS_NOT_PERMITTED'));
        }
        return $result;
    }

    /**
     * Refreshes the cached manifest information for an extension.
     *
     * @param    int        extension identifier (key in #__extensions)
     * @return    boolean    result of refresh
     * @since    1.0
     */
    function refresh($eid)
    {
        if (!is_array($eid)) {
            $eid = array($eid => 0);
        }

        // Get a database connector
        $db = MolajoFactory::getDbo();

        // Get an installer object for the extension type
        jimport('joomla.installer.installer');
        $installer = JInstaller::getInstance();
        $row = JTable::getInstance('extension');
        $result = 0;

        // Uninstall the chosen extensions
        foreach ($eid as $id) {
            $result |= $installer->refreshManifestCache($id);
        }
        return $result;
    }

    /**
     * Remove (uninstall) an extension
     *
     * @param    array    An array of identifiers
     * @return    boolean    True on success
     * @since    1.0
     */
    function remove($eid = array())
    {
        // Initialise variables.
        $user = MolajoFactory::getUser();
        if ($user->authorise('core.delete', 'installer')) {

            // Initialise variables.
            $failed = array();

            /*
               * Ensure eid is an array of extension ids in the form id => application_id
               * TODO: If it isn't an array do we want to set an error and fail?
               */
            if (!is_array($eid)) {
                $eid = array($eid => 0);
            }

            // Get a database connector
            $db = MolajoFactory::getDbo();

            // Get an installer object for the extension type
            jimport('joomla.installer.installer');
            $installer = JInstaller::getInstance();
            $row = JTable::getInstance('extension');

            // Uninstall the chosen extensions
            foreach ($eid as $id) {
                $id = trim($id);
                $row->load($id);
                if ($row->type) {
                    $result = $installer->uninstall($row->type, $id);

                    // Build an array of extensions that failed to uninstall
                    if ($result === false) {
                        $failed[] = $id;
                    }
                }
                else {
                    $failed[] = $id;
                }
            }

            $langstring = 'INSTALLER_TYPE_TYPE_' . strtoupper($row->type);
            $rowtype = MolajoTextHelper::_($langstring);
            if (strpos($rowtype, $langstring) !== false) {
                $rowtype = $row->type;
            }

            if (count($failed)) {

                // There was an error in uninstalling the package
                $msg = MolajoTextHelper::sprintf('INSTALLER_UNINSTALL_ERROR', $rowtype);
                $result = false;
            } else {

                // Package uninstalled sucessfully
                $msg = MolajoTextHelper::sprintf('INSTALLER_UNINSTALL_SUCCESS', $rowtype);
                $result = true;
            }

            MolajoFactory::getApplication()->setMessage($msg);
            $this->setState('action', 'remove');
            $this->setState('name', $installer->get('name'));
            MolajoFactory::getUser()->setUserState('installer.message', $installer->message);
            MolajoFactory::getUser()->setUserState('installer.extension_message', $installer->get('extension_message'));
            return $result;
        } else {
            $result = false;
            MolajoError::raiseWarning(403, MolajoTextHelper::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
        }
    }

    /**
     * Method to get the database query
     *
     * @return    JDatabaseQuery    The database query
     * @since    1.0
     */
    protected function getListQuery()
    {
        $enabled = $this->getState('filter.enabled');
        $type = $this->getState('filter.type');
        $application = $this->getState('filter.application_id');
        $group = $this->getState('filter.group');
        $hideprotected = $this->getState('filter.hideprotected');
        $query = MolajoFactory::getDbo()->getQuery(true);
        $query->select('*');
        $query->from('#__extensions');
        $query->where('state=0');
        if ($hideprotected) {
            $query->where('protected!=1');
        }
        if ($enabled != '') {
            $query->where('enabled=' . intval($enabled));
        }
        if ($type) {
            $query->where('type=' . $this->db->Quote($type));
        }
        if ($application != '') {
            $query->where('application_id=' . intval($application));
        }
        if ($group != '' && in_array($type, array('plugin', 'library', ''))) {

            $query->where('folder=' . $this->db->Quote($group == '*' ? '' : $group));
        }

        // Filter by search in id
        $search = $this->getState('filter.search');
        if (!empty($search) && stripos($search, 'id:') === 0) {
            $query->where('extension_id = ' . (int)substr($search, 3));
        }

        return $query;
    }

    /**
     * Method to get the row form.
     *
     * @param    array    $data        Data for the form.
     * @param    boolean    $loadData    True if the form is to load its own data (default case), false if not.
     * @return    mixed    A JForm object on success, false on failure
     * @since    1.0
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        jimport('joomla.form.form');

        JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
        JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
        $form = JForm::getInstance('installer.manage', 'manage', array('load_data' => $loadData));

        // Check for an error.
        if ($form == false) {
            $this->setError($form->getMessage());
            return false;
        }
        // Check the session for previously entered form data.
        $data = $this->loadFormData();

        // Bind the form data if present.
        if (!empty($data)) {
            $form->bind($data);
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     * @since    1.0
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = MolajoFactory::getUser()->getUserState('installer.manage.data', array());

        return $data;
    }
}
