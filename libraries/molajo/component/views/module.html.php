<?php
/**
 * @package     Molajo
 * @subpackage  Module View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * View to Display All Layouts, except the Editor Layout
 *
 * @package	Molajo
 * @subpackage	Display View
 * @since	1.0
 */
class MolajoViewModule extends MolajoView
{

    /**
     * display
     *
     * View for Display View that uses no forms
     *
     * @param null $tpl
     * @return bool
     */
    public function display($tpl = null)
    {
        echo 'yellow!';

        /** 1. Request */
        $this->request = $this->get('Request');

        /** 2. State */
        $this->state = $this->get('State');

        /** 3. Parameters */
        $this->params = $this->get('Params');

        /** 4. Query Results */
        $this->rowset = $this->get('Items');

        /** 5. Pagination */
        $this->pagination = $this->get('Pagination');

        /** 6. System Variables */
        parent::display($tpl);

        /** Model errors */
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        /** No results */
        if ($this->params->get('suppress_no_results', false) === true
            && count($this->rowset == 0)) {
            return;
        }

        /** Render Layout */
        $this->findPath($this->request['layout'], $this->request['layout_type']);
        if ($this->layout_path === false) {
            // load an error layout
            return;
        }
        $renderedOutput = $this->renderLayout ($this->request['layout']);

        /** Wrap Rendered Layout */
        $session = JFactory::getSession();
        $layout = $this->params->get('wrap', 'div');
        if ($layout == 'horz') {
            $layout = 'horizontal';
        }

        $this->rowset = array();

		$this->rowset[0]->title     = $session->get('page.title', '');
		$this->rowset[0]->subtitle  = $session->set('page.subtitle', '');
		$this->rowset[0]->style     = $session->set('page.style', '');
		$this->rowset[0]->position  = $session->set('page.position', '');
		$this->rowset[0]->content   = $renderedOutput;
		$this->rowset[0]->position  = '';

        $this->findPath($layout, 'wrap');

        /** Wrap Rendered */
        if ($this->layout_path === false) {
            return $renderedOutput;
        } else {
            return $this->renderLayout ($layout);
        }
    }
}

/** 7. Optional data (put this into a model parent?) */
//		$this->category	            = $this->get('Category');
//		$this->categoryAncestors    = $this->get('Ancestors');
//		$this->categoryParent       = $this->get('Parent');
//		$this->categoryPeers	    = $this->get('Peers');
//		$this->categoryChildren	    = $this->get('Children');

//      $this->authorProfile        = $this->get('Author');

//      $this->tags (tag cloud)
//      $this->tagCategories (menu)
//      $this->calendar

/** blog variables
 move variables into $options
 retrieve variables here in view - and split int rowset if needed

protected $category;
protected $children;
protected $lead_items = array();
protected $intro_items = array();
protected $link_items = array();
protected $columns = 1;
 */
//Navigation
//$this->navigation->get('form_return_to_link')
//$this->navigation->get('previous')
//$this->navigation->get('next')
//
// Pagination
//$this->navigation->get('pagination_start')
//$this->navigation->get('pagination_limit')
//$this->navigation->get('pagination_links')
//$this->navigation->get('pagination_ordering')
//$this->navigation->get('pagination_direction')
//$this->breadcrumbs
//$total = $this->getTotal();

//$this->configuration
//Parameters (Includes Global Options, Menu Item, Item)
//$this->params->get('layout_show_page_heading', 1)
//$this->params->get('layout_page_class_suffix', '')
