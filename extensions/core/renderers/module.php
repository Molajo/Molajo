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
 * MolajoModule
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
class MolajoModule
{
    /**
     *  Module Output
     *
     * @var array
     * @since 1.0
     */
    protected $module_output = null;

    /**
     *  Parameters
     *
     * @var array
     * @since 1.0
     */
    protected $parameters = null;

    /**
     *  Config
     *
     * @var array
     * @since 1.0
     */
    protected $config = null;

    public function __construct($parameters = array(), $config = null)
    {
        $this->parameters = $this->parameters;
        $this->config = $config;
    }

    /**
     * getModule
     *
     * Get module by name (real, eg 'Breadcrumbs' or folder, eg 'breadcrumbs')
     *
     * @param   string  The name of the module
     * @param   string  The title of the module, optional
     *
     * @return  object  The Module object
     */
    public static function getModule($name, $title = null)
    {
        $result = null;
        $modules = self::_load();

        $total = count($modules);
        for ($i = 0; $i < $total; $i++)
        {
            // Match the name of the module
            if ($modules[$i]->title == $name) {
                // Match the title if we're looking for a specific instance of the module
                if (!$title || $modules[$i]->title == $title) {
                    $result = &$modules[$i];
                    break; // Found it
                }
            }
        }

        // If we didn't find it, and the name is something, create a dummy object
        if (is_null($result)) {
            $result = new stdClass;
            $result->id = 0;
            $result->title = '';
            $result->subtitle = '';
            $result->module = $name;
            $result->position = '';
            $result->content = '';
            $result->showtitle = 0;
            $result->showsubtitle = 0;
            $result->control = '';
            $result->parameters = '';
            $result->user = 0;
        }

        return $result;
    }

    /**
     * getModules
     *
     * Get modules by position
     *
     * @param   string  $position    The position of the module
     *
     * @return  array  An array of module objects
     */
    public static function getModules($position)
    {
        $position = strtolower($position);
        $result = array();

        $modules = self::_load();

        $total = count($modules);
        for ($i = 0; $i < $total; $i++)
        {
            if ($modules[$i]->position == $position) {
                $result[] = &$modules[$i];
            }
        }
        if (count($result) == 0) {

            if (JRequest::getBool('tp')
                && MolajoComponent::getParameters('templates')->get('template_positions_display')
            ) {
                $result[0] = self::getModule('' . $position);
                $result[0]->title = $position;
                $result[0]->content = $position;
                $result[0]->position = $position;
            }
        }

        return $result;
    }

    /**
     * isEnabled
     *
     * Checks if a module is enabled
     *
     * @param   string  The module name
     *
     * @return  boolean
     */
    public static function isEnabled($module)
    {
        $result = self::getModule($module);
        return (!is_null($result));
    }

    /**
     * renderModule
     *
     * Render the module.
     *
     * @param   object  A module object.
     * @param   array   An array of attributes for the module (probably from the XML).
     *
     * @return  string  The HTML content of the module output.
     */
    public function render($module_object)
    {
        $output = '';
        $this->module_object = $module_object;
        $module = $module_object->module;

        //echo '<pre>';var_dump($module);'</pre>';

        // Get module path
        $module->title = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->title);
        $path = MOLAJO_EXTENSIONS_MODULES . '/' . $module->extension_name . '/' . $module->extension_name . '.php';

        // Load the module
        if (file_exists($path)) {

            $lang = MolajoFactory::getLanguage();
            $lang->load($module->extension_name, MOLAJO_EXTENSIONS_MODULES . '/' . $module->extension_name, $lang->getDefault(), false, false);

            /** view */
            $view = new MolajoView ();

            /** defaults */
            $request = array();
            $state = array();
            $rowset = array();
            $pagination = array();
            $layout = 'default';
            if (isset($this->parameters->wrap)) {
                $wrap = $this->parameters->wrap;
            } else {
                $wrap = 'none';
            }

            $application = MolajoFactory::getApplication();
            $document = MolajoFactory::getDocument();
            $user = MolajoFactory::getUser();

            $this->parameters = new JRegistry;
            $this->parameters->loadString($module->parameters);

            $request = self::getRequest($module, $this->parameters, $wrap);

            $request['wrap_title'] = $module->title;
            $request['wrap_subtitle'] = $module->subtitle;
            $request['wrap_id'] = '';
            $request['wrap_class'] = '';
            $request['wrap_date'] = '';
            $request['wrap_author'] = '';
            $request['position'] = $module->position;
            $request['wrap_more_array'] = array();

            /** execute the module */
            include $path;

            /** 1. Application */
            $view->app = $application;

            /** 2. Document */
            $view->document = $document;

            /** 3. User */
            $view->user = $user;

            /** 4. Request */
            $view->request = $request;

            /** 5. State */
            $view->state = $module;

            /** 6. Parameters */
            $view->parameters = $this->parameters;

            /** 7. Query */
            $view->rowset = $rowset;

            /** 8. Pagination */
            $view->pagination = $pagination;

            /** 9. Layout Type */
            $view->layout_type = 'extensions';

            /** 10. Layout */
            $view->layout = $layout;

            /** 11. Wrap */
            $view->wrap = $wrap;

            /** display view */
            ob_start();
            $view->display();
            $output = ob_get_contents();
            ob_end_clean();
        }

        return $output;
    }

    /**
     * getRequest
     *
     * Gets the Request Object and populates Page Session Variables for Component
     *
     * @return bool
     */
    protected function getRequest($module, $parameters, $wrap)
    {
        $session = MolajoFactory::getSession();

        /** 1. Request */
        $request = array();
        $request['application_id'] = $session->get('page.application_id');
        $request['current_url'] = $session->get('page.current_url');
        $request['component_path'] = $session->get('page.component_path');
        $request['base_url'] = $session->get('page.base_url');
        $request['item_id'] = $session->get('page.item_id');

        $request['controller'] = 'module';
        $request['extension_type'] = 'module';
        $request['option'] = $session->get('page.option');
        $request['view'] = 'module';
        $request['model'] = 'module';
        $request['task'] = 'display';
        $request['format'] = 'html';
        $request['plugin_type'] = 'content';

        $request['id'] = $session->get('page.id');
        $request['cid'] = $session->get('page.cid');
        $request['catid'] = $session->get('page.catid');
        $request['parameters'] = $this->parameters;

        $request['acl_implementation'] = $session->get('page.acl_implementation');
        $request['component_table'] = $session->get('page.component_table');
        $request['filter_name'] = $session->get('page.filter_name');
        $request['select_name'] = $session->get('page.select_name');
        $request['title'] = $module->title;
        $request['subtitle'] = $module->subtitle;
        $request['metakey'] = $session->get('page.metakey');
        $request['metadesc'] = $session->get('page.metadesc');
        $request['metadata'] = $session->get('page.metadata');
        $request['wrap'] = $wrap;
        $request['position'] = $module->position;

        return $request;
    }

    /**
     * _load
     *
     * Load published modules
     *
     * @return  array
     */
    protected function &_load()
    {
        static $modules;

        if (isset($modules)) {
            return $modules;
        }

        $modules = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_MODULE);

        return $modules;
    }

    /**
     * Module cache helper
     */
    public static function moduleCache($module, $moduleparameters, $cacheparameters)
    {

    }
}
