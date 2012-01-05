<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display
 *
 * @package      Molajo
 * @subpackage   Controller
 * @since        1.0
 */
class MolajoControllerDisplay extends MolajoControllerExtension
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param    array   $request    From Extension Level
     *
     * @since    1.0
     */
    public function __construct($request = array())
    {
        parent::__construct($request);
    }

    /**
     * display
     *
     * Display task is used to render view output
     *
     * @return   object   Rendered output
     *
     * @since    1.0
     */
    public function display()
    {
//todo amy fix and remove
//$this->requestArray['model'] = 'dummy';

        /** model */
        $this->model = new $this->requestArray['model']();

        /** set model properties */
        $this->model->requestArray = $this->requestArray;
        $this->model->parameters = $this->parameters;

        /** check out */
        if ($this->requestArray['task'] == 'edit') {
            $results = parent::checkoutItem();
            if ($results === false) {
                return $this->redirectClass->setSuccessIndicator(false);
            }
        }

        /** Query Results */
        $this->rowset = $this->model->getItems();

        /** Pagination */
        $this->pagination = $this->model->getPagination();

        /** No results */
        if (count($this->rowset) == 0
            && $this->parameters->def('suppress_no_results', false) === true
        ) {
            return;
        }

        /** Render View */
        $this->view_path = $this->requestArray['view_path'];
        $this->view_path_url = $this->requestArray['view_path_url'];
        $renderedOutput = $this->renderView($this->requestArray['view'], $this->requestArray['view_type']);

        /** Wrap */
        $this->rowset = array();

        $tmpobj = new JObject();
        $tmpobj->set('wrap_id', $this->requestArray['wrap_id']);
        $tmpobj->set('wrap_class', $this->requestArray['wrap_class']);
        $tmpobj->set('content', $renderedOutput);

        $this->rowset[] = $tmpobj;

        /** Render Wrap */
        $this->view_path = $this->requestArray['wrap_path'];
        $this->view_path_url = $this->requestArray['wrap_path_url'];
        $wrappedOutput = $this->renderView($this->requestArray['wrap'], 'wraps');

        /** Wrapped View */
        echo $wrappedOutput;
        return;
    }

    /**
     * renderView
     *
     * Can do one of two things:
     *
     * 1. Provide the entire set of query results in the $this->rowset object for the view to process
     *      How? Include a view file named custom.php (and no view file and body.php)
     *
     * 2. Loop thru the $this->rowset object processing each row, one at a time.
     *      How? Include top.php, header.php, body.php, footer.php, and/or bottom.php
     *
     * Loops through rowset, one row at a time, including top, header, body, footer, and bottom files
     *
     * @return string
     *
     */
    protected function renderView($view, $view_type)
    {
        /** @var $rowCount */
        $rowCount = 1;

        /** Media */
        $this->loadMedia();

        /** start collecting output */
        ob_start();

        /**
         *  I. Rowset processed by View
         *
         *  If the custom.php file exists in viewFolder, view handles $this->rowset processing
         *
         */
        if (file_exists($this->view_path . '/views/custom.php')) {
            include $this->view_path . '/views/custom.php';

        } else {

            /**
             * II. Loop through each row, one at a time
             *
             * The following viewFolder/views/ files are included, if existing
             *
             * 1. Before any rows and if there is a top.php file:
             *
             *       - beforeDisplayContent output is rendered;
             *
             *       - the top.php file is included.
             *
             * 2. For each row:
             *
             *      if there is a header.php file, it is included,
             *        and the event afterDisplayTitle output is rendered.
             *
             *      If there is a body.php file, it is included;
             *
             *      If there is a footer.php file, it is included;
             *
             * 3. After all rows and if there is a footer.php file:
             *      the footer.php file is included;
             *      afterDisplayContent output is rendered;
             *
             */
            foreach ($this->rowset as $this->row) {

                /** view: top */
                if ($rowCount == 1) {

                    /** event: Before Content Display */
                    if (isset($this->row->event->beforeDisplayContent)) {
                        echo $this->row->event->beforeDisplayContent;
                    }

                    if (file_exists($this->view_path . '/views/top.php')) {
                        include $this->view_path . '/views/top.php';
                    }
                }

                if ($this->row == null) {
                } else {

                    /** item: header */
                    if (file_exists($this->view_path . '/views/header.php')) {
                        include $this->view_path . '/views/header.php';

                        /** event: After Display of Title */
                        if (isset($this->row->event->afterDisplayTitle)) {
                            echo $this->row->event->afterDisplayTitle;
                        }
                    }

                    /** item: body */
                    if (file_exists($this->view_path . '/views/body.php')) {
                        include $this->view_path . '/views/body.php';
                    }

                    /** item: footer */
                    if (file_exists($this->view_path . '/views/footer.php')) {
                        include $this->view_path . '/views/footer.php';
                    }

                    $rowCount++;
                }

                /** view: bottom */
                if (file_exists($this->view_path . '/views/bottom.php')) {
                    include $this->view_path . '/views/bottom.php';

                    /** event: After View is finished */
                    if (isset($this->row->event->afterDisplayContent)) {
                        echo $this->row->event->afterDisplayContent;
                    }
                }
            }
        }

        /** collect output */
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * loadMedia
     *
     * Automatically includes the following files (if existing)
     *
     * 1. Application-specific CSS and JS in => media/[application]/css[js]/XYZ.css[js]
     * 2. Extension specific CSS and JS in => media/[extension]/css[js]/XYZ.css[js]
     * 3. Asset ID specific CSS and JS in => media/[asset_id]/css[js]/XYZ.css[js]
     *
     * 4. View Path determined earlier (Template, Extension, Core precedence)
     *
     * Note: Right-to-left css files should begin with rtl_
     *
     * @return void
     */
    protected function loadMedia()
    {
        /** Extension specific CSS and JS in => media/[extension]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/system/' . $this->requestArray['option'] . '/views';
        $urlPath = MOLAJO_BASE_URL . '/sites/' . MOLAJO_SITE . '/media/' . $this->requestArray['option'] . '/views';
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);

        /** Asset ID specific CSS and JS in => media/[application]/[asset_id]/css[js]/XYZ.css[js] */
        /** todo: amy deal with assets for all levels        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA.'/'.$this->requestArray['asset_id'];
        $urlPath = MOLAJO_BASE_URL . '/sites/'.MOLAJO_SITE.'/media/'.$this->requestArray['asset_id'];
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);
         */
        /** View specific CSS and JS in path identified in getPath */
        $filePath = $this->view_path . '/views';
        $urlPath = $this->view_path_url . '/views';
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);
    }

    /**
     * Escapes a value for output in a controller script.
     *
     * If escaping mechanism is either htmlspecialchars or htmlentities, uses
     * {@link $_encoding} setting.
     *
     * @param   mixed  $var  The output to escape.
     *
     * @return  mixed  The escaped value.
     *
     * @since   1.0
     */
    function escape($var)
    {
        if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
        }

        return call_user_func($this->_escape, $var);
    }
}

/** 7. Optional data (put this into a model parent?) */
//		$this->category	            = $this->get('Category');
//		$this->categoryAncestors    = $this->get('Ancestors');
//		$this->categoryParent       = $this->get('Parent');
//		$this->categoryPeers	    = $this->get('Peers');
//		$this->categoryChildren	    = $this->get('Children');

/** used in manager */

/**
 * @var $render object
 */
//protected $render;

/**
 * @var $saveOrder string
 */
// protected $saveOrder;
//      $this->authorProfile        = $this->get('Author');

//      $this->tags (tag cloud)
//      $this->tagCategories (menu)
//      $this->calendar

/** blog variables
move variables into $options
retrieve variables here in controller - and split int rowset if needed

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

//$this->configuration;
//Parameters (Includes Global Options, Menu Item, Item);
//$this->parameters->get('view_show_page_heading', 1);
//$this->parameters->get('view_page_class_suffix', '');