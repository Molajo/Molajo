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
        /** 7. System Variables */
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
//var_dump($this->state);
//echo 'layout '.$this->request['layout'].'<br />';
//echo 'type '.$this->request['layout_type'].'<br />';
        $this->findPath($this->request['layout'], $this->request['layout_type']);
//echo 'layout_path '.$this->layout_path.'<br />';
        if ($this->layout_path === false) {
            // load an error layout
            return;
        }
        $renderedOutput = $this->renderLayout ($this->request['layout']);

/** Wrap Rendered Layout */
// Dynamically add outline style
//  if (JRequest::getBool('tp')
//      && MolajoComponentHelper::getParams('com_templates')->get('template_positions_display')) {
//      $attribs['style'] .= ' outline';
//  }

//consolidate with view.html - consider $this->state for wrap (module is there)

       /** Wrap Rendered Layout */
        $layout = $this->params->get('wrap', 'none');
        if ($layout == 'horz') { $layout = 'horizontal'; }
        if ($layout == 'xhtml') { $layout = 'div'; }
        if ($layout == 'rounded') { $layout = 'div'; }

        $this->rowset = array();

		$this->rowset[0]->title     = $this->state->title;
		$this->rowset[0]->subtitle  = $this->state->subtitle;
		$this->rowset[0]->style     = $this->state->style;
		$this->rowset[0]->position  = $this->state->position;

		$this->rowset[0]->content   = $renderedOutput;

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
