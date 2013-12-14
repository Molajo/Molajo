<?php
/**
 * Resource Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
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
     * @return  object
     * @since   1.0
     */
    public function onAfterRoute()
    {
        $page_type = strtolower($this->runtime_data->route->page_type);

        if ($page_type == 'item') {
            return $this->getResourceItem();
        } elseif ($page_type == 'form') {
            return $this->getResourceForm();
        } elseif ($page_type == 'list') {
            return $this->getResourceList();
        } else {
            return $this->getResourceItem();
        }
    }

    /**
     * Retrieve Resource Item
     *
     * @return  $this
     * @since   1.0
     */
    protected function getResourceForm()
    {
        $this->getResourceItem();

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
        $model    = 'Molajo//' . $this->runtime_data->route->model_name . '//Configuration.xml';
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
            $item           = $resource->getData();
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

        $resource                 = new stdClass();
        $resource->data           = $item;
        $resource->model_registry = $model_registry;

        $this->runtime_data->resource = $resource;

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

        return $resource;
    }
}
