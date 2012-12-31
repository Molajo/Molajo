<?php
/**
 * Theme Service Extension Helper
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Theme\Helper;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Extension Helper provides an interface to different types of extension information, like:
 *
 * - generating a list of extensions for which the site visitor is authorised to view;
 * - returning a list of language strings to be for site interface translation;
 * - query for a specific extension, be it a resource, view, language, or theme, etc.
 * - determine the path, URL path, or namespace for an extension
 * - return the path to the Favicon
 * - translate the catalog type id to text, and visa versa
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class ExtensionHelper
{
    /**
     * Primary interface to extension information enabling queries for a single extension,
     * all extensions the site visitor is authorised to view, or all extensions of a specific catalog
     * type, etc.
     *
     * Usage:
     *
     * To retrieve a specific Extension Instance
     *  $this->extensionHelper = new ExtensionHelper();
     *  $object = $this->extensionHelper->get($extension_instance_id);
     *
     * To retrieve a list of Extension Instances for a specific Catalog Type
     *  $this->extensionHelper = new ExtensionHelper();
     *  $object = $this->extensionHelper->get(0, $catalog_type);
     *
     * To override the default Extension Instance Model Registry
     *  $this->extensionHelper = new ExtensionHelper();
     *  $object = $this->extensionHelper->get($extension_instance_id, null, $model_type, $model_name);
     *
     * @param   string  $extension_instance_id
     * @param   string  $catalog_type      Numeric or textual key for View Catalog Type
     * @param   string  $model_type
     * @param   string  $model_name
     * @param   string  $check_permissions (not possible during language list)
     *
     * @return  bool
     * @since   1.0
     */
    public function get(
        $extension_instance_id = 0,
        $catalog_type = null,
        $model_type = null,
        $model_name = null,
        $check_permissions = null
    ) {
        if (((int)$catalog_type == 0 && trim($catalog_type) == '') || is_null($catalog_type)) {
            $catalog_type_id = 0;
        } elseif (is_numeric($catalog_type)) {
            $catalog_type_id = $catalog_type;
        } else {
            $catalog_type_id = $this->getType(0, $catalog_type);
        }

        if (is_null($model_type)) {
            $model_type = 'datasource';
        }
        if (is_null($model_name)) {
            $model_name = 'Extensioninstances';
        }
        $model_type = ucfirst(strtolower(trim($model_type)));
        $model_name = ucfirst(strtolower(trim($model_name)));

        $controllerClass = CONTROLLER_CLASS;
        $controller      = new $controllerClass();
        $controller->getModelRegistry($model_type, $model_name, 1);

        $primary_prefix = $controller->get('primary_prefix', 'a', 'model_registry');
        $primary_key    = $controller->get('primary_key', 'id', 'model_registry');

        if ((int)$extension_instance_id == 0) {
            $query_object = QUERY_OBJECT_LIST;

        } elseif (Services::Registry()->exists('AuthorisedExtensions') === true) {
            $saved = Services::Registry()->get('AuthorisedExtensions', $extension_instance_id);

            if (is_object($saved)) {
                $controller->set('get_customfields', 1, 'model_registry');
                $temp                    = $controller->addCustomFields(array($saved), QUERY_OBJECT_ITEM, 2);
                $temp[0]->model_registry = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
                $query_results           = $temp[0];

                return $query_results;

            } else {
                return false;
            }

        } else {
            $controller->model->query->where(
                $controller->model->db->qn($primary_prefix)
                    . '.'
                    . $controller->model->db->qn($primary_key)
                    . ' = '
                    . (int)$extension_instance_id
            );

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

        if (strtolower($query_object) == strtolower(QUERY_OBJECT_LIST)
            && $model_type == 'datasource'
            && $model_name == 'Extensioninstances'
        ) {

            $controller->set('model_offset', 0, 'model_registry');
            $controller->set('model_count', 999999, 'model_registry');
            $controller->set('use_pagination', 0, 'model_registry');
            $controller->set('use_special_joins', 1, 'model_registry');
            $controller->set('get_customfields', 2, 'model_registry');
        }

        if (strtolower($query_object) == strtolower(QUERY_OBJECT_LIST)) {
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

        if ($check_permissions === null) {
        } else {
            $controller->set('check_view_level_access', $check_permissions, 'model_registry');
        }

        if (strtolower($model_type) == 'datasource') {
        } else {
            $controller->model->query->where(
                $controller->model->db->qn('catalog')
                    . '.'
                    . $controller->model->db->qn('enabled')
                    . ' = '
                    . (int)1
            );
        }

        $query_results = $controller->getData($query_object);

        if ($query_object == QUERY_OBJECT_ITEM) {
            $query_results->model_registry = $model_name . $model_type;
        }

        return $query_results;
    }

    /**
     * Retrieves Extension ID for specified Extension Instance Title and Catalog Type values
     *
     * Note: All Extension queries first check the Registry of all Extensions for which the user is authorised.
     *
     * @param   $catalog_type  Numeric or textual key for View Catalog Type
     * @param   $title         Title of the Extension Instance
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public function getId($catalog_type, $title)
    {
        if (is_numeric($catalog_type)) {
            $catalog_type_id = $catalog_type;
        } else {
            $catalog_type_id = $this->getType(0, $catalog_type);
        }

        if (Services::Registry()->exists('AuthorisedExtensionsByInstanceTitle') === true) {
            $key = trim($title) . $catalog_type_id;
            $id  = Services::Registry()->get('AuthorisedExtensionsByInstanceTitle', $key, 0);
            if ((int)$id == 0) {
            } else {
                return $id;
            }
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('datasource', 'Extensioninstances');

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
     * Retrieves Extension Title given the Extension Instance ID value
     *
     * Note: All Extension queries first check the Registry of all Extensions for which the user is authorised.
     *
     * @param   $extension_instance_id  Primary key for the Extension Instance
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
        $controller      = new $controllerClass();
        $controller->getModelRegistry('datasource', 'Extensioninstances');
        $controller->setDataobject();
        $controller->connectDatabase();

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
     * Retrieves the Node (which is the Extension Name and Folder Name) for the specified Extension Instance
     *
     * Note: The Extension Instance Title might be different than the Node since each Extension can be used
     *  multiple times as instances.
     *
     * @param   $extension_instance_id  Primary key for the Extension Instance
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public function getExtensionNode($extension_instance_id)
    {
        if (Services::Registry()->exists('AuthorisedExtensions') === true) {
            $object = Services::Registry()->get('AuthorisedExtensions', $extension_instance_id, '');
            if (is_object($object)) {
                return $object->extensions_name;
            } else {
                return false;
            }
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('datasource', 'Extensions');

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
     * @param   string  $catalog_type  Numeric or textual key for View Catalog Type
     * @param   string  $node          Extension Name (folder name) for Extension Instance ID
     * @param   string  $registry      Registry for storing results
     *
     * @return  string
     * @since   1.0
     */
    public function getPath($catalog_type, $node, $registry = null)
    {
        if (is_numeric($catalog_type)) {
            $catalog_type = $this->getType(0, $catalog_type);
        }

        if ($catalog_type === false) {
            throw new \RuntimeException
            ('ExtensionHelper: Invalid Catalog Type Value: ' . $catalog_type . ' sent in to getPath');
        }

        $catalog_type = ucfirst(strtolower($catalog_type));

        if ($registry === null) {
            $registry = 'parameters';
        }

        if ($catalog_type == CATALOG_TYPE_RESOURCE_LITERAL) {
            if (file_exists(
                EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node));
            }

            if (file_exists(
                PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node));
            }

        } elseif ($catalog_type == CATALOG_TYPE_MENUITEM_LITERAL) {
            if (file_exists(
                EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node));
            }

        } elseif ($catalog_type == CATALOG_TYPE_LANGUAGE_LITERAL) {
            if (file_exists(
                EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node));
            }

        } elseif ($catalog_type == CATALOG_TYPE_THEME_LITERAL) {
            if (file_exists(EXTENSIONS_THEMES . '/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
                return EXTENSIONS_THEMES . '/' . ucfirst(strtolower($node));
            }

            if (file_exists(CORE_THEMES . '/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
                return CORE_THEMES . '/' . ucfirst(strtolower($node));
            }

        } elseif ($catalog_type == CATALOG_TYPE_PAGE_VIEW_LITERAL
            || $catalog_type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL
            || $catalog_type == CATALOG_TYPE_WRAP_VIEW_LITERAL
        ) {

            $plus = '/View/' . $catalog_type . '/' . ucfirst(strtolower($node));

            if (file_exists(Services::Registry()->get($registry, 'theme_path') . $plus . '/Configuration.xml')) {
                return Services::Registry()->get($registry, 'theme_path') . $plus;
            }

            if (file_exists(Services::Registry()->get($registry, 'extension_path') . $plus . '/Configuration.xml')) {
                return Services::Registry()->get($registry, 'extension_path') . $plus;
            }

            if (file_exists(
                EXTENSIONS_VIEWS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return EXTENSIONS_VIEWS . '/' . $catalog_type . '/' . ucfirst(strtolower($node));
            }

            if (file_exists(
                CORE_VIEWS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return CORE_VIEWS . '/' . $catalog_type . '/' . ucfirst(strtolower($node));
            }


            if (file_exists(
                PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node));
            }
        }

        throw new \Exception('ExtensionHelper: getPath not found for Catalog Type: '
            . $catalog_type . ' and Node: ' . $node);
    }

    /**
     * Return URL path for Extension
     *
     * @param   string  $catalog_type  Numeric or textual key for View Catalog Type
     * @param   string  $node          Folder name of extension
     * @param   string  $registry      Registry for storing results
     *
     * @return  mixed
     * @since   1.0
     */
    public function getPathURL($catalog_type, $node, $registry = null)
    {
        if (is_numeric($catalog_type)) {
            $catalog_type = $this->getType(0, $catalog_type);
        }

        if ($catalog_type === false) {
            throw new \RuntimeException
            ('ExtensionHelper: Invalid Catalog Type Value: ' . $catalog_type . ' sent in to getPath');
        }

        $catalog_type = ucfirst(strtolower($catalog_type));

        if ($registry === null) {
            $registry = 'parameters';
        }

        if ($catalog_type == CATALOG_TYPE_RESOURCE_LITERAL) {
            if (file_exists(
                EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return EXTENSIONS_URL . '/' . $catalog_type . '/' . ucfirst(strtolower($node));
            }

            if (file_exists(
                PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return CORE_SYSTEM_URL . '/' . ucfirst(strtolower($node));
            }

        } elseif ($catalog_type == CATALOG_TYPE_MENUITEM_LITERAL) {
            if (file_exists(
                EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return EXTENSIONS_URL . '/' . $catalog_type . '/' . ucfirst(strtolower($node));
            }

        } elseif ($catalog_type == CATALOG_TYPE_LANGUAGE_LITERAL) {
            if (file_exists(
                EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return EXTENSIONS_URL . '/' . $catalog_type . '/' . ucfirst(strtolower($node));
            }

        } elseif ($catalog_type == CATALOG_TYPE_THEME_LITERAL) {

            if (file_exists(EXTENSIONS_THEMES . '/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
                return EXTENSIONS_THEMES_URL . '/' . ucfirst(strtolower($node));
            }

            if (file_exists(CORE_THEMES . '/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
                return CORE_THEMES_URL . '/' . ucfirst(strtolower($node));
            }

        } elseif ($catalog_type == CATALOG_TYPE_PAGE_VIEW_LITERAL
            || $catalog_type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL
            || $catalog_type == CATALOG_TYPE_WRAP_VIEW_LITERAL
        ) {

            $plus = '/View/' . $catalog_type . '/' . ucfirst(strtolower($node));

            if (file_exists(Services::Registry()->get($registry, 'theme_path') . $plus . '/Configuration.xml')) {
                return Services::Registry()->get($registry, 'theme_path_url') . $plus;
            }

            if (file_exists(Services::Registry()->get($registry, 'extension_path') . $plus . '/Configuration.xml')) {
                return Services::Registry()->get($registry, 'extension_path_url') . $plus;
            }

            if (file_exists(
                EXTENSIONS_VIEWS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return EXTENSIONS_VIEWS_URL . '/' . $catalog_type . '/' . ucfirst(strtolower($node));
            }

            if (file_exists(
                CORE_VIEWS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return CORE_VIEWS_URL . '/' . $catalog_type . '/' . ucfirst(strtolower($node));
            }
        }

        throw new \Exception('ExtensionHelper: getPathURL not found for Catalog Type: '
            . $catalog_type . ' and Node: ' . $node);
    }

    /**
     * Return namespace for extension
     *
     * @param   string  $catalog_type  Numeric or textual key for View Catalog Type
     * @param   string  $node          Folder name of extension
     * @param   string  $registry      Registry for storing results
     *
     * @return  bool|string
     * @since   1.0
     */
    public function getNamespace($catalog_type, $node, $registry = null)
    {
        if (is_numeric($catalog_type)) {
            $catalog_type = $this->getType(0, $catalog_type);
        }

        if ($catalog_type === false) {
            throw new \RuntimeException
            ('ExtensionHelper: Invalid Catalog Type Value: ' . $catalog_type . ' sent in to getPath');
        }

        if ($registry === null) {
            $registry = 'parameters';
        }

        $catalog_type = ucfirst(strtolower($catalog_type));

        if ($catalog_type == CATALOG_TYPE_RESOURCE_LITERAL) {
            if (file_exists(
                EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return 'Extension\\Resource\\' . ucfirst(strtolower($node));
            }

            if (file_exists(
                PLATFORM_FOLDER . '/' . SYSTEM_LITERAL . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return 'Vendor\\Molajo\\System\\' . ucfirst(strtolower($node));
            }

        } elseif ($catalog_type == CATALOG_TYPE_MENUITEM_LITERAL) {
            if (file_exists(
                EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return 'Extension\\Menuitem\\' . ucfirst(strtolower($node));

            } elseif ($catalog_type == CATALOG_TYPE_LANGUAGE_LITERAL) {
                if (file_exists(
                    EXTENSIONS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
                )
                ) {
                    return 'Extension\\Language\\' . $catalog_type . '\\' . ucfirst(strtolower($node));
                }
            }

        } elseif ($catalog_type == CATALOG_TYPE_THEME_LITERAL) {

            if (file_exists(EXTENSIONS_THEMES . '/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
                return 'Extension\\Theme\\' . ucfirst(strtolower($node));
            }

            if (file_exists(CORE_THEMES . '/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
                return 'Molajo\\Theme\\' . ucfirst(strtolower($node));
            }

        } elseif ($catalog_type == CATALOG_TYPE_PAGE_VIEW_LITERAL
            || $catalog_type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL
            || $catalog_type == CATALOG_TYPE_WRAP_VIEW_LITERAL
        ) {

            $plus   = '/View/' . $catalog_type . '/' . ucfirst(strtolower($node));
            $plusNS = 'View\\' . $catalog_type . '\\' . ucfirst(strtolower($node));

            if (file_exists(Services::Registry()->get($registry, 'theme_path') . $plus . '/Configuration.xml')) {
                return 'Extension\\Theme\\' . Services::Registry()->get($registry, 'theme_path_node') . '\\' . $plusNS;
            }

            if (file_exists(Services::Registry()->get($registry, 'extension_path') . $plus . '/Configuration.xml')) {
                return 'Extension\\Resource\\' . Services::Registry()->get(
                    $registry,
                    'extension_title'
                ) . '\\' . $plusNS;
            }

            if (file_exists(
                EXTENSIONS_VIEWS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return 'Extension\\' . $plusNS;
            }

            if (file_exists(
                CORE_VIEWS . '/' . $catalog_type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml'
            )
            ) {
                return 'Molajo\\MVC\\' . $plusNS;
            }

        }

        throw new \Exception('ExtensionHelper: getPathNamespace not found for Catalog Type: '
            . $catalog_type . ' and Node: ' . $node);
    }

    /**
     * Retrieve Favicon Path from Theme Folder
     *
     * Note: Expects theme_path to already be set in the $registry
     *
     * @param   string  $registry      Registry for storing results
     *
     * @return  mixed
     * @since   1.0
     */
    public function getFavicon($registry)
    {
        $path = Services::Registry()->get($registry, 'theme_path') . '/images/';
        if (file_exists($path . 'favicon.ico')) {
            Services::Registry()->get($registry, 'theme_path_url') . '/images/favicon.ico';
        }

        $path = BASE_FOLDER;
        if (file_exists($path . 'favicon.ico')) {
            return BASE_URL . '/favicon.ico';
        }

        return false;
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

        } elseif ($catalog_type_id == CATALOG_TYPE_TEMPLATE_VIEW) {
            return CATALOG_TYPE_TEMPLATE_VIEW_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_THEME) {
            return CATALOG_TYPE_THEME_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_USERS) {
            return CATALOG_TYPE_USER_LITERAL;

        } elseif ($catalog_type_id == CATALOG_TYPE_WRAP_VIEW) {
            return CATALOG_TYPE_WRAP_VIEW_LITERAL;
        }

        return CATALOG_TYPE_RESOURCE_LITERAL;
    }
}
