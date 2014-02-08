<?php
/**
 * Resource Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Resource;

use stdClass;
use Exception;
use CommonApi\Event\SystemInterface;
use Molajo\Plugin\SystemEventPlugin;
use CommonApi\Exception\UnexpectedValueException;

/**
 * Resource Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class ResourcePlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Get Resource, Theme and View Data for Page Type and other Route Data
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRoute()
    {
        $page_type = strtolower($this->runtime_data->route->page_type);

        if ($page_type == 'item' || $page_type == 'edit' || $page_type == 'delete') {
            $this->getResourceItem();

        } elseif ($page_type == 'new') {
            $this->getResourceNew();

        } elseif ($page_type == 'list') {
            $this->getResourceList();

        } else {
            $this->getResourceMenu();
        }

        return $this;
    }

    /**
     * Retrieve Resource Item
     *
     * @return  $this
     * @since   1.0
     */
    protected function getResourceNew()
    {
        $this->getResourceExtension();

        return $this;
    }

    /**
     * Retrieve Resource Item
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    protected function getResourceItem()
    {
        $model    = 'Molajo//Resource//'
            . $this->runtime_data->route->model_name . '//Configuration.xml';
        $resource = $this->resource->get('query:///' . $model);

        $resource->setModelRegistry('check_view_level_access', 1);
        $resource->setModelRegistry('process_events', 1);
        $resource->setModelRegistry('query_object', 'item');
        $resource->setModelRegistry('get_customfields', 1);
        $resource->setModelRegistry('use_special_joins', 1);

        $resource->setModelRegistry(
            'primary_key_value',
            (int)$this->runtime_data->route->source_id
        );

        try {
            $item = $resource->getData();

            $model_registry = $resource->getModelRegistry('*');

        } catch (Exception $e) {
            throw new UnexpectedValueException ($e->getMessage());
        }

        if (count($item) == 0) {
            throw new UnexpectedValueException ('Resource Item not found.');
        }

        if (isset($item->parameters->theme_id) && (int)$item->parameters->theme_id > 0) {
        } else {
            $item->parameters->theme_id
                = $this->runtime_data->application->parameters->application_default_theme_id;
        }

        $resource   = new stdClass();
        $parameters = $item->parameters;
        unset($item->parameters);

        $resource->data           = $item;
        $resource->parameters     = $parameters;
        $resource->model_registry = $model_registry;

        $this->plugin_data->resource = $resource;

        return $this;
    }

    /**
     * Retrieve Resource List
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    protected function getResourceList()
    {
        $this->resource->setModelRegistry(
            'primary_key_value',
            (int)$this->runtime_data->route->source_id
        );
        $this->resource->setModelRegistry('query_object', 'list');

        try {
            $item = $this->resource->getData();
        } catch (Exception $e) {
            throw new UnexpectedValueException ($e->getMessage());
        }

        $resource = new stdClass();

        if (count($item) == 0) {
            throw new UnexpectedValueException ('Resource Data not found.');
        }

        foreach (\get_object_vars($item) as $key => $value) {
            $resource->$key = $value;
        }

        $this->plugin_data->resource = $resource;

        return $this;
    }

    /**
     * Retrieve Resource List
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    protected function getResourceMenu()
    {
        $page_type = strtolower($this->runtime_data->route->page_type);

        $controller = $this->resource->get('query:///Molajo//Menuitem//' . $page_type . '//Configuration.xml');

        $controller->setModelRegistry('check_view_level_access', 1);
        $controller->setModelRegistry('process_events', 1);
        $controller->setModelRegistry('query_object', 'item');
        $controller->setModelRegistry('get_customfields', 1);
        $controller->setModelRegistry('use_special_joins', 1);
        $controller->setModelRegistry(
            'primary_key_value',
            (int)$this->runtime_data->route->source_id
        );

        try {
            $menu_item = $controller->getData();

        } catch (Exception $e) {
            throw new UnexpectedValueException ($e->getMessage());
        }

        if (count($menu_item) == 0) {
            throw new UnexpectedValueException ('Resource Plugin: Resource Menu Item not found.');
        }

        if (isset($menu_item->parameters->theme_id) && (int)$menu_item->parameters->theme_id > 0) {
        } else {
            $menu_item->parameters->theme_id
                = $this->runtime_data->application->parameters->application_default_theme_id;
        }

        $catalog_type_id = $menu_item->parameters->criteria_catalog_type_id;

        if ((int)$catalog_type_id === 0) {
            $resource_parameters = array();
        } else {

            $this->getMenuitemResourceList($catalog_type_id);

            $resource_parameters = $this->plugin_data->resource->parameters;

            foreach ($menu_item->parameters as $key => $value) {

                if (substr($key, 0, strlen($page_type)) == $page_type) {
                    $resource_parameters->$key = $value;
                } elseif (isset($resource_parameters->$key)) {
                } else {
                    $resource_parameters->$key = $value;
                }

                $this->plugin_data->resource->parameters = $resource_parameters;
            }
        }

        $this->plugin_data->resource->menuitem = new stdClass();
        $parameters                            = $menu_item->parameters;
        unset($menu_item->parameters);
        $this->plugin_data->resource->menuitem->data           = $menu_item;
        $this->plugin_data->resource->menuitem->parameters     = $parameters;
        $this->plugin_data->resource->menuitem->model_registry = $controller->model->getModelRegistry('*');

        return $this;
    }

    /**
     * Retrieve Resource Item
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    protected function getMenuitemResourceList($catalog_type_id)
    {
        $resource_model_name = $this->getModelName($catalog_type_id);

        $model      = 'Molajo//' . $resource_model_name . '//Extension.xml';
        $controller = $this->resource->get('query:///' . $model);
        $controller->setModelRegistry('check_view_level_access', 1);
        $controller->setModelRegistry('process_events', 1);
        $controller->setModelRegistry('query_object', 'item');
        $controller->setModelRegistry('get_customfields', 1);
        $controller->setModelRegistry('use_special_joins', 1);

        try {
            $data = $controller->getData();

            $parameters = $data->parameters;
            unset($data->parameters);

            $resource = new stdClass();

            $resource->catalog_type_id     = $data->catalog_type_id;
            $resource->resource_model_name = $resource_model_name;
            $resource->data                = $data;
            $resource->parameters          = $parameters;
            $resource->model_registry      = $controller->getModelRegistry('*');

            $this->plugin_data->resource = $resource;

        } catch (Exception $e) {
            throw new UnexpectedValueException ($e->getMessage());
        }

        return $this;
    }

    /**
     * Retrieve Resource Item
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    protected function getModelName($catalog_type_id)
    {
        $model      = 'Molajo//Datasource//Catalogtypes.xml';
        $controller = $this->resource->get('query:///' . $model);

        $controller->setModelRegistry('check_view_level_access', 0);
        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('query_object', 'result');
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('use_special_joins', 0);
        $controller->setModelRegistry('primary_key_value', (int)$catalog_type_id);

        $controller->model->query->select(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . '.' . $controller->model->database->qn('model_name')
        );

        try {
            return $controller->getData();

        } catch (Exception $e) {
            throw new UnexpectedValueException ($e->getMessage());
        }
    }

    /**
     * Retrieve Resource Extension
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    protected function getResourceExtension()
    {
        $model    = 'Molajo//Resource//' . $this->runtime_data->route->model_name . '//Configuration.xml';
        $resource = $this->resource->get('query:///' . $model);

        $resource->setModelRegistry('check_view_level_access', 1);
        $resource->setModelRegistry('process_events', 1);
        $resource->setModelRegistry('query_object', 'item');
        $resource->setModelRegistry('get_customfields', 1);
        $resource->setModelRegistry('use_special_joins', 1);

        $resource->setModelRegistry(
            'primary_key_value',
            (int)$this->runtime_data->route->source_id
        );

        try {
            $item = $resource->getData();

            $model_registry = $resource->getModelRegistry('*');

        } catch (Exception $e) {
            throw new UnexpectedValueException ($e->getMessage());
        }

        if (count($item) == 0) {
            throw new UnexpectedValueException ('Resource Item not found.');
        }

        if (isset($item->parameters->theme_id) && (int)$item->parameters->theme_id > 0) {
        } else {
            $item->parameters->theme_id = $this->runtime_data->application->parameters->application_default_theme_id;
        }

        $resource   = new stdClass();
        $parameters = $item->parameters;
        unset($item->parameters);

        $resource->data           = $item;
        $resource->parameters     = $parameters;
        $resource->model_registry = $model_registry;

        $this->plugin_data->resource = $resource;

        return $this;
    }
}
