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
 * @subpackage  Helper
 * @since       1.0
 */
ClassViewHelper
{
    /**
     * View Name
     *
     * @var    string
     */
    protected $view = null;

    /**
     * View Type
     *
     * @var    string
     */
    protected $view_type = null;

    /**
     * Extension Name
     *
     * @var    string
     */
    protected $extension_name = null;

    /**
     * Extension Type
     *
     * @var    string
     */
    protected $extension_type = null;

    /**
     * Theme Name
     *
     * @var    string
     */
    protected $theme_name = null;

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
     * @param  $theme_name - name of theme
     *
     * @return  array
     * @since   1.0
     */
    public function __construct($view, $view_type, $extension_name,
                                $extension_type, $theme_name)
    {
        $this->view = strtolower($view);
        if (strtolower($view_type) == 'pages'
            || strtolower($view_type) == 'wraps'
        ) {
        } else {
            $view_type = 'templates';
        }
        $this->view_type = strtolower($view_type);
        $this->extension_name = strtolower($extension_name);
        $this->extension_type = strtolower($extension_type);
        $this->theme_name = strtolower($theme_name);

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
        $plus = '/views/' . $this->view_type . '/' . $this->view;

        /** 1. Theme */
        $theme = MOLAJO_EXTENSIONS_THEMES . '/' . $this->theme_name;
        $themeViewPath = $theme . $plus;
        $themeViewPathURL = MOLAJO_EXTENSIONS_THEMES_URL . '/' . $this->theme_name . $plus;

        /** 2. Extension */
        $extensionPath = '';
        if ($this->extension_type == 'plugin') {
            $extensionPath = MOLAJO_EXTENSIONS_PLUGINS . '/' . $this->extension_name . $plus;
            $extensionPathURL = MOLAJO_EXTENSIONS_PLUGINS_URL . '/' . $this->extension_name . $plus;

        } else if ($this->extension_type == 'component') {
            $extensionPath = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->extension_name . $plus;
            $extensionPathURL = MOLAJO_EXTENSIONS_COMPONENTS_URL . '/' . $this->extension_name . $plus;

        } else if ($this->extension_type == 'module') {
            $extensionPath = MOLAJO_EXTENSIONS_MODULES . '/' . $this->extension_name . $plus;
            $extensionPathURL = MOLAJO_EXTENSIONS_MODULES_URL . '/' . $this->extension_name . $plus;

        } else {
            $extensionPath = '';
            $extensionPathURL = '';
        }

        /** 3. Views */
        $corePath = MOLAJO_EXTENSIONS_VIEWS . '/' . $this->view_type . '/' . $this->view;
        $corePathURL = MOLAJO_EXTENSIONS_VIEWS_URL . '/' . $this->view_type . '/' . $this->view;

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
        Services::Language()
            ->load(
            MOLAJO_EXTENSIONS_VIEWS . '/' . $this->view_type . '/' . $this->view,
            Services::Language()->get('tag'),
            false,
            false);
    }

    /**
     * getViewDefaultsApplication
     *
     * Retrieve application defaults for views and wraps
     *
     * @return bool
     * @since 1.0
     */
    static public function getViewDefaultsApplication($type = 'template',
                                           $task = null,
                                           $id = 0)
    {
        $view = 0;
        if ($type == 'template') {
            if ($task == 'add' || $task == 'edit') {
                $view = (int)Services::Configuration()->get('default_edit_template_view_id', 0);;

            } else if ((int)$id == 0) {
                $view = (int)Services::Configuration()->get('default_items_template_view_id', 0);

            } else {
                $view = (int)Services::Configuration()->get('default_item_template_view_id', 0);
            }
        }

        if ($type == 'wrap') {
            if ($task == 'add' || $task == 'edit') {
                $view = (int)Services::Configuration()->get('default_edit_wrap_view_id', 0);

            } else if ((int)$id == 0) {
                $view = (int)Services::Configuration()->get('default_items_wrap_view_id', 0);

            } else {
                $view = (int)Services::Configuration()->get('default_item_wrap_view_id', 0);
            }
        }
        return $view;
    }

    /**
     * getViewDefaultsOther
     *
     * Retrieve view and wrap defaults for categories and extensions
     *
     * @return bool
     * @since 1.0
     */
    static public function getViewDefaultsOther($type = 'template',
                                           $task = null,
                                           $id = 0,
                                           $parameters)
    {
        $view = 0;
        if ($type == 'template') {
            if ($task == 'add' || $task == 'edit') {
                $view = $parameters->get('default_edit_template_view_id', 0);

            } else if ((int)$id == 0) {
                $view = $parameters->get('default_items_template_view_id', 0);

            } else {
                $view = $parameters->get('default_item_template_view_id', 0);
            }
        }

        if ($type == 'wrap') {
            if ($task == 'add' || $task == 'edit') {
                $view = $parameters->get('default_edit_wrap_view_id', 0);

            } else if ((int)$id == 0) {
                $view = $parameters->get('default_items_wrap_view_id', 0);

            } else {
                $view = $parameters->get('default_item_wrap_view_id', 0);
            }
        }
        return $view;
    }
}
