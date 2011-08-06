<?php
/**
 * @package     Molajo
 * @subpackage  Display View
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
class MolajoViewDisplay extends MolajoView
{
    /**
     * @var $options object
     *
     * Contains all options which can be retrieved as this->state->get('option_name')
     *
     * 1. Filters and filtered values (for Administrator) - ex. $this->state->get('filter.category')
     *
     * 2. Merged Component Parameters (Global Options, Menu Item, Item)
     *    A. Including those used as selection criteria - ex. $this->state->get('filter.category')
     *    B. And those parameters needed by the layout - ex. $this->option->get('layout.show_title')
     *
     * 3. Component Request Variables
     *    $this->request['option'], and 'component_' + model, view, layout, DefaultView, EditView and task
     *
     * 4. 
     *
     */

    /** used in manager */

    /**
     * @var $render object
     */
    protected $render;

    /**
     * @var $saveOrder string
     */
    protected $saveOrder;

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

        /**
         * Render
         */

        /** Find Folder */
        $this->findPath($this->request['layout'], $this->request['layout_type']);

        /** Render */
        if ($this->layout_path === false) {
            parent::display($tpl);
            return;
        }

        $renderedOutput = $this->renderLayout ($this->request['layout']);
 
        /**
         *  Wrap
         */

        /** Find Folder */
        if (0 == 9) {
            echo $renderedOutput;
        }

        $this->rowset = array();

//        $rowset[0] = MolajoModuleHelper::getModule('mod_'.$position);
		$this->rowset[0]->title = 'This is the title';
		$this->rowset[0]->subtitle = 'This is the subtitle';
		$this->rowset[0]->content = $renderedOutput;
		$this->rowset[0]->position = '9';

        $this->findPath($this->params->get('wrap', 'section'), 'wrap');

        /** Wrap Rendered */
        if ($this->layout_path === false) {
        } else {
            /* title, subtitle, content, class, heading level */
            echo $this->renderLayout ('section');
            die();
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
