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
     * @var s$aveOrder string
     */
    protected $saveOrder;

    /** blog variables
     move variables into $options
     retrieve variables here in view - and split int rowset if needed
     */
	protected $category;
	protected $children;
	protected $lead_items = array();
	protected $intro_items = array();
	protected $link_items = array();
	protected $columns = 1;

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
        /** 1. Get Request */
        $this->request    = $this->get('Request');

        /** 2. Get State */
        $this->state      = $this->get('State');

        /** 3. Get System Variables */
        parent::display($tpl);

        /** 3. Output Layout for System Requests */
        //echo $this->getColumns ('system');

        /** 4. Retrieve Query Results */
        $this->rowset = $this->get('Items');

        /** 5. Retrieve Layout Parameters */
        $this->params = $this->get('Params');
        
        /** 6. Get Pagination data */
        $this->pagination = $this->get('Pagination');

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

        /** process model errors */
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        /**
         * Navigation
         */
        if (MolajoFactory::getApplication()->getName() == 'site') {

        }
var_dump($this->request);
echo 'Layout'.$this->request['layout'];
        $layoutFolder = $this->findPath($this->request['layout']);
        
        if ($layoutFolder === false) {
            parent::display($tpl);
        } else {
            echo $this->renderMolajoLayout ($layoutFolder);
        }
    }
}