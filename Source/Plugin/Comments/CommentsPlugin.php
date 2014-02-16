<?php
/**
 * Comments Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Comments;

use CommonApi\Event\DisplayInterface;
use CommonApi\Exception\RuntimeException;
use Exception;
use Molajo\Plugin\DisplayEventPlugin;
use stdClass;

/**
 * Comments Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class CommentsPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Retrieve data for the Comments, Commentform, and Comment Template Views
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRenderView()
    {
        if (isset($this->plugin_data->render->extension->title)) {
        } else {
            return $this;
        }

        $test_title = strtolower($this->plugin_data->render->extension->title);

        if ($test_title == 'comments') {
        } else {
            return $this;
        }

        if (isset($this->parameters->source_id)) {
        } else {
            return $this;
        }

        $name = 'comments' . $this->parameters->source_id;

        if (isset($this->plugin_data->$name->parameters)
            && isset($this->plugin_data->$name->data)
        ) {
        } else {
            return $this;
        }

        if (isset($this->plugin_data->$name->data[0]->start_publishing_datetime)
        ) {
        } else {
            return $this;
        }

        foreach ($this->plugin_data->$name->parameters as $key => $value) {
            $this->parameters->$key = $value;
        }

        $this->parameters->start_publishing_datetime = $this->plugin_data->$name->data[0]->start_publishing_datetime;

        unset($this->plugin_data->$name);

        if (isset($this->parameters->enable_response_comments)) {
        } else {
            return $this;
        }

        if ($this->parameters->enable_response_comments == 1) {
        } else {
            return $this;
        }

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

        $this->plugin_data->comment_open = $this->getCommentsOpen();

        $this->getCommentHeading();
        $this->getComments();
        $this->getCommentForm();

        return $this;
    }

    /**
     * Determine if Comments are still open for Content
     *
     * @return  int
     * @since   1.0
     */
    protected function getCommentsOpen()
    {
        if ($this->parameters->enable_response_comment_form_open_days) {
            $open_days = (int)$this->parameters->enable_response_comment_form_open_days;
        } else {
            $open_days = 0;
        }

        if ((int)$open_days == 0) {
            return 0;
        }

        $converted = $this->date_controller->convertCCYYMMDD(
            $this->parameters->start_publishing_datetime
        );

        if ($converted === false) {
            return 0;
        }

        $actual = $this->date_controller->getNumberofDaysAgo($converted);
        if ($actual > $open_days) {
            return 0;
        }

        return 1;
    }

    /**
     * Retrieve Data for Comment Heading
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getCommentHeading()
    {
        $model = 'Molajo//Model//Datasource//Comments//Configuration.xml';

        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        $comments = $this->resource->get('query:///' . $model, $options);

        $comments->setModelRegistry('check_view_level_access', 1);
        $comments->setModelRegistry('process_events', 0);
        $comments->setModelRegistry('get_customfields', 1);
        $comments->setModelRegistry('query_object', 'result');

        $comments->model->query->select('count(*)');

        $comments->model->query->where(
            $comments->model->database->qn($comments->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $comments->model->database->qn('root')
            . ' = '
            . (int)$this->plugin_data->resource->data->id
        );

        $comments->model->query->where(
            $comments->model->database->qn($comments->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $comments->model->database->qn('status')
            . ' >  0'
        );

        try {
            $count = $comments->getData();

        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }

        $temp_row = new stdClass();

        $temp_row->count_of_comments = (int)$count;

        if ((int)$count == 0) {
            $temp_row->title        = $this->language_controller->translate('No comments');
            $temp_row->content_text = $this->language_controller->translate('There are no comments.');
        } else {
            $temp_row->title        = $this->language_controller->translate('Comments');
            $temp_row->content_text = $this->language_controller->translate('Comments');
        }

        if ((int)$this->plugin_data->comment_open == 0) {
            $temp_row->closed_comment = $this->language_controller->translate('Comments are closed.');
            $temp_row->closed         = 1;
        } else {
            $temp_row->closed_comment = '';
            $temp_row->closed         = 0;
        }

        $this->plugin_data->comments_heading->data           = $temp_row;
        $this->plugin_data->comments_heading->model_registry = $comments->getModelRegistry('*');

        return $this;
    }

    /**
     * Get Comments
     *
     * @returns  $this
     * @since    1.0
     * @throws   \CommonApi\Exception\RuntimeException
     */
    protected function getComments()
    {
        $model = 'Molajo//Model//Datasource//Comments//Configuration.xml';

        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        $comments = $this->resource->get('query:///' . $model, $options);

        $comments->setModelRegistry('check_view_level_access', 1);
        $comments->setModelRegistry('process_events', 1);
        $comments->setModelRegistry('get_customfields', 1);
        $comments->setModelRegistry('get_customfields', 1);
        $comments->setModelRegistry('query_object', 'list');

        $comments->model->query->where(
            $comments->model->database->qn($comments->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $comments->model->database->qn('root')
            . ' = '
            . (int)$this->plugin_data->resource->data->id
        );

        $comments->model->query->where(
            $comments->model->database->qn($comments->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $comments->model->database->qn('status')
            . ' >  0'
        );

        $comments->model->query->order(
            $comments->model->database->qn($comments->getModelRegistry('primary_prefix', 'a'))
            . '.' . $comments->model->database->qn('lft')
        );

        try {
            $this->plugin_data->comments_list->data           = $comments->getData();
            $this->plugin_data->comments_list->model_registry = $comments->getModelRegistry('*');

        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }

        return $this;
    }

    /**
     * Retrieve Data for Comment Heading
     *
     * @returns  stdClass
     * @since    1.0
     * @throws   \CommonApi\Exception\RuntimeException
     */
    protected function getCommentForm()
    {
        $temp_row         = new stdClass();
        $temp_row->comment_open = $this->plugin_data->comment_open;

        $this->plugin_data->comments_form->data           = $temp_row;

        return $this;

        //if ((int)$this->runtime_data->comments->open == 1) {

        /** Get configuration menuitem settings for this resource */
        $menuitem = $this->content_helper->getResourceMenuitemParameters('Configuration', 17000);

        /** Create Tabs */
        $namespace = 'Comments';

        $page_array = $this->registry->get('ConfigurationMenuitemParameters', 'comments_form_page_array');
        $page_array = '{{Comments,visitor*,email*,website*,ip*,spam*}}';

        /*
        visitor_name
        email_address
        website
        ip_address
        spam_protection
        */

        $tabs = Services::Form()->setPageArray(
            'System',
            'Comments',
            'Comments',
            $page_array,
            'comments_page_',
            'Comment',
            'Commenttab',
            17000,
            array()
        );

        $this->set('model_type', 'xxxx', 'runtime_data');
        $this->set('model_name', 'Edit', 'runtime_data');
        $this->set('model_query_object', 'item', 'runtime_data');

        $this->registry->set('Template', 'Commentform', $tabs);

        return $this;
    }

    /**
     * Retrieve data for the Comments, Commentform, and Comment Template Views
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRenderView()
    {
        if (isset($this->parameters->enable_response_comments)
            && isset($this->query_results[0]->id)
        ) {
        } else {
            return $this;
        }

        if ($this->parameters->enable_response_comments == 1) {
        } else {
            return $this;
        }

        $name                                 = 'comments' . $this->query_results[0]->id;
        $this->plugin_data->$name             = new stdClass();
        $this->plugin_data->$name->parameters = $this->parameters;
        $this->plugin_data->$name->data       = $this->query_results;

        return $this;
    }
}
