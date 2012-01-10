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
     * @param    array   $request
     * @since    1.0
     */
    public function __construct(JObject $request)
    {
        parent::__construct($request);
    }

    /**
     * display
     *
     * Display task is used to render view output
     *
     * @return   object   Rendered output
     * @since    1.0
     */
    public function display()
    {
        /** model */
        $modelClass = $this->request->get('model');
        $this->model = new $modelClass();

        /** set model properties */
        $this->model->request = $this->request;
        $this->model->parameters = $this->parameters;

        /** check out */
        if ($this->request->get('task') == 'edit') {
            $results = parent::checkoutItem();
            if ($results === false) {
                return $this->redirectClass->setSuccessIndicator(false);
            }
        }

        /** query results */
        $this->rowset = $this->model->getItems();

        /** pagination */
        $this->pagination = $this->model->getPagination();

        /** no results */
        if (count($this->rowset) == 0
            && $this->parameters->def('suppress_no_results', false) === true
        ) {
            return;
        }

        /** render view */
        $this->view_path = $this->request->get('view_path');
        $this->view_path_url = $this->request->get('view_path_url');
        $renderedOutput = $this->renderView($this->request->get('view'), $this->request->get('view_type'));

        /** wrap view */
        return $this->wrapView($this->request->get('wrap'), 'wraps', $renderedOutput);
    }

    /**
     * wrapView
     *
     * @param $view
     * @param $view_type
     * @param $renderedOutput
     *
     * @return string
     * @since 1.0
     */
    public function wrapView($view, $view_type, $renderedOutput)
    {
        /** Wrap */
        $this->rowset = array();

        $tempObject = new JObject();
        $tempObject->set('wrap_id', $this->request->get('wrap_id'));
        $tempObject->set('wrap_class', $this->request->get('wrap_class'));
        $tempObject->set('content', $renderedOutput);

        $this->rowset[] = $tempObject;

        /** Render Wrap */
        $this->view_path = $this->request->get('wrap_path');
        $this->view_path_url = $this->request->get('wrap_path_url');

        return $this->renderView($this->request->get('wrap'), 'wraps');
    }

    /**
     * renderView
     *
     * Depending on the files within view/view-type/view-name/views/*.*:
     *
     * 1. Provide all query results in $this->rowset for the view to process
     *      How? Include view named custom.php
     *
     * 2. Loop thru the $this->rowset object processing each row, one at a time.
     *      How? Include top.php, header.php, body.php, footer.php, and/or bottom.php views
     *
     * @return string
     * @since 1.0
     */
    protected function renderView($view, $view_type)
    {
        /** @var $rowCount */
        $rowCount = 1;

        /** Media */
        $this->loadMedia();

        /** start collecting output */
        ob_start();

        /** 1. view handles loop and event processing */
        if (file_exists($this->view_path . '/views/custom.php')) {
            include $this->view_path . '/views/custom.php';

        } else {

            /** 2. controller manages loop and event processing */
            $totalRows = count($this->rowset);
            foreach ($this->rowset as $this->row) {

                /** view: before any rows are processed */
                if ($rowCount == 1) {

                    if (isset($this->row->event->beforeRenderView)) {
                        echo $this->row->event->beforeRenderView;
                    }

                    if (file_exists($this->view_path . '/views/top.php')) {
                        include $this->view_path . '/views/top.php';
                    }
                }

                /** view: row header, body, and footer */
                if ($this->row == null) {
                } else {

                    if (isset($this->row->event->beforeRenderViewItem)) {
                        echo $this->row->event->beforeRenderViewItem;
                    }

                    if (file_exists($this->view_path . '/views/header.php')) {
                        include $this->view_path . '/views/header.php';
                    }

                    if (file_exists($this->view_path . '/views/body.php')) {
                        include $this->view_path . '/views/body.php';
                    }

                    if (file_exists($this->view_path . '/views/footer.php')) {
                        include $this->view_path . '/views/footer.php';
                    }

                    if (isset($this->row->event->afterRenderViewItem)) {
                        echo $this->row->event->afterRenderViewItem;
                    }

                    $rowCount++;
                }

                /** view: after all rows are processed */
                if ($rowCount > $totalRows) {
                    if (file_exists($this->view_path . '/views/bottom.php')) {
                        include $this->view_path . '/views/bottom.php';

                        if (isset($this->row->event->afterRenderView)) {
                            echo $this->row->event->afterRenderView;
                        }
                    }
                }
            }
        }

        /** collect and return rendered output */
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
     * @since 1.0
     */
    protected function loadMedia()
    {
        /** Extension specific CSS and JS in => media/[extension]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/system/' . $this->request->get('option') . '/views';
        $urlPath = MOLAJO_BASE_URL . '/sites/' . MOLAJO_SITE . '/media/' . $this->request->get('option') . '/views';
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);

        /** Asset ID specific CSS and JS in => media/[application]/[asset_id]/css[js]/XYZ.css[js] */
        /** todo: amy deal with assets for all levels        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA.'/'.$this->request->get('asset_id');
        $urlPath = MOLAJO_BASE_URL . '/sites/'.MOLAJO_SITE.'/media/'.$this->request->get('asset_id');
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);
         */
        /** View specific CSS and JS in path identified in getPath */
        $filePath = $this->view_path . '/views';
        $urlPath = $this->view_path_url . '/views';
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);
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