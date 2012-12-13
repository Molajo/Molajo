<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Helper;

use Molajo\Service\Services\Theme\Helper\ExtensionHelper;
use Molajo\Service\Services\Theme\Helper\ThemeHelper;
use Molajo\Service\Services\Theme\Helper\ViewHelper;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Content Helper
 *
 * Retrieves Item, List, or Menu Item Parameters for Route from Content, Extension, and Menu Item
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
Class ContentHelper
{
    /**
     * Helpers
     *
     * @var    object
     * @since  1.0
     */
    protected $extensionHelper;
    protected $themeHelper;
    protected $viewHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->extensionHelper = new ExtensionHelper();
        $this->themeHelper = new ThemeHelper();
        $this->viewHelper = new ViewHelper();
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
     * Get Parameters and data for Resource
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
     * Get Menuitem Content Parameters for specific Resource
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

        $this->themeHelper->get($theme_id);
        $this->viewHelper->get($page_view_id, CATALOG_TYPE_PAGE_VIEW_LITERAL);

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

        $this->viewHelper->get($template_view_id, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);
        $this->viewHelper->get($wrap_view_id, CATALOG_TYPE_WRAP_VIEW_LITERAL);

        return;
    }
}
