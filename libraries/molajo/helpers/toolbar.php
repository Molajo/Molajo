<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Toolbar Helper
 *
 * @package     Molajo
 * @subpackage  Toolbar Helper
 * @since       1.0
 */
class MolajoToolbarHelper
{
    /**
     * addButtonsDefaultLayout
     *
     * @param	string	The name of the active view.
     * @since	1.0
     */
    public function addButtonsDefaultLayout ($state, $userToolbarButtonPermissions)
    {
        /** ToolBar title **/
        $params = MolajoComponentHelper::getParams(JRequest::getCmd('option'));
        $this->addTitle ($params->def('config_manager_title_image', 1), $params->def('config_manager_title', 1), JRequest::getCmd('default_view'), JRequest::getCmd('option'), JRequest::getCmd('view'));

        /** Process Buttons **/
        $buttonParameterFieldName = 'config_manager_button_bar_option';

        $this->buttonLoop ($buttonParameterFieldName, $state, $userToolbarButtonPermissions, 0, array());

        return;
    }

    /**
     * addButtonsEditLayout
     *
     * @param	string	The name of the active view.
     * @since	1.0
     */
    public function addButtonsEditLayout ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        /** do not allow during edit session **/
        JRequest::setVar('hidemainmenu', true);

        /** ToolBar title **/
        $params = MolajoComponentHelper::getParams(JRequest::getCmd('option'));
        $this->addTitle ($params->def('config_manager_title_image', 1), $params->def('config_manager_title', 1), JRequest::getCmd('default_view'), JRequest::getCmd('option'), JRequest::getCmd('view'));

        if ((int) $id == 0) {
            $buttonParameterFieldName = 'config_manager_editor_button_bar_new_option';
        } else {
            $buttonParameterFieldName = 'config_manager_editor_button_bar_edit_option';
        }

