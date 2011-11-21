<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoModuleHelper
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoModuleHelper
{
    /**
     * getModule
     *
     * Get module by name (real, eg 'Breadcrumbs' or folder, eg 'mod_breadcrumbs')
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
            if ($modules[$i]->name == $name) {
                // Match the title if we're looking for a specific instance of the module
                if (!$title || $modules[$i]->title == $title) {
                    $result = &$modules[$i];
                    break; // Found it
                }
            }
        }

        // If we didn't find it, and the name is mod_something, create a dummy object
        if (is_null($result) && substr($name, 0, 4) == 'mod_') {
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
        $app = MolajoFactory::getApplication();
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
                && MolajoComponentHelper::getParameters('com_templates')->get('template_positions_display')
            ) {

                $result[0] = self::getModule('mod_'.$position);
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
    public static function renderModule($module, $attribs = array())
    {
        $output = '';

        // Record the scope.
        $scope = MolajoFactory::getApplication()->scope;

        // Set scope to module name
        MolajoFactory::getApplication()->scope = $module->title;

        // Get module path
        $module->title = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->title);
        $path = MOLAJO_EXTENSION_MODULES.'/'.$module->extension_name.'/'.$module->extension_name.'.php';

        // Load the module
        if (file_exists($path)) {

            $lang = MolajoFactory::getLanguage();
            $lang->load($module->extension_name, MOLAJO_EXTENSION_MODULES.'/'.$module->extension_name, $lang->getDefault(), false, false);
            
            /** view */
            $view = new MolajoView ();

            /** defaults */
            $request = array();
            $state = array();
            $rowset = array();
            $pagination = array();
            $layout = 'default';
            if (isset($attribs->wrap)) {
                $wrap = $attribs->wrap;
            } else {
                $wrap = 'none';
            }

            $application = MolajoFactory::getApplication();
            $document = MolajoFactory::getDocument();
            $user = MolajoFactory::getUser();

            $parameters = new JRegistry;
            $parameters->loadString($module->parameters);

            $request = self::getRequest($module, $parameters, $wrap);

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
            $view->parameters = $parameters;

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
        $request['no_com_option'] = $session->get('page.no_com_option');
        $request['view'] = 'module';
        $request['model'] = 'module';
        $request['task'] = 'display';
        $request['format'] = 'html';
        $request['plugin_type'] = 'content';

        $request['id'] = $session->get('page.id');
        $request['cid'] = $session->get('page.cid');
        $request['catid'] = $session->get('page.catid');
        $request['parameters'] = $parameters;

        $request['acl_implementation'] = $session->get('page.acl_implementation');
        $request['component_table'] = $session->get('page.component_table');
        $request['filter_fieldname'] = $session->get('page.filter_fieldname');
        $request['select_fieldname'] = $session->get('page.select_fieldname');
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

        $modules = MolajoExtensionHelper::getExtensions(MOLAJO_CONTENT_TYPE_EXTENSION_MODULES);

        return $modules;
    }

    /**
     * Module cache helper
     *
     * Caching modes:
     * To be set in XML:
     * 'static'        one cache file for all pages with the same module parameters
     * 'oldstatic'    1.5. definition of module caching, one cache file for all pages with the same module id and user aid,
     * 'itemid'        changes on itemid change,
     * To be called from inside the module:
     * 'safeuri'        id created from $cacheparameters->modeparameters array,
     * 'id'            module sets own cache id's
     *
     * @param   object  $module    Module object
     * @param   object  $moduleparameters module parameters
     * @param   object  $cacheparameters module cache parameters - id or url parameters, depending on the module cache mode
     * @param   array   $parameters - parameters for given mode - calculated id or an array of safe url parameters and their
     *                     variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @since   11.1
     */
    public static function moduleCache($module, $moduleparameters, $cacheparameters)
    {
        if (!isset ($cacheparameters->modeparameters)) {
            $cacheparameters->modeparameters = null;
        }

        if (!isset ($cacheparameters->cachegroup)) {
            $cacheparameters->cachegroup = $module->title;
        }

        $user = MolajoFactory::getUser();
        $cache = MolajoFactory::getCache($cacheparameters->cachegroup, 'callback');
        $conf = MolajoFactory::getConfig();

        // Turn cache off for internal callers if parameters are set to off and for all logged in users
        if ($moduleparameters->get('owncache', null) === 0 || $conf->get('caching') == 0 || $user->get('id')) {
            $cache->setCaching(false);
        }

        $cache->setLifeTime($moduleparameters->get('cache_time', $conf->get('cachetime') * 60));

        $wrkaroundoptions = array(
            'nopathway' => 1,
            'nohead' => 0,
            'nomodules' => 1,
            'modulemode' => 1,
            'mergehead' => 1
        );

        $wrkarounds = true;

        $acl = new MolajoACL();
        $view_levels = md5(serialize($acl->getList('viewaccess')));

        switch ($cacheparameters->cachemode) {

            case 'id':
                $ret = $cache->get(array($cacheparameters->class, $cacheparameters->method), $cacheparameters->methodparameters, $cacheparameters->modeparameters, $wrkarounds, $wrkaroundoptions);
                break;

            case 'safeuri':
                $secureid = null;
                if (is_array($cacheparameters->modeparameters)) {
                    $uri = JRequest::get();
                    $safeuri = new stdClass();
                    foreach ($cacheparameters->modeparameters AS $key => $value) {
                        // Use int filter for id/catid to clean out spamy slugs
                        if (isset($uri[$key])) {
                            $safeuri->$key = JRequest::_cleanVar($uri[$key], 0, $value);
                        }
                    }
                }
                $secureid = md5(serialize(array($safeuri, $cacheparameters->method, $moduleparameters)));
                $ret = $cache->get(array($cacheparameters->class, $cacheparameters->method), $cacheparameters->methodparameters, $module->id.$view_levels.$secureid, $wrkarounds, $wrkaroundoptions);
                break;

            case 'static':
                $ret = $cache->get(array($cacheparameters->class, $cacheparameters->method), $cacheparameters->methodparameters, $module->title.md5(serialize($cacheparameters->methodparameters)), $wrkarounds, $wrkaroundoptions);
                break;

            case 'oldstatic': // provided for backward compatibility, not really usefull
                $ret = $cache->get(array($cacheparameters->class, $cacheparameters->method), $cacheparameters->methodparameters, $module->id.$view_levels, $wrkarounds, $wrkaroundoptions);
                break;

            case 'itemid':
            default:
                $ret = $cache->get(array($cacheparameters->class, $cacheparameters->method), $cacheparameters->methodparameters, $module->id.$view_levels.JRequest::getVar('Itemid', null, 'default', 'INT'), $wrkarounds, $wrkaroundoptions);
                break;
        }

        return $ret;
    }
}
