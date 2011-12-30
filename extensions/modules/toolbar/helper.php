<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * @package        Molajo
 * @subpackage    header
 * @since        1.0
 */
abstract class MolajoToolbarHelper
{
    /**
     * $data
     *
     * @since    1.0
     */
    protected static $data = array();

    /**
     * Helper method to generate data
     *
     * @param    array    A named array with keys link, image, text, access and imagePath
     *
     * @return    string    HTML for button
     * @since    1.0
     */
    public function getList($parameters)
    {
        $object = new JObject();
        $object->set('site_title', MolajoFactory::getApplication()->get('site_title', 'Molajo'));
        $data[] = $object;
        return $data;
    }

    /**
     * addButtonsDisplayView
     *
     * @param    string    The name of the active view.
     * @since    1.0
     */
    public function addButtonsDisplayView($state, $userToolbarButtonPermissions)
    {
        $parameters = MolajoComponent::getParameters(JRequest::getCmd('option'));

        $this->addTitle($parameters->def('config_manager_title_image', 1),
                        $parameters->def('config_manager_title', 1),
                        JRequest::getCmd('DefaultView'),
                        JRequest::getCmd('option'),
                        JRequest::getCmd('view'));

        $buttonParameterFieldName = 'config_manager_button_bar_option';
        $this->_buttonLoop($buttonParameterFieldName, $state, $userToolbarButtonPermissions, 0, array());

        return;
    }

    /**
     * addButtonsEditView
     *
     * @param    string    The name of the active view.
     * @since    1.0
     */
    public function addButtonsEditView($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        JRequest::setVar('hidemainmenu', true);

        $parameters = MolajoComponent::getParameters(JRequest::getCmd('option'));

        $this->addTitle($parameters->def('config_manager_title_image', 1),
                        $parameters->def('config_manager_title', 1),
                        JRequest::getCmd('DefaultView'),
                        JRequest::getCmd('option'),
                        JRequest::getCmd('view'));

        if ((int)$id == 0) {
            $buttonParameterFieldName = 'config_manager_editor_button_bar_new_option';
        } else {
            $buttonParameterFieldName = 'config_manager_editor_button_bar_edit_option';
        }
        $this->_buttonLoop($buttonParameterFieldName, $state, $userToolbarButtonPermissions, $id, $item);
    }

    /**
     * addTitle
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addTitle($imageParameter, $titleParameter)
    {
        if ($imageParameter == '1') {
            $titleImage = JRequest::getCmd('DefaultView') . '.png';
        } else {
            $titleImage = '';
        }

        if ($titleParameter == '1') {
            self::title(MolajoTextHelper::_('MOLAJO_MANAGER_' . strtoupper(JRequest::getCmd('view'))), $titleImage);
        } else {
            if ($titleImage == '') {
            } else {
                self::title('', $titleImage);
            }
        }
    }

    /**
     * buttonLoop
     *
     * @param $buttonParameterFieldName - Parameter name in configuraiton file for Toolbar Button Section
     * @param $state
     * @param $userToolbarButtonPermissions
     * @param $id
     * @param $item
     * @return void
     */
    private function _buttonLoop($buttonParameterFieldName, $state, $userToolbarButtonPermissions, $id, $item)
    {
        /** component parameters **/
        $parameters = MolajoComponent::getParameters(JRequest::getCmd('option'));

        /** loop thru config options and add ToolBar buttons **/
        $count = 0;

        /** filters **/
        $loadedButtonArray = array();

        for ($i = 1; $i < 99; $i++) {
            $buttonValue = $parameters->def($buttonParameterFieldName . $i, null);

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
                    $functionName = 'add' . ucfirst($buttonValue) . 'Button';
                    if (method_exists('MolajoToolbarHelper', $functionName)) {
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
            if (method_exists('MolajoToolbarHelper', $functionName)) {
                $this->$functionName ($state, $userToolbarButtonPermissions, $id, $item);
            }
            //            }
        }
    }

    /**
     * LIST VIEW BUTTONS
     */

    /**
     * addArchiveButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addArchiveButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::archiveList('archive', 'MOLAJO_TOOLBAR_ARCHIVE_BUTTON');
    }

    /**
     * addCheckinButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addCheckinButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::checkinList('checkin', 'MOLAJO_TOOLBAR_CHECKIN_BUTTON');
    }

    /**
     * addDeleteButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addDeleteButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        if ($state == -2) {
            self::deleteList('', JRequest::getCmd('DefaultView') . '.delete', 'MolajoToolbar_EMPTY_TRASH');
        }
    }

    /**
     * addEditButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addEditButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::editList(JRequest::getCmd('EditView') . '.edit', 'TOOLBAR_EDIT');
    }

    /**
     * addFeatureButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addFeatureButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::custom(JRequest::getCmd('DefaultView') . '.feature', 'featured.png', 'featured_f2.png', 'MolajoToolbar_FEATURED', true);
    }

    /**
     * addOptionsButton
     *
     * @param    string    The name of the active view.
     * @since    1.0
     */
    public function addOptionsButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::preferences(JRequest::getCmd('option'));
    }