        /** Process Buttons **/
        $this->buttonLoop ($buttonParameterFieldName, $state, $userToolbarButtonPermissions, $id, $item);
    }

    /**
     * addTitle
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addTitle ($imageParameter, $titleParameter)
    {
        if ($imageParameter == '1') {
            $titleImage = JRequest::getCmd('default_view').'.png';
        } else {
            $titleImage = '';
        }

        if ($titleParameter == '1') {
            JToolBarHelper::title(JText::_('MOLAJO_MANAGER_'.strtoupper(JRequest::getCmd('view'))), $titleImage);
        } else {
            if ($titleImage == '') {
            } else {
                JToolBarHelper::title('',$titleImage);
            }
        }
    }
    
    /**
     * buttonLoop
     * @param string $buttonParameterFieldName - Parameter name in configuraiton file for Toolbar Button Section
     * @return int 
     */
    public function buttonLoop ($buttonParameterFieldName, $state, $userToolbarButtonPermissions, $id, $item)
    {
        /** component parameters **/
        $params = MolajoComponentHelper::getParams(JRequest::getCmd('option'));

        /** loop thru config options and add ToolBar buttons **/
        $count = 0;

        /** filters **/
        $loadedButtonArray = array();

        for ($i=1; $i < 99; $i++) {
            $buttonValue = $params->def($buttonParameterFieldName.$i, null);

            if ($buttonValue == null) {
               break;
            }
            if ($buttonValue == '0') {

            } else if (in_array($buttonValue, $loadedButtonArray)) {

            } else {
                if ($buttonValue == 'separator') {
                } else {
                    $loadedButtonArray[] = $buttonValue;
                }

                if ($userToolbarButtonPermissions[$buttonValue] === true) {
                    $functionName = 'add'.ucfirst($buttonValue).'Button';
                    if (method_exists('MolajoToolbarHelper',$functionName)) {
                        $count++;
                        $this->$functionName ($state, $userToolbarButtonPermissions, $id, $item);
                    }
                }
            }
        }

        /** after install, there will be no buttons - make certain Options are there **/
        if ($count == 0) {
            $functionName = 'addOptionsButton';
//amy?            if ($userToolbarButtonPermissions['options'] === true) {
                if (method_exists('MolajoToolbarHelper',$functionName)) {
                    $this->$functionName ($state, $userToolbarButtonPermissions, $id, $item);
                }
//            }
        }
    }

    /**
     * LIST LAYOUT BUTTONS
     */

    /**
     * addArchiveButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addArchiveButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::archiveList(JRequest::getCmd('default_view').'.archive','JToolBar_ARCHIVE');
    }

    /**
     * addCheckinButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addCheckinButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::custom(JRequest::getCmd('default_view').'.checkin', 'checkin.png', 'checkin_f2.png', 'JToolBar_CHECKIN', true);
    }

    /**
     * addDeleteButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addDeleteButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        if ($state == -2) {
            JToolBarHelper::deleteList('', JRequest::getCmd('default_view').'.delete','JToolBar_EMPTY_TRASH');
        }
    }

    /**
     * addEditButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addEditButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::editList(JRequest::getCmd('single_view').'.edit','JTOOLBAR_EDIT');
    }

    /**
     * addFeatureButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addFeatureButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::custom(JRequest::getCmd('default_view').'.feature', 'featured.png', 'featured_f2.png', 'JToolBar_FEATURED', true);
    }

    /**
     * addOptionsButton
     *
     * @param	string	The name of the active view.
     * @since	1.0
     */
    public function addOptionsButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::preferences(JRequest::getCmd('option'));
    }
    
    /**
     * addPublishButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addPublishButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::custom(JRequest::getCmd('default_view').'.publish', 'publish.png', 'publish_f2.png','JToolBar_PUBLISH', true);
    }

    /**
     * addSpamButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addSpamButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        $params = MolajoComponentHelper::getParams(JRequest::getVar('option'));
        if ($params->def('config_component_state_spam', '0') == 1) {
            JToolBarHelper::custom(JRequest::getCmd('default_view').'.spam', 'spam.png', 'spam_f2.png','JToolBar_SPAM', true);
        }
    }

    /**
     * addStickyButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addStickyButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::custom(JRequest::getCmd('default_view').'.sticky', 'stickied.png', 'stickied_f2.png','JToolBar_STICKIED', true);
    }

    /**
     * addTrashButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addTrashButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        if ($state == -2) {
        } else {
            JToolBarHelper::trash(JRequest::getCmd('default_view').'.trash','JToolBar_TRASH');
        }
    }

    /**
     * addUnpublishButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addUnpublishButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::custom(JRequest::getCmd('default_view').'.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolBar_UNPUBLISH', true);
    }

    /**
     * EDIT LAYOUT BUTTONS
     */

    /**
     * addApplyButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addApplyButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        if ($state == MOLAJO_STATE_ARCHIVED || $state == MOLAJO_STATE_VERSION) {
            return;
        }
        JToolBarHelper::apply(JRequest::getCmd('single_view').'.apply', 'JTOOLBAR_APPLY');
    }

    /**
     * addCloseButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addCloseButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::cancel(JRequest::getCmd('single_view').'.cancel', 'JToolBar_CLOSE');
    }

    /**
     * addCancelButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addCancelButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::cancel(JRequest::getCmd('single_view').'.cancel', 'JToolBar_CANCEL');
    }

    /**
     * addOpenButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addOpenButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::custom(JRequest::getCmd('single_view').'.open', 'open.png', 'open_f2.png', 'JTOOLBAR_OPEN', false);
    }

    /**
     * addSaveButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addSaveButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        if ($state == MOLAJO_STATE_ARCHIVED || $state == MOLAJO_STATE_VERSION) {
            return;
        }
        JToolBarHelper::save(JRequest::getCmd('single_view').'.save', 'JToolBar_SAVE');
    }
    
    /**
     * addSaveandnewButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addSave2newButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        if ((int) $id == 0) {
            return;
        }
        if ($state == MOLAJO_STATE_ARCHIVED || $state == MOLAJO_STATE_VERSION) {
            return;
        }
        if ($item->state == MOLAJO_STATE_ARCHIVED || $item->state == MOLAJO_STATE_VERSION) {
            return;
        }
        JToolBarHelper::custom(JRequest::getCmd('single_view').'.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
    }

    /**
     * addSaveascopyButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addSave2copyButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        if ((int) $id == 0) {
            return;
        }
        JToolBarHelper::custom(JRequest::getCmd('single_view').'.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
    }

    /**
     * SHARED BUTTONS
     */

    /**
     * addSeparatorButton
     *
     * @param	string	The name of the active view.
     * @since	1.0
     */
    public function addSeparatorButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::divider();
    }

    /**
     * addHelpButton
     *
     * @param	string	The name of the active view.
     * @since	1.0
     */
    public function addHelpButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER');
    }

    /**
     * addNewButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addNewButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {
        JToolBarHelper::addNew(JRequest::getCmd('single_view').'.add','JTOOLBAR_NEW');
    }

    /**
     * addRestoreButton
     *
     * @param	array $userToolbarButtonPermissions
     * @since	1.0
     */
    public function addRestoreButton ($state, $userToolbarButtonPermissions, $id, $item=null)
    {        
        if ($state == MOLAJO_STATE_VERSION || $state == '*') {
        } else {
            return;
        }
        $params = MolajoComponentHelper::getParams(JRequest::getVar('option'));
        if ($params->def('config_component_version_management', '1') == 1) {
            JToolBarHelper::custom(JRequest::getCmd('single_view').'.restore', 'restore.png', 'restore_f2.png','JToolBar_RESTORE', false);
        }
    }
}