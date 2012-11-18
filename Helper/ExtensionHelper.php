<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Helper;

use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * ExtensionHelper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class ExtensionHelper
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
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
            self::$instance = new ExtensionHelper();
        }

        return self::$instance;
    }

    /**
     * Retrieve Route information for a specific Extension
     *
     * @param  $extension_id
     * @param string $model_type
     * @param string $model_name
	 * @param string $acl_check
     *
     * @return boolean
     * @since   1.0
     */
    public function getExtension($extension_id, $model_type = 'Datasource', $model_name = 'ExtensionInstances', $acl_check = 0)
    {
        $item = Helpers::Extension()->get($extension_id, $model_type, $model_name, $acl_check);

        /** 404: routeRequest handles redirecting to error page */
        if (count($item) == 0) {
            Services::Registry()->set('Parameters', 'status_found', false);
            return false;
        }

        /** Route Registry */
        Services::Registry()->set('Parameters', 'extension_id', $item->extensions_id);
        Services::Registry()->set('Parameters', 'extension_name', $item->extensions_name);
        Services::Registry()->set('Parameters', 'extension_name_path_node', $item->extensions_name);
        Services::Registry()->set('Parameters', 'extension_title', $item->title);
        Services::Registry()->set('Parameters', 'extension_translation_of_id', (int) $item->translation_of_id);
        Services::Registry()->set('Parameters', 'extension_language', $item->language);
        Services::Registry()->set('Parameters', 'extension_view_group_id', $item->view_group_id);
        Services::Registry()->set('Parameters', 'extension_catalog_id', $item->catalog_id);
        Services::Registry()->set('Parameters', 'extension_catalog_type_id', (int) $item->catalog_type_id);
        Services::Registry()->set('Parameters', 'extension_catalog_type_title', $item->catalog_types_title);

        Services::Registry()->set('Parameters', 'extension_path',
            $this->getPath((int) $item->catalog_type_id,
                Services::Registry()->get('Parameters', 'extension_name_path_node'))
        );

        Services::Registry()->set('Parameters', 'extension_path_url',
            $this->getPathURL((int) $item->catalog_type_id,
                Services::Registry()->get('Parameters', 'extension_name_path_node'))
        );

        /** Process each field namespace  */
        $customFieldTypes = Services::Registry()->get($item->model_registry, 'CustomFieldGroups');

        if (count($customFieldTypes) > 0) {
            foreach ($customFieldTypes as $customFieldName) {
                $customFieldName = ucfirst(strtolower($customFieldName));
                Services::Registry()->merge($item->model_registry . $customFieldName, $customFieldName);
                Services::Registry()->deleteRegistry($item->model_registry . $customFieldName);
            }
        }

        return true;
    }

	/**
	 * Common query for all Extensions - Merges into Parameter Registry
	 *
	 * @param        $extension_id
	 * @param string $model_type
	 * @param string $model_name
	 * @param string $query_object
	 * @param string $catalog_type_id
	 *
	 * @return bool
	 * @since   1.0
	 */
	public function setAuthorisedExtensions(
		$extension_id = 0, $model_type = 'Datasource', $model_name = 'ExtensionInstances',
		$query_object = 'item', $catalog_type_id = null)
	{
		$results = Helpers::Extension()->get(0, 'Datasource', 'ExtensionInstances', 'List', NULL, 1);
		if ($results === false || count($results) == 0) {
			//throw error
			echo 'No authorised extensions for user.';
			die;
		}

		Services::Registry()->createRegistry('AuthorisedExtensions');
		Services::Registry()->createRegistry('AuthorisedExtensionsByInstanceTitle');

		foreach ($results as $extension) {

			Services::Registry()->set('AuthorisedExtensions', $extension->id, $extension);

			if ($extension->catalog_type_id == CATALOG_TYPE_MENUITEM) {
			} else {
				$key = trim($extension->title) . $extension->catalog_type_id;
				Services::Registry()->set('AuthorisedExtensionsByInstanceTitle', $key, $extension->id);
			}
		}

		Services::Registry()->sort('AuthorisedExtensions');
		Services::Registry()->sort('AuthorisedExtensionsByInstanceTitle');

		return true;
	}

	/**
     * Common query for all Extensions - Merges into Parameter Registry
     *
     * @param        $extension_id
     * @param string $model_type
     * @param string $model_name
     * @param string $query_object
     * @param string $catalog_type_id
	 * @param string $acl_check (not possible during language list)
     *
     * @return bool
     * @since   1.0
     */
    public function get(
        $extension_id = 0, $model_type = 'Datasource', $model_name = 'ExtensionInstances',
        $query_object = 'item', $catalog_type_id = null, $acl_check = 0)
    {
        if (Services::Registry()->get('CurrentPhase') == 'LOG_OUTPUT_ROUTING') {
            $phase = LOG_OUTPUT_ROUTING;
        } else {
            $phase = LOG_OUTPUT_RENDERING;
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $controller->getModelRegistry($model_type, $model_name);

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $primary_prefix = $controller->get('primary_prefix');
        $primary_key = $controller->get('primary_key');

        if ((int) $extension_id == 0) {
        } else {

			$controller->model->query->where(
				$controller->model->db->qn($primary_prefix)
					. '.'
					. $controller->model->db->qn('id')
					. ' = '
					. (int) $extension_id
			);
            $controller->set('process_plugins', 0);
            $query_object = 'item';
        }

        if ((int) $catalog_type_id == 0) {
        } else {

            $controller->model->query->where(
					$controller->model->db->qn($primary_prefix)
				. '.'
				. $controller->model->db->qn('catalog_type_id')
                . ' = '
				. (int) $catalog_type_id
			);
        }

        if (strtolower($query_object) == 'list') {
            $controller->set('model_offset', 0);
            $controller->set('model_count', 999999);
			$controller->set('use_pagination', 0);
			$controller->set('use_special_joins', 1);
			$controller->set('get_customfields', 2);

			$controller->model->query->where(
				$controller->model->db->qn($primary_prefix)
					. '.'
					. $controller->model->db->qn('catalog_type_id')
					. ' <> '
					. $controller->model->db->qn($primary_prefix)
					. '.'
					. $controller->model->db->qn($primary_key)
			);
        }

		$controller->set('check_view_level_access', $acl_check);

		if ($model_type == 'Datasource') {
		} else {
			$controller->model->query->where(
				$controller->model->db->qn('catalog')
					. '.'
					. $controller->model->db->qn('enabled')
					. ' = '
					. (int) 1
			);
		}

		/** First, attempt to retrieve from registry */
		if (Services::Registry()->exists('AuthorisedExtensions') === true) {

			if (strtolower($query_object) == 'item' && (int) $extension_id > 0) {
				$saved =  Services::Registry()->get('AuthorisedExtensions', $extension_id, '');
				if (is_object($saved)) {
					$controller->set('get_customfields', 1);
					$query_results = $controller->addCustomFields(array($saved), 'item', 1);
					$query_results->model_registry = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
					return $query_results;
				}
			}
		}

		/** Then, if not available, run the query */
        $query_results = $controller->getData($query_object);

        if ($query_results === false || $query_results === null) {

            echo 'Extension ID ' . $extension_id . '<br />';
            echo 'Model Type ' . $model_type . '<br />';
            echo 'Model Name ' . $model_name . '<br />';
            echo 'Query Object ' . $query_object . '<br />';
            echo 'Catalog Type ID ' . $catalog_type_id . '<br />';

            echo '<br />';
            echo $controller->model->query->__toString();
            echo '<br />';

            echo '<pre>';
            var_dump($query_results);
            echo '</pre>';

            return false;
        }

		if ($query_object == 'item') {
			$query_results->model_registry = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
		}

        return $query_results;
    }

    /**
     * getInstanceID
     *
     * Retrieves Extension ID, given Title and catalog ID, first from registry, if not available, the DB
     *
     * @param  $catalog_type_id
     * @param  $title
     *
     * @return bool|mixed
     * @since   1.0
     */
    public function getInstanceID($catalog_type_id, $title)
    {
		if (Services::Registry()->exists('AuthorisedExtensionsByInstanceTitle') === true) {
			$key = trim($title) . $catalog_type_id;
			$id = Services::Registry()->get('AuthorisedExtensionsByInstanceTitle', $key, 0);
			if ((int) $id == 0) {
			} else {
				return $id;
			}
		}

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $query_results = $controller->getModelRegistry('Datasource', 'ExtensionInstances');
        if ($query_results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('process_plugins', 0);

        $controller->model->query->select($controller->model->db->qn('a.id'));
        $controller->model->query->where($controller->model->db->qn('a.title') . ' = ' . $controller->model->db->q($title));
        $controller->model->query->where($controller->model->db->qn('a.catalog_type_id') . ' = ' . (int) $catalog_type_id);

        return $controller->getData('result');
    }

    /**
     * getInstanceTitle
	 *
	 * Retrieves Extension Title, given the extension_instance_id, first from registry, if not available, the DB
     *
     * @param   $extension_instance_id
     *
     * @return bool|mixed
     * @since   1.0
     */
    public function getInstanceTitle($extension_instance_id)
    {
		if (Services::Registry()->exists('AuthorisedExtensions') === true) {
			$object = Services::Registry()->get('AuthorisedExtensions', $extension_instance_id, '');
			if ($object === false) {
				$title = '';
			} else {
				$title = $object->title;
			}

			if ($title == '') {
			} else {
				return $title;
			}
		}

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $query_results = $controller->getModelRegistry('Datasource', 'ExtensionInstances');
        if ($query_results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }
        $controller->set('process_plugins', 0);

        $controller->model->query->select($controller->model->db->qn('a.title'));
        $controller->model->query->where($controller->model->db->qn('a.id') . ' = ' . (int) $extension_instance_id);

        return $controller->getData('result');
    }

    /**
     * getExtensionNode
     *
     * Retrieves the folder node for the specific extension
     *
     * @param  $extension_instance_id
     *
     * @return bool|mixed
     * @since   1.0
     */
    public function getExtensionNode($extension_instance_id)
    {
		if (Services::Registry()->exists('AuthorisedExtensions') === true) {
			$object = Services::Registry()->get('AuthorisedExtensions', $extension_instance_id, '');
			if (is_object($object)) {
				$name = $object->extensions_name;
			} else {
				$name = '';
			}

			if ($name == '') {
			} else {
				return $name;
			}
		}

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $query_results = $controller->getModelRegistry('Datasource', 'Extensions');
        if ($query_results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('process_plugins', 0);

        $controller->model->query->select($controller->model->db->qn('a.name'));

        $controller->model->query->from($controller->model->db->qn('#__extensions') . ' as ' . $controller->model->db->qn('a'));
        $controller->model->query->from($controller->model->db->qn('#__extension_instances') . ' as ' . $controller->model->db->qn('b'));

        $controller->model->query->where($controller->model->db->qn('a.id') . ' = ' . $controller->model->db->qn('b.extension_id'));
        $controller->model->query->where($controller->model->db->qn('b.id') . ' = ' . (int) $extension_instance_id);

        return $controller->getData('result');
    }

    /**
     * getPath
     *
     * Return path for Extension - make certain to send in extension name, not
     *     extension instance title. Extensions Instances do not have to have
     *  the same name as the Extension, itself. The Extension name is what
     *  is used in the path statements.
     *
     * @param $catalog_type_id
     * @param $node
     *
     * @return string
     * @since 1.0
     */
    public function getPath($catalog_type_id, $node)
    {
        if ($catalog_type_id == CATALOG_TYPE_PAGE_VIEW) {
            return Helpers::View()->getPath($node, 'Page');

        } elseif ($catalog_type_id == CATALOG_TYPE_TEMPLATE_VIEW) {
            return Helpers::View()->getPath($node, 'Template');

        } elseif ($catalog_type_id == CATALOG_TYPE_WRAP_VIEW) {
            return Helpers::View()->getPath($node, 'Wrap');
        }

        $type = Helpers::Extension()->getType($catalog_type_id);

        if ($type == 'Resource') {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            if (file_exists(PLATFORM_FOLDER . '/' . 'System' . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return PLATFORM_FOLDER . '/' . 'System' . '/' . ucfirst(strtolower($node));
            }

            return false;

        } elseif ($type == 'Menuitem') {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            return false;

        } elseif ($type == 'Language') {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            return false;

        }    if (file_exists(PLATFORM_FOLDER . '/' . 'System' . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
        return PLATFORM_FOLDER . '/' . 'System' . '/' . ucfirst(strtolower($node));
    }

        return false;
    }

    /**
     * getPathURL
     *
     * Return URL path for Extension
     *
     * @param $catalog_type_id
     * @param $node
     *
     * @return mixed
     * @since 1.0
     */
    public function getPathURL($catalog_type_id, $node)
    {
        if ($catalog_type_id == CATALOG_TYPE_PAGE_VIEW) {
            return Helpers::View()->getPathURL($node, 'Page');

        } elseif ($catalog_type_id == CATALOG_TYPE_TEMPLATE_VIEW) {
            return Helpers::View()->getPathURL($node, 'Template');

        } elseif ($catalog_type_id == CATALOG_TYPE_WRAP_VIEW) {
            return Helpers::View()->getPathURL($node, 'Wrap');
        }

        $type = Helpers::Extension()->getType($catalog_type_id);

        if ($type == 'Resource') {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            if (file_exists(PLATFORM_FOLDER . '/' . 'System' . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return CORE_SYSTEM_URL . '/' . ucfirst(strtolower($node));
            }

            return false;

        } elseif ($type == 'Menuitem') {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            return false;

        } elseif ($type == 'Language') {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            return false;
        }
    }

    /**
     * getNamespace - Return namespace for extension
     *
     * @param   $node
     *
     * @return bool|string
     * @since   1.0
     */
    public function getNamespace($catalog_type_id, $node)
    {
        if ($catalog_type_id == CATALOG_TYPE_PAGE_VIEW) {
            return Helpers::View()->getNamespace($node, 'Page');

        } elseif ($catalog_type_id == CATALOG_TYPE_TEMPLATE_VIEW) {
            return Helpers::View()->getNamespace($node, 'Template');

        } elseif ($catalog_type_id == CATALOG_TYPE_WRAP_VIEW) {
            return Helpers::View()->getNamespace($node, 'Wrap');

        }

        $type = Helpers::Extension()->getType($catalog_type_id);

        if ($type == 'Resource') {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return 'Extension\\Resource\\' . ucfirst(strtolower($node));
            }

            if (file_exists(PLATFORM_FOLDER . '/' . 'System' . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return 'Vendor\\Molajo\\System\\' . ucfirst(strtolower($node));
            }

            return false;

        } elseif ($type == 'Menuitem') {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return 'Extension\\Menuitem\\' . ucfirst(strtolower($node));
            }

            return false;

        }

        return false;
    }

    /**
     * setThemePageView
     *
     * Determine the default theme value, given system default sequence
     *
     * @return boolean
     * @since   1.0
     */
    public function setThemePageView()
    {
        $theme_id = (int) Services::Registry()->get('Parameters', 'theme_id');
        $page_view_id = (int) Services::Registry()->get('Parameters', 'page_view_id');

        Helpers::Theme()->get($theme_id);

        Helpers::View()->get($page_view_id, 'Page');

        return true;
    }

    /**
     * setTemplateWrapModel - Determine the default Template and Wrap values
     *
     * @return string
     * @since   1.0
     */
    public function setTemplateWrapModel()
    {
        $template_view_id = Services::Registry()->get('Parameters', 'template_view_id');
        $wrap_view_id = Services::Registry()->get('Parameters', 'wrap_view_id');

        Helpers::View()->get($template_view_id, 'Template');

        Helpers::View()->get($wrap_view_id, 'Wrap');

        return;
    }

    /**
     * Retrieve the path node for a specified catalog type or
     * it retrieves the catalog id value for the requested type
     *
     * @param int  $catalog_type_id
     * @param null $catalog_type
     *
     * @return string
     * @since   1.0
     */
    public function getType($catalog_type_id = 0, $catalog_type = null)
    {
        if ((int) $catalog_type_id == 0) {

            if ($catalog_type == 'Resource') {
                return CATALOG_TYPE_RESOURCE;

            } elseif ($catalog_type == 'Menuitem') {
                return CATALOG_TYPE_MENUITEM;

            } elseif ($catalog_type == 'Language') {
                return CATALOG_TYPE_LANGUAGE;

            } elseif ($catalog_type == 'Theme') {
                return CATALOG_TYPE_THEME;

            } elseif ($catalog_type == 'Plugin') {
                return CATALOG_TYPE_PLUGIN;

            } elseif ($catalog_type == 'Page') {
                return CATALOG_TYPE_PAGE_VIEW;

            } elseif ($catalog_type == 'Template') {
                return CATALOG_TYPE_TEMPLATE_VIEW;

            } elseif ($catalog_type == 'Wrap') {
                return CATALOG_TYPE_WRAP_VIEW;
            }

            return CATALOG_TYPE_RESOURCE;

        } else {

            if ($catalog_type_id == CATALOG_TYPE_RESOURCE) {
                return 'Resource';

            } elseif ($catalog_type_id == CATALOG_TYPE_MENUITEM) {
                return 'Menuitem';

            } elseif ($catalog_type_id == CATALOG_TYPE_LANGUAGE) {
                return 'Language';

            } elseif ($catalog_type_id == CATALOG_TYPE_THEME) {
                return 'Theme';

            } elseif ($catalog_type_id == CATALOG_TYPE_PLUGIN) {
                return 'Plugin';

            } elseif ($catalog_type_id == CATALOG_TYPE_PAGE_VIEW) {
                return 'Page';

            } elseif ($catalog_type_id == CATALOG_TYPE_TEMPLATE_VIEW) {
                return 'Template';

            } elseif ($catalog_type_id == CATALOG_TYPE_WRAP_VIEW) {
                return 'Wrap';
            }

            return 'Resource';
        }

        /** Should not be reachable */
		//throw error
        return '';
    }
}
