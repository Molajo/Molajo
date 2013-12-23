<?php
/**
 * Comments Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Comments;

use stdClass;
use Exception;
use CommonApi\Event\SystemInterface;
use CommonApi\Exception\RuntimeException;
use Molajo\Plugin\SystemEventPlugin;

/**
 * Comments Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class CommentsPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Retrieve data for the Comments, Commentform, and Comment Template Views
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeExecute()
    {
        if (isset($this->runtime_data->plugin_data->comments_open)) {
            return $this;
        }

        $this->runtime_data->plugin_data->comments_open                    = new stdClass();
        $this->runtime_data->plugin_data->comments_heading                 = new stdClass();
        $this->runtime_data->plugin_data->comments_heading->data           = new stdClass();
        $this->runtime_data->plugin_data->comments_heading->model_registry = new stdClass();
        $this->runtime_data->plugin_data->comments                         = new stdClass();
        $this->runtime_data->plugin_data->comments->data                   = new stdClass();
        $this->runtime_data->plugin_data->comments->model_registry         = new stdClass();
        $this->runtime_data->plugin_data->comment_form                     = new stdClass();
        $this->runtime_data->plugin_data->comment_form->data               = new stdClass();
        $this->runtime_data->plugin_data->comment_form->model_registry     = new stdClass();

        $page_type = strtolower($this->runtime_data->route->page_type);
        if ($page_type == 'item') {
        } else {
            return $this;
        }

        $this->runtime_data->resource->parameters->enable_response_comments = 1;

        if (isset($this->runtime_data->resource->parameters->enable_response_comments)
            && (int)$this->runtime_data->resource->parameters->enable_response_comments == 1) {
        } else {
            return $this;
        }

        if (strtolower($this->runtime_data->route->page_type) == 'item') {
        } else {
            return $this;
        }

        $this->runtime_data->plugin_data->comments_open = $this->getCommentsOpen();

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
        if (isset($this->runtime_data->resource->parameters->enable_response_comments)
            && isset($this->runtime_data->resource->parameters->enable_response_comment_form_open_days)
            && (int)$this->runtime_data->resource->parameters->enable_response_comments == 1
        ) {
            $open_days = (int)$this->runtime_data->resource->parameters->enable_response_comment_form_open_days;
        } else {
            $open_days = 0;
        }

        if ((int)$open_days == 0) {
            return 0;
        }

        $converted = $this->date_controller->convertCCYYMMDD(
            $this->runtime_data->resource->data->start_publishing_datetime
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
        $comments = $this->resource->get(
            'query:///Molajo//Datasource//Comments//Configuration.xml',
            array('runtime_data', $this->runtime_data)
        );

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
            . (int)$this->runtime_data->route->source_id
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

        if ((int)$this->runtime_data->plugin_data->comments_open == 0) {
            $temp_row->closed_comment = $this->language_controller->translate('Comments are closed.');
            $temp_row->closed         = 1;
        } else {
            $temp_row->closed_comment = '';
            $temp_row->closed         = 0;
        }

        $this->runtime_data->plugin_data->comments_heading->data           = $temp_row;
        $this->runtime_data->plugin_data->comments_heading->model_registry = $comments->getModelRegistry('*');

        return $this;
    }

    /**
     * Get Comments
     *
     * @returns  stdClass
     * @since    1.0
     * @throws   \CommonApi\Exception\RuntimeException
     */
    protected function getComments()
    {
        $comments = $this->resource->get(
            'query:///Molajo//Datasource//Comments//Configuration.xml',
            array('Parameters', $this->runtime_data)
        );

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
            . (int)$this->runtime_data->route->source_id
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
            $this->runtime_data->plugin_data->comments->data           = $comments->getData();
            $this->runtime_data->plugin_data->comments->model_registry = $comments->getModelRegistry('*');

        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }
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
        $temp_row->closed = $this->runtime_data->plugin_data->comments_open;

        return $temp_row;

        //if ((int)$this->runtime_data->comments->open == 1) {

        /** Get configuration menuitem settings for this resource */
        $menuitem = $this->content_helper->getResourceMenuitemParameters('Configuration', 17000);

        /** Create Tabs */
        $namespace = 'Comments';

        $page_array = $this->registry->get('ConfigurationMenuitemParameters', 'commentform_page_array');
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
}
