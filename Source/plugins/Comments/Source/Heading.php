<?php
/**
 * Comments Heading Data
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Comments;

use stdClass;

/**
 * Comments Heading Data
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Heading extends Data
{
    /**
     * Retrieve Data for Comment Heading
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCommentsHeading()
    {
        $count = count($this->plugin_data->{$this->comments_list}->data);

        $temp_row                    = new stdClass();
        $temp_row->count_of_comments = (int)$count;
        $this->processCommentsHeadingTitleContent($temp_row);
        $this->processCommentsHeadingCommentOpen($temp_row);

        $this->plugin_data->{$this->comments_heading}->data           = array($temp_row);
        $this->plugin_data->{$this->comments_heading}->model_registry = array();
        $this->plugin_data->{$this->comments_heading}->model_registry['fields']
                                                                      = $this->setCommentsHeadingModelRegistry();
        return $this;
    }

    /**
     * Set the Header Title and Content Text
     *
     * @param   stdClass $temp_row
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processCommentsHeadingTitleContent($temp_row)
    {
        if ((int)$temp_row->count_of_comments === 0) {
            $temp_row->title        = $this->language->translateString('No comments');
            $temp_row->content_text = $this->language->translateString('There are no comments.');
        } else {
            $temp_row->title        = $this->language->translateString('Comments');
            $temp_row->content_text = $this->language->translateString('Comments');
        }

        return $this;
    }

    /**
     * Set Model Registry for Comments Heading Data
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setCommentsHeadingModelRegistry()
    {
        $fields                      = array();
        $fields['count_of_comments'] = $this->setModelRegistryField('count_of_comments', 'integer');
        $fields['title']             = $this->setModelRegistryField('title', 'string');
        $fields['content_text']      = $this->setModelRegistryField('content_text', 'string');
        $fields['closed_comment']    = $this->setModelRegistryField('closed_comment', 'integer');
        $fields['closed']            = $this->setModelRegistryField('closed', 'integer');

        return $fields;
    }

    /**
     * Set the Row Closed Comment and Indicator
     *
     * @param   object $temp_row
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processCommentsHeadingCommentOpen($temp_row)
    {
        if ((int)$this->comments_open === 0) {
            $temp_row->closed_comment = $this->language->translateString('Comments are closed.');
            $temp_row->closed         = 1;
        } else {
            $temp_row->closed_comment = '';
            $temp_row->closed         = 0;
        }

        return $this;
    }

    /**
     * Place Comment Heading into Query Results and Model Registry objects for Rendering View
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getCommentsHeading()
    {
        $this->controller['query_results']
            = $this->plugin_data->{$this->comments_heading}->data;

        $this->controller['model_registry']
            = $this->plugin_data->{$this->comments_heading}->model_registry;

        return $this;
    }
}
