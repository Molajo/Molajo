<?php
/**
 * Theme Service Content Helper
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2012 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Theme\Helper;

use Molajo\Service\Services\Theme\Helper\ExtensionHelper;
use Molajo\Service\Services\Theme\Helper\ThemeHelper;
use Molajo\Service\Services\Theme\Helper\ViewHelper;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Retrieves Item, List, or Menu Item Parameters for Route from Content, Extension, and Menu Item
 *
 * Also, provides a number of methods that can be useful for plugins:
 *
 *  - getResourceCatalogTypes -  Get Category Type information for Resource
 *  - getResourceContentParameters - Get Parameter and Custom Fields for Resource Content
 *      (no data, just field definitions)
 *  - getResourceExtensionParameters - Get Parameters for Resource
 *  - getResourceMenuitemParameters($page_type, $extension_instance_id) Get Menuitem Content
 *      Parameters for Resource
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2012 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class ContentHelper
{
    /**
     * Extension Helper
     *
     * @var    object
     * @since  1.0
     */
    protected $extensionHelper;

    /**
     * Theme Helper
     *
     * @var    object
     * @since  1.0
     */
    protected $themeHelper;

    /**
     * View Helper
     *
     * @var    object
     * @since  1.0
     */
    protected $viewHelper;

    /**
     * Parameters for rendering the page
     *
     * @var    $parameters
     * @since  1.0
     */
    protected $parameters;

    /**
     * List of valid properties for parameters
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array();

    /**
     * Initialise parameter settings for Page Parameter Queries
     *
     * @param   array  $parameters
     *
     * @return  ContentHelper
     * @since   1.0
     */
    public function initialise($parameters)
    {
        $this->parameters = $parameters;
        $this->extensionHelper = new ExtensionHelper();
        $this->themeHelper = new ThemeHelper();
        $this->viewHelper = new ViewHelper();

        return $this;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    protected function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        $this->parameters[$key] = $default;
        return $this->parameters[$key];
    }

    /**
     * Set the value of a specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    protected function set($key, $value = null)
    {
        $key = strtolower($key);

        $this->parameters[$key] = $value;
        return $this->parameters[$key];
    }

    /**
     * Set Parameters for Item Page Type
     *
     * @return  array
     * @since   1.0
     */
    public function getRouteItem()
    {
        $id = (int) $this->get('catalog_source_id');
        $model_type = $this->get('catalog_model_type');
        $model_name = $this->get('catalog_model_name');

        $item = $this->getData($id, $model_type, $model_name);
        if (count($item) == 0) {
            return $this->set('status_found', false);
        }

        if (isset($item->extension_instance_id)) {
            $extension_instance_id = (int)$item->extension_instance_id;
            $extension_instance_catalog_type_id = (int)$item->catalog_catalog_type_id;
        } else {
            $extension_instance_id = (int)$item->catalog_extension_instance_id;
            $extension_instance_catalog_type_id = (int)$item->catalog_catalog_type_id;
        }

        $this->set('extension_instance_id', $extension_instance_id);
        $this->set('extension_catalog_type_id', $extension_instance_catalog_type_id);
        $this->set('criteria_extension_instance_id', (int)$extension_instance_id);
        $this->set('criteria_source_id', (int)$item->id);
        $this->set('page_type', QUERY_OBJECT_ITEM);
        $this->set('criteria_catalog_type_id', (int)$item->catalog_type_id);

        $this->getResourceExtensionParameters((int)$extension_instance_id);

        if (strtolower($this->get('request_action')) == ACTION_READ) {
            $page_type_namespace = 'item';
        } else {
            $page_type_namespace = 'form';
        }

        if ($this->get('catalog_model_type') == 'Resource') {
            $resource_or_system = 'Resource';
        } else {
            $resource_or_system = 'System';
        }

        $this->set('extension_name_path_node', $this->get('catalog_model_name'));
        $this->set('model_registry_name', $item->model_registry_name);

        $this->setParameters(
            $page_type_namespace,
            $item->model_registry_name . PARAMETERS_LITERAL,
            $item->model_registry_name . METADATA_LITERAL,
            'ResourcesSystem',
            $resource_or_system
        );

        $customfields = Services::Registry()->get($item->model_registry_name . CUSTOMFIELDS_LITERAL);

        if (is_array($customfields) && count($customfields) > 0) {
            foreach ($customfields as $key => $value) {
                if ($value === 0 || trim($value) == '' || $value === null) {
                } else {
                    $item->$key = $value;
                }
            }
        }

        $parent_menu_id = Services::Registry()->get('ResourcesSystemParameters',
            $page_type_namespace . '_parent_menu_id');

        Services::Registry()->set(PRIMARY_LITERAL, DATA_LITERAL, array($item));

        $this->set('parent_menu_id', $parent_menu_id);

        if ($page_type_namespace == 'form') {
            $this->set('page_type', PAGE_TYPE_EDIT);
        }

        $parameters = $this->parameters;
        ksort($parameters);

        $this->property_array = array();
        foreach ($parameters as $key=>$value) {
            $this->property_array[] = $key;
        }
        $property_array = $this->property_array;

        return array($parameters, $property_array);
    }

    /**
     * Set Parameters for List Page Type
     *
     * @return  array
     * @since   1.0
     */
    public function getRouteList()
    {
        $id = (int) $this->get('catalog_id');
        $model_type = $this->get('catalog_model_type');
        $model_name = $this->get('catalog_model_name');

        $item = $this->getData($id, $model_type, $model_name, QUERY_OBJECT_ITEM);

        if (count($item) == 0) {
            return $this->set('status_found', false);
        }

        $this->set('extension_instance_id', (int)$item->id);
        $this->set('extension_title', $item->title);
        $this->set('extension_translation_of_id', (int)$item->translation_of_id);
        $this->set('extension_language', $item->language);
        $this->set('extension_catalog_type_id', (int)$item->catalog_type_id);
        $this->set('extension_modified_datetime', $item->modified_datetime);
        $this->set('extension_catalog_type_title', $item->catalog_types_title);
        $this->set('catalog_type_id', $item->catalog_type_id);
        $this->set('page_type', QUERY_OBJECT_LIST);
        $this->set('primary_category_id', $item->catalog_primary_category_id);
        $this->set('source_id', (int)$item->id);

        if ($this->get('catalog_model_type') == 'Resource') {
            $resource_or_system = 'Resource';
        } else {
            $resource_or_system = 'System';
        }

        $this->set('extension_name_path_node', $this->get('catalog_model_name'));

        $this->setParameters(
            QUERY_OBJECT_LIST,
            $item->model_registry_name . PARAMETERS_LITERAL,
            $item->model_registry_name . METADATA_LITERAL,
            null,
            $resource_or_system
        );

        $parameters = $this->parameters;
        ksort($parameters);

        $property_array = $this->property_array;
        ksort($property_array);

        return array($parameters, $property_array);
    }

    /**
     * Set Parameters for Menu Item Page Type
     *
     * @return  array
     * @since   1.0
     */
    public function getRouteMenuitem()
    {
        $id = (int) $this->get('catalog_source_id');
        $model_type = CATALOG_TYPE_MENUITEM_LITERAL;
        $model_name = $this->get('catalog_page_type');

        $item = $this->getData($id, $model_type, $model_name);

        if (count($item) == 0) {
            return $this->set('status_found', false);
        }

        $this->set('menuitem_lvl', (int)$item->lvl);
        $this->set('menuitem_title', $item->title);
        $this->set('menuitem_parent_id', $item->parent_id);
        $this->set('menuitem_translation_of_id', (int)$item->translation_of_id);
        $this->set('menuitem_language', $item->language);
        $this->set('menuitem_catalog_type_id', (int)$item->catalog_type_id);
        $this->set('menuitem_catalog_type_title', $item->catalog_types_title);
        $this->set('menuitem_modified_datetime', $item->modified_datetime);
        $this->set('menu_id', (int)$item->extension_id);
        $this->set('menu_title', $item->extensions_name);
        $this->set('menu_extension_id', (int)$item->extensions_id);
        $this->set('menu_path_node', $item->extensions_name);

        $page_type = $this->get('catalog_page_type');
        $this->set('page_type', $page_type);

        $registry = $page_type . CATALOG_TYPE_MENUITEM_LITERAL;

        $parameters = Services::Registry()->copy($registry . PARAMETERS_LITERAL);
        if (count($parameters) > 0) {
            foreach ($parameters as $key => $value) {
                if (in_array($key, $this->property_array)) {
                } else {
                    $this->property_array[] = $key;
                }
                $this->set($key, $value);
            }
        }

        $metadata = Services::Registry()->copy($registry . METADATA_LITERAL);
        if (count($metadata) > 0) {
            foreach ($metadata as $key => $value) {
                Services::Metadata()->set($key, $value);
            }
        }

        if ($this->get('catalog_model_type') == 'Resource') {
            $resource_or_system = 'Resource';
        } else {
            $resource_or_system = 'System';
        }

        $this->set('extension_name_path_node', $this->get('catalog_model_name'));

        $this->setParameters(strtolower(CATALOG_TYPE_MENUITEM_LITERAL),
            $registry . PARAMETERS_LITERAL,
            $registry . METADATA_LITERAL,
            null,
            $resource_or_system
        );

        /** Must be after parameter set so as to not strip off menuitem */
        $this->set('menuitem_id', (int)$item->id);
        $this->set('page_type', $this->get('catalog_page_type'));

        /** Retrieve Model Registry for Resource */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(
            $this->get('catalog_model_type'),
            $this->get('catalog_model_name'),
            0
        );

        $parameters = $this->parameters;
        ksort($parameters);
        $property_array = $this->property_array;
        ksort($property_array);

        return array($parameters, $property_array);
    }

    /**
     * Get data for Menu Item, Item or List
     *
     * @param   $id
     * @param   $model_type
     * @param   $model_name
     *
     * @return  array  An object containing an array of data
     * @since   1.0
     */
    public function getData($id = 0, $model_type = DATA_SOURCE_LITERAL, $model_name = 'Content')
    {
        Services::Profiler()->set(
            'ContentHelper get ' . ' ID: ' . $id . ' Model Type: ' . $model_type
                . ' Model Name: ' . $model_name,
            PROFILER_ROUTING,
            VERBOSE
        );

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry($model_type, $model_name, 1);

        $controller->set('primary_key_value', (int)$id, 'model_registry');
        $controller->set('process_plugins', 1, 'model_registry');
        $controller->set('get_customfields', 1, 'model_registry');

        $item = $controller->getData(QUERY_OBJECT_ITEM);

        if ($item === false || $item === null || count($item) == 0) {
            return array();
        }

        $item->model_registry_name = $controller->get('model_registry_name');

        return $item;
    }

    /**
     * Determines parameter values from primary item (form, item, list, or menuitem)
     *  Extension and Application defaults applied following item values
     *
     * @param   string  $page_type_namespace (ex. item, list, menuitem)
     * @param   string  $parameter_namespace (ex. $item->model_registry_name . PARAMETERS_LITERAL)
     * @param   string  $metadata_namespace (ex. $item->model_registry_name . METADATA_LITERAL)
     * @param   string  $resource_namespace for extension (ex. ResourcesSystem)
     * @param   string  $resource_or_system for extension (values 'resource' or 'system')
     *
     * @return  boolean
     * @since   1.0
     */
    public function setParameters(
        $page_type_namespace,
        $parameter_namespace,
        $metadata_namespace,
        $resource_namespace = null,
        $resource_or_system = 'resource'
    ) {
        $this->set('page_type', $page_type_namespace);

        /** I. Priority 1 - Item level (Item, List, Menu Item) */
        $parameter_set = Services::Registry()->get($parameter_namespace, $page_type_namespace . '*');
        if (is_array($parameter_set) && count($parameter_set) > 0) {
            $this->processParameterSet($parameter_set, $page_type_namespace);
        }

        $parameter_set = Services::Registry()->get($parameter_namespace, 'criteria*');
        if (is_array($parameter_set) && count($parameter_set) > 0) {
            $this->processParameterSet($parameter_set, $page_type_namespace);
        }

        $parameter_set = Services::Registry()->get($parameter_namespace, 'enable*');
        if (is_array($parameter_set) && count($parameter_set) > 0) {
            $this->processParameterSet($parameter_set, $page_type_namespace);
        }

        /** II. Priority 2 - Extension level defaults */
        if ($resource_namespace === null) {
        } else {

            $parameter_set = Services::Registry()->get($resource_namespace . PARAMETERS_LITERAL,
                $page_type_namespace . '*');

            if (is_array($parameter_set) && count($parameter_set) > 0) {
                $this->processParameterSet($parameter_set, $page_type_namespace);
            }

            $parameter_set = Services::Registry()->get($resource_namespace . PARAMETERS_LITERAL, 'criteria*');
            if (is_array($parameter_set) && count($parameter_set) > 0) {
                $this->processParameterSet($parameter_set, $page_type_namespace);
            }

            $parameter_set = Services::Registry()->get($resource_namespace . PARAMETERS_LITERAL, 'enable*');
            if (is_array($parameter_set) && count($parameter_set) > 0) {
                $this->processParameterSet($parameter_set, $page_type_namespace);
            }
        }

        /** III. Finally, Application level defaults */
        $applicationDefaults = Services::Registry()->get(CONFIGURATION_LITERAL, $page_type_namespace . '*');
        if (count($applicationDefaults) > 0) {
            $this->processParameterSet($applicationDefaults, $page_type_namespace);
        }

        /** Merge in the rest */
        $random = 'r' . mt_rand ( 10000 , 60000000 );
        Services::Registry()->createRegistry($random);
        Services::Registry()->loadArray($random, $this->parameters);
        Services::Registry()->merge($parameter_namespace, $random);

        /** Set Theme and View values */
        $this->themeHelper->get((int)$this->get('theme_id'), $random);
        $this->viewHelper->get((int)$this->get('page_view_id'), CATALOG_TYPE_PAGE_VIEW_LITERAL, $random);
        $this->viewHelper->get((int)$this->get('template_view_id'), CATALOG_TYPE_TEMPLATE_VIEW_LITERAL, $random);
        $this->viewHelper->get((int)$this->get('wrap_view_id'), CATALOG_TYPE_WRAP_VIEW_LITERAL, $random);

        $this->set('extension_path',
            $this->extensionHelper->getPath(
                $resource_or_system,
                $this->get('extension_name_path_node'),
                $random)
        );

        $this->set('extension_path_url',
            $this->extensionHelper->getPathURL(
                $resource_or_system,
                $this->get('extension_name_path_node'),
                $random)
        );

        $this->set('extension_namespace',
            $this->extensionHelper->getNamespace(
                $resource_or_system,
                $this->get('extension_name_path_node'),
                $random)
        );

        /** Metadata defaulting */
        Services::Registry()->merge($metadata_namespace, METADATA_LITERAL);
        if ($resource_namespace == '') {
        } else {
            Services::Registry()->merge($resource_namespace . METADATA_LITERAL, METADATA_LITERAL, true);
        }

        /** Remove standard patterns no longer needed  */
        Services::Registry()->delete($random, 'list*');

        Services::Registry()->delete($random, 'form*');
        Services::Registry()->delete($random, 'menuitem*');
        Services::Registry()->delete($random, 'item*');

        /** Copy some configuration data */
        $fields = Services::Registry()->get(CONFIGURATION_LITERAL, 'application*');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set($random, $key, $value);
            }
        }

        Services::Registry()->sort($random);
        $this->parameters = Services::Registry()->get($random);
        Services::Registry()->deleteRegistry($random);

        $property = $this->property_array;
        sort($property);
        $this->property_array = $property;

        return true;
    }

    /**
     * Iterates parameter set to determine whether or not value should be applied
     *
     * @param   $parameter_set
     * @param   $page_type_namespace
     *
     * @return  void
     * @since   1.0
     */
    protected function processParameterSet($parameter_set, $page_type_namespace)
    {
        foreach ($parameter_set as $key => $value) {

            $copy_from = $key;

            if (substr($key, 0, strlen($page_type_namespace)) == $page_type_namespace) {
                $copy_to = substr($key, strlen($page_type_namespace) + 1, 9999);
            } else {
                $copy_to = $key;
            }

            $existing = $this->get($copy_to);

            if ($existing === 0 || trim($existing) == '' || $existing === null || $existing === false) {
                if ($value === 0 || trim($value) == '' || $value === null) {
                } else {
                    $this->set($copy_to, $value);
                }
            }
        }
    }

    /**
     * Get Category Type information for Resource
     *
     * @param   $id
     *
     * @return  array  An object containing an array of basic resource info, parameters in registry
     * @since   1.0
     */
    public function getResourceCatalogType($id = 0)
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATA_SOURCE_LITERAL, 'CatalogTypes', 1);

        $controller->set('process_plugins', 0, 'model_registry');
        $controller->set('get_customfields', 0, 'model_registry');
        $prefix = $controller->get('primary_prefix', 'a', 'model_registry');

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
     *      Parameter Fields => Services::Registry()->get('ArticlesResource', PARAMETERS_LITERAL)
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
        $controller->getModelRegistry($model_type, $model_name, 0);

        $controller->set('process_plugins', 0, 'model_registry');
        $controller->set('get_customfields', 1, 'model_registry');

        return $controller->setDataobject();
    }

    /**
     * Get Parameters for Resource
     *
     * Usage:
     *  $this->contentHelper->getResourceExtensionParameters($extension_instance_id);
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
        $controller->getModelRegistry(SYSTEM_LITERAL, 'Resources', 1);

        $controller->set('primary_key_value', (int)$id, 'model_registry');
        $controller->set('process_plugins', 0, 'model_registry');
        $controller->set('get_customfields', 1, 'model_registry');
        $controller->set('check_view_level_access', 0, 'model_registry');

        return $controller->getData(QUERY_OBJECT_ITEM);
    }

    /**
     * Get Menuitem Content Parameters for Resource
     *
     * Usage:
     *  $this->contentHelper->getResourceMenuitemParameters(PAGE_TYPE_GRID, $extension_instance_id);
     *
     * Populates this registry:
     * If the menuitem is found, parameters can be accessed using the 'Menuitemtype' + 'MenuitemParameters' registry
     *      Parameters => Services::Registry()->get('GridMenuitemParameters', '*');
     *
     * @param   string  $page_type
     * @param   string  $extension_instance_id
     *
     * @return  mixed   false, or an object containing an array of basic resource info, parameters in registry
     * @since   1.0
     */
    public function getResourceMenuitemParameters($page_type, $extension_instance_id)
    {
        $page_type = ucfirst(strtolower($page_type));

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(CATALOG_TYPE_MENUITEM_LITERAL, $page_type, 1);

        $prefix = $controller->get('primary_prefix', 'a', 'model_registry');

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
                . $controller->model->db->qn(PARAMETERS_LITERAL)
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
