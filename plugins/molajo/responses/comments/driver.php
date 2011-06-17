<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Responses
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

class MolajoResponsesComments {

    /**
     * Item name
     *
     * @var		string
     * @access	public
     */
    protected $item;

    /**
     * Form name
     *
     * @var		string
     * @access	public
     */
    protected $form;

    /**
     * Total
     *
     * @var		string
     * @access	public
     */
    protected $total;

    /**
     * Users
     *
     * @var		string
     * @access	public
     */
    protected $user;

    /**
     * Author
     *
     * @var		string
     * @access	public
     */
    protected $author;

    /**
     * Responses Component Parameters for Administrator
     *
     * @var		array
     * @access	public
     */
    protected $responsesParams;

    /**
     * $closed_message
     *
     * @var		string
     * @access	public
     */
    protected $closed_message;

    /**
     * $no_responses_message
     *
     * @var		string
     * @access	public
     */
    protected $no_responses_message;

    /**
     * Driver
     *
     * Method called by plgMolajoResponses::MolajoOnContentAfterDisplay to generate
     * all elements of contents including Summary, Form, and existing comment listing.
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content params
     * @param	int		The 'page' number
     * @param   string          The field containing text for the content object
     * @return	string
     * @since	1.6
     */
    function driver ($context, &$content, &$params, $page, $location, $closed_in_content)
    {
        return;
        
        /** user object **/
        $this->user= JFactory::getUser();

        /** created by value **/
        if (isset($content->created_by)) {
            $this->author = $content->created_by;
        } else {
            $this->author = 0;
        }

        /** request values **/
        $option = JRequest::getVar('option');
        $view = JRequest::getVar('view');
        if (($view == 'archive') || ($view == 'featured') || ($view == 'category') || ($view == 'categories')) {
            $multiple = true;
        } else {
            $multiple = false;
        }

        /** response component parameters **/
        $this->responsesParams = JComponentHelper::getParams('com_responses', true);
        if (in_array($content->catid, $this->responsesParams->def('enable_comments_categories', array()))) {
        } else {
            return;
        }

        /** close date **/
        if ($closed_in_content) {
            $closed = true;
        } else {
            $closed = false;
        }
        $opendays = (int) $this->responsesParams->def('opendays', 0);
        if ((int) $opendays < 1) {
            $opendays = 99999999;
        }
        $published = null;
        if (isset($content->publish_up)) {
            $published = MolajoDateHelper::convertCCYYMMDD ($content->publish_up);
        } else {
            if (isset($content->created)) {
                $published = MolajoDateHelper::convertCCYYMMDD ($content->created);
            }
        }
        if ($published) {
            $daysSincePublished = MolajoDateHelper::differenceDays (date('Y-m-d'), $published);
        } else {
            $daysSincePublished = 0;
        }
        if ($opendays < $daysSincePublished) {
            $closed = true;
        }

        /** closed message **/
        if ($closed == true) {
            $this->closed_message = $this->responsesParams->def('closed_message', JText::_('COM_RESPONSES_CONFIG_CLOSED_MESSAGE_DEFAULT'));
        } else {
            $this->closed_message = '';
        }
        $this->no_responses_message = $this->responsesParams->def('be_the_first_to_comment_message', JText::_('COM_RESPONSES_CONFIG_SUMMARY_MESSAGE_FIRST_DEFAULT'));

        /** language **/
        $language = JFactory::getLanguage();
        $language->load('com_responses', JPATH_ADMINISTRATOR.'/components/com_responses');

        /**
         *  Comment Layouts
         */
        $renderedLayouts = '';

        /** Layout: Summary **/
        $showSummary = $this->responsesParams->def('show_summary', 3);

        if (($showSummary == 3)                                  /** always **/
        || (($showSummary == 1) && ($multiple === true))         /** summary **/
        || (($showSummary == 2) && ($multiple === false)) ) {    /** detail **/
            $results = MolajoResponsesComments::renderResponsesSummary ($content->id, $summaryMessage);
            if ($results) {
                $renderedLayouts .= $results;
            }
        }

        /** Layout: Form **/
        if ($closed == false) {
            $showForm = $this->responsesParams->def('show_form', 2);

            if (($showForm == 3)                                  /** always **/
            || (($showForm == 1) && ($multiple === true))         /** summary **/
            || (($showForm == 2) && ($multiple === false)) ) {    /** detail **/

                $results = MolajoResponsesComments::renderResponseForm ($content->id);
                if ($results) {
                    $renderedLayouts .= $results;
                }
            }
        }

        /** Layout: Responses **/
        if ($multiple) {
        } else {
            $results = MolajoResponsesComments::renderResponsesResponses ($content->id);
            if ($results) {
                $renderedLayouts .= $results;
            }
        }

        return $renderedLayouts;
    }

