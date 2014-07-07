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
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onBeforeRenderView()
    {
        if ($this->processAuthorPlugin() === false) {
            return $this;
        }

        $this->getAuthorProfile($this->parameters->token->attributes['author']);

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processAuthorPlugin()
    {
        if (isset($this->runtime_data->render->extension->title)) {
        } else {
            return false;
        }

        $test_title = strtolower($this->runtime_data->render->extension->title);

        if ($test_title === 'author') {
        } else {
            return false;
        }

        if (isset($this->parameters->token->attributes['author'])) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Get Author Profile
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getAuthorProfile($author_id)
    {
        $controller = $this->setAuthorProfileQuery($author_id);

        try {
            $data          = $controller->getData();
            $data->sef_url = $this->getAuthorProfileURL($author_id);

            $this->query_results   = array();
            $this->query_results[] = $data;
            $this->model_registry  = $controller->getModelRegistry('*');

        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $this;
    }

    /**
     * Get Author Profile
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setAuthorProfileQuery($author_id)
    {
        $controller = $this->resource->get(
            'query:///Molajo//Model//Datasource//User.xml',
            array(
                'runtime_data' => $this->runtime_data,
                'plugin_data'  => $this->plugin_data
            )
        );

        $controller->setModelRegistry('query_object', 'item');
        $controller->setModelRegistry('primary_key_value', (int)$author_id);
        $controller->setModelRegistry('get_item_children', 0);

        return $controller;
    }

    /**
     * After-read processing
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getAuthorProfileURL($author_id)
    {
        $controller = $this->setAuthorProfileURLQuery($author_id);

        try {
            return $controller->getData();

        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * After-read processing
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setAuthorProfileURLQuery($author_id)
    {
        $controller = $this->resource->get('query:///Molajo//Model//Datasource//Catalog.xml');

        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('query_object', 'result');

        $prefix               = $controller->getModelRegistry('primary_prefix', 'a');
        $application_id       = (int)$this->runtime_data->application->id;
        $catalog_type_user_id = (int)$this->runtime_data->reference_data->catalog_type_user_id;

        $controller->select($prefix . '.' . 'sef_request');

        $controller->where('column', $prefix . '.' . 'application_id', '=', 'integer', $application_id);
        $controller->where('column', $prefix . '.' . 'enabled', '=', 'integer', 1);
        $controller->where('column', $prefix . '.' . 'redirect_to_id', '=', 'integer', 0);
        $controller->where('column', $prefix . '.' . 'page_type', '<>', 'string', 'link');
        $controller->where('column', $prefix . '.' . 'source_id', '=', 'integer', $author_id);
        $controller->where('column', $prefix . '.' . 'catalog_type_id', '=', 'integer', $catalog_type_user_id);

        return $controller;
    }
}
