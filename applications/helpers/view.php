<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Content
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
     * Theme Name
     *
     * @var    string
     */
    protected $_theme_name = null;

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
     * @param  $view - name or id of the view
     * @param  $view_type - type of view (pages, templates, wraps)
     * @param  $extension_name - name of component, module, theme, or listener
     * @param  $extension_type - type of extension
     * @param  $extension_folder - subtype, or folder, for view and listener extensions
     * @param  $theme_name - name of theme
     *
     * @return  array
     * @since   1.0
     */
    public function __construct($view, $view_type, $extension_name,
                                $extension_type, $extension_folder, $theme_name)
    {
        $this->_view = strtolower($view);
        if (strtolower($view_type) == 'pages'
            || strtolower($view_type) == 'wraps') {
        } else {
            $view_type = 'templates';
        }
        $this->_view_type = strtolower($view_type);
        $this->_extension_name = strtolower($extension_name);
        $this->_extension_type = strtolower($extension_type);
        $this->_extension_folder = strtolower($extension_folder);
        $this->_theme_name = strtolower($theme_name);

        $results = $this->_findPath();

        if ($results === false) {
            return false;
        }

        //$this->_loadLanguage();

        return array($this->view_path, $this->view_path_url);
    }

    /**
     * _findPath
     *
     * Looks for path of view in this order:
     *
     *  1. Theme - extensions/themes/theme-name/
     *  2. Extension - [extension_type]/[extension-name]/
     *  3. Views - extensions/
     *  4. MVC - applications/mvc/
     *
     *  Plus: views/[view-type]/[view-folder]
     *
     * @return bool|string
     */
    protected function _findPath()
    {
        /** initialise */
        $this->view_path = false;

        /** Remaining portion of path for all locations */
        $plus = '/views/' . $this->_view_type . '/' . $this->_view;

        /** 1. Theme */
        $theme = MOLAJO_EXTENSIONS_THEMES . '/' . $this->_theme_name;
        $themeViewPath = $theme . $plus;
        $themeViewPathURL = MOLAJO_EXTENSIONS_THEMES_URL . '/' . $this->_theme_name . $plus;

        /** 2. Extension */
        $extensionPath = '';
        if ($this->_extension_type == 'plugin') {
            $extensionPath = MOLAJO_EXTENSIONS_PLUGINS . '/' . $this->_extension_folder . '/' . $this->_extension_name . $plus;
            $extensionPathURL = MOLAJO_EXTENSIONS_PLUGINS_URL . '/' . $this->_extension_folder . '/' . $this->_extension_name . $plus;

        } else if ($this->_extension_type == 'component') {
            $extensionPath = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->_extension_name . $plus;
            $extensionPathURL = MOLAJO_EXTENSIONS_COMPONENTS_URL . '/' . $this->_extension_name . $plus;

        } else if ($this->_extension_type == 'module') {
            $extensionPath = MOLAJO_EXTENSIONS_MODULES . '/' . $this->_extension_name . $plus;
            $extensionPathURL = MOLAJO_EXTENSIONS_MODULES_URL . '/' . $this->_extension_name . $plus;

        } else {
            $extensionPath = '';
            $extensionPathURL = '';
        }

        /** 3. Views */
        $corePath = MOLAJO_EXTENSIONS_VIEWS . '/' . $this->_view_type . '/' . $this->_view;
        $corePathURL = MOLAJO_EXTENSIONS_VIEWS_URL . '/' . $this->_view_type . '/' . $this->_view;

        /** 4. MVC */
        $mvcPath = MOLAJO_APPLICATIONS_MVC . $plus;
        $mvcPathURL = MOLAJO_APPLICATIONS_MVC_URL . $plus;

        /**
         * Determine path in order of priority
         */

        /* 1. Theme */
        if (is_dir($themeViewPath)) {
            $found = true;
            $this->view_path = $themeViewPath;
            $this->view_path_url = $themeViewPathURL;

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
        Molajo::Services()
            ->connect('language')
            ->load(
            MOLAJO_EXTENSIONS_VIEWS . '/' . $this->_view_type . '/' . $this->_view,
            Molajo::Application()->get('language'),
            false,
            false);
    }

    /**
     * getViewDefaults
     *
     * Retrieve application defaults for views and wraps
     *
     * @return bool
     * @since 1.0
     */
    static public function getViewDefaults($type='template',
                                           $task=null,
                                           $id=0)
    {
        if ($type = 'template') {

            if ($task == 'add' || $task == 'edit') {
                $template_view_id =
                    (int)Molajo::Application()->get(
                        'default_edit_template_view_id',
                        0
                    );
            } else if ((int)$id == 0) {
                $template_view_id =
                    (int)Molajo::Application()->get(
                        'default_items_template_view_id',
                        0
                    );
            } else {
                $template_view_id =
                    (int)Molajo::Application()->get(
                    'default_item_template_view_id',
                    0
                );
            }
        }

        if ($type == 'wrap') {
            if ($task == 'add' || $task == 'edit') {
                $wrap_view_id =
                    (int)Molajo::Application()->get(
                        'default_edit_wrap_view_id',
                        0
                    );

            } else if ((int)$id == 0) {
                $wrap_view_id = (int)Molajo::Application()->get(
                    'default_items_wrap_view_id',
                    0
                );

            } else {
                $wrap_view_id = (int)Molajo::Application()->get(
                    'default_item_wrap_view_id',
                    0
                );
            }
        }
    }
}
