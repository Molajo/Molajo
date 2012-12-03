<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
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
     * Retrieve Route information for a specific Extension
     *
     * @param   string  $extension_id
     * @param   string  $model_type
     * @param   string  $model_name
     * @param   string  $check_permissions
     *
     * @return  boolean
     * @since   1.0
     */
    public function getExtension(
        $extension_id,
        $model_type = DATA_SOURCE_LITERAL,
        $model_name = 'ExtensionInstances',
        $check_permissions = 0
    ) {
        $item = Helpers::Extension()->get($extension_id, $model_type, $model_name, $check_permissions);

        if (count($item) == 0) {
            Services::Registry()->set(PARAMETERS_LITERAL, 'status_found', false);
            return false;
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_id', $item->extensions_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_name', $item->extensions_name);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_name_path_node', $item->extensions_name);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_title', $item->title);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_translation_of_id', (int)$item->translation_of_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_language', $item->language);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_view_group_id', $item->view_group_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_id', $item->catalog_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_id', (int)$item->catalog_type_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_title', $item->catalog_types_title);

        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'extension_path',
            $this->getPath(
                (int)$item->catalog_type_id,
                Services::Registry()->get('parameters', 'extension_name_path_node')
            )
        );

        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'extension_path_url',
            $this->getPathURL(
                (int)$item->catalog_type_id,
                Services::Registry()->get('parameters', 'extension_name_path_node')
            )
        );

        $customFieldTypes = Services::Registry()->get($item->model_registry, CUSTOMFIELDGROUPS_LITERAL);

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
     * Common query for all Extensions -
     * First time it runs, ALL extension data is returned and stored in registry
     * Subsequent calls reuse the registry instead of issuing new calls
     *
     * @param   string  $extension_id
     * @param   string  $model_type
     * @param   string  $model_name
     * @param   string  $query_object
     * @param   string  $catalog_type_id
     *
     * @return  bool
     * @since   1.0
     */
    public function setAuthorisedExtensions(
        $extension_id = 0,
        $model_type = DATA_SOURCE_LITERAL,
        $model_name = 'ExtensionInstances',
        $query_object = QUERY_OBJECT_ITEM,
        $catalog_type_id = null
    ) {

        $results = Helpers::Extension()->get(0, DATA_SOURCE_LITERAL, 'ExtensionInstances', QUERY_OBJECT_LIST, null, 1);

        if ($results === false || count($results) == 0) {
            echo 'No authorised extensions for user.';
            //throw error
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
     * @param   string  $extension_id
     * @param   string  $model_type
     * @param   string  $model_name
     * @param   string  $query_object
     * @param   string  $catalog_type_id
     * @param   string  $check_permissions (not possible during language list)
     *
     * @return  bool
     * @since   1.0
     */
    public function get(
        $extension_id = 0,
        $model_type = DATA_SOURCE_LITERAL,
        $model_name = 'ExtensionInstances',
        $query_object = QUERY_OBJECT_ITEM,
        $catalog_type_id = null,
        $check_permissions = 0
    ) {
        if (Services::Registry()->get('CurrentPhase') == 'PROFILER_ROUTING') {
            $phase = PROFILER_ROUTING;
        } else {
            $phase = PROFILER_RENDERING;
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry($model_type, $model_name);
        $controller->setDataobject();

        $primary_prefix = $controller->get('primary_prefix', 'a', 'model_registry');
        $primary_key = $controller->get('primary_key', 'id', 'model_registry');

        if ((int)$extension_id == 0) {
        } else {

            $controller->model->query->where(
                $controller->model->db->qn($primary_prefix)
                    . '.'
                    . $controller->model->db->qn('id')
                    . ' = '
                    . (int)$extension_id
            );
            $controller->set('process_plugins', 0, 'model_registry');
            $query_object = QUERY_OBJECT_ITEM;
        }

        if ((int)$catalog_type_id == 0) {
        } else {

            $controller->model->query->where(
                $controller->model->db->qn($primary_prefix)
                    . '.'
                    . $controller->model->db->qn('catalog_type_id')
                    . ' = '
                    . (int)$catalog_type_id
            );
        }

        if (strtolower($query_object) == QUERY_OBJECT_LIST) {

            $controller->set('model_offset', 0, 'model_registry');
            $controller->set('model_count', 999999, 'model_registry');
            $controller->set('use_pagination', 0, 'model_registry');
            $controller->set('use_special_joins', 1, 'model_registry');
            $controller->set('get_customfields', 2, 'model_registry');

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

        $controller->set('check_view_level_access', $check_permissions, 'model_registry');

        if ($model_type == DATA_SOURCE_LITERAL) {

        } else {
            $controller->model->query->where(
                $controller->model->db->qn('catalog')
                    . '.'
                    . $controller->model->db->qn('enabled')
                    . ' = '
                    . (int)1
            );
        }

        /** All Extension Rows available to avoid additional queries */
        if (Services::Registry()->exists('AuthorisedExtensions') === true) {

            if (strtolower($query_object) == QUERY_OBJECT_ITEM && (int)$extension_id > 0) {
                $saved = Services::Registry()->get('AuthorisedExtensions', $extension_id, '');
                if (is_object($saved)) {
                    $controller->set('get_customfields', 1, 'model_registry');
                    $temp = $controller->addCustomFields(array($saved), QUERY_OBJECT_ITEM, 1);
                    $temp[0]->model_registry = ucfirst(strtolower($model_name)) . ucfirst(
                        strtolower($model_type)
                    );
                    $query_results = $temp[0];
                    return $query_results;
                }
            }
        }

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

        if ($query_object == QUERY_OBJECT_ITEM) {
            $query_results->model_registry = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
        }

        return $query_results;
    }

    /**
     * Retrieves Extension ID, given Title and catalog ID, first from registry, if not available, the DB
     *
     * @param   $catalog_type_id
     * @param   $title
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public function getInstanceID($catalog_type_id, $title)
    {
        if (Services::Registry()->exists('AuthorisedExtensionsByInstanceTitle') === true) {
            $key = trim($title) . $catalog_type_id;
            $id = Services::Registry()->get('AuthorisedExtensionsByInstanceTitle', $key, 0);
            if ((int)$id == 0) {
            } else {
                return $id;
            }
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATA_SOURCE_LITERAL, 'ExtensionInstances');
        $controller->setDataobject();

        $controller->set('process_plugins', 0, 'model_registry');
        $prefix = $controller->get('primary_prefix', 'a', 'model_registry');

        $controller->model->query->select(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('id')
        );

        $controller->model->query->select(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('title')
                . ' = '
                . $controller->model->db->q($title)
        );

        $controller->model->query->select(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('catalog_type_id')
                . ' = '
                . (int)$catalog_type_id
        );

        return $controller->getData(QUERY_OBJECT_RESULT);
    }

    /**
     * Retrieves Extension Title, given the extension_instance_id, first from registry, if not available, the DB
     *
     * @param   $extension_instance_id
     *
     * @return  bool|mixed
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
        $controller->getModelRegistry(DATA_SOURCE_LITERAL, 'ExtensionInstances');
        $controller->setDataobject();

        $controller->set('process_plugins', 0, 'model_registry');
        $prefix = $controller->get('primary_prefix', 'a', 'model_registry');

        $controller->model->query->select(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('title')
        );

        $controller->model->query->where(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('id')
                . ' = '
                . (int)$extension_instance_id
        );

        return $controller->getData(QUERY_OBJECT_RESULT);
    }

    /**
     * Retrieves the folder node for the specific extension
     *
     * @param   $extension_instance_id
     *
     * @return  bool|mixed
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
        $controller->getModelRegistry(DATA_SOURCE_LITERAL, 'Extensions');
        $controller->setDataobject();

        $controller->set('process_plugins', 0, 'model_registry');

        $controller->model->query->select(
            $controller->model->db->qn('a')
                . '.'
                . $controller->model->db->qn('name')
        );

        $controller->model->query->from(
            $controller->model->db->qn('#__extensions')
                . ' as '
                . $controller->model->db->qn('a')
        );

        $controller->model->query->from(
            $controller->model->db->qn('#__extension_instances')
                . ' as '
                . $controller->model->db->qn('b')
        );

        $controller->model->query->where(
            $controller->model->db->qn('a')
                . '.'
                . $controller->model->db->qn('id')
                . ' = '
                . $controller->model->db->qn('b')
                . '.'
                . $controller->model->db->qn('extension_id')
        );

        $controller->model->query->where(
            $controller->model->db->qn('b')
                . '.'
                . $controller->model->db->qn('id')
                . ' = '
                . (int)$extension_instance_id
        );

        return $controller->getData(QUERY_OBJECT_RESULT);
    }

    /**
     * Returns path for Extension - make certain to send in extension name, not instance title.
     *
     * @param   string  $catalog_type_id
     * @param   string  $node
     *
     * @return  string
     * @since   1.0
     */
    public function getPath($catalog_type_id, $node)
    {
        if ($catalog_type_id == CATALOG_TYPE_PAGE_VIEW) {
            return Helpers::View()->getPath($node, CATALOG_TYPE_PAGE_VIEW_LITERAL);

        } elseif ($catalog_type_id == CATALOG_TYPE_TEMPLATE_VIEW) {
            return Helpers::View()->getPath($node, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        } elseif ($catalog_type_id == CATALOG_TYPE_WRAP_VIEW) {
            return Helpers::View()->getPath($node, CATALOG_TYPE_WRAP_VIEW_LITERAL);
        }

        $type = Helpers::Extension()->getType($catalog_type_id);

        if ($type == CATALOG_TYPE_RESOURCE_LITERAL) {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            if (file_exists(
                PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node));
            }

            return false;

        } elseif ($type == CATALOG_TYPE_MENUITEM_LITERAL) {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            return false;

        } elseif ($type == CATALOG_TYPE_LANGUAGE_LITERAL) {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            return false;

        }
        if (file_exists(
            PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')
        ) {
            return PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node));
        }

        return false;
    }

    /**
     * Return URL path for Extension
     *
     * @param    $catalog_type_id
     * @param    $node
     *
     * @return   mixed
     * @since    1.0
     */
    public function getPathURL($catalog_type_id, $node)
    {
        if ($catalog_type_id == CATALOG_TYPE_PAGE_VIEW) {
            return Helpers::View()->getPathURL($node, CATALOG_TYPE_PAGE_VIEW_LITERAL);

        } elseif ($catalog_type_id == CATALOG_TYPE_TEMPLATE_VIEW) {
            return Helpers::View()->getPathURL($node, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        } elseif ($catalog_type_id == CATALOG_TYPE_WRAP_VIEW) {
            return Helpers::View()->getPathURL($node, CATALOG_TYPE_WRAP_VIEW_LITERAL);
        }

        $type = Helpers::Extension()->getType($catalog_type_id);

        if ($type == CATALOG_TYPE_RESOURCE_LITERAL) {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            if (file_exists(
                PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return CORE_SYSTEM_URL . '/' . ucfirst(strtolower($node));
            }

            return false;

        } elseif ($type == CATALOG_TYPE_MENUITEM_LITERAL) {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            return false;

        } elseif ($type == CATALOG_TYPE_LANGUAGE_LITERAL) {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return EXTENSIONS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
            }

            return false;
        }
    }

    /**
     * Return namespace for extension
     *
     * @param   $node
     *
     * @return  bool|string
     * @since   1.0
     */
    public function getNamespace($catalog_type_id, $node)
    {
        if ($catalog_type_id == CATALOG_TYPE_PAGE_VIEW) {
            return Helpers::View()->getNamespace($node, CATALOG_TYPE_PAGE_VIEW_LITERAL);

        } elseif ($catalog_type_id == CATALOG_TYPE_TEMPLATE_VIEW) {
            return Helpers::View()->getNamespace($node, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        } elseif ($catalog_type_id == CATALOG_TYPE_WRAP_VIEW) {
            return Helpers::View()->getNamespace($node, CATALOG_TYPE_WRAP_VIEW_LITERAL);
        }

        $type = Helpers::Extension()->getType($catalog_type_id);

        if ($type == CATALOG_TYPE_RESOURCE_LITERAL) {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return 'Extension\\Resource\\' . ucfirst(strtolower($node));
            }

            if (file_exists(
                PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return 'Vendor\\Molajo\\System\\' . ucfirst(strtolower($node));
            }

            return false;

        } elseif ($type == CATALOG_TYPE_MENUITEM_LITERAL) {
            if (file_exists(EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
                return 'Extension\\Menuitem\\' . ucfirst(strtolower($node));
            }

            return false;

        }

        return false;
    }

    /**
     * Determine the default theme value, given system default sequence
     *
     * @return  boolean
     * @since   1.0
     */
    public function setThemePageView()
    {
        $theme_id = (int)Services::Registry()->get('parameters', 'theme_id');
        $page_view_id = (int)Services::Registry()->get('parameters', 'page_view_id');

        Helpers::Theme()->get($theme_id);
        Helpers::View()->get($page_view_id, CATALOG_TYPE_PAGE_VIEW_LITERAL);

        return true;
    }

    /**
     * setTemplateWrapModel - Determine the default Template and Wrap values
     *
     * @return  string
     * @since   1.0
     */
    public function setTemplateWrapModel()
    {
        $template_view_id = Services::Registry()->get('parameters', 'template_view_id');
        $wrap_view_id = Services::Registry()->get('parameters', 'wrap_view_id');

        Helpers::View()->get($template_view_id, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        Helpers::View()->get($wrap_view_id, CATALOG_TYPE_WRAP_VIEW_LITERAL);

        return;
    }

    /**
     * Retrieve the path node for a specified catalog type or
     * it retrieves the catalog id value for the requested type
     *
     * @param   int   $catalog_type_id
     * @param   null  $catalog_type
     *
     * @return  string
     * @since   1.0
     */
    public function getType($catalog_type_id = 0, $catalog_type = null)
    {
        if ((int)$catalog_type_id == 0) {

            if ($catalog_type == CATALOG_TYPE_APPLICATION_LITERAL) {
                return CATALOG_TYPE_APPLICATION;

            } elseif ($catalog_type == CATALOG_TYPE_FIELD_LITERAL) {
                return CATALOG_TYPE_FIELD;

            } elseif ($catalog_type == CATALOG_TYPE_LANGUAGE_LITERAL) {
                return CATALOG_TYPE_LANGUAGE;

            } elseif ($catalog_type == CATALOG_TYPE_LANGUAGE_STRING_LITERAL) {
                return CATALOG_TYPE_LANGUAGE_STRING;

            } elseif ($catalog_type == CATALOG_TYPE_MENUITEM_LITERAL) {
                return CATALOG_TYPE_MENUITEM;

            } elseif ($catalog_type == CATALOG_TYPE_MESSAGE_LITERAL) {
                return CATALOG_TYPE_MESSAGE;

            } elseif ($catalog_type == CATALOG_TYPE_PAGE_VIEW_LITERAL) {
                return CATALOG_TYPE_PAGE_VIEW;

            } elseif ($catalog_type == CATALOG_TYPE_PLUGIN_LITERAL) {
                return CATALOG_TYPE_PLUGIN;

            } elseif ($catalog_type == CATALOG_TYPE_RESOURCE_LITERAL) {
                return CATALOG_TYPE_RESOURCE;

            } elseif ($catalog_type == CATALOG_TYPE_SERVICE_LITERAL) {
                return CATALOG_TYPE_SERVICE;

            } elseif ($catalog_type == CATALOG_TYPE_SITE_LITERAL) {
                return CATALOG_TYPE_SITE;

            } elseif ($catalog_type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL) {
                return CATALOG_TYPE_TEMPLATE_VIEW;

            } elseif ($catalog_type == CATALOG_TYPE_THEME_LITERAL) {
                return CATALOG_TYPE_THEME;

            } elseif ($catalog_type == CATALOG_TYPE_USER_LITERAL) {
                return CATALOG_TYPE_USERS;

            } elseif ($catalog_type == CATALOG_TYPE_WRAP_VIEW_LITERAL) {
                return CATALOG_TYPE_WRAP_VIEW;
            }

            return CATALOG_TYPE_RESOURCE;
        }

        if ($catalog_type_id == CATALOG_TYPE_APPLICATION) {
            return CATALOG_TYPE_APPLICATION_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_FIELD) {
            return CATALOG_TYPE_FIELD_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_LANGUAGE) {
            return CATALOG_TYPE_LANGUAGE_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_LANGUAGE_STRING) {
            return CATALOG_TYPE_LANGUAGE_STRING_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_MENUITEM) {
            return CATALOG_TYPE_MENUITEM_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_MESSAGE) {
            return CATALOG_TYPE_MESSAGE_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_PAGE_VIEW) {
            return CATALOG_TYPE_PAGE_VIEW_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_PLUGIN) {
            return CATALOG_TYPE_PLUGIN_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_RESOURCE) {
            return CATALOG_TYPE_RESOURCE_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_SERVICE) {
            return CATALOG_TYPE_SERVICE_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_SITE) {
            return CATALOG_TYPE_SITE_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_TEMPLATE) {
            return CATALOG_TYPE_TEMPLATE_VIEW_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_THEME) {
            return CATALOG_TYPE_THEME_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_USERS) {
            return CATALOG_TYPE_USER_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_WRAP) {
            return CATALOG_TYPE_WRAP_VIEW_LITERAL;
        }

        return CATALOG_TYPE_RESOURCE_LITERAL;
    }
}
