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
class MolajoComponent
{
    /**
     * Name
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * Attributes - from the template <include:component statement>
     *
     * @var    array
     * @since  1.0
     */
    protected $attributes = array();

    /**
     * Config
     *
     * @var    array
     * @since  1.0
     */
    protected $config = array();

    /**
     * Option
     *
     * @var    string
     * @since  1.0
     */
    protected $option = null;

    /**
     *  Template folder name
     *
     * @var string
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Page include file
     *
     * @var string
     * @since 1.0
     */
    protected $page = null;

    /**
     *  Layout include file
     *
     * @var string
     * @since 1.0
     */
    protected $layout = null;

    /**
     *  Wrap for Layout
     *
     * @var string
     * @since 1.0
     */
    protected $wrap = null;

    /**
     *  Template Parameters
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = null;

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
                echo '<pre>';
                var_dump($config);
                '</pre>';
echo $name;
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
        $path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->option . '/' . $this->option . '.php';

        /** installation */
        if (MOLAJO_APPLICATION_ID == 0
            && file_exists($path)
        ) {

        /** language */
        } elseif (file_exists($path)
        ) {
            //            MolajoFactory::getLanguage()->load($this->option, $path, MolajoFactory::getLanguage()->getDefault(), false, false);

        } else {
            MolajoError::raiseError(404, MolajoTextHelper::_('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
        }
        echo '<pre>';var_dump($request);'</pre>';

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

        /** 10. id, cid and catid */
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

        /** other */
        //        $extension = JRequest::getCmd('extension', '');
        //        $component_specific = JRequest::getCmd('component_specific', '');

        /** Page Session Variables */
        $session = MolajoFactory::getSession();

        $session->set('page.application_id', MOLAJO_APPLICATION_ID);
        $session->set('page.current_url', MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH . '/' . MOLAJO_PAGE_REQUEST);
        $session->set('page.base_url', MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH . '/');
        $session->set('page.item_id', $this->config->id);

        $session->set('page.controller', $controller);
        $session->set('page.extension_type', 'component');
        $session->set('page.option', $this->option);
        $session->set('page.view', $view);
        $session->set('page.model', $model);
        $session->set('page.layout', $layout);

        if (isset($this->attributes->wrap)) {
            $session->set('page.wrap', $this->attributes->wrap);
        } else {
            $session->set('page.wrap', 'none');
        }
        if (isset($this->attributes->wrap)) {
            $session->set('page.wrap_id', $this->attributes->wrap_id);
        } else {
            $session->set('page.wrap_id', '');
        }
        if (isset($this->attributes->wrap_class)) {
            $session->set('page.wrap_class', $this->attributes->wrap_class);
        } else {
            $session->set('page.wrap_cass', 'none');
        }

        $session->set('page.layout_type', 'extension');
        $session->set('page.task', $task);
        $session->set('page.format', $format);
        $session->set('page.plugin_type', $plugin_type);

        $session->set('page.id', (int)$id);
        $session->set('page.cid', (array)$cids);
        //        $session->set('page.catid', (int)$catid);

        $session->set('page.acl_implementation', $acl_implementation);
        $session->set('page.component_table', $component_table);
        $session->set('page.component_path', $component_path);
        $session->set('page.filter_name', 'config_manager_list_filters');
        $session->set('page.select_name', 'config_manager_grid_column');

        /** other */
        $session->set('page.extension', $this->option);
        //        $session->set('page.component_specific', $component_specific);

        /** retrieve from db */
        //        if ($controller == 'display') {
        //            $this->getContentInfo();
        //        }

        /** load into $data array for creation of the request object */
        $request = array();

        $request['application_id'] = $session->get('page.application_id');
        $request['current_url'] = $session->get('page.current_url');
        $request['component_path'] = $session->get('page.component_path');
        $request['base_url'] = $session->get('page.base_url');

        $request['extension_type'] = $session->get('page.extension_type');
        $this->option = $session->get('page.option');

        $request['model'] = $session->get('page.model');
        $request['view'] = $session->get('page.view');
        $request['controller'] = $session->get('page.controller');

        $request['layout'] = $session->get('page.layout');
        $request['format'] = $session->get('page.format');
        $request['wrap'] = $session->get('page.wrap');
        $request['wrap_id'] = $session->get('page.wrap_id');
        $request['wrap_class'] = $session->get('page.wrap_class');

        $request['task'] = $session->get('page.task');

        $request['plugin_type'] = $session->get('page.plugin_type');

        $request['id'] = $session->get('page.id');
        $request['parameters'] = $session->get('page.parameters');
        $request['extension'] = $session->get('page.extension');
        $request['component_specific'] = $session->get('page.component_specific');

        $request['acl_implementation'] = $session->get('page.acl_implementation');
        $request['component_table'] = $session->get('page.component_table');
        $request['filter_name'] = $session->get('page.filter_name');
        $request['select_name'] = $session->get('page.select_name');

        $request['title'] = $session->get('page.title');
        $request['subtitle'] = $session->get('page.subtitle');
        $request['metakey'] = $session->get('page.metakey');
        $request['metadesc'] = $session->get('page.metadesc');
        $request['metadata'] = $session->get('page.metadata');
        $request['position'] = $session->get('page.position');

        $request['wrap_title'] = $request['title'];
        $request['wrap_subtitle'] = $request['subtitle'];
        $request['wrap_date'] = '';
        $request['wrap_author'] = '';
        $request['wrap_more_array'] = array();

        return $request;
    }
}
