<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * View
 *
 * @package     Molajo
 * @subpackage  View
 * @since       1.0
 */
class MolajoViewHelper
{
    /**
     * View Name
     *
     * @var    string
     */
    protected $_view = null;

    /**
     * View Type
     *
     * @var    string
     */
    protected $_view_type = null;

    /**
     * Extension Name
     *
     * @var    string
     */
    protected $_extension_name = null;

    /**
     * Extension Type
     *
     * @var    string
     */
    protected $_extension_type = null;

    /**
     * Extension Folder
     *
     * @var    string
     */
    protected $_extension_folder = null;

    /**
     * Template Name
     *
     * @var    string
     */
    protected $_template_name = null;

    /**
     * Path
     *
     * @var    string
     */
    public $view_path = null;

    /**
     * Path URL
     *
     * @var    string
     */
    public $view_path_url = null;

    /**
     * Constructor
     *
     * @param   string  $view
     *
     * @param   string  $view_type
     *
     * @since   1.0
     */
    public function __construct($view, $view_type, $extension_name,
                                $extension_type, $extension_folder, $template_name)
    {
        $this->_view = strtolower($view);
        $this->_view_type = strtolower($view_type);
        $this->_extension_name = strtolower($extension_name);
        $this->_extension_type = strtolower($extension_type);
        $this->_extension_folder = strtolower($extension_folder);
        $this->_template_name = strtolower($template_name);

        $results = $this->_findPath();
        if ($results === false) {
            return false;
        }

        //$this->_loadLanguage();

        return array($this->view_path, $this->view_path_url);
    }

    /**
     * _getView
     *
     * Get the view data of a specific type if no specific view is specified
     * otherwise only the specific view data is returned.
     *
     * @return  mixed    An array of view data objects, or a view data object.
     * @since   1.0
     */
    protected function _getView()
    {
        return MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_VIEW, $this->_view, $this->_view_type);
    }

    /**
     * _findPath
     *
     * Looks for path of View, in this order:
     *
     *  1. Template - [template]/views/[view-type]/[view-folder]
     *  2. Extension - [extension_type]/[extension-name]/views/[view-type]/[view-folder]
     *  3. Views - extensions/views/[view_type]/[view-folder]
     *  4. MVC - applications/mvc/views/[view_type]/[view-folder]
     *
     * @return bool|string
     */
    protected function _findPath()
    {
        /** initialise */
        $this->view_path = false;

        $template = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->_template_name;
        $end = '/views/' . $this->_view_type . '/' . $this->_view;

        /** 1. Template */
        $templateViewPath = $template . $end;
        $templateViewPathURL = MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $this->_template_name . $end;

        /** 2. Extension */
        $extensionPath = '';
        if ($this->_extension_type == 'plugin') {
            $extensionPath = MOLAJO_EXTENSIONS_PLUGINS . '/' . $this->_extension_folder . '/' . $this->_extension_name . $end;
            $extensionPathURL = MOLAJO_EXTENSIONS_PLUGINS_URL . '/' . $this->_extension_folder . '/' . $this->_extension_name . $end;

        } else if ($this->_extension_type == 'component') {
            $extensionPath = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->_extension_name . $end;
            $extensionPathURL = MOLAJO_EXTENSIONS_COMPONENTS_URL . '/' . $this->_extension_name . $end;

        } else if ($this->_extension_type == 'module') {
            $extensionPath = MOLAJO_EXTENSIONS_MODULES . '/' . $this->_extension_name . $end;
            $extensionPathURL = MOLAJO_EXTENSIONS_MODULES_URL . '/' . $this->_extension_name . $end;

        } else {
            $extensionPath = '';
            $extensionPathURL = '';
        }

        /** 3. Views */
        $corePath = MOLAJO_EXTENSIONS_VIEWS . '/' . $this->_view_type . '/' . $this->_view;
        $corePathURL = MOLAJO_EXTENSIONS_VIEWS_URL . '/' . $this->_view_type . '/' . $this->_view;

        /** 4. MVC */
        $mvcPath = MOLAJO_APPLICATIONS_MVC . $end;
        $mvcPathURL = MOLAJO_APPLICATIONS_MVC_URL . $end;

        /**
         * Determine path in order of priority
         */

        /* 1. Template */
        if (is_dir($templateViewPath)) {
            $found = true;
            $this->view_path = $templateViewPath;
            $this->view_path_url = $templateViewPathURL;

            /** 2. Extension **/
        } else if (is_dir($extensionPath)) {
            $found = true;
            $this->view_path = $extensionPath;
            $this->view_path_url = $extensionPathURL;

            /** 3. View **/
        } else if (is_dir($corePath)) {
            $found = true;
            $this->view_path = $corePath;
            $this->view_path_url = $corePathURL;

            /** 4. MVC **/
        } else if (is_dir($mvcPath)) {
            $found = true;
            $this->view_path = $mvcPath;
            $this->view_path_url = $mvcPathURL;

        } else {
            $found = false;
            $this->view_path = false;
            $this->view_path_url = false;
        }

        return $found;
    }

    /**
     * _loadLanguage
     *
     * Loads Language Files
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadLanguage()
    {
        MolajoController::getApplication()->getLanguage()->load(
            MOLAJO_EXTENSIONS_VIEWS . '/' . $this->_view_type . '/' . $this->_view,
            MolajoController::getApplication()->getLanguage()->getDefault(), false, false);
    }

    /**
     * getViewDefaults
     *
     *  Retrieve application defaults for views and wraps
     *
     * @return bool
     * @since 1.0
     */
    static public function getViewDefaults($type='view', $task=null, $id=0)
    {
        if ($type = 'view') {

            if ($task == 'add' || $task == 'edit') {
                $view_id = MolajoController::getApplication()->get('default_edit_view_id', '');

            } else if ((int)$id == 0) {
                $view_id = MolajoController::getApplication()->get('default_items_view_id', '');

            } else {
                $view_id = MolajoController::getApplication()->get('default_item_view_id', '');
            }
        }

        if ($type == 'wrap') {

            if ($task == 'add' || $task == 'edit') {
                $wrap_id = MolajoController::getApplication()->get('default_edit_wrap_id', '');

            } else if ((int)$id == 0) {
                $wrap_id = MolajoController::getApplication()->get('default_items_wrap_id', '');

            } else {
                $wrap_id = MolajoController::getApplication()->get('default_item_wrap_id', '');
            }
        }
    }
}