    /**
     * addPublishButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addPublishButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::custom(JRequest::getCmd('DefaultView') . '.publish', 'publish.png', 'publish_f2.png', 'MolajoToolbar_PUBLISH', true);
    }

    /**
     * addSpamButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addSpamButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        $parameters = MolajoComponent::getParameters(JRequest::getVar('option'));
        if ($parameters->def('state_spam', '0') == 1) {
            self::custom(JRequest::getCmd('DefaultView') . '.spam', 'spam.png', 'spam_f2.png', 'MolajoToolbar_SPAM', true);
        }
    }

    /**
     * addStickyButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addStickyButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::custom(JRequest::getCmd('DefaultView') . '.sticky', 'stickied.png', 'stickied_f2.png', 'MolajoToolbar_STICKIED', true);
    }

    /**
     * addTrashButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addTrashButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        if ($state == -2) {
        } else {
            self::trash(JRequest::getCmd('DefaultView') . '.trash', 'MolajoToolbar_TRASH');
        }
    }

    /**
     * addUnpublishButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addUnpublishButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::custom(JRequest::getCmd('DefaultView') . '.unpublish', 'unpublish.png', 'unpublish_f2.png', 'MolajoToolbar_UNPUBLISH', true);
    }

    /**
     * EDIT VIEW BUTTONS
     */

    /**
     * addApplyButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addApplyButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        if ($state == MOLAJO_STATUS_ARCHIVED || $state == MOLAJO_STATUS_VERSION) {
            return;
        }
        self::apply(JRequest::getCmd('EditView') . '.apply', 'TOOLBAR_APPLY');
    }

    /**
     * addCloseButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addCloseButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::cancel(JRequest::getCmd('EditView') . '.cancel', 'MolajoToolbar_CLOSE');
    }

    /**
     * addCancelButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addCancelButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::cancel(JRequest::getCmd('EditView') . '.cancel', 'MolajoToolbar_CANCEL');
    }

    /**
     * addOpenButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addOpenButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::custom(JRequest::getCmd('EditView') . '.open', 'open.png', 'open_f2.png', 'TOOLBAR_OPEN', false);
    }

    /**
     * addSaveButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addSaveButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        if ($state == MOLAJO_STATUS_ARCHIVED || $state == MOLAJO_STATUS_VERSION) {
            return;
        }
        self::save(JRequest::getCmd('EditView') . '.save', 'MolajoToolbar_SAVE');
    }

    /**
     * addSaveandnewButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addSavethennewButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        if ((int)$id == 0) {
            return;
        }
        if ($state == MOLAJO_STATUS_ARCHIVED || $state == MOLAJO_STATUS_VERSION) {
            return;
        }
        if ($item->state == MOLAJO_STATUS_ARCHIVED || $item->state == MOLAJO_STATUS_VERSION) {
            return;
        }
        self::custom(JRequest::getCmd('EditView') . '.saveandnew', 'savethennew.png', 'savethennew_f2.png', 'TOOLBAR_SAVEANDNEW', false);
    }

    /**
     * addSaveascopyButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addSaveascopyButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        if ((int)$id == 0) {
            return;
        }
        self::custom(JRequest::getCmd('EditView') . '.saveascopy', 'save-copy.png', 'save-copy_f2.png', 'TOOLBAR_SAVEASCOPY', false);
    }

    /**
     * SHARED BUTTONS
     */

    /**
     * addSeparatorButton
     *
     * @param    string    The name of the active view.
     * @since    1.0
     */
    public function addSeparatorButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::divider();
    }

    /**
     * addHelpButton
     *
     * @param    string    The name of the active view.
     * @since    1.0
     */
    public function addHelpButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::help('MolajoApplicationHelper_CONTENT_ARTICLE_MANAGER');
    }

    /**
     * addNewButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addNewButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        self::addNew(JRequest::getCmd('EditView') . '.add', 'TOOLBAR_NEW');
    }

    /**
     * addRestoreButton
     *
     * @param    array $userToolbarButtonPermissions
     * @since    1.0
     */
    public function addRestoreButton($state, $userToolbarButtonPermissions, $id, $item = null)
    {
        if ($state == MOLAJO_STATUS_VERSION || $state == '*') {
        } else {
            return;
        }
        $parameters = MolajoComponent::getParameters(JRequest::getVar('option'));
        if ($parameters->def('version_management', '1') == 1) {
            self::custom(JRequest::getCmd('EditView') . '.restore', 'restore.png', 'restore_f2.png', 'MolajoToolbar_RESTORE', false);
        }
    }
}