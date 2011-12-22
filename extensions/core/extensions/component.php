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
     * Asset
     *
     * @var    array
     * @since  1.0
     */
    public $asset = array();

    /**
     * Option
     *
     * @var    array
     * @since  1.0
     */
    public $option = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   array  $asset  A configuration array
     *
     * @since  1.0
     */
    public function __construct($asset)
    {
        /** configuration */
        $this->asset = $asset;

        $this->option = $this->asset->option;

        $this->parameters = new JRegistry;
        $this->parameters = $this->asset->source_parameters;

        echo '<pre>';
        var_dump($this->asset);
        echo '</pre>';
    }

    /**
     * getRequest
     *
     * Gets the Request Object and populates Page Session Variables for Component
     *
     * @return bool
     */
    public function getRequest()
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
        $task = $this->asset->task;
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
            $view = $this->asset->view;
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
            $layout = $this->asset->layout;
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
            $layout = $this->asset->layout;
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
            $format = $this->asset->format;
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
        $id = $this->asset->id;
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
        $session->set('page.item_id', $this->asset->id);

        $session->set('page.controller', $controller);
        $session->set('page.extension_type', 'component');
        $session->set('page.option', $this->option);
        $session->set('page.view', $view);
        $session->set('page.model', $model);
        $session->set('page.layout', $layout);

        $session->set('page.wrap', $this->parameters->wrap);
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
        $session->set('page.extension', $this->option);
        $session->set('page.component_specific', $component_specific);

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
        $request['option'] = $session->get('page.option');

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

        /** Return output */
        return $output;
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
            $session->set('page.title', MolajoFactory::getApplication()->getConfig->get('sitename', 'Molajo'));
            $session->set('page.subtitle', '');
            $session->set('page.metakey', '');
            $session->set('page.metadesc', '');
            $session->set('page.metadata', '');
            $session->set('page.parameters', '');
        }

        /** Set Document Information */
        $document = MolajoFactory::getDocument();

        $menus = MolajoFactory::getMenu();
        if ($menus == null) {
            $menu = false;
            $id = 0;
        } else {
            $menu = $menus->getActive();
            $id = (int)@$menu->query['id'];
        }

        $pathway = MolajoFactory::getPathway();
        $title = null;
        $parameters = MolajoComponent::getParameters($session->get('page.option'));

        //        $title = $this->parameters->get('page_title', '');

        if (empty($title)) {
            $title = $session->get('page.title');
        }
        if (empty($title)) {
            $title = MolajoFactory::getApplication()->getConfig->get('sitename', 'Molajo');
        }

        if (MolajoFactory::getApplication()->getConfig->get('sitename_pagetitles', 0) == 1) {
            $title = MolajoTextHelper::sprintf('JPAGETITLE', MolajoFactory::getApplication()->getConfig->get('sitename', 'Molajo'), $title);

        } elseif (MolajoFactory::getApplication()->getConfig->get('sitename_pagetitles', 0) == 2) {
            $title = MolajoTextHelper::sprintf('JPAGETITLE', $title, MolajoFactory::getApplication()->getConfig->get('sitename', 'Molajo'));
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
        //		$parameters = MolajoFactory::getApplication()->getParameters();
        //		$menuParameters = new JRegistry;

        //		if ($menu = MolajoFactory::getApplication()->getMenu()->getActive()) {
        //			$menuParameters->loadString($menu->parameters);
        //		}

        //		$mergedParameters = clone $menuParameters;
        //		$mergedParameters->merge($parameters);

        return;
    }
}
