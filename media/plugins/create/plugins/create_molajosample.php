<?php
/**
 * @version     $id: com_installer
 * @package     Molajo
 * @subpackage  Create
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
require_once JPATH_COMPONENT.'/models/discover.php';

/**
 * Extension Manager Create Model
 *
 * @package	Molajo
 * @subpackage	com_installer
 * @since	1.6
 */
class InstallerModelCreateMolajosamples extends InstallerModelCreate
{

   /**
     * $this->_replacesingle
     *
     * @var string
     */
    protected $_replacename = 'molajosample';

    /**
     * $_pluginType
     *
     * @var string
     */
    protected $_pluginType = null;

    /**
     * $_pluginName
     *
     * @var string
     */
    protected $_pluginName = null;

    /**
     * create
     *
     * Creates and Install a Molajo Plugin as per user instructions
     *
     * @return	boolean result of install
     * @since	1.5
     */
    function create()
    {
        /** plugin type **/
        $this->_pluginType = 'content';

        /** language files **/
        JFactory::getLanguage()->load('plg_system_create', JPATH_SITE.'/media/plg_system_create/plugins/'.$this->_pluginType, JFactory::getLanguage()->getDefault(), true, true);

        /** edit **/
        $results = $this->_edit ();
        if ($results == false) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_PLUGIN_FAILED'), 'error');
            return false;
        }

        /** copy **/
        $results = $this->_copy ();
        if ($results === false) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_PLUGIN_FAILED'), 'error');
            return false;
        }

        return 'plg_'.$this->_pluginName;
    }

    /**
     * _edit
     *
     * Ensure data requested was provided
     *
     * @return boolean
     */
    protected function _edit ()
    {
        /** plugin_type **/
        $this->_pluginType = JRequest::getWord('plugin_type', 'content');
        $this->_pluginType = JFile::makeSafe($this->_pluginType);
        $this->_pluginType = JFilterOutput::stringURLSafe($this->_pluginType);
echo $this->_pluginType;
        die();
        if ($this->_pluginType == '' || $this->pluginType = null) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_INVALID').': '. $this->_pluginType, 'error');
            return false;
        }

        /** Plugin name **/
        $this->_pluginName = JRequest::getWord('plugin_name', 'item');
        $this->_pluginName = JFile::makeSafe($this->_pluginName);
        $this->_pluginName = JFilterOutput::stringURLSafe($this->_pluginName);

        if ($this->_pluginName == '' || $this->_pluginName == 'molajosample') {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_PLUGIN_NAME_INVALID').': '. $this->_pluginName, 'error');
            return false;
        }
echo $this->_pluginType.$this->_pluginName;
        die();
        /** does the destination exist? **/
        if (JFolder::exists(JPATH_ADMINISTRATOR.'/plugins/'.$this->_pluginType.'/'.$this->_pluginName)) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_PLUGIN_DESTINATION_FOLDER_ALREADY_EXISTS').' '.$this->_pluginName, 'error');
            return false;
        }
        
        /** is it already installed? **/
        $db = $this->getDbo();
        $query = 'SELECT extension_id FROM #__extensions where state = -1  AND folder = "'.$this->_pluginType.'"'.' AND element = "'.'plg_'.$this->_pluginName.'"';
        $db->setQuery($query);

        $discoveredExtensionID = $db->loadResult();
        if (count ($discoveredExtensionID) > 0) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_EXTENSION_ALREADY_INSTALLED').': '. $discoveredExtensionID, 'error');
            return false;
        }

        /** is the plugin name already installed? **/
        return true;
    }

    /**
     * _copy
     *
     * Copy files from source to destination
     * Rename files and folders, as needed
     * Change singular and plural words to new values
     *
     * @return boolean
     */
    function _copy()
    {
        /** set ftp credentials, if used **/
        JClientHelper::setCredentialsFromRequest('ftp');

        $source = JPATH_SITE.'/media/plg_system_create/plugins/'.$this->_pluginType.'/'.$this->_replacename;
        $destination = JPATH_PLUGINS.'/'.$this->_pluginType.'/'.$this->_pluginName;
        $results = $this->_copySource ($source, $destination);
        if ($results === false) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_COPY_FOLDER_FAILED').$source, 'error');
            return false;
        }
        return true;
    }
}