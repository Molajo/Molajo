<?php
/**
 * Author Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Author;

use CommonApi\Event\DisplayInterface;
use CommonApi\Exception\RuntimeException;
use Exception;
use Molajo\Plugins\DisplayEventPlugin;

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
     * Before Rendering the Author Template View, retrieve and store Author in plugin_data
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onBeforeRenderView()
    {
        if (isset($this->runtime_data->render->extension->title)) {
        } else {
            return $this;
        }

        $test_title = strtolower($this->runtime_data->render->extension->title);

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
     * @throws  \CommonApi\Exception\RuntimeException
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

        $author->setModelRegistry('query_object', 'item');
        $author->setModelRegistry('primary_key_value', (int)$author_id);
        $author->setModelRegistry('get_item_children', 0);

        try {
            $data          = $author->getData();
            $data->sef_url = $this->getAuthorProfileURL($author_id);

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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getAuthorProfileURL($author_id)
    {
        $controller = $this->resource->get('query:///Molajo//Model//Datasource//Catalog.xml');

        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('query_object', 'result');

        $controller->select($controller->getModelRegistry('primary_prefix', 'a') . '.' . 'sef_request');

        $controller->where(
            'column',
            $controller->getModelRegistry('primary_prefix', 'a') . '.' . 'application_id',
            '=',
            'integer',
            (int)$this->runtime_data->application->id
        );

        $controller->where(
            'column',
            $controller->getModelRegistry('primary_prefix', 'a') . '.' . 'enabled',
            '=',
            'integer',
            1
        );

        $controller->where(
            'column',
            $controller->getModelRegistry('primary_prefix', 'a') . '.' . 'redirect_to_id',
            '=',
            'integer',
            0
        );

        $controller->where(
            'column',
            $controller->getModelRegistry('primary_prefix', 'a') . '.' . 'page_type',
            '<>',
            'string',
            'link'
        );

        $controller->where(
            'column',
            $controller->getModelRegistry('primary_prefix', 'a') . '.' . 'source_id',
            '=',
            'integer',
            (int)$author_id
        );

        $controller->where(
            'column',
            $controller->getModelRegistry('primary_prefix', 'a') . '.' . 'catalog_type_id',
            '=',
            'integer',
            (int)$this->runtime_data->reference_data->catalog_type_user_id
        );

        try {
            return $controller->getData();
        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }
    }
}
