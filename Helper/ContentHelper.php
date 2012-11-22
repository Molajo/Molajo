<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Helper;

use Molajo\Helpers;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Content Helper
 *
 * Retrieves Item, List, or Menu Item Parameters for Route from Content, Extension, and Menu Item
 *
 * @package      Molajo
 * @subpackage   Helper
 * @since        1.0
 */
Class ContentHelper
{
    /**
     * Static instance
     *
     * @var     object
     * @since   1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return  bool|object
     * @since   1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ContentHelper();
        }

        return self::$instance;
    }

    /**
     * Retrieves List Route information
     *
     * @param   $id
     * @param   $model_type
     * @param   $model_name
     *
     * @return  boolean
     * @since   1.0
     */
    public function getRouteList($id, $model_type, $model_name)
    {
        $item = $this->get($id, $model_type, $model_name, QUERY_OBJECT_ITEM);
        if (count($item) == 0) {
            return Services::Registry()->set('Parameters', 'status_found', false);
        }

        Services::Registry()->set('Parameters', 'extension_instance_id', (int)$item->id);
        Services::Registry()->set('Parameters', 'extension_title', $item->title);
        Services::Registry()->set('Parameters', 'extension_translation_of_id', (int)$item->translation_of_id);
        Services::Registry()->set('Parameters', 'extension_language', $item->language);
        Services::Registry()->set('Parameters', 'extension_catalog_type_id', (int)$item->catalog_type_id);
        Services::Registry()->set('Parameters', 'extension_modified_datetime', $item->modified_datetime);
        Services::Registry()->set('Parameters', 'extension_catalog_type_title', $item->catalog_types_title);
        Services::Registry()->set('Parameters', 'catalog_type_id', $item->catalog_type_id);
        Services::Registry()->set('Parameters', 'content_type', (int)$item->page_type);
        Services::Registry()->set('Parameters', 'primary_category_id', $item->catalog_primary_category_id);
        Services::Registry()->set('Parameters', 'source_id', (int)$item->id);

        $this->setParameters(
            QUERY_OBJECT_LIST,
            $item->model_registry . 'Parameters',
            $item->model_registry . 'Metadata'
        );

        $this->setExtensionPaths();

        return true;
    }

    /**
     * Retrieve Route information for a specific Content Item for either Display or Editing
     *
     * @return   boolean
     * @since    1.0
     */
    public function getRouteItem($id, $model_type, $model_name)
    {
        if (strtolower(Services::Registry()->get('Parameters', 'request_action')) == ACTION_VIEW) {
            $pageTypeNamespace = 'item';
        } else {
            $pageTypeNamespace = 'form';
        }

        $item = $this->get($id, $model_type, $model_name, $pageTypeNamespace);
        if (count($item) == 0) {
            return Services::Registry()->set('Parameters', 'status_found', false);
        }

        Services::Registry()->set(
            PRIMARY_QUERY_RESULTS_MODEL_NAME,
            PRIMARY_QUERY_RESULTS_MODEL_NAME_RESULTS,
            array($item)
        );

        if (isset($item->extension_instance_id)) {
            $extension_instance_id = (int)$item->extension_instance_id;
            $extension_instance_catalog_type_id = (int)$item->catalog_catalog_type_id;
        } else {
            $extension_instance_id = (int)$item->catalog_extension_instance_id;
            $extension_instance_catalog_type_id = (int)$item->catalog_catalog_type_id;
        }

        Services::Registry()->set('Parameters', 'extension_instance_id', $extension_instance_id);
        Services::Registry()->set('Parameters', 'extension_catalog_type_id', $extension_instance_catalog_type_id);
        Services::Registry()->set('Parameters', 'criteria_extension_instance_id', (int)$extension_instance_id);
        Services::Registry()->set('Parameters', 'criteria_source_id', (int)$item->id);
        Services::Registry()->set('Parameters', 'criteria_catalog_type_id', (int)$item->catalog_type_id);

        $parameterNamespace = $item->model_registry . 'Parameters';

        $this->getResourceExtensionParameters((int)$extension_instance_id);

        $this->setParameters(
            $pageTypeNamespace,
            $item->model_registry . 'Parameters',
            $item->model_registry . 'Metadata',
            'ResourcesSystem'
        );

        $parent_menu_id = Services::Registry()->get(
            'ResourcesSystemParameters',
            $pageTypeNamespace . '_parent_menu_id'
        );

        Services::Registry()->set('Parameters', 'parent_menu_id', $parent_menu_id);

        $this->setExtensionPaths();

        if ($pageTypeNamespace == 'form') {
            Services::Registry()->set('Parameters', 'page_type', 'Edit');
        }

        return true;
    }

    /**
     * Retrieves the Menu Item Route information
     *
     * @return  boolean
     * @since   1.0
     */
    public function getRouteMenuitem()
    {
        $item = $this->get(
            Services::Registry()->get('Parameters', 'catalog_source_id'),
            CATALOG_TYPE_MENUITEM_LITERAL,
            Services::Registry()->get('Parameters', 'catalog_page_type'),
            CATALOG_TYPE_MENUITEM_LITERAL
        );

        if (count($item) == 0) {
            return Services::Registry()->set('Parameters', 'status_found', false);
        }

        Services::Registry()->set('Parameters', 'menuitem_lvl', (int)$item->lvl);
        Services::Registry()->set('Parameters', 'menuitem_title', $item->title);
        Services::Registry()->set('Parameters', 'menuitem_parent_id', $item->parent_id);
        Services::Registry()->set('Parameters', 'menuitem_translation_of_id', (int)$item->translation_of_id);
        Services::Registry()->set('Parameters', 'menuitem_language', $item->language);
        Services::Registry()->set('Parameters', 'menuitem_catalog_type_id', (int)$item->catalog_type_id);
        Services::Registry()->set('Parameters', 'menuitem_catalog_type_title', $item->catalog_types_title);
        Services::Registry()->set('Parameters', 'menuitem_modified_datetime', $item->modified_datetime);
        Services::Registry()->set('Parameters', 'menu_id', (int)$item->extension_id);
        Services::Registry()->set('Parameters', 'menu_title', $item->extensions_name);
        Services::Registry()->set('Parameters', 'menu_extension_id', (int)$item->extensions_id);
        Services::Registry()->set('Parameters', 'menu_path_node', $item->extensions_name);

        $registry = Services::Registry()->get('Parameters', 'catalog_page_type')
            . CATALOG_TYPE_MENUITEM_LITERAL;

        Services::Registry()->set(
            'Parameters',
            'criteria_source_id',
            (int)Services::Registry()->get($registry . 'Parameters', 'criteria_source_id')
        );
        Services::Registry()->set(
            'Parameters',
            'criteria_catalog_type_id',
            (int)Services::Registry()->get($registry . 'Parameters', 'criteria_catalog_type_id')
        );
        Services::Registry()->set(
            'Parameters',
            'criteria_extension_instance_id',
            (int)Services::Registry()->get($registry . 'Parameters', 'criteria_extension_instance_id')
        );

        Services::Registry()->copy($registry . 'Parameters', 'Parameters');
        Services::Registry()->copy($registry . 'Metadata', 'Metadata');

        $this->setParameters(
            strtolower(CATALOG_TYPE_MENUITEM_LITERAL),
            $registry . 'Parameters',
            $registry . 'Metadata'
        );

        /** Must be after parameter set so as to not strip off menuitem */
        Services::Registry()->set('Parameters', 'menuitem_id', (int)$item->id);
        Services::Registry()->set(
            'Parameters',
            'page_type',
            Services::Registry()->get('Parameters', 'catalog_page_type')
        );

        $this->setExtensionPaths();

        return true;
    }

    /**
     * Get data for Menu Item, Item or List
     *
     * @param   $id
     * @param   $model_type
     * @param   $model_name
     * @param   $page_type
     *
     * @return  array  An object containing an array of data
     * @since   1.0
     */
    public function get($id = 0, $model_type = 'Datasource', $model_name = 'Content', $page_type = '')
    {
        Services::Profiler()->set(
            'ContentHelper->get '
                . ' ID: ' . $id
                . ' Model Type: ' . $model_type
                . ' Model Name: ' . $model_name
                . ' Page Type : ' . $page_type,
            LOG_OUTPUT_ROUTING,
            VERBOSE
        );

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry($model_type, $model_name);
        $controller->setDataobject(QUERY_OBJECT_ITEM);

        $controller->set('id', (int)$id);
        $controller->set('process_plugins', 1);

        if ($page_type == QUERY_OBJECT_ITEM) {
            $controller->set('get_customfields', 2);
        } else {
            $controller->set('get_customfields', 1);
        }

        /** Regardless of page_type, this query returns only one row */
        $item = $controller->getData(QUERY_OBJECT_ITEM);
        if ($item === false || $item === null || count($item) == 0) {
            return array();
        }

        $item->model_registry = $controller->model_registry;

        return $item;
    }

    /**
     * Determines parameter values from primary item (form, item, list, or menuitem)
     *  Extension and Application defaults applied following item values
     *
     * @param   $pageTypeNamespace (ex. item, list, menuitem)
     * @param   $parameterNamespace (ex. $item->model_registry . 'Parameters')
     * @param   $metadataNamespace (ex. $item->model_registry . 'Metadata')
     * @param   string $resourceNamespace For extension (ex. ResourcesSystem)
     *
     * @return  boolean
     * @since   1.0
     */
    public function setParameters(
        $pageTypeNamespace,
        $parameterNamespace,
        $metadataNamespace,
        $resourceNamespace = ''
    ) {
        Services::Registry()->set('Parameters', 'page_type', $pageTypeNamespace);

        /** Retrieve array of Extension Instances Authorised for User  */
        Helpers::Extension()->setAuthorisedExtensions(0, 'Datasource', 'ExtensionInstances', QUERY_OBJECT_LIST);

        /** I. Priority 1 - Item parameter values (be it an item, menu item, list) */
        $newParameters = Services::Registry()->get($parameterNamespace, $pageTypeNamespace . '*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $pageTypeNamespace);
        }

        $newParameters = Services::Registry()->get($parameterNamespace, 'criteria*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $pageTypeNamespace);
        }

        $newParameters = Services::Registry()->get($parameterNamespace, 'enable*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $pageTypeNamespace);
        }

        /** II. Next, Extension level defaults */
        if ($resourceNamespace == '') {
        } else {

            $newParameters = Services::Registry()->get($resourceNamespace . 'Parameters', $pageTypeNamespace . '*');
            if (is_array($newParameters) && count($newParameters) > 0) {
                $this->processParameterSet($newParameters, $pageTypeNamespace);
            }

            $newParameters = Services::Registry()->get($resourceNamespace . 'Parameters', 'criteria*');
            if (is_array($newParameters) && count($newParameters) > 0) {
                $this->processParameterSet($newParameters, $pageTypeNamespace);
            }

            $newParameters = Services::Registry()->get($resourceNamespace . 'Parameters', 'enable*');
            if (is_array($newParameters) && count($newParameters) > 0) {
                $this->processParameterSet($newParameters, $pageTypeNamespace);
            }
        }

        /** III. Finally, Application level defaults */
        $applicationDefaults = Services::Registry()->get('Configuration', $pageTypeNamespace . '*');
        if (count($applicationDefaults) > 0) {
            $this->processParameterSet($applicationDefaults, $pageTypeNamespace);
        }

        /** Merge in the rest */
        Services::Registry()->merge($parameterNamespace, 'Parameters', true);

        /** Metadata defaulting */
        Services::Registry()->merge($metadataNamespace, 'Metadata');

        if ($resourceNamespace == '') {
        } else {
            Services::Registry()->merge($resourceNamespace . 'Metadata', 'Metadata', true);
        }

        Services::Registry()->merge('Configuration', 'Parameters', true);

        /** Hold parameters while registry is used during Theme and View assignment */
        $savedParameters = array();
        $temp = Services::Registry()->getArray('Parameters');
        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {
                if ($value === 0 || trim($value) == '' || $value === null) {
                } else {
                    $savedParameters[$key] = $value;
                }
            }
        }

        /** Set Theme and View values */
        Helpers::Extension()->setThemePageView();

        Helpers::Extension()->setTemplateWrapModel();

        /** Merge held parameters back in */
        if (is_array($savedParameters) && count($savedParameters) > 0) {
            foreach ($savedParameters as $key => $value) {
                Services::Registry()->set('Parameters', $key, $value);
            }
        }

        Services::Registry()->sort('Parameters');
        Services::Registry()->sort('Metadata');

        /** Remove standard patterns no longer needed  */
        Services::Registry()->delete('Parameters', 'list*');
        Services::Registry()->delete('Parameters', 'item*');
        Services::Registry()->delete('Parameters', 'form*');
        Services::Registry()->delete('Parameters', 'menuitem*');

        /** Copy some configuration data */
        $fields = Services::Registry()->get('Configuration', 'application*');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set('Parameters', $key, $value);
            }
        }

        return true;
    }

    /**
     * Iterates parameter set to determine whether or not value should be applied
     *
     * @param   $parameterSet
     * @param   $pageTypeNamespace
     *
     * @return  void
     * @since   1.0
     */
    protected function processParameterSet($parameterSet, $pageTypeNamespace)
    {
        foreach ($parameterSet as $key => $value) {

            $copy_from = $key;

            if (substr($key, 0, strlen($pageTypeNamespace)) == $pageTypeNamespace) {
                $copy_to = substr($key, strlen($pageTypeNamespace) + 1, 9999);
            } else {
                $copy_to = $key;
            }

            $existing = Services::Registry()->get('Parameters', $copy_to);

            if ($existing === 0 || trim($existing) == '' || $existing === null || $existing === false) {
                if ($value === 0 || trim($value) == '' || $value === null) {
                } else {
                    Services::Registry()->set('Parameters', $copy_to, $value);
                }
            }
        }
    }

    /**
     * Sets the namespace, path and URL path for extensions
     *
     * @return boolean
     * @since   1.0
     */
    public function setExtensionPaths()
    {
        Services::Registry()->set(
            'Parameters',
            'extension_name_path_node',
            Services::Registry()->get('Parameters', 'model_name')
        );

        if (Services::Registry()->get('Parameters', 'model_type') == 'Resource') {
            $cattype = CATALOG_TYPE_RESOURCE;
        } else {
            $cattype = Services::Registry()->get('Parameters', 'criteria_catalog_type_id');
        }

        Services::Registry()->set(
            'Parameters',
            'extension_path',
            Helpers::Extension()->getPath(
                $cattype,
                Services::Registry()->get('Parameters', 'extension_name_path_node')
            )
        );

        Services::Registry()->set(
            'Parameters',
            'extension_path_url',
            Helpers::Extension()->getPathURL(
                $cattype,
                Services::Registry()->get('Parameters', 'extension_name_path_node')
            )
        );

        Services::Registry()->set(
            'Parameters',
            'extension_namespace',
            Helpers::Extension()->getNamespace(
                $cattype,
                Services::Registry()->get('Parameters', 'extension_name_path_node')
            )
        );

        Services::Registry()->sort('Parameters');

        return true;
    }

    /**
     * Get Category Type information for Resource
     *
     * @param  $id
     *
     * @return array An object containing an array of basic resource info, parameters in registry
     * @since   1.0
     */
    public function getResourceCatalogType($id = 0)
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry('Datasource', 'CatalogTypes');
        $controller->setDataobject();

        $controller->set('process_plugins', 0);
        $controller->set('get_customfields', 0);
        $prefix = $controller->get('primary_prefix', 'a');

        $controller->model->query->where(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('extension_instance_id')
                . ' = '
                . (int)$id
        );

        $item = $controller->getData(QUERY_OBJECT_ITEM);

        if (count($item) == 0) {
            return array();
        }

        return $item;
    }

    /**
     * Get Parameter and Custom Fields for Resource Content (no data, just field definitions)
     *
     * Populates these registries (ex. Model Type Resource and Model Name Articles):
     *      Model => Services::Registry()->get('ArticlesResource', '*');
     *      Parameter Fields => Services::Registry()->get('ArticlesResource', 'Parameters')
     *
     * @param   string  $model_type
     * @param   string  $model_name
     *
     * @return  array   An object containing an array of basic resource info, parameters in registry
     * @since   1.0
     */
    public function getResourceContentParameters($model_type = 'Resource', $model_name)
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $controller->set('process_plugins', 0);
        $controller->set('get_customfields', 1);

        $controller->getModelRegistry($model_type, $model_name);
        return $controller->setDataobject();
    }

    /**
     * Get Parameters and data for Resource
     *
     * Usage:
     *  Helpers::Content()->getResourceExtensionParameters($extension_instance_id);
     *
     * Populates these registries:
     *      Model => Services::Registry()->get('ResourcesSystem', '*');
     *      Parameters => Services::Registry()->get('ResourcesSystemParameters', '*');
     *
     * @param   $id     Resource Extension
     *
     * @return  array   An object containing an array of basic resource info, parameters in registry
     * @since   1.0
     */
    public function getResourceExtensionParameters($id = 0)
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry('System', 'Resources');
        $controller->setDataobject();

        $controller->set('id', (int)$id);
        $controller->set('process_plugins', 0);
        $controller->set('get_customfields', 1);
        $controller->set('check_view_level_access', 0);

        return $controller->getData(QUERY_OBJECT_ITEM);
    }

    /**
     * Get Menuitem Content Parameters for specific Resource
     *
     * Usage:
     *  Helpers::Content()->getResourceMenuitemParameters('Grid', $extension_instance_id);
     *
     * Populates this registry:
     * If the menuitem is found, parameters can be accessed using the 'Menuitemtype' + 'MenuitemParameters' registry
     *      Parameters => Services::Registry()->get('GridMenuitemParameters', '*');
     *
     * @param   string  $page_type
     * @param   string  $extension_instance_id
     *
     * @return  mixed   False, or an object containing an array of basic resource info, parameters in registry
     * @since   1.0
     */
    public function getResourceMenuitemParameters($page_type, $extension_instance_id)
    {
        $page_type = ucfirst(strtolower($page_type));

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(CATALOG_TYPE_MENUITEM_LITERAL, $page_type);
        $controller->setDataobject();

        $prefix = $controller->get('primary_prefix', 'a');

        $controller->model->query->where(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('page_type')
                . ' = '
                . $controller->model->db->q($page_type)
        );

        $controller->model->query->where(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('catalog_type_id')
                . ' = '
                . (int)CATALOG_TYPE_MENUITEM
        );

        $value = '"criteria_extension_instance_id":"' . $extension_instance_id . '"';
        $controller->model->query->where(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('parameters')
                . ' = '
                . $controller->model->db->q('%' . $value . '%')
        );

        $menuitem = $controller->getData(QUERY_OBJECT_ITEM);
        if ($menuitem === false || $menuitem === null || count($menuitem) == 0) {
            return false;
        }

        $menuitem->table_registry = $page_type . CATALOG_TYPE_MENUITEM_LITERAL;

        return $menuitem;
    }
}