    /**
     * renderResponsesSummary
     * $id - content id
     * return rendered results
     */
    function renderResponsesSummary ($id) {

        /** model **/
        $modelPath = JPATH_SITE.'/components/com_responses/models/summary.php';
        require_once $modelPath;

        $summaryModel = JModel::getInstance('ModelSummary', 'Responses', array('ignore_request' => true));
        $summaryModel->setState('filter.content_id', $id);
        $summaryModel->setState('filter.response_type', 1);

        if ((!$this->user->authorise('edit.state', 'com_responses')) &&  (!$this->user->authorise('edit', 'com_responses'))){
            $summaryModel->setState('filter.published', 1);
        }

        $this->total = $summaryModel->getTotal();

        /** layout **/
        $layoutPath = JPATH_SITE.'/components/com_responses/views/summary/tmpl/default.php';

        /** generate layout **/
        $renderedLayout = MolajoPluginHelper::generateLayout ($layoutPath);
    }

   /**
     * renderResponsesResponses
     * $id - content id
     * return rendered results
     */
    function renderResponseForm ($content_id) {

        /** ACL **/
        require_once JPATH_SITE.'/components/com_responses/models/response.php';
        $responsesModel = JModel::getInstance('ModelResponse', 'Responses', array('ignore_request' => true));
        $results = $responsesModel->allowAdd($content_id);
        if ($results === false) {
            return false;
        }

        /** model **/
        require_once JPATH_SITE.'/components/com_responses/models/form.php';
        require_once JPATH_ADMINISTRATOR.'/components/com_responses/tables/response.php';
        JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/com_responses/models/forms');

        $formModel = JModel::getInstance('ModelForm', 'Responses', array('ignore_request' => true));
        $formModel->setState('response.content_id', (int) $content_id);
        $formModel->setState('reponse.id', 0);
        $this->item = $formModel->getItem();
        $this->form = $formModel->getForm();
        if (!empty($this->item)) {
            $this->form->bind($this->item);
        }

        /** layout **/
        $layoutPath = JPATH_SITE.'/components/com_responses/views/form/tmpl/create.php';

        /** generate layout **/
        $renderedLayout = MolajoPluginHelper::generateLayout ($layoutPath);
    }

    /**
     * renderResponsesResponses
     * $id - content id
     * return rendered results
     */
    function renderResponsesResponses ($id) {

        /** model **/
        $modelPath = JPATH_SITE.'/components/com_responses/models/responses.php';
        require_once $modelPath;
        $order_date = $this->responsesParams->def('order_date', 'a.created');
        if ($order_date == 'a.publish_up') {
        } else {
            $order_date = 'a.created';
        }
        $orderby_sec = $this->responsesParams->def('orderby_sec', 'asc');
        if ($orderby_sec == 'desc') {
        } else {
            $orderby_sec = 'asc';
        }

        $responsesModel = JModel::getInstance('ModelResponses', 'Responses', array('ignore_request' => true));
        $responsesModel->setState('filter.content_id', $id);
        $responsesModel->setState('filter.response_type', 1);
        $responsesModel->setState('list.ordering', $order_date);
        $responsesModel->setState('list.direction', $orderby_sec);
        if ((!$this->user->authorise('edit.state', 'com_responses')) &&  (!$this->user->authorise('edit', 'com_responses'))){
            $responsesModel->setState('filter.published', 1);
        }

        $this->items = $responsesModel->getItems();

        for ($i = 0, $n = count($this->items); $i < $n; $i++)
        {
                $item = &$this->items[$i];
                $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

                $item->event = new stdClass();
                $dispatcher = JDispatcher::getInstance();

                $item->textual_response = JHtml::_('content.prepare', $item->textual_response);
        }

        /** layout **/
        $layoutPath = JPATH_SITE.'/components/com_responses/views/responses/tmpl/default.php';

        /** generate layout **/
        $renderedLayout = MolajoPluginHelper::generateLayout ($layoutPath);
    }
}