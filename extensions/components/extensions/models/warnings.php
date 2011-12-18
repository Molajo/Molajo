<?php
/**
 * @version        $Id: warnings.php 21320 2011-05-11 01:01:37Z dextercowley $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.application.component.modellist');
jimport('joomla.filesystem.folder');

/**
 * Extension Manager Templates Model
 *
 * @package        Joomla.Administrator
 * @subpackage    installer
 * * * @since        1.0
 */
class InstallerModelWarnings extends JModelList
{
    /**
     * Extension Type
     * @var    string
     */
    var $type = 'warnings';

    /**
     * Return the byte value of a particular string.
     *
     * @param    string    String optionally with G, M or K suffix
     * @return    int        size in bytes
     * @since 1.6
     */
    function return_bytes($val)
    {
        $val = trim($val);
        $last = strtolower($val{strlen($val) - 1});
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * Load the data.
     *
     * @since    1.0
     */
    function getItems()
    {
        static $messages;
        if ($messages) {
            return $messages;
        }
        $messages = Array();
        $file_uploads = ini_get('file_uploads');
        if (!$file_uploads) {
            $messages[] = Array('message' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_FILEUPLOADSDISABLED'), 'description' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_FILEUPLOADISDISABLEDDESC'));
        }


        $upload_dir = ini_get('upload_tmp_dir');
        if (!$upload_dir) {
            $messages[] = Array('message' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_PHPUPLOADNOTSET'), 'description' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_PHPUPLOADNOTSETDESC'));
        } else {
            if (!is_writeable($upload_dir)) {
                $messages[] = Array('message' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_PHPUPLOADNOTWRITEABLE'), 'description' => MolajoTextHelper::sprintf('INSTALLER_MSG_WARNINGS_PHPUPLOADNOTWRITEABLEDESC', $upload_dir));
            }
        }

        $config = MolajoFactory::getApplication()->getConfig();
        $temp_path = $config->get('temp_path');
        if (!$temp_path) {
            $messages[] = Array('message' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_JOOMLATMPNOTSET'), 'description' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_JOOMLATMPNOTSETDESC'));
        } else {
            if (!is_writeable($temp_path)) {
                $messages[] = Array('message' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_JOOMLATMPNOTWRITEABLE'), 'description' => MolajoTextHelper::sprintf('INSTALLER_MSG_WARNINGS_JOOMLATMPNOTWRITEABLEDESC', $temp_path));
            }
        }

        $memory_limit = $this->return_bytes(ini_get('memory_limit'));
        if ($memory_limit < (8 * 1024 * 1024)) { // 8MB
            $messages[] = Array('message' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_LOWMEMORYWARN'), 'description' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_LOWMEMORYDESC'));
        } else if ($memory_limit < (16 * 1024 * 1024)) { //16MB
            $messages[] = Array('message' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_MEDMEMORYWARN'), 'description' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_MEDMEMORYDESC'));
        }


        $post_max_size = $this->return_bytes(ini_get('post_max_size'));
        $upload_max_filesize = $this->return_bytes(ini_get('upload_max_filesize'));

        if ($post_max_size < $upload_max_filesize) {
            $messages[] = Array('message' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_UPLOADBIGGERTHANPOST'), 'description' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_UPLOADBIGGERTHANPOSTDESC'));
        }

        if ($post_max_size < (4 * 1024 * 1024)) // 4MB
        {
            $messages[] = Array('message' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_SMALLPOSTSIZE'), 'description' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_SMALLPOSTSIZEDESC'));
        }

        if ($upload_max_filesize < (4 * 1024 * 1024)) // 4MB
        {
            $messages[] = Array('message' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_SMALLUPLOADSIZE'), 'description' => MolajoTextHelper::_('INSTALLER_MSG_WARNINGS_SMALLUPLOADSIZEDESC'));
        }


        return $messages;
    }
}
