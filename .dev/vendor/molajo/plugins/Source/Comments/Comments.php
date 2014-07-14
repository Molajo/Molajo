<?php
/**
 * Comments
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Comments;

use CommonApi\Exception\RuntimeException;
use Exception;
use stdClass;

/**
 * Comments Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Comments extends ProcessComments
{
    /**
     * Get Comments
     *
     * @returns  $this
     * @since    1.0
     * @throws   \CommonApi\Exception\RuntimeException
     */
    protected function getComments()
    {
        return $this->getData('setCommentsQuery', 'comments_list');
    }

    /**
     * Set Query for Comments
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCommentsQuery()
    {
        $model = 'Molajo//Comments//Configuration.xml';

        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        $controller = $this->resource->get('query:///' . $model, $options);

        $controller->setModelRegistry('use_pagination', 0);

        $prefix = $controller->getModelRegistry('primary_prefix', 'a');
        $id     = (int)$this->runtime_data->resource->data->id;

        $controller->where('column', $prefix . '.' . 'root', '=', 'integer', $id);
        $controller->where('column', $prefix . '.' . 'status', '>', 'integer', 0);
        $controller->orderBy($prefix . '.' . 'lft', 'ASC');

        return $controller;
    }

    /**
     * Retrieve Data for Comment Heading
     *
     * @returns  $this
     * @since    1.0
     * @throws   \CommonApi\Exception\RuntimeException
     */
    protected function getCommentForm()
    {
        $temp_row               = new stdClass();
        $temp_row->comment_open = $this->plugin_data->comment_open;

        $this->plugin_data->comments_form->data = $temp_row;

        return $this;
    }

    /**
     * Create and execute Query
     *
     * @param    string $set_method
     * @param string $plugin_node
     *
     * @returns  $this
     * @since    1.0
     * @throws   \CommonApi\Exception\RuntimeException
     */
    protected function getData($set_method, $plugin_node)
    {
        $controller = $this->$set_method();

        try {
            $this->plugin_data->$plugin_node->data           = $controller->getData();
            $this->plugin_data->$plugin_node->model_registry = $controller->getModelRegistry('*');

        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $this;
    }
}
