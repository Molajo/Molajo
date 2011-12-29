<?php
/**
 * @package     Molajo
 * @subpackage  Component
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Component
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
class MolajoComponentRenderer
{
    /**
     * Name - from MolajoExtension
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * Config - from MolajoExtension
     *
     * @var    array
     * @since  1.0
     */
    protected $config = array();

    /**
     * Option - extracted from config
     *
     * @var    string
     * @since  1.0
     */
    protected $option = null;

    /**
     *  Template folder name - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Page include file - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $page = null;

    /**
     *  Layout include file - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $layout = null;

    /**
     *  Wrap for Layout - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $wrap = null;

    /**
     *  Template Parameters - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = null;

    /**
     * Attributes - from the Molajo Format Class <include:component statement>
     *
     * @var    array
     * @since  1.0
     */
    protected $attributes = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param null $name
     * @param array $config
     * @since 1.0
     */
    public function __construct($name = null, $config = array())
    {
        /**
        echo '<pre>';
        var_dump($config);
        '</pre>';
         **/
        /** set class properties */
        $this->name = $name;

        $this->config = $config;
        $this->option = $config->option;
        $this->template = $config->template;
        $this->page = $config->page;
        $this->layout = $config->layout;
        $this->wrap = $config->wrap;
    }

    /**
     * render
     *
     * Render the component.
     *
     * @return  object
     * @since  1.0
     */
    public function render($attributes)
    {
        /** renderer $attributes from template */
        $this->attributes = $attributes;

        /** set up request for MVC */
        $request = $this->request();

        /** Events */
        MolajoPlugin::importPlugin('system');
        MolajoFactory::getApplication()->triggerEvent('onBeforeComponentRender');

        /** path */
        $path = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->option . '/' . $this->option . '.php';

        /** installation */
        if (MOLAJO_APPLICATION_ID == 0
            && file_exists($path)
        ) {

            /** language */
        } elseif (file_exists($path)) {
            //            MolajoFactory::getLanguage()->load($this->option, $path, MolajoFactory::getLanguage()->getDefault(), false, false);

        } else {
            MolajoError::raiseError(404, MolajoTextHelper::_('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
        }
/**
        echo '<pre>';
        var_dump($request);
        '</pre>';
*/
        /** execute the component */
        ob_start();
        require_once $path;
        $output = ob_get_contents();
        ob_end_clean();

        /** Events */
        MolajoPlugin::importPlugin('system');
        MolajoFactory::getApplication()->triggerEvent('onAfterComponentRender');

        /** Return output */
        return $output;
    }

    /**
     * getRequest
     *
     * Gets the Request Object and populates Page Session Variables for Component
     *
     * @return bool
     */
    public function request()
    {
        //todo: amy remove all the application-specific values

        /** initialization */
        $task = '';
        $view = '';
        $model = '';
        $layout = '';
        $format = '';
        $component_table = '';

        $molajoConfig = new MolajoModelConfiguration ($this->option);

        /** 2. Component Path */
        $component_path = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->option;

        /** 3. Task */
        $task = $this->config->task;
        if (strpos($task, '.')) {
            $task = substr($task, (strpos($task, '.') + 1), 99);
        }

        /** 4. Controller */
        $controller = $molajoConfig->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_TASKS_CONTROLLER, $task);
        if ($controller === false) {
            MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_INVALID_TASK_DISPLAY_CONTROLLER') . ' ' . $task);
            return false;
        }

        if ($task == 'display') {

            /** 5. View **/
            $view = $this->config->view;
            if ($view == null) {
                $results = false;
            } else {
                $results = $molajoConfig->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_VIEWS, $view);
            }

            if ($results === false) {
                $view = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_VIEWS_DEFAULT);
                if ($view === false) {
                    MolajoFactory::getApplication()->setMessage(MolajoTextHelper::_('MOLAJO_NO_VIEWS_DEFAULT_DEFINED'), 'error');
                    return false;
                }
            }

            /** 7. Model **/
            $model = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_MODEL);
            if ($model === false) {
                $model = $view;
            }

            /** 8. Layout **/
            $layout = $this->config->layout;
            if ($layout == null) {
                $results = false;
            } else {
                if ($view == 'edit') {
                    $results = $molajoConfig->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_LAYOUTS_EDIT, $layout);
                } else {
                    $results = $molajoConfig->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_LAYOUTS_DISPLAY, $layout);
                }
            }

            /** 9. Layout **/
            $layout = $this->config->layout;
            if ($layout == null) {
                $results = false;
            } else {
                if ($view == 'edit') {
                    $results = $molajoConfig->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_LAYOUTS_EDIT, $layout);
                } else {
                    $results = $molajoConfig->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_LAYOUTS_DISPLAY, $layout);
                }
            }

            if ($results === false) {
                if ($view == 'edit') {
                    $layout = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_LAYOUTS_EDIT_DEFAULT);
                } else {
                    $layout = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_LAYOUTS_DISPLAY_DEFAULT);
                }
                if ($layout === false) {
                    MolajoFactory::getApplication()->setMessage(MolajoTextHelper::_('MOLAJO_NO_DEFAULT_LAYOUT_FOR_VIEW_DEFINED'), 'error');
                    return false;
                }
            }

            /** 9. Format */
            $format = $this->config->format;
            if ($format == null) {
                $results = false;
            } else {
                if ($view == 'edit') {
                    $results = $molajoConfig->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_VIEWS_EDIT_FORMATS, $format);
                } else {
                    $results = $molajoConfig->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_FORMATS, $format);
                }
            }

            if ($results === false) {
                if ($view == 'edit') {
                    $format = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_VIEWS_EDIT_FORMATS_DEFAULT);
                } else {
                    $format = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_FORMATS_DEFAULT);
                }
                if ($format === false) {
                    $format = 'html';
                }
            }
        } else {
            /** todo: amy: come back and get redirect */
            $view = '';
            $layout = '';
            $format = '';
        }

        /** 10. id, cid and category_id */
        $id = $this->config->id;
        //amy        $cids = JRequest::getVar('cid', array(), '', 'array');
        $cids = array();
        JArrayHelper::toInteger($cids);

        if ($task == 'add') {
            $id = 0;
            $cids = array();

        } else if ($task == 'edit' || $task == 'restore') {

            if ($id > 0 && count($cids) == 0) {

            } else if ($id == 0 && count($cids) == 1) {
                $id = $cids[0];
                $cids = array();

            } else if ($id == 0 && count($cids) == 0) {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_TASK_MUST_HAVE_REQUEST_ID_TO_EDIT'));
                return false;

            } else if (count($cids) > 1) {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_TASK_MAY_NOT_HAVE_MULTIPLE_REQUEST_IDS'));
                return false;
            }
        }

        /** 11. acl implementation */
        $acl_implementation = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_ACL_IMPLEMENTATION);
        if ($acl_implementation === false) {
            $acl_implementation = 'core';
        }

        /** 12. component table */
        $component_table = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_TABLE);
        if ($component_table === false) {
            $component_table = '__common';
        }

        /** 13. plugin helper */
        $plugin_type = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE);
        if ($plugin_type === false) {
            $plugin_type = 'content';
        }

        /** MVC Request Variables */
        $request = array();

        $request['current_url'] = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH;
        if (MOLAJO_PAGE_REQUEST == '') {
        } else {
            $request['current_url'] .= '/' . MOLAJO_PAGE_REQUEST;
        }
        $request['base_url'] = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH;
        $request['component_path'] = $component_path;

        $request['extension_type'] = $this->name;
        $request['option'] = $this->option;
        $request['extension'] = $this->option;

        $request['model'] = $model;
        $request['view'] = $view;
        $request['controller'] = $controller;
        $request['task'] = $task;

        $request['template'] = $this->template;
        $request['page'] = $this->page;
        $request['layout'] = $layout;
        $request['layout_type'] = 'extensions';
        $request['format'] = $format;
        if (isset($this->attributes->wrap)) {
            $request['wrap'] = $this->attributes->wrap;
        } else {
            $request['wrap'] = 'none';
        }
        if (isset($this->attributes->wrap)) {
            $request['wrap_id'] = $this->attributes->wrap_id;
        } else {
            $request['wrap_id'] = '';
        }
        if (isset($this->attributes->wrap)) {
            $request['wrap_class'] = $this->attributes->wrap_class;
        } else {
            $request['wrap_class'] = '';
        }

        $request['plugin_type'] = $plugin_type;

        $request['id'] = (int) $id;
        $request['cids'] = (array) $cids;
        $category_id = 0;
        $request['category_id'] = (int) $category_id;

        $request['parameters'] = $this->parameters;

        $request['acl_implementation'] = $acl_implementation;
        $request['component_table'] = $component_table;
        $request['filter_name'] = 'config_manager_list_filters';
        $request['select_name'] = 'config_manager_grid_column';

        return $request;
    }
}
