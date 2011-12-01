<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Responses
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoResponsesRatings {

	/**
	 * State
	 *
	 * @var		string
	 * @access	protected
	 */
	var $state;

	/**
	 * Item name
	 *
	 * @var		string
	 * @access	protected
	 */
	var $item;

	/**
	 * Form name
	 *
	 * @var		string
	 * @access	protected
	 */
	var $form;

	/**
	 * Users
	 *
	 * @var		string
	 * @access	protected
	 */
	var $user;

    /**
     * Driver
     *
     * Method called by plgMolajoResponses::MolajoOnContentAfterDisplay to generate
     * all elements of contents including Summary, Form, and existing comment listing.
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content parameters
     * @param	int		The 'page' number
     * @return	string
     * @since	1.6
     */
    function driver ($context, &$content, &$parameters, $page = 0)
    {

        /** request values **/
        $option = JRequest::getVar('option');
        $view = JRequest::getVar('view');
        if (($view == 'archive') || ($view == 'featured') || ($view == 'category') || ($view == 'categories')) {
            $multiple = true;
        } else {
            $multiple = false;
        }

        /** response component parameters **/
        $responsesParameters = MolajoApplicationComponent::getParameters('responses', true);
        if (in_array($content->catid, $responsesParameters->def('enable_comments_categories', array()))) {
        } else {
            return;
        }

        /**
         *  Comment Layouts
         */
        $renderedLayouts = '';

        /** Layout: Summary **/
        $results = MolajoResponsesRatings::renderResponsesSummary ($content->id);
        if ($results) {
            $renderedLayouts .= $results;
        }

        return $renderedLayouts;
        /** Layout: Form **/
        if ($multiple) {
        } else {
            $results = MolajoResponsesRatings::renderResponseForm ($content->id);
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
        $modelPath = JPATH_SITE.'/components/responses/models/responses.php';
        require_once $modelPath;

        $responsesModel = JModel::getInstance('ModelResponses', 'Responses', array('ignore_request' => true));
        $responsesModel->setState('filter.content_id', $id);
        $responsesModel->setState('filter.response_type', 1);
        $responsesModel->setState('list.ordering', 'a.created');
        $responsesModel->setState('list.direction', 'desc');

        $this->state	= $responsesModel->getState();
        $this->item	= $responsesModel->getItems();

        /** layout **/
        $layoutPath = JPATH_SITE.'/components/responses/views/summary/layouts/default.php';

        /** generate layout **/
        return MolajoResponsesRatings::generate_layout ($layoutPath);
    }

   /**
     * renderResponsesResponses
     * $id - content id
     * return rendered results
     */
    function renderResponseForm ($content_id) {

        /** ACL **/
        require_once JPATH_SITE.'/components/responses/models/response.php';
        $responsesModel = JModel::getInstance('ModelResponse', 'Responses', array('ignore_request' => true));
        $results = $responsesModel->allowAdd($content_id);
        if ($results === false) {
            return false;
        }

        /** model **/
        require_once JPATH_SITE.'/components/responses/models/form.php';
        require_once JPATH_ADMINISTRATOR.'/components/responses/tables/response.php';
        JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/responses/models/forms');

        $formModel = JModel::getInstance('ModelForm', 'Responses', array('ignore_request' => true));
        $formModel->setState('response.content_id', (int) $content_id);
        $formModel->setState('reponse.id', 0);
        $this->state = $formModel->getState();
        $this->item = $formModel->getItem();
        $this->form = $formModel->getForm();
        if (!empty($this->item)) {
            $this->form->bind($this->item);
        }

        /** layout **/
        $layoutPath = JPATH_SITE.'/components/responses/views/form/layouts/create.php';

        /** generate layout **/
        return MolajoResponsesRatings::generate_layout ($layoutPath);
    }

    /**
     * generate_layout
     * @param string $layoutPath
     * @return string
     */
    function generate_layout ($layoutPath) {
        ob_start();
        require $layoutPath;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
}