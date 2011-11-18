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
 * MolajoXYZHelper
 * 
 * Processes requests for various Molajo extension types:
 * 
 * MOLAJO_CONTENT_TYPE_EXTENSION_COMPONENTS 1
 * MOLAJO_CONTENT_TYPE_EXTENSION_MENU 5
 * MOLAJO_CONTENT_TYPE_EXTENSION_MODULES 6
 * MOLAJO_CONTENT_TYPE_EXTENSION_PLUGINS 8
 * MOLAJO_CONTENT_TYPE_EXTENSION_TEMPLATES 9
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoXYZHelper
{
    /**
     * isEnabled
     *
     * Checks if an extension is enabled
     *
     * @param   string  The extension name
     *
     * @return  boolean
     */
    public static function isEnabled($extension)
    {
        $result = self::getXYZ($extension);
        return (!is_null($result));
    }
    
    /**
     * getXYZ
     *
     * Get Extension Instance by Type and Name, Title or ID 
     * 
     * @static
     * @param $content_type_id
     * @param $extension
     * 
     * @return null|stdClass
     */
    public static function getXYZ($extension)
    {
        $result = null;
        
        $extensions = self::_load();

        $total = count($extensions);
        for ($i = 0; $i < $total; $i++)
        {
            // Match the name of the extension
            if ($extensions[$i]->name == $extension) {
                // Match the title if we're looking for a specific instance of the extension
                if (!$title || $extensions[$i]->title == $title) {
                    $result = &$extensions[$i];
                    break; // Found it
                }
            }
        }

        // If we didn't find it, and the name is mod_something, create a dummy object
        if (is_null($result) && substr($extension, 0, 4) == 'mod_') {
            $result = new stdClass;
            $result->id = 0;
            $result->title = '';
            $result->subtitle = '';
            $result->extension = $extension;
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
     * getXYZs
     *
     * Get extensions by position
     *
     * @param   string  $position The position of the extension
     *
     * @return  array  An array of extension objects
     */
    public static function getXYZs($position)
    {
        $result = array();

        $extensions = self::_load();

        for ($i = 0; $i < count($extensions); $i++) {
            if (strtolower($extensions[$i]->position) == strtolower($position)) {
                $result[] = &$extensions[$i];
            }
        }
        
        if (count($result) == 0) {

            if (JRequest::getBool('tp')
                && MolajoComponentHelper::getParameters('com_templates')->get('template_positions_display')
            ) {
                $result[0] = self::getXYZ('mod_'.$position);
                $result[0]->title = $position;
                $result[0]->content = $position;
                $result[0]->position = $position;
            }
        }

        return $result;
    }

    /**
     * _load
     *
     * Load published extensions
     *
     * @return  array
     */
    protected function &_load()
    {
        static $clean;

        if (isset($clean)) {
            return $clean;
        }

        $Itemid = JRequest::getInt('Itemid');
        $user = MolajoFactory::getUser();
        $lang = MolajoFactory::getLanguage()->getTag();
        $application_id = MOLAJO_APPLICATION_ID;

        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoFactory::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();

        $query->select('m.id as id, title, title as subtitle ');
        $query->select('extension, position, content, showtitle ');
        $query->select('showtitle, showtitle as showsubtitle, parameters, mm.menu_item_id');
        $query->from('#__extensions AS m');
        $query->join('inner', '#__extensions_menu AS mm');
        $query->where('mm.extension_id = m.id');
        $query->where('m.published = 1');
        $query->where('m.id <> 1');
        $query->where('(m.start_publishing_datetime = '.$db->Quote($nullDate).' OR m.start_publishing_datetime <= '.$db->Quote($now).')');
        $query->where('(m.stop_publishing_datetime = '.$db->Quote($nullDate).' OR m.stop_publishing_datetime >= '.$db->Quote($now).')');

        $acl = new MolajoACL ();
        $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'm'));

        $query->where('m.application_id = '.MOLAJO_APPLICATION_ID);
        $query->where('(mm.menu_item_id = '.(int) $Itemid
                       .'OR mm.menu_item_id <= 0)');

        $query->where('m.language IN ('.$db->Quote($lang).','.$db->Quote('*').')');

        $query->order('position, ordering');

        $db->setQuery($query->__toString());

        $extensions = $db->loadObjectList();
    
        if ($db->getErrorNum()) {
            MolajoError::raiseWarning(500, MolajoText::sprintf('MOLAJO_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
            return;
        }

        return $extensions;
    }

    /**
     * XYZ cache helper
     *
     * Caching modes:
     * To be set in XML:
     * 
     * @param   object  $extension    XYZ object
     * @param   object  $extensionparameters extension parameters
     * @param   object  $cacheparameters extension cache parameters - id or url parameters, depending on the extension cache mode
     * @param   array   $parameters - parameters for given mode - calculated id or an array of safe url parameters and their
     *                     variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @since   11.1
     */
    public static function extensionCache($extension, $extensionparameters, $cacheparameters)
    {

    }

    /**
     * renderXYZ
     *
     * Render the extension.
     *
     * @param   object  A extension object.
     * @param   array   An array of attributes for the extension (probably from the XML).
     *
     * @return  string  The HTML content of the extension output.
     */
    public static function renderXYZ($extension, $attribs = array())
    {
        $output = '';

        // Record the scope.
        $scope = MolajoFactory::getApplication()->scope;

        // Set scope to extension name
        MolajoFactory::getApplication()->scope = $extension->extension;

        // Get extension path
        $extension->extension = preg_replace('/[^A-Z0-9_\.-]/i', '', $extension->extension);
        $path = MOLAJO_EXTENSION_MODULES.'/'.$extension->extension.'/'.$extension->extension.'.php';

        // Load the extension
        if ($extension->user) {
        } else if (file_exists($path)) {

            $lang = MolajoFactory::getLanguage();

            $lang->load($extension->extension, MOLAJO_BASE_FOLDER, null, false, false)
            || $lang->load($extension->extension, dirname($path), null, false, false)
            || $lang->load($extension->extension, MOLAJO_BASE_FOLDER, $lang->getDefault(), false, false)
            || $lang->load($extension->extension, dirname($path), $lang->getDefault(), false, false);

            /** view */
            $view = new MolajoView ();

            /** defaults */
            $request = array();
            $state = array();
            $parameters = array();
            $rowset = array();
            $pagination = array();
            $layout = 'default';
            $wrap = 'none';

            $application = MolajoFactory::getApplication();
            $document = MolajoFactory::getDocument();
            $user = MolajoFactory::getUser();

            $parameters = new JRegistry;
            $parameters->loadJSON($extension->parameters);

            $request = self::getRequest($extension, $parameters);

            $request['wrap_title'] = $extension->title;
            $request['wrap_subtitle'] = $extension->subtitle;
            $request['wrap_id'] = '';
            $request['wrap_class'] = '';
            $request['wrap_date'] = '';
            $request['wrap_author'] = '';
            $request['position'] = $extension->position;
            $request['wrap_more_array'] = array();

            /** execute the extension */
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
            $view->state = $extension;

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
    protected function getRequest($extension, $parameters)
    {
        $session = MolajoFactory::getSession();

        /** 1. Request */
        $request = array();
        $request['application_id'] = $session->get('page.application_id');
        $request['current_url'] = $session->get('page.current_url');
        $request['component_path'] = $session->get('page.component_path');
        $request['base_url'] = $session->get('page.base_url');
        $request['item_id'] = $session->get('page.item_id');

        $request['controller'] = 'extension';
        $request['extension_type'] = 'extension';
        $request['option'] = $session->get('page.option');
        $request['no_com_option'] = $session->get('page.no_com_option');
        $request['view'] = 'extension';
        $request['model'] = 'extension';
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
        $request['title'] = $extension->title;
        $request['subtitle'] = $extension->subtitle;
        $request['metakey'] = $session->get('page.metakey');
        $request['metadesc'] = $session->get('page.metadesc');
        $request['metadata'] = $session->get('page.metadata');
        $request['wrap'] = $extension->style;
        $request['position'] = $extension->position;

        return $request;
    }    
}
