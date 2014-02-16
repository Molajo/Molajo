<?php
/**
 * Author Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Author;

use Exception;
use CommonApi\Event\DisplayInterface;
use CommonApi\Exception\RuntimeException;
use Molajo\Plugin\DisplayEventPlugin;
use stdClass;

/**
 * Author Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class AuthorPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * After-read processing
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function onBeforeRenderView()
    {
        $test_title = strtolower($this->plugin_data->render->extension->title);

        if ($test_title == 'author') {
        } else {
            return $this;
        }

        if (isset($this->parameters->token->attributes['author'])) {
        } else {
            return $this;
        }

        $author_id = $this->parameters->token->attributes['author'];

        $this->getAuthorProfile($author_id);

        return $this;
    }

    /**
     * Get Author Profile
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function getAuthorProfile($author_id)
    {
        $author = $this->resource->get(
            'query:///Molajo//Model//Datasource//User.xml',
            array(
                'runtime_data' => $this->runtime_data,
                'plugin_data'  => $this->plugin_data
            )
        );

        $author->setModelRegistry('check_view_level_access', 0);
        $author->setModelRegistry('process_events', 1);
        $author->setModelRegistry('query_object', 'item');
        $author->setModelRegistry('use_special_joins', 1);
        $author->setModelRegistry('get_customfields', 1);
        $author->setModelRegistry('primary_key_value', (int)$author_id);
        $author->setModelRegistry('get_item_children', 0);

        try {
            $data = $author->getData();
            $data->sef_url = $this->getAuthorProfileURL($author_id);

            if (isset($data->parameters)) {
                $parameters = $data->parameters;
                unset($data->parameters);
            } else {
                $parameters = new stdClass();
            }

            foreach (\get_object_vars($parameters) as $key => $value) {
                $this->parameters->$key = $value;
            }

            $this->query_results   = array();
            $this->query_results[] = $data;
            $this->model_registry  = $author->getModelRegistry('*');

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
    protected function getAuthorProfileURL($author_id)
    {
        $controller = $this->resource->get('query:///Molajo//Model//Datasource//Catalog.xml');

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
            . ' = ' . (int)$author_id
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
