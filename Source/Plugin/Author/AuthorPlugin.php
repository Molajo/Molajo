<?php
/**
 * Author Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Author;

use stdClass;
use CommonApi\Event\SystemInterface;
use CommonApi\Exception\RuntimeException;
use Molajo\Plugin\SystemEventPlugin;

/**
 * Author Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class AuthorPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * After-read processing
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function onBeforeExecute()
    {
        $this->runtime_data->plugin_data->author                 = new stdClass();
        $this->runtime_data->plugin_data->author->data           = new stdClass();
        $this->runtime_data->plugin_data->author->model_registry = new stdClass();

        if (isset($this->runtime_data->resource->data->created_by)) {
        } else {
            return $this;
        }

        if ((int)$this->runtime_data->resource->data->created_by == 0) {
            return $this;
        }

        $this->getAuthorProfile();

        $this->runtime_data->plugin_data->author->data->sef_url = $this->getAuthorProfileURL();

        return $this;
    }

    /**
     * Get Author Profile
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function getAuthorProfile()
    {
        if (isset($this->runtime_data->resource->data->created_by)) {
        } else {
            return $this;
        }

        if ((int)$this->runtime_data->resource->data->created_by == 0) {
            return $this;
        }

        $author = $this->resource->get(
            'query:///Molajo//Datasource//User.xml',
            array('Parameters', $this->runtime_data)
        );

        $author->setModelRegistry('check_view_level_access', 0);
        $author->setModelRegistry('process_events', 1);
        $author->setModelRegistry('query_object', 'item');
        $author->setModelRegistry('use_special_joins', 1);
        $author->setModelRegistry('get_customfields', 0);
        $author->setModelRegistry('primary_key_value', (int)$this->runtime_data->resource->data->created_by);
        $author->setModelRegistry('get_item_children', 0);

        try {
            $this->runtime_data->plugin_data->author->data           = $author->getData();
            $this->runtime_data->plugin_data->author->model_registry = $author->getModelRegistry('*');

        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }

        return $this;
    }

    /**
     * After-read processing
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getAuthorProfileURL()
    {
        $controller = $this->resource->get('query:///Molajo//Datasource//Catalog.xml');

        $controller->setModelRegistry('check_view_level_access', 0);
        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('query_object', 'result');

        $controller->model->query->select(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('sef_request')
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('application_id')
            . ' = '
            . (int)$this->runtime_data->application->id
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('enabled')
            . ' = '
            . ' 1 '
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('redirect_to_id')
            . ' = '
            . ' 0 '
        );
        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('page_type')
            . ' <> '
            . $controller->model->database->q('link')
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->model->getModelRegistry('primary_prefix', 'a'))
            . '.' . $controller->model->database->qn('source_id')
            . ' = ' . $this->runtime_data->plugin_data->author->data->id
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->model->getModelRegistry('primary_prefix', 'a'))
            . '.' . $controller->model->database->qn('catalog_type_id')
            . ' = ' . (int)$this->runtime_data->reference_data->catalog_type_user_id
        );

        try {
            return $controller->getData();
        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }
    }
}
