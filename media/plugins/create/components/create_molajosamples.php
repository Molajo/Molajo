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
    protected $_replacesingle = 'molajosample';

    /**
     * $this->_replaceplural
     *
     * @var string
     */
    protected $_replaceplural = 'molajosamples';

    /**
     * $_single
     *
     * @var string
     */
    protected $_single = null;

    /**
     * $_plural
     *
     * @var string
     */
    protected $_plural = null;

    /**
     * create
     *
     * Creates and then Installs a Molajo Extension as per user instructions
     *
     * @return	boolean result of install
     * @since	1.5
     */
    function create()
    {
        /** language files **/
        JFactory::getLanguage()->load('plg_system_create_molajosamples', JPATH_SITE.'/media/plg_system_create/components/', JFactory::getLanguage()->getDefault(), true, true);

        /** edit **/
        $results = $this->_edit ();
        if ($results == false) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_COMPONENT_FAILED'), 'error');
            return false;
        }

        /** copy **/
        $results = $this->_copy ();
        if ($results === false) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_COMPONENT_FAILED'), 'error');
            return false;
        }

        return 'com_'.$this->_plural;
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
        /** single - must have valid file name **/
        $this->_single = JRequest::getWord('component_singular_item_name', 'item');
        $this->_single = JFile::makeSafe($this->_single);
        $this->_single = JFilterOutput::stringURLSafe($this->_single);
        if ($this->_single == '' || $this->_single == 'item') {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_SINGULAR_ITEM_VALUE_INVALID').': '. $this->_single, 'error');
            return false;
        }

        /** plural - must have valid file name **/
        $this->_plural = JRequest::getWord('component_plural_item_name', 'items');
        $this->_plural = JFile::makeSafe($this->_plural);
        $this->_plural = JFilterOutput::stringURLSafe($this->_plural);
        if ($this->_plural == '' || $this->_plural == 'items') {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_PLURAL_ITEM_VALUE_INVALID').': '. $this->_plural, 'error');
            return false;
        }

        /** single and plural must not match **/
        if ($this->_plural == $this->_single) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_SINGULAR_AND_PLURAL_CANNOT_MATCH').': '. $this->_plural, 'error');
            return false;
        }

        /** does the destination exist? **/
        if (JFolder::exists(JPATH_ADMINISTRATOR.'/components/'.'com_'.$this->_plural)) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_EXTENSION_ADMIN_DESTINATION_FOLDER_ALREADY_EXISTS').' '.$destination, 'error');
            return false;
        }
        if (JFolder::exists(JPATH_SITE.'/components/'.'com_'.$this->_plural)) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_EXTENSION_SITE_DESTINATION_FOLDER_ALREADY_EXISTS').' '.$destination, 'error');
            return false;
        }
        if (JFolder::exists(JPATH_SITE.'/media/'.'com_'.$this->_plural)) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_EXTENSION_MEDIA_DESTINATION_FOLDER_ALREADY_EXISTS').' '.$destination, 'error');
            return false;
        }
        
        /** is it already installed? **/
        $db = $this->getDbo();
        $query = 'SELECT extension_id FROM #__extensions where state = -1  AND element = "'.'com_'.$this->_plural.'"';
        $db->setQuery($query);

        $discoveredExtensionID = $db->loadResult();
        if (count ($discoveredExtensionID) > 0) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_EXTENSION_ALREADY_INSTALLED').': '. $discoveredExtensionID, 'error');
            return false;
        }

        /** is the component name already installed? **/
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

        /**                 **/
        /** ADMINISTRATOR   **/
        /**                 **/
        $source = JPATH_SITE.'/media/plg_system_create/components/'.$this->_replaceplural.'/admin/com_'.$this->_replaceplural;
        $destination = JPATH_ADMINISTRATOR.'/components/'.'com_'.$this->_plural;
        $results = $this->_copySource ($source, $destination);
        if ($results === false) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_COPY_FOLDER_FAILED').$source, 'error');
            return false;
        }

        /**                 **/
        /** MEDIA           **/
        /**                 **/
        $source = JPATH_SITE.'/media/plg_system_create/components/'.$this->_replaceplural.'/media/com_'.$this->_replaceplural;
        $destination = JPATH_SITE.'/media/'.'com_'.$this->_plural;
        $results = $this->_copySource ($source, $destination);
        if ($results === false) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_COPY_FOLDER_FAILED').$source, 'error');
            return false;
        }

        /**                 **/
        /** FRONTEND        **/
        /**                 **/
        $source = JPATH_SITE.'/media/plg_system_create/components/'.$this->_replaceplural.'/site/com_'.$this->_replaceplural;
        $destination = JPATH_SITE.'/components/'.'com_'.$this->_plural;
        $results = $this->_copySource ($source, $destination);
        if ($results === false) {
            $this->_app->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_COPY_FOLDER_FAILED').$source, 'error');
            return false;
        }

        return true;
    }
}