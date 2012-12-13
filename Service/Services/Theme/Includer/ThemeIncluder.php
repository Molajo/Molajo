<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Includer;
use Molajo\Service\Services\Theme\Helper\ContentHelper;
use Molajo\Service\Services\Theme\Helper\ExtensionHelper;
use Molajo\Service\Services\Theme\Helper\ThemeHelper;
use Molajo\Service\Services\Theme\Helper\ViewHelper;
use Molajo\MVC\Controller\DisplayController;

defined('MOLAJO') or die;

/**
 * Theme
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
class ThemeIncluder extends Includer
{
    /**
     * Helpers
     *
     * @var    object
     * @since  1.0
     */
    protected $contentHelper;
    protected $extensionHelper;
    protected $themeHelper;
    protected $viewHelper;

    /**
     * @return  null
     * @since   1.0
     */
    public function __construct($include_name = null, $include_type = null)
    {
        $this->contentHelper = new ContentHelper();
        $this->extensionHelper = new ExtensionHelper();
        $this->themeHelper = new ThemeHelper();
        $this->viewHelper = new ViewHelper();

        return;
    }

    /**
     * For Item, List, or Menu Item, retrieve Parameter data needed to generate page.
     *
     * Once parameters are available, return page cache, if available.
     *
     * @return   string (null or will contain page cache)
     * @since    1.0
     * @throws   /Exception
     */
    public function setPrimaryData()
    {
        $catalog_id = Services::Registry()->get('Parameters', 'catalog_id');
        $id = Services::Registry()->get('Parameters', 'catalog_source_id');
        $catalog_page_type = Services::Registry()->get('Parameters', 'catalog_page_type');
        $model_type = ucfirst(strtolower(Services::Registry()->get('Parameters', 'catalog_model_type')));
        $model_name = ucfirst(strtolower(Services::Registry()->get('Parameters', 'catalog_model_name')));

        if (strtolower(trim($catalog_page_type)) == QUERY_OBJECT_LIST) {
            $response = $this->getRouteList($id, $model_type, $model_name);

        } elseif (strtolower(trim($catalog_page_type)) == QUERY_OBJECT_ITEM) {
            $response = $this->getRouteItem($id, $model_type, $model_name);

        } else {
            $response = $this->getRouteMenuitem();
        }

        if ($response === false) {
            throw new \Exception('Theme: Could not identify Primary Data for Catalog ID ' . $catalog_id);
        }

        $this->set('extension_catalog_type_id', CATALOG_TYPE_RESOURCE, 'parameters');

        $this->getPageCache();

        return $this->rendered_output;
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
            return Services::Registry()->set(PARAMETERS_LITERAL, 'status_found', false);
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_instance_id', (int)$item->id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_title', $item->title);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_translation_of_id', (int)$item->translation_of_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_language', $item->language);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_id', (int)$item->catalog_type_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_modified_datetime', $item->modified_datetime);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_title', $item->catalog_types_title);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_type_id', $item->catalog_type_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'page_type', QUERY_OBJECT_LIST);
        Services::Registry()->set(PARAMETERS_LITERAL, 'primary_category_id', $item->catalog_primary_category_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'source_id', (int)$item->id);

        $this->setParameters(
            QUERY_OBJECT_LIST,
            $item->model_registry . PARAMETERS_LITERAL,
            $item->model_registry . METADATA_LITERAL
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_name_path_node',
            Services::Registry()->get('parameters', 'model_name')
        );

        if (Services::Registry()->get('parameters', 'model_type') == 'Resource') {
            $cattype = CATALOG_TYPE_RESOURCE;
        } else {
            $cattype = Services::Registry()->get('parameters', 'criteria_catalog_type_id');
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_path',
            $this->extensionHelper->getPath($cattype,
                Services::Registry()->get('parameters', 'extension_name_path_node'))
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_path_url',
            $this->extensionHelper->getPathURL($cattype,
                Services::Registry()->get('parameters', 'extension_name_path_node')
            )
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_namespace',
            $this->extensionHelper->getNamespace($cattype,
                Services::Registry()->get('parameters', 'extension_name_path_node')
            )
        );

        Services::Registry()->sort(PARAMETERS_LITERAL);

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
        $item = $this->get($id, $model_type, $model_name);
        if (count($item) == 0) {
            return Services::Registry()->set(PARAMETERS_LITERAL, 'status_found', false);
        }

        if (isset($item->extension_instance_id)) {
            $extension_instance_id = (int)$item->extension_instance_id;
            $extension_instance_catalog_type_id = (int)$item->catalog_catalog_type_id;
        } else {
            $extension_instance_id = (int)$item->catalog_extension_instance_id;
            $extension_instance_catalog_type_id = (int)$item->catalog_catalog_type_id;
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_instance_id', $extension_instance_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_id', $extension_instance_catalog_type_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'criteria_extension_instance_id', (int)$extension_instance_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'criteria_source_id', (int)$item->id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'page_type', QUERY_OBJECT_ITEM);
        Services::Registry()->set(PARAMETERS_LITERAL, 'criteria_catalog_type_id', (int)$item->catalog_type_id);

        $this->getResourceExtensionParameters((int)$extension_instance_id);

        if (strtolower(Services::Registry()->get('parameters', 'request_action')) == ACTION_READ) {
            $page_type_namespace = 'item';
        } else {
            $page_type_namespace = 'form';
        }

        $this->setParameters(
            $page_type_namespace,
            $item->model_registry . PARAMETERS_LITERAL,
            $item->model_registry . METADATA_LITERAL,
            'ResourcesSystem'
        );

        $customfields = Services::Registry()->get($item->model_registry . CUSTOMFIELDS_LITERAL);
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

        Services::Registry()->set(PARAMETERS_LITERAL, 'parent_menu_id', $parent_menu_id);

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_name_path_node',
            Services::Registry()->get('parameters', 'model_name')
        );

        if (Services::Registry()->get('parameters', 'model_type') == 'Resource') {
            $cattype = CATALOG_TYPE_RESOURCE;
        } else {
            $cattype = Services::Registry()->get('parameters', 'criteria_catalog_type_id');
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_path',
            $this->extensionHelper->getPath($cattype,
                Services::Registry()->get('parameters', 'extension_name_path_node'))
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_path_url',
            $this->extensionHelper->getPathURL($cattype,
                Services::Registry()->get('parameters', 'extension_name_path_node')
            )
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_namespace',
            $this->extensionHelper->getNamespace($cattype,
                Services::Registry()->get('parameters', 'extension_name_path_node')
            )
        );

        Services::Registry()->sort(PARAMETERS_LITERAL);

        if ($page_type_namespace == 'form') {
            Services::Registry()->set(PARAMETERS_LITERAL, 'page_type', PAGE_TYPE_EDIT);
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
            Services::Registry()->get('parameters', 'catalog_source_id'),
            CATALOG_TYPE_MENUITEM_LITERAL,
            Services::Registry()->get('parameters', 'catalog_page_type'),
            CATALOG_TYPE_MENUITEM_LITERAL
        );

        if (count($item) == 0) {
            return Services::Registry()->set(PARAMETERS_LITERAL, 'status_found', false);
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'menuitem_lvl', (int)$item->lvl);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menuitem_title', $item->title);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menuitem_parent_id', $item->parent_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menuitem_translation_of_id', (int)$item->translation_of_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menuitem_language', $item->language);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menuitem_catalog_type_id', (int)$item->catalog_type_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menuitem_catalog_type_title', $item->catalog_types_title);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menuitem_modified_datetime', $item->modified_datetime);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menu_id', (int)$item->extension_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menu_title', $item->extensions_name);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menu_extension_id', (int)$item->extensions_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'menu_path_node', $item->extensions_name);

        $page_type = Services::Registry()->get('parameters', 'catalog_page_type');
        Services::Registry()->set(PARAMETERS_LITERAL, 'page_type', $page_type);
        $registry = $page_type . CATALOG_TYPE_MENUITEM_LITERAL;

        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'criteria_source_id',
            (int)Services::Registry()->get($registry . PARAMETERS_LITERAL, 'criteria_source_id')
        );
        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'criteria_catalog_type_id',
            (int)Services::Registry()->get($registry . PARAMETERS_LITERAL, 'criteria_catalog_type_id')
        );
        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'criteria_extension_instance_id',
            (int)Services::Registry()->get($registry . PARAMETERS_LITERAL, 'criteria_extension_instance_id')
        );

        Services::Registry()->copy($registry . PARAMETERS_LITERAL, PARAMETERS_LITERAL);
        Services::Registry()->copy($registry . METADATA_LITERAL, METADATA_LITERAL);

        $this->setParameters(
            strtolower(CATALOG_TYPE_MENUITEM_LITERAL),
            $registry . PARAMETERS_LITERAL,
            $registry . METADATA_LITERAL
        );

        /** Must be after parameter set so as to not strip off menuitem */
        Services::Registry()->set(PARAMETERS_LITERAL, 'menuitem_id', (int)$item->id);
        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'page_type',
            Services::Registry()->get('parameters', 'catalog_page_type')
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_name_path_node',
            Services::Registry()->get('parameters', 'model_name')
        );

        if (Services::Registry()->get('parameters', 'model_type') == 'Resource') {
            $cattype = CATALOG_TYPE_RESOURCE;
        } else {
            $cattype = Services::Registry()->get('parameters', 'criteria_catalog_type_id');
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_path',
            $this->extensionHelper->getPath($cattype,
                Services::Registry()->get('parameters', 'extension_name_path_node'))
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_path_url',
            $this->extensionHelper->getPathURL($cattype,
                Services::Registry()->get('parameters', 'extension_name_path_node')
            )
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_namespace',
            $this->extensionHelper->getNamespace($cattype,
                Services::Registry()->get('parameters', 'extension_name_path_node')
            )
        );

        /** Retrieve Model Registry for Resource */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(
            Services::Registry()->get('parameters', 'model_type'),
            Services::Registry()->get('parameters', 'model_name'),
            0
        );

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
    public function get($id = 0, $model_type = DATA_SOURCE_LITERAL, $model_name = 'Content')
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

        $item->model_registry = $controller->get('model_registry_name');

        return $item;
    }

    /**
     * Determines parameter values from primary item (form, item, list, or menuitem)
     *  Extension and Application defaults applied following item values
     *
     * @param   string  $page_type_namespace (ex. item, list, menuitem)
     * @param   string  $parameter_namespace (ex. $item->model_registry . PARAMETERS_LITERAL)
     * @param   string  $metadata_namespace (ex. $item->model_registry . METADATA_LITERAL)
     * @param   string  $resource_namespace For extension (ex. ResourcesSystem)
     *
     * @return  boolean
     * @since   1.0
     */
    public function setParameters(
        $page_type_namespace,
        $parameter_namespace,
        $metadata_namespace,
        $resource_namespace = ''
    ) {
        Services::Registry()->set(PARAMETERS_LITERAL, 'page_type', $page_type_namespace);

        /** I. Priority 1 - Item parameter values (be it an item, menu item, list) */
        $newParameters = Services::Registry()->get($parameter_namespace, $page_type_namespace . '*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $page_type_namespace);
        }

        $newParameters = Services::Registry()->get($parameter_namespace, 'criteria*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $page_type_namespace);
        }

        $newParameters = Services::Registry()->get($parameter_namespace, 'enable*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $page_type_namespace);
        }

        /** II. Next, Extension level defaults */
        if ($resource_namespace == '') {
        } else {

            $newParameters = Services::Registry()->get(
                $resource_namespace . PARAMETERS_LITERAL,
                $page_type_namespace . '*'
            );
            if (is_array($newParameters) && count($newParameters) > 0) {
                $this->processParameterSet($newParameters, $page_type_namespace);
            }

            $newParameters = Services::Registry()->get($resource_namespace . PARAMETERS_LITERAL, 'criteria*');
            if (is_array($newParameters) && count($newParameters) > 0) {
                $this->processParameterSet($newParameters, $page_type_namespace);
            }

            $newParameters = Services::Registry()->get($resource_namespace . PARAMETERS_LITERAL, 'enable*');
            if (is_array($newParameters) && count($newParameters) > 0) {
                $this->processParameterSet($newParameters, $page_type_namespace);
            }
        }

        /** III. Finally, Application level defaults */
        $applicationDefaults = Services::Registry()->get(CONFIGURATION_LITERAL, $page_type_namespace . '*');
        if (count($applicationDefaults) > 0) {
            $this->processParameterSet($applicationDefaults, $page_type_namespace);
        }

        /** Merge in the rest */
        Services::Registry()->merge($parameter_namespace, PARAMETERS_LITERAL);

        /** Metadata defaulting */
        Services::Registry()->merge($metadata_namespace, METADATA_LITERAL);

        if ($resource_namespace == '') {
        } else {
            Services::Registry()->merge($resource_namespace . METADATA_LITERAL, METADATA_LITERAL, true);
        }

        Services::Registry()->merge(CONFIGURATION_LITERAL, PARAMETERS_LITERAL, true);

        /** Hold parameters while registry is used during Theme and View assignment */
        $savedParameters = array();
        $temp = Services::Registry()->getArray(PARAMETERS_LITERAL);
        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {
                if ($value === 0 || trim($value) == '' || $value === null) {
                } else {
                    $savedParameters[$key] = $value;
                }
            }
        }

        /** Set Theme and View values */
        $this->setThemePageView();

        $this->setTemplateWrapModel();

        /** Merge held parameters back in */
        if (is_array($savedParameters) && count($savedParameters) > 0) {
            foreach ($savedParameters as $key => $value) {
                Services::Registry()->set(PARAMETERS_LITERAL, $key, $value);
            }
        }

        Services::Registry()->sort(PARAMETERS_LITERAL);
        Services::Registry()->sort(METADATA_LITERAL);

        /** Remove standard patterns no longer needed  */
        Services::Registry()->delete(PARAMETERS_LITERAL, 'list*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'item*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'form*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'menuitem*');

        /** Copy some configuration data */
        $fields = Services::Registry()->get(CONFIGURATION_LITERAL, 'application*');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set(PARAMETERS_LITERAL, $key, $value);
            }
        }

        return true;
    }

    /**
     * Iterates parameter set to determine whether or not value should be applied
     *
     * @param   $parameterSet
     * @param   $page_type_namespace
     *
     * @return  void
     * @since   1.0
     */
    protected function processParameterSet($parameterSet, $page_type_namespace)
    {
        foreach ($parameterSet as $key => $value) {

            $copy_from = $key;

            if (substr($key, 0, strlen($page_type_namespace)) == $page_type_namespace) {
                $copy_to = substr($key, strlen($page_type_namespace) + 1, 9999);
            } else {
                $copy_to = $key;
            }

            $existing = Services::Registry()->get('parameters', $copy_to);

            if ($existing === 0 || trim($existing) == '' || $existing === null || $existing === false) {
                if ($value === 0 || trim($value) == '' || $value === null) {
                } else {
                    Services::Registry()->set(PARAMETERS_LITERAL, $copy_to, $value);
                }
            }
        }
    }

    /**
     * See if page exists in Page Cache
     *
     * @return  mixed | false or string
     * @since   1.0
     */
    protected function getPageCache()
    {
        if (file_exists(Services::Registry()->get('Parameters', 'theme_path_include'))) {
        } else {
            Services::Error()->set(500, 'Theme Not found');
            throw new \Exception('Theme not found '
                . Services::Registry()->get('Parameters', 'theme_path_include'));
        }

        $parameters = Services::Registry()->getArray('Parameters');

        $this->rendered_output = Services::Cache()->get(PAGE_LITERAL, implode('', $parameters));

        return;
    }

    /**
     * Render and return output
     *
     * @param   $attributes
     *
     * @return  mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        $this->loadPlugins();

        $this->renderOutput();

        return $this->rendered_output;
    }

    /**
     * Load Plugins Overrides from the Theme and/or Page View folders
     *
     * @return  void
     * @since   1.0
     */
    protected function loadPlugins()
    {
        Services::Event()->registerPlugins(
            Services::Registry()->get('include', 'theme_path'),
            Services::Registry()->get('include', 'theme_namespace')
        );
return;
        Services::Event()->registerPlugins(
            Services::Registry()->get('include', 'page_view_path'),
            Services::Registry()->get('include', 'page_view_namespace')
        );

        Services::Event()->registerPlugins(
            Services::Registry()->get('include', 'extension_path'),
            Services::Registry()->get('include', 'extension_namespace')
        );

        return;
    }

    /**
     * The Theme Includer renders the Theme include file and feeds in the Page Name Value
     *  The rendered output from that process provides the initial data to be parsed for Include statements
     */
    protected function renderOutput()
    {
        $controller = new DisplayController();
        $controller->set('include', Services::Registry()->getArray('include'));
        $this->set($this->get('extension_catalog_type_id', '', 'parameters'),
            CATALOG_TYPE_RESOURCE, 'parameters');

        $this->rendered_output = $controller->execute();
        echo $this->rendered_output;
        $this->loadMedia();

        $this->loadViewMedia();

        return;
    }

    /**
     * loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  bool
     * @since   1.0
     */
    protected function loadMedia()
    {
        $this->loadMediaPlus('',
            Services::Registry()->get('include', 'asset_priority_site', 100));

        $this->loadMediaPlus('/application' . APPLICATION,
            Services::Registry()->get('include', 'asset_priority_application', 200));

        $this->loadMediaPlus('/user' . Services::Registry()->get(USER_LITERAL, 'id'),
            Services::Registry()->get('include', 'asset_priority_user', 300));

        $this->loadMediaPlus('/category' . Services::Registry()->get('include', 'catalog_category_id'),
            Services::Registry()->get('include', 'asset_priority_primary_category', 700));

        $this->loadMediaPlus('/menuitem' . Services::Registry()->get('include', 'menu_item_id'),
            Services::Registry()->get('include', 'asset_priority_menuitem', 800));

        $this->loadMediaPlus('/source/' . Services::Registry()->get('include', 'extension_title')
                . Services::Registry()->get('include', 'criteria_source_id'),
            Services::Registry()->get('include', 'asset_priority_item', 900));

        $this->loadMediaPlus('/resource/' . Services::Registry()->get('include', 'extension_title'),
            Services::Registry()->get('include', 'asset_priority_extension', 900));

        $priority = Services::Registry()->get('include', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('include', 'theme_path');
        $url_path = Services::Registry()->get('include', 'theme_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $priority = Services::Registry()->get('include', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('include', 'page_view_path');
        $url_path = Services::Registry()->get('include', 'page_view_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        Services::Asset()->addLink(
            $url = Services::Registry()->get('include', 'theme_favicon'),
            $relation = 'shortcut icon',
            $relation_type = 'image/x-icon',
            $attributes = array()
        );

        $this->loadMediaPlus('', Services::Registry()->get('include', 'asset_priority_site', 100));

        return true;
    }

    /**
     * loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return bool
     * @since   1.0
     */
    protected function loadMediaPlus($plus = '', $priority = 500)
    {
        /** Theme */
        $file_path = Services::Registry()->get('include', 'theme_path');
        $url_path = Services::Registry()->get('include', 'theme_path_url');
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Application */
        $file_path = SITE_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITE_MEDIA_URL . '/' . APPLICATION . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path = SITE_MEDIA_URL . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, false);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITES_MEDIA_URL . '/' . APPLICATION . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path = SITES_MEDIA_URL . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** nothing was loaded */

        return true;
    }
}
