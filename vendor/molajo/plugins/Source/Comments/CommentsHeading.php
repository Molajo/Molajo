<?php
/**
 * Comments Heading
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Comments;

use CommonApi\Exception\RuntimeException;
use stdClass;

/**
 * Comments Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class CommentsHeading extends Comments
{
    /**
     * Retrieve Data for Comment Heading
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getCommentHeading()
    {
        return $this->getData('setCommentHeadingQuery', 'comments_heading');
    }

    /**
     * Set Query for Comment Headings
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCommentHeadingQuery()
    {
        $model = 'Molajo//Comments//Configuration.xml';

        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        $controller = $this->resource->get('query:///' . $model, $options);

        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('query_object', 'result');

        $controller->select('count(*)', null, null, 'special');

        $prefix = $controller->getModelRegistry('primary_prefix', 'a');
        $controller->where('column', $prefix . '.' . 'root', '=', 'integer', $this->runtime_data->resource->data->id);
        $controller->where('column', $prefix . '.' . 'status', '>', 'integer', 0);

        return $controller;
    }

    /**
     * Process Query Results for Comment Heading
     *
     * @param   integer $count
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function processCommentHeadingQueryResults($count)
    {
        $temp_row                    = new stdClass();
        $temp_row->count_of_comments = (int)$count;
        $temp_row                    = $this->processCommentHeadingTitleContent($temp_row);
        $temp_row                    = $this->processCommentHeadingCommentOpen($temp_row);

        return $temp_row;
    }

    /**
     * Set the Header Title and Content Text
     *
     * @param   stdClass $temp_row
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function processCommentHeadingTitleContent($temp_row)
    {
        if ((int)$temp_row->count_of_comments === 0) {
            $temp_row->title        = $this->language_controller->translateString('No comments');
            $temp_row->content_text = $this->language_controller->translateString('There are no comments.');
        } else {
            $temp_row->title        = $this->language_controller->translateString('Comments');
            $temp_row->content_text = $this->language_controller->translateString('Comments');
        }

        return $temp_row;
    }

    /**
     * Set the Row Closed Comment and Indicator
     *
     * @param   stdClass $temp_row
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function processCommentHeadingCommentOpen($temp_row)
    {
        if ((int)$this->plugin_data->comment_open === 0) {
            $temp_row->closed_comment = $this->language_controller->translateString('Comments are closed.');
            $temp_row->closed         = 1;
        } else {
            $temp_row->closed_comment = '';
            $temp_row->closed         = 0;
        }

        return $temp_row;
    }
}
