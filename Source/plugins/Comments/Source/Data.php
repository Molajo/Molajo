<?php
/**
 * Comments Data
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Comments;

/**
 * Comments Data
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Data extends Form
{
    /**
     * Retrieve Data for Comments List
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setCommentsData()
    {
        $this->setCommentsQuery();

        $data                                            = $this->runQuery();
        $this->plugin_data->{$this->comments_list}->data = $this->setCommentPageUrl($data);

        $model_registry                                            = $this->query->getModelRegistry();
        $model_registry['fields']['page_url']                      = $this->setModelRegistryField('page_url', 'string');
        $this->plugin_data->{$this->comments_list}->model_registry = $model_registry;

        return $this;
    }

    /**
     * Set Page Url on Comments
     *
     * @param   array $data
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setCommentPageUrl($data)
    {
        $new_data = array();

        if (count($data) === 0) {
            return $new_data;
        }

        foreach ($data as $row) {
            $row->page_url = $this->runtime_data->request->data->url;
            $new_data[]    = $row;
        }

        return $new_data;
    }

    /**
     * Set Query for Comments
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCommentsQuery()
    {
        $comments_list = $this->comments_list;

        $this->setQueryController('Molajo//Resources//Comments//Content.xml');

        $this->setQueryControllerDefaults(
            $process_events = 1,
            $query_object = 'list',
            $get_customfields = 1,
            $use_special_joins = 1,
            $use_pagination = 0,
            $check_view_level_access = 1,
            $get_item_children = 0
        );

        $prefix = $this->query->getModelRegistry('primary_prefix', 'a');

        $this->query->where('column', $prefix . '.' . 'root', '=', 'integer', $this->source_id);
        $this->query->where('column', $prefix . '.' . 'id', '<>', 'integer', $this->source_id);
        $this->query->where('column', $prefix . '.' . 'status', '>', 'integer', 0);
        $this->query->orderby($prefix . '.' . 'lft');

        $this->plugin_data->$comments_list->model_registry = $this->query->getModelRegistry();

        return $this;
    }

    /**
     * Place Comments into Query Results and Model Registry objects for Rendering View
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getCommentsData()
    {
        $this->controller['query_results']
            = $this->plugin_data->{$this->comments_list}->data;

        $this->controller['model_registry']
            = $this->plugin_data->{$this->comments_list}->model_registry;

        return $this;
    }
}
