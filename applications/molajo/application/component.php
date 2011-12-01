<?php
/**
 * @package     Molajo
 * @subpackage  Application
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
     * @var array $_components - list of components from cache
     *
     * @since  1.0
     */
    protected static $_components = array();

    /**
     * Verifies login requirement for application and default options
     *
     * @return    string        option
     * @since    1.0
     */
    public static function verifyComponent($option = null)
    {
        if ($option == null) {
            $option = strtolower(JRequest::getCmd('option', null));
        }

        if (MolajoFactory::getUser()->get('guest') == 1
            && MolajoFactory::getApplicationConfig()->get('application_logon_requirement', 1) == 1
        ) {

            $option = MolajoFactory::getApplicationConfig()->get('application_guest_option', 'login');

        } elseif ($option == null) {
            $option = MolajoFactory::getApplicationConfig()->get('application_default_option', 'dashboard');
        }

        JRequest::setVar('option', $option);
        return $option;
    }

    /**
     * getComponentName
     *
     * Return the name of the request component [main component]
     * Moved in from the Application Helper
     *
     * @param   string  $default The default option
     * @return  string  Option
     * @since   1.0
     */
    public static function getComponentName($default = NULL)
    {
        static $option;
        if ($option) {
            return $option;
        }

        $option = strtolower(JRequest::getCmd('option'));

        if (empty($option)) {
            $option = $default;
        }

        JRequest::setVar('option', $option);
        return $option;
    }

    /**
     * getComponent
     *
     * Get component information.
     *
     * @param   string   $option  component option.
     * @param   boolean  $string  If set and the component does not exist, the enabled attribute will be set to false
     *
     * @return  object   An object with information about the component.
     * @since  1.0
     */
    public static function getComponent($option, $strict = false)
    {
        if (isset(self::$_components[$option])) {
            $result = self::$_components[$option];

        } else {
            if (self::_load($option)) {
                $result = self::$_components[$option];

            } else {
                $result = new stdClass;
                $result->enabled = $strict ? false : true;
                $result->parameters = new JRegistry;
            }
        }

        return $result;
    }

    /**
     * isEnabled
     *
     * Checks if the component is enabled
     *
     * @param   string   $option  The component option.
     * @param   boolean  $string  If set and the component does not exist, false will be returned
     *
     * @return  boolean
     * @since  1.0
     */
    public static function isEnabled($option, $strict = false)
    {
        $result = self::getComponent($option, $strict);
        if ($result[0]->status == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * getParameters
     *
     * Gets the parameter object for the component
     *
     * @param   string   $option  The option for the component.
     * @param   boolean  $strict  If set and the component does not exist, false will be returned
     *
     * @return  JRegistry  A JRegistry object.
     *
     * @see     JRegistry
     * @since  1.0
     */
    public static function getParameters($option, $strict = false)
    {
        $component = self::getComponent($option, $strict);
        return $component[0]->parameters;
    }

    /**
     * _load
     *
     * Load installed components into the _components array.
     *
     * @param   string  $option  The element value for the extension
     *
     * @return  bool  True on success
     * @since  1.0
     */
    protected static function _load($option)
    {
        self::$_components[$option] = MolajoExtension::getExtensions(MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT, $option);
        if (isset(self::$_components[$option]->parameters)) {
            $temp = new JRegistry;
            $temp->loadString(self::$_components[$option]->parameters);
            self::$_components[$option]->parameters = $temp;
        }

        return true;
    }

    /**
     * renderComponent
     *
     * Render the component.
     *
     * @param   string  $request An array of component information
     * @param   array   $parameters  The component parameters
     *
     * @return  object
     * @since  1.0
     */
    public static function renderComponent($request, $parameters = array())
    {
        /** scope */
        $scope = MolajoFactory::getApplication()->scope;
        MolajoFactory::getApplication()->scope = $request['option'];

        /** path */
        $path = $request['component_path'] . '/' . $request['option'] . '.php';

        /** installation */
        if ($request['application_id'] == 0
            && file_exists($path)
        ) {

            /** language */
        } elseif (self::isEnabled($request['option'])
                  && file_exists($path)
        ) {
            MolajoFactory::getLanguage()->load($request['option'], $path, MolajoFactory::getLanguage()->getDefault(), false, false);

        } else {
            MolajoError::raiseError(404, MolajoTextHelper::_('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
        }
        //echo '<pre>';var_dump($request);'</pre>';

        /** execute the component */
        ob_start();
        require_once $path;
        $output = ob_get_contents();
        ob_end_clean();

        /** Revert scope */
        MolajoFactory::getApplication()->scope = $scope;

        return $output;
    }

    /**
     * getRequest
     *
     * Gets the Request Object and populates Page Session Variables for Component
     *
     * @return bool
     */
    public function getRequest($option = null)
    {
        //todo: amy remove all the application-specific values

        /** initialization */
        $task = '';
        $view = '';
        $model = '';
        $layout = '';
        $format = '';
        $component_table = '';

        $molajoConfig = new MolajoModelConfiguration ($option);

        /** 1. Option */
        if ($option == null) {
            $option = $this->verifyComponent();
        }

        /** 2. Component Path */
        $component_path = MOLAJO_CMS_COMPONENTS . '/' . $option;

        /** 3. Task */
        $task = JRequest::getCmd('task', 'display');
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
            $view = JRequest::getCmd('view', null);
            if ($view == null) {
                $results = false;
            } else {
                $results = $molajoConfig->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_VIEWS, $view);
            }

            if ($results === false) {
                $view = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_VIEWS_DEFAULT);
                if ($view === false) {
                    $this->enqueueMessage(MolajoTextHelper::_('MOLAJO_NO_VIEWS_DEFAULT_DEFINED'), 'error');
                    return false;
                }
            }

            /** 7. Model **/
            $model = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_MODEL);
            if ($model === false) {
                $model = $view;
            }

            /** 8. Layout **/
            $layout = JRequest::getCmd('layout', null);
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
                    $this->enqueueMessage(MolajoTextHelper::_('MOLAJO_NO_DEFAULT_LAYOUT_FOR_VIEW_DEFINED'), 'error');
                    return false;
                }
            }

            /** 9. Format */
            $format = JRequest::getCmd('format', null);
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
        $id = JRequest::getInt('id');
        $cids = JRequest::getVar('cid', array(), '', 'array');
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
        $catid = JRequest::getInt('catid');

        /** 11. acl implementation */
        $acl_implementation = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_ACL_IMPLEMENTATION);
        if ($acl_implementation === false) {
            $acl_implementation = 'core';
        }

        /** 12. component table */
        $component_table = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_TABLE);
        if ($component_table === false) {
            $component_table = '_common';
        }

        /** 13. plugin helper */
        $plugin_type = $molajoConfig->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE);
        if ($plugin_type === false) {
            $plugin_type = 'content';
        }

        /** 14. parameters */
        $parameters = MolajoComponent::getParameters($option);

        /** other */
        $extension = JRequest::getCmd('extension', '');
        $component_specific = JRequest::getCmd('component_specific', '');

        /** Request Object */
        JRequest::setVar('option', $option);
        JRequest::setVar('view', $view);
        JRequest::setVar('layout', $layout);
        JRequest::setVar('task', $task);
        JRequest::setVar('format', $format);

        JRequest::setVar('id', (int)$id);
        JRequest::setVar('cid', (array)$cids);

        /** Page Session Variables */
        $session = MolajoFactory::getSession();

        $session->set('page.application_id', MOLAJO_APPLICATION_ID);
        $session->set('page.current_url', MOLAJO_BASE_URL);
        $session->set('page.base_url', JURI::base());
        $session->set('page.item_id', JRequest::getInt('Itemid', 0));

        $session->set('page.controller', $controller);
        $session->set('page.extension_type', 'component');
        $session->set('page.option', $option);
        $session->set('page.view', $view);
        $session->set('page.model', $model);
        $session->set('page.layout', $layout);

        $session->set('page.wrap', 'none');
        $session->set('page.wrap_id', '');
        $session->set('page.wrap_class', '');

        $session->set('page.layout_type', 'extension');
        $session->set('page.task', $task);
        $session->set('page.format', $format);
        $session->set('page.plugin_type', $plugin_type);

        $session->set('page.id', (int)$id);
        $session->set('page.cid', (array)$cids);
        $session->set('page.catid', (int)$catid);

        $session->set('page.acl_implementation', $acl_implementation);
        $session->set('page.component_table', $component_table);
        $session->set('page.component_path', $component_path);
        $session->set('page.filter_name', 'config_manager_list_filters');
        $session->set('page.select_name', 'config_manager_grid_column');

        /** other */
        $session->set('page.extension', $extension);
        $session->set('page.component_specific', $component_specific);

        /** retrieve from db */
        if ($controller == 'display') {
            $this->getContentInfo();
        }

        /** load into $data array for creation of the request object */
        $request = array();

        $request['application_id'] = $session->get('page.application_id');
        $request['current_url'] = $session->get('page.current_url');
        $request['component_path'] = $session->get('page.component_path');
        $request['base_url'] = $session->get('page.base_url');
        $request['item_id'] = $session->get('page.item_id');

        $request['controller'] = $session->get('page.controller');
        $request['extension_type'] = $session->get('page.extension_type');
        $request['option'] = $session->get('page.option');
        $request['view'] = $session->get('page.view');
        $request['layout'] = $session->get('page.layout');
        $request['wrap'] = $session->get('page.wrap');
        $request['wrap_id'] = $session->get('page.wrap_id');
        $request['wrap_class'] = $session->get('page.wrap_class');

        $request['model'] = $session->get('page.model');
        $request['task'] = $session->get('page.task');
        $request['format'] = $session->get('page.format');
        $request['plugin_type'] = $session->get('page.plugin_type');

        $request['id'] = $session->get('page.id');
        $request['cid'] = $session->get('page.cid');
        $request['catid'] = $session->get('page.catid');
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

    /**
     * getContentInfo
     *
     * @return    array
     * @since    1.0
     */
    public function getContentInfo()
    {
        $session = MolajoFactory::getSession();
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);
        $doquery = false;

        if ((int)$session->get('page.item_id') > 0) {
            $query->select('"" as ' . $db->namequote('metakey'));
            $query->select('"" as ' . $db->namequote('metadesc'));
            $query->select('"" as ' . $db->namequote('metadata'));
            $query->select($db->namequote('parameters'));
            $query->from($db->namequote($session->get('page.component_table')));
            $query->where($db->namequote('id') . ' = ' . (int)$session->get('page.id'));
            $doquery = true;

        } else if ((int)$session->get('page.id') > 0) {
            $query->select($db->namequote('metakey'));
            $query->select($db->namequote('metadesc'));
            $query->select($db->namequote('metadata'));
            $query->select($db->namequote('parameters'));
            $query->from($db->namequote($session->get('page.component_table')));
            $query->where($db->namequote('id') . ' = ' . (int)$session->get('page.id'));
            $doquery = true;

        } else if ((int)$session->get('page.cid') > 0) {
            $query->select($db->namequote('metakey'));
            $query->select($db->namequote('metadesc'));
            $query->select($db->namequote('metadata'));
            $query->select($db->namequote('parameters'));
            $query->from($db->namequote('#__categories'));
            $query->where($db->namequote('id') . ' > ' . (int)$session->get('page.catid'));
            $doquery = true;
        }

        if ($doquery === true) {
            $query->select($db->namequote('id'));
            $query->select($db->namequote('title'));
            $query->select('"" as ' . $db->namequote('subtitle'));

            $db->setQuery($query->__toString());

            $results = $db->loadObjectList();

        } else {
            $session->set('page.title', '');
            $session->set('page.subtitle', '');
            $session->set('page.metakey', '');
            $session->set('page.metadesc', '');
            $session->set('page.metadata', '');
            $session->set('page.parameters', '');
            $results = array();
        }

        if (count($results) > 0) {
            foreach ($results as $count => $item) {
                $session->set('page.title', $item->title);
                $session->set('page.subtitle', $item->subtitle);
                $session->set('page.metakey', $item->metakey);
                $session->set('page.metadesc', $item->metadesc);
                $session->set('page.metadata', $item->metadata);
                $session->set('page.parameters', $item->parameters);
            }
        } else {
            $session->set('page.title', MolajoFactory::getConfig()->get('sitename', 'Molajo'));
            $session->set('page.subtitle', '');
            $session->set('page.metakey', '');
            $session->set('page.metadesc', '');
            $session->set('page.metadata', '');
            $session->set('page.parameters', '');
        }

        /** Set Document Information */
        $classname = 'Molajo' . ucfirst(MOLAJO_APPLICATION) . 'Application';
        $appClass = new $classname();
        $document = MolajoFactory::getDocument();

        $menus = $appClass->getMenu();
        if ($menus == null) {
            $menu = false;
            $id = 0;
        } else {
            $menu = $menus->getActive();
            $id = (int)@$menu->query['id'];
        }

        $pathway = $appClass->getPathway();
        $title = null;
        $parameters = MolajoComponent::getParameters($session->get('page.option'));

        //        $title = $this->parameters->get('page_title', '');

        if (empty($title)) {
            $title = $session->get('page.title');
        }
        if (empty($title)) {
            $title = MolajoFactory::getConfig()->get('sitename', 'Molajo');
        }

        if (MolajoFactory::getConfig()->get('sitename_pagetitles', 0) == 1) {
            $title = MolajoTextHelper::sprintf('JPAGETITLE', MolajoFactory::getConfig()->get('sitename', 'Molajo'), $title);

        } elseif (MolajoFactory::getConfig()->get('sitename_pagetitles', 0) == 2) {
            $title = MolajoTextHelper::sprintf('JPAGETITLE', $title, MolajoFactory::getConfig()->get('sitename', 'Molajo'));
        }

        $document->setTitle($title);
        $document->setDescription($session->get('page.metadesc'));
        $document->setMetadata('keywords', $session->get('page.metakey'));
        $document->setMetadata('robots', $session->get('page.robots'));

        $metadata = explode(',', $session->get('page.metadata'));
        foreach ($metadata as $k => $v) {
            if ($v) {
                $document->setMetadata($k, $v);
            }
        }

        //        if ($this->parameters->get('show_feed_link', 1)) {
        //            $link = '&format=feed&limitstart=';
        //            $attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
        //            $document->addHeadLink(MolajoRouteHelper::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
        //            $attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
        //            $document->addHeadLink(MolajoRouteHelper::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
        //        }

        //        $session->set('page.parameters', $this->parameters);
        $session->set('page.parameters', array());
        $session->set('page.wrap', '');
        $session->set('page.position', 'component');

        // Load the parameters. Merge Global and Menu Item parameters into new object
        //		$parameters = $app->getParameters();
        //		$menuParameters = new JRegistry;

        //		if ($menu = $app->getMenu()->getActive()) {
        //			$menuParameters->loadString($menu->parameters);
        //		}

        //		$mergedParameters = clone $menuParameters;
        //		$mergedParameters->merge($parameters);

        return;
    }
}
