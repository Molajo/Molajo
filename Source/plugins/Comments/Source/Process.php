<?php
/**
 * Get and Set Comments Overview Data
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Comments;

use stdClass;

/**
 * Get and Set Comments Overview Data
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Process extends Heading
{
    /**
     * Prepare data for the Comments after rendering Source View
     *  ex. After the Blog Post View is rendered, retrieve comments for that post
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCommentData()
    {
        $this->setCommentFormOpenDays();
        $this->setCommentsOpen();
        $this->setCommentsRow();
        $this->setCommentsModelRegistry();
        $this->setCommentsData();
        $this->setCommentsHeading();
        $this->setCommentsForm();
        $this->getComments();

        return $this;
    }

    /**
     * Get the Number of Days the Form is to be displayed
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCommentFormOpenDays()
    {
        $this->open_days = 0;

        if (isset($this->controller['parameters']->token->attributes['comment_form_open_days'])) {
            $this->open_days = $this->controller['parameters']->token->attributes['comment_form_open_days'];
        }

        return $this;
    }

    /**
     * Determine if Comments are still accepted
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCommentsOpen()
    {
        $this->comments_open = 0;

        if ((int)$this->open_days === 0) {
            return $this;
        }

        $start_publishing_days_ago = $this->setStartPublishingDaysAgo();

        if ($start_publishing_days_ago > $this->open_days) {
            return $this;
        }

        $this->comments_open = 1;

        return $this;
    }

    /**
     * Retrieve Number of days since Start Publishing Date
     *
     * @return  integer
     * @since   1.0.0
     */
    protected function setStartPublishingDaysAgo()
    {
        if (isset($this->controller['parameters']->token->attributes['start_publish'])) {
        } else {
            return 9999;
        }

        $this->start_publishing_date = $this->controller['parameters']->token->attributes['start_publish'];

        $converted = $this->date->convertCCYYMMDD($this->start_publishing_date);

        return $this->date->getNumberofDaysAgo($converted);
    }

    /**
     * Build Comment Object
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCommentsRow()
    {
        $temp_row                        = new stdClass();
        $temp_row->comments_name         = $this->comments_name;
        $temp_row->comments_form         = $this->comments_form;
        $temp_row->comments_heading      = $this->comments_heading;
        $temp_row->comments_list         = $this->comments_list;
        $temp_row->start_publishing_date = $this->start_publishing_date;
        $temp_row->open_days             = $this->open_days;
        $temp_row->comments_open         = $this->comments_open;
        $temp_row->source_id             = $this->source_id;

        $this->plugin_data->{$this->comments_name}->data = array($temp_row);

        return $this;
    }

    /**
     * Set Model Registry for Comments
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCommentsModelRegistry()
    {
        $fields                          = array();
        $fields['comments_name']         = $this->setModelRegistryField('comments_name', 'string');
        $fields['comments_form']         = $this->setModelRegistryField('comments_form', 'string');
        $fields['comments_heading']      = $this->setModelRegistryField('comments_heading', 'string');
        $fields['comments_list']         = $this->setModelRegistryField('comments_list', 'string');
        $fields['start_publishing_date'] = $this->setModelRegistryField('start_publishing_date', 'string');
        $fields['open_days']             = $this->setModelRegistryField('open_days', 'integer');
        $fields['comments_open']         = $this->setModelRegistryField('comments_open', 'integer');
        $fields['source_id']             = $this->setModelRegistryField('source_id', 'integer');

        $this->plugin_data->{$this->comments_name}->model_registry = array();
        $this->plugin_data->{$this->comments_name}->model_registry = $fields;

        return $this;
    }

    /**
     * Place Comment Data into Query Results and Model Registry objects for Rendering View
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getComments()
    {
        $this->plugin_data->comments = new stdClass();

        $this->plugin_data->comments->data           = $this->plugin_data->{$this->comments_name}->data;
        $this->plugin_data->comments->model_registry = $this->plugin_data->{$this->comments_name}->model_registry;

        return $this;
    }
}
