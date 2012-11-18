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
 * Retrieves Item, List, or TemplateView Parameter information for Route
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
     * @return bool|object
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
     * @return boolean
     * @since   1.0
     */
    public function getRouteList($id, $model_type, $model_name)
    {

        $item = $this->get($id, $model_type, $model_name, 'list');

        if (count($item) == 0) {
            return Services::Registry()->set('Parameters', 'status_found', false);
        }

        /** Route Registry */
        Services::Registry()->set('Parameters', 'extension_instance_id', (int) $item->id);
        Services::Registry()->set('Parameters', 'extension_title', $item->title);
        Services::Registry()->set('Parameters', 'extension_translation_of_id', (int) $item->translation_of_id);
        Services::Registry()->set('Parameters', 'extension_language', $item->language);
        Services::Registry()->set('Parameters', 'extension_catalog_type_id', (int) $item->catalog_type_id);
        Services::Registry()->set('Parameters', 'extension_modified_datetime', $item->modified_datetime);

        /** Content Extension and Source */
        Services::Registry()->set('Parameters', 'extension_catalog_type_title', $item->catalog_types_title);
        Services::Registry()->set('Parameters', 'catalog_type_id', $item->catalog_type_id);
        Services::Registry()->set('Parameters', 'content_type', (int) $item->page_type);
        Services::Registry()->set('Parameters', 'primary_category_id', $item->catalog_primary_category_id);
        Services::Registry()->set('Parameters', 'source_id',  (int) $item->id);

        /** Set Parameters */
        $this->setParameters('list',
            $item->model_registry . 'Parameters',
            $item->model_registry . 'Metadata'
        );

		$this->setExtensionPaths();

        return true;
    }

    /**
     * Retrieve Route information for a specific Content Item or Form
     *
     * @return boolean
     * @since    1.0
     */
    public function getRouteItem($id, $model_type, $model_name)
    {
        /** Theme, Page, Template and Wrap Views */
        if (strtolower(Services::Registry()->get('Parameters', 'request_action')) == 'display') {
            $pageTypeNamespace = 'item';
        } else {
            $pageTypeNamespace = 'form';
        }

        $item = $this->get($id, $model_type, $model_name, $pageTypeNamespace);
        if (count($item) == 0) {
            return Services::Registry()->set('Parameters', 'status_found', false);
        }

        Services::Registry()->set('Plugindata', 'PrimaryRequestQueryResults', array($item));

		if (isset($item->extension_instance_id)) {
			$extension_instance_id = (int) $item->extension_instance_id;
			$extension_instance_catalog_type_id = (int) $item->catalog_catalog_type_id;
		} else {
			$extension_instance_id = (int) $item->catalog_extension_instance_id;
			$extension_instance_catalog_type_id = (int) $item->catalog_catalog_type_id;
		}

		Services::Registry()->set('Parameters', 'extension_instance_id', $extension_instance_id);
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', $extension_instance_catalog_type_id);

		Services::Registry()->set('Parameters', 'criteria_extension_instance_id', (int) $extension_instance_id);
        Services::Registry()->set('Parameters', 'criteria_source_id', (int) $item->id);
        Services::Registry()->set('Parameters', 'criteria_catalog_type_id', (int) $item->catalog_type_id);

		$parameterNamespace = $item->model_registry . 'Parameters';

        $this->getResourceExtensionParameters((int) $extension_instance_id);

        $this->setParameters($pageTypeNamespace,
            $item->model_registry . 'Parameters',
            $item->model_registry . 'Metadata',
            'ResourcesSystem'
        );

        $parent_menu_id = Services::Registry()->get(
            'ResourcesSystemParameters',
            $pageTypeNamespace . '_parent_menu_id');

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
     * @return boolean
     * @since   1.0
     */
    public function getRouteMenuitem()
    {
        $item = $this->get(
            Services::Registry()->get('Parameters', 'catalog_source_id'),
            'Menuitem',
            Services::Registry()->get('Parameters', 'catalog_page_type'),
            'Menuitem'
        );

        if (count($item) == 0) {
            return Services::Registry()->set('Parameters', 'status_found', false);
        }

        /** Route Registry */
        Services::Registry()->set('Parameters', 'menuitem_lvl', (int) $item->lvl);
        Services::Registry()->set('Parameters', 'menuitem_title', $item->title);
        Services::Registry()->set('Parameters', 'menuitem_parent_id', $item->parent_id);
        Services::Registry()->set('Parameters', 'menuitem_translation_of_id', (int) $item->translation_of_id);
        Services::Registry()->set('Parameters', 'menuitem_language', $item->language);
        Services::Registry()->set('Parameters', 'menuitem_catalog_type_id', (int) $item->catalog_type_id);
        Services::Registry()->set('Parameters', 'menuitem_catalog_type_title', $item->catalog_types_title);
        Services::Registry()->set('Parameters', 'menuitem_modified_datetime', $item->modified_datetime);

        /** Menu Extension */
        Services::Registry()->set('Parameters', 'menu_id', (int) $item->extension_id);
        Services::Registry()->set('Parameters', 'menu_title', $item->extensions_name);
        Services::Registry()->set('Parameters', 'menu_extension_id', (int) $item->extensions_id);
        Services::Registry()->set('Parameters', 'menu_path_node', $item->extensions_name);

        $registry = Services::Registry()->get('Parameters', 'catalog_page_type')
                . 'Menuitem'
                . 'Parameters';

        Services::Registry()->set('Parameters', 'criteria_source_id',
            (int) Services::Registry()->get($registry, 'criteria_source_id'));
        Services::Registry()->set('Parameters', 'criteria_catalog_type_id',
            (int) Services::Registry()->get($registry, 'criteria_catalog_type_id'));
        Services::Registry()->set('Parameters', 'criteria_extension_instance_id',
            (int) Services::Registry()->get($registry, 'criteria_extension_instance_id'));

        Services::Registry()->copy($item->model_registry . 'Parameters', 'Parameters');
        Services::Registry()->copy($item->model_registry . 'Metadata', 'Metadata');
        $this->setParameters('menuitem',
            $item->model_registry . 'Parameters',
            $item->model_registry . 'Metadata'
        );

	        /** Must be after parameters so as to not strip off menuitem */
        Services::Registry()->set('Parameters', 'menuitem_id', (int) $item->id);
        Services::Registry()->set('Parameters', 'page_type',
            Services::Registry()->get('Parameters', 'catalog_page_type'));

		$this->setExtensionPaths();

        return true;
    }

    /**
     * Get data for Menu Item or Item or List
     *
     * @param $id
     * @param $model_type
     * @param $model_name
     * @param $model_query_object
     *
     * @return array An object containing an array of data
     * @since   1.0
     */
    public function get($id = 0, $model_type = 'Datasource', $model_name = 'Content', $page_type = '')
    {
        Services::Profiler()->set('ContentHelper->get '
                . ' ID: ' . $id
                . ' Model Type: ' . $model_type
                . ' Model Name: ' . $model_name,
            LOG_OUTPUT_ROUTING, VERBOSE);

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $results = $controller->getModelRegistry($model_type, $model_name);
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('id', (int) $id);
        $controller->set('process_plugins', 1);

        if ($page_type == 'item') {
            $controller->set('get_customfields', 2);
        } else {
            $controller->set('get_customfields', 1);
        }

        $item = $controller->getData('item');
        if (count($item) == 0) {
            return array();
        }

        $item->model_registry = $controller->model_registry;

        return $item;
    }

    /**
     * Retrieves parameter set (form, item, list, or menuitem) and populates Parameters registry
     *
     * @param $pageTypeNamespace (ex. item, list, menuitem)
     * @param $parameterNamespace (ex. $item->model_registry . 'Parameters')
     * @param $metadataNamespace (ex. $item->model_registry . 'Metadata')
     * @param string $resourceNamespace (ex. ResourcesSystem)
     *
     * @return boolean
     * @since   1.0
     */
    public function setParameters($pageTypeNamespace, $parameterNamespace,
                                  $metadataNamespace, $resourceNamespace = '')
    {
        Services::Registry()->set('Parameters', 'page_type', $pageTypeNamespace);

		/** Retrieve array of Extension Instances Authorised for User  */
		Helpers::Extension()->setAuthorisedExtensions(0, 'Datasource', 'ExtensionInstances', 'List', NULL);

		/** I. $parameterNamespace: For an article, would be ArticlesResourceParameters */

        /** 1. $pageTypeNamespace ex. Item, Menuitem, List, Form		*/
        $newParameters = Services::Registry()->get($parameterNamespace, $pageTypeNamespace . '*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $pageTypeNamespace);
        }

        /** 2. Criteria Parameters (ex. criteria_content_type_id, etc.) */
        $newParameters = Services::Registry()->get($parameterNamespace, 'criteria*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $pageTypeNamespace);
        }

        /** 3. Enable Parameters (ex. enable_response_comments, etc.) */
        $newParameters = Services::Registry()->get($parameterNamespace, 'enable*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $pageTypeNamespace);
        }

        /** I. $resourceNamespace: For an article, would be ResourcesSystem */
        if ($resourceNamespace == '') {
        } else {
            /** 1. $pageTypeNamespace ex. Item, Menuitem, List, Form		*/
            $newParameters = Services::Registry()->get($resourceNamespace . 'Parameters', $pageTypeNamespace . '*');
            if (is_array($newParameters) && count($newParameters) > 0) {
                $this->processParameterSet($newParameters, $pageTypeNamespace);
            }

            /** 2. Criteria Parameters (ex. criteria_content_type_id, etc.) */
            $newParameters = Services::Registry()->get($resourceNamespace . 'Parameters', 'criteria*');
            if (is_array($newParameters) && count($newParameters) > 0) {
                $this->processParameterSet($newParameters, $pageTypeNamespace);
            }
            /** 3. Enable Parameters (ex. enable_response_comments, etc.) */
            $newParameters = Services::Registry()->get($resourceNamespace . 'Parameters', 'enable*');

            if (is_array($newParameters) && count($newParameters) > 0) {
                $this->processParameterSet($newParameters, $pageTypeNamespace);
            }
        }

        /** 3. Application defaults */
        $applicationDefaults = Services::Registry()->get('Configuration', $pageTypeNamespace . '*');
        if (count($applicationDefaults) > 0) {
            $this->processParameterSet($applicationDefaults, $pageTypeNamespace);
        }

        /** Merge in remaining */
        Services::Registry()->merge($parameterNamespace, 'Parameters', true);

        Services::Registry()->merge($metadataNamespace, 'Metadata');

        if ($resourceNamespace == '') {
        } else {
            Services::Registry()->merge($resourceNamespace . 'Metadata', 'Metadata', true);
        }

        /**  Merge in matching Configuration data  */
        Services::Registry()->merge('Configuration', 'Parameters', true);

        /** Save existing parameters */
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

		/** Set Theme, Page, Template and Wrap */
        Helpers::Extension()->setThemePageView();

        Helpers::Extension()->setTemplateWrapModel();

        /** Merge Parameters in (Pre-wrap) */
        if (is_array($savedParameters) && count($savedParameters) > 0) {
            foreach ($savedParameters as $key => $value) {
                Services::Registry()->set('Parameters', $key, $value);
            }
        }

        Services::Registry()->sort('Parameters');
        Services::Registry()->sort('Metadata');

        /** Remove standard patterns no longer needed -- need for configuration views  */
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
     * processParameterSet iterates a new parameter set to determine whether or not it should be applied
     *
     * @param $parameterSet
     * @param $pageTypeNamespace
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
	 * Sets the namespace, path and URL Path for extensions
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function setExtensionPaths()
	{
		Services::Registry()->set('Parameters', 'extension_name_path_node',
			Services::Registry()->get('Parameters', 'model_name'));

		if (Services::Registry()->get('Parameters', 'model_type') == 'Resource') {
			$cattype = CATALOG_TYPE_RESOURCE;
		} else {
			$cattype = Services::Registry()->get('Parameters', 'criteria_catalog_type_id');
		}

		Services::Registry()->set('Parameters', 'extension_path',
			Helpers::Extension()->getPath($cattype,
				Services::Registry()->get('Parameters', 'extension_name_path_node'))
		);

		Services::Registry()->set('Parameters', 'extension_path_url',
			Helpers::Extension()->getPathURL($cattype,
				Services::Registry()->get('Parameters', 'extension_name_path_node'))
		);

		Services::Registry()->set('Parameters', 'extension_namespace',
			Helpers::Extension()->getNamespace($cattype,
				Services::Registry()->get('Parameters', 'extension_name_path_node')));

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
        $controller->set('process_plugins', 0);
        $controller->set('get_customfields', 0);

        $results = $controller->getModelRegistry('Datasource', 'CatalogTypes');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->model->query->where('a.extension_instance_id = ' . (int) $id);
        $item = $controller->getData('item');

        if (count($item) == 0) {
            return array();
        }

        return $item;
    }

    /**
     * Get Parameters for Resource Content
     *
     * Registry => Services::Registry()->get('ArticlesResource', '*');
     * Parameters => Services::Registry()->get('ArticlesResource', 'Parameters')
     *
     * @param string $model_type
     * @param $model_name
     *
     * @return array An object containing an array of basic resource info, parameters in registry
     * @since   1.0
     */
    public function getResourceContentParameters($model_type = 'Resource', $model_name)
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $controller->set('process_plugins', 0);
        $controller->set('get_customfields', 1);

        $results = $controller->getModelRegistry($model_type, $model_name);
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        return true;
    }

    /**
     * Get Parameters for Resource
     *
     * Registry => Services::Registry()->get('ResourcesSystem', '*');
     * Parameters => Services::Registry()->get('ResourcesSystemParameters', '*');
     *
     * @param  $id
     *
     * @return array An object containing an array of basic resource info, parameters in registry
     * @since   1.0
     */
    public function getResourceExtensionParameters($id = 0)
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $results = $controller->getModelRegistry('System', 'Resources');
        if ($results === false) {
            return false;
        }
        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('id', (int) $id);
        $controller->set('process_plugins', 0);
        $controller->set('get_customfields', 1);
        $controller->set('check_view_level_access', 0);

        $item = $controller->getData('item');
        if (count($item) == 0) {
            return array();
        }

        return $item;
    }

    /**
     * Get Menuitem Content Parameters for specific Resource
     *
     * Helpers::Content()->getResourceMenuitemParameters('Grid', $extension_instance_id);
     *
     * If the menuitem is found, parameters can be accessed, as follows (assumes Grid menuitem type):
     * Parameters => Services::Registry()->get('GridMenuitemParameters', '*');
     *
     * @param string $page_type
     * @param $extension_instance_id
     *
     * @return array An object containing an array of basic resource info, parameters in registry
     * @since   1.0
     */
    public function getResourceMenuitemParameters($page_type, $extension_instance_id)
    {
        $page_type = ucfirst(strtolower($page_type));

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $controller->set('process_plugins', 0);
        $controller->set('get_customfields', 1);

        $controller->getModelRegistry('Menuitem', $page_type);

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->model->query->where('a.page_type = ' . $controller->model->db->q($page_type));
        $controller->model->query->where('a.catalog_type_id = ' . (int) CATALOG_TYPE_MENUITEM);
        $value = '"criteria_extension_instance_id":"' . $extension_instance_id . '"';
        $controller->model->query->where('a.parameters LIKE ' . $controller->model->db->q('%' . $value . '%'));

        $menuitem = $controller->getData('item');

        if ($menuitem === false) {
            return false;
        }

        $menuitem->table_registry = $page_type . 'Menuitem';

        return $menuitem;
    }
}
