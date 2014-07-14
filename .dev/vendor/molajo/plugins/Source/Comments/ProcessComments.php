<?php
/**
 * Process Comments
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Comments;

use Molajo\Plugins\DisplayEventPlugin;
use stdClass;

/**
 * Process Comments
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class ProcessComments extends DisplayEventPlugin
{

    /**
     * Initialise Plugindata for Comments
     *
     * @return  ProcessComments
     * @since   1.0.0
     */
    protected function initialiseCommentsPlugin()
    {
        $this->initialiseCommentsPluginData();

        $name = 'comments' . $this->parameters->source_id;

        foreach ($this->plugin_data->$name->parameters as $key => $value) {
            $this->parameters->$key = $value;
        }

        $this->parameters->start_publishing_datetime = $this->plugin_data->$name->data[0]->start_publishing_datetime;

        unset($this->plugin_data->$name);

        return $this;
    }

    /**
     * Initialise Plugindata->comment object for Comments
     *
     * @return  ProcessComments
     * @since   1.0.0
     */
    protected function initialiseCommentsPluginData()
    {
        $this->plugin_data->comment = new stdClass();

        $this->plugin_data->comment_open = 0;

        $this->plugin_data->comments_heading                 = new stdClass();
        $this->plugin_data->comments_heading->data           = new stdClass();
        $this->plugin_data->comments_heading->model_registry = new stdClass();

        $this->plugin_data->comments_list                 = new stdClass();
        $this->plugin_data->comments_list->data           = new stdClass();
        $this->plugin_data->comments_list->model_registry = new stdClass();

        $this->plugin_data->comments_form                 = new stdClass();
        $this->plugin_data->comments_form->data           = new stdClass();
        $this->plugin_data->comments_form->model_registry = new stdClass();

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processCommentsPlugin()
    {
        $methods = array(
            'processCommentsPluginExtensionTitle',
            'processCommentsPluginSourceId',
            'processCommentsPluginAvailableData',
            'processCommentsPluginEnabled'
        );

        foreach ($methods as $method) {
            if ($this->$method() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Should plugin be executed based on Extension Title?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processCommentsPluginExtensionTitle()
    {
        if (isset($this->runtime_data->render->extension->title)) {
        } else {
            return false;
        }

        $test_title = strtolower($this->runtime_data->render->extension->title);

        if ($test_title === 'comments') {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Should plugin be executed based on Source ID?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processCommentsPluginSourceId()
    {
        if (isset($this->parameters->source_id)) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Should plugin be executed based on Extension Title?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processCommentsPluginAvailableData()
    {
        $name = 'comments' . $this->parameters->source_id;

        if (isset($this->plugin_data->$name->parameters)
            && isset($this->plugin_data->$name->data)
        ) {
        } else {
            return false;
        }

        if (isset($this->plugin_data->$name->data[0]->start_publishing_datetime)
        ) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Should plugin be executed based on Comments Enabled?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processCommentsPluginEnabled()
    {
        if (isset($this->parameters->enable_response_comments)) {
        } else {
            return false;
        }

        if ($this->parameters->enable_response_comments === 1) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Determine if Comments are still open for Content
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function getCommentsOpen()
    {
        $open_days = $this->setFormOpenDays();

        if ((int)$open_days === 0) {
            return false;
        }

        $start_publishing_days_ago = $this->setStartPublishingDaysAgo();

        if ($start_publishing_days_ago > $open_days) {
            return false;
        }

        return true;
    }

    /**
     * Get the Number of Days the Form is to be displayed
     *
     * @return  integer
     * @since   1.0.0
     */
    protected function setFormOpenDays()
    {
        if ($this->parameters->enable_response_comment_form_open_days) {
            $open_days = (int)$this->parameters->enable_response_comment_form_open_days;
        } else {
            $open_days = 0;
        }

        return $open_days;
    }

    /**
     * Set Start Publishing Days Ago for comparison to Open Days
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function setStartPublishingDaysAgo()
    {
        $start_publishing_date = $this->date_controller->convertCCYYMMDD(
            $this->parameters->start_publishing_datetime
        );

        return $this->date_controller->getNumberofDaysAgo($start_publishing_date);
    }
}
