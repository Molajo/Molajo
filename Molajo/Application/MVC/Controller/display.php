<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Application\MVC\Controller;

defined('MOLAJO') or die;

/**
 * Display
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class DisplayController extends Controller
{

    /**
     * display
     *
     * Display task is used to render view output
     *
     * @return  string  Rendered output
     * @since   1.0
     */
    public function display()
    {
        /** check out */
        if ($this->get('task') == 'edit') {
            $results = parent::checkoutItem();

            if ($results === false) {
                //
            }
        }

        /** Set Criteria and Run Query */

        $this->rowset = $this->model->getData();
        $this->pagination = $this->model->getPagination();
        $this->model_state = $this->model->getState();

        /**
         *  For primary content (the extension determined in Molajo::Request),
         *      save query results in the Request object for reuse by other
         *      extensions. MolajoRequestModel retrieves data.
         */
        if ($this->get('extension_primary') === true) {
            Molajo::Request()->set('query_rowset', $this->rowset);
            Molajo::Request()->set('query_pagination', $this->pagination);
            Molajo::Request()->set('query_state', $this->model_state);
        }

        /** no results */
        if (count($this->rowset) == 0
            && $this->parameters->get('suppress_no_results') == 1
        ) {
            return '';
        }

        /** render template view */
        $this->view_path = $this->get('template_view_path');
        $this->view_path_url = $this->get('template_view_path_url');
        $renderedOutput = $this->renderView($this->get('template_view_name'));

        /** mustache */
        if ($this->parameters->get('mustache', 1) == 1) {
            $renderedOutput = $this->processRenderedOutput($renderedOutput);
        }

        /** render wrap view around template view results */
        return $this->wrapView($this->get('wrap_view_name'), $renderedOutput);
    }

    /**
     * wrapView
     *
     * @param $view
     * @param $renderedOutput
     *
     * @return string
     * @since 1.0
     */
    public function wrapView($view, $renderedOutput)
    {
        $this->rowset = array();

        $temp = new stdClass();
        $temp->wrap_view_css_id = $this->get('wrap_view_css_id');
        $temp->wrap_view_css_class = $this->get('wrap_view_css_class');
        $temp->content = $renderedOutput;

        $this->rowset[] = $temp;

        /** paths */
        $this->view_path = $this->get('wrap_view_path');
        $this->view_path_url = $this->get('wrap_view_path_url');

        /** render wrap */
        return $this->renderView($this->get('wrap_view_name'), 'wraps');
    }

    /**
     * renderView
     *
     * Depending on the files within view/view-type/view-name/views/*.*:
     *
     * 1. Include a single custom.php file to process all query results in $this->rowset
     *
     * 2. Include header.php, body.php, and/or footer.php views for Molajo to
     *  perform the looping, sending $row into the views
     *
     * @return string
     * @since 1.0
     */
    protected function renderView($view)
    {
        /** @var $rowCount */
        $rowCount = 1;

        /** start collecting output */
        ob_start();

        /** 1. view handles loop and event processing */
        if (file_exists($this->view_path . '/views/custom.php')) {
            include $this->view_path . '/views/custom.php';

        } else {

            /** 2. controller manages loop and event processing */
            $totalRows = count($this->rowset);
            foreach ($this->rowset as $this->row) {

                /** header: before any rows are processed */
                if ($rowCount == 1) {

                    if (isset($this->row->event->beforeRenderView)) {
                        echo $this->row->event->beforeRenderView;
                    }

                    if (file_exists($this->view_path . '/views/header.php')) {
                        include $this->view_path . '/views/header.php';
                    }
                }

                /** body: once for each row */
                if ($this->row == null) {
                } else {

                    if (isset($this->row->event->beforeRenderViewItem)) {
                        echo $this->row->event->beforeRenderViewItem;
                    }

                    if (file_exists($this->view_path . '/views/body.php')) {
                        include $this->view_path . '/views/body.php';
                    }

                    if (isset($this->row->event->afterRenderViewItem)) {
                        echo $this->row->event->afterRenderViewItem;
                    }

                    $rowCount++;
                }
            }

            /** footer: after all rows are processed */
            if ($rowCount > $totalRows) {

                if (file_exists($this->view_path . '/views/footer.php')) {
                    include $this->view_path . '/views/footer.php';

                    if (isset($this->row->event->afterRenderView)) {
                        echo $this->row->event->afterRenderView;
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
     * processRenderedOutput
     *
     * Passes the rendered output and the entire rowset into the
     * Theme Helper and Mustache for processing.
     *
     * @param $template
     *
     * @return string rendered output
     * @since  1.0
     */
    protected function processRenderedOutput($template)
    {
        /** quick check for mustache commands */
        if (stripos($template, '}}') > 0) {
        } else {
            return $template;
        }

        /** Instantiate Mustache before Theme Helper */
        $m = new Mustache;

        /** Theme Helper */
        $helperClass = 'Molajo' .
            ucfirst(Molajo::Request()->get('theme_name'))
            . 'ThemeHelper';

        if (class_exists($helperClass)) {
            $h = new $helperClass();
        } else {
            $h = new MolajoThemeHelper();
        }

        /** create input dataset */
        $totalRows = count($this->rowset);
        $row = 0;
        if ($totalRows > 0) {
            foreach ($this->rowset as $this->row) {
                $item = new stdClass ();
                $pairs = get_object_vars($this->row);
                foreach ($pairs as $key => $value) {
                    $item->$key = $value;
                }
                $h->data[$row] = $item;
                $row++;
            }
        }

        /** Pass rendered output and data to Helper */
        ob_start();
        echo $h->render($template, $h);
        $output = ob_get_contents();
        ob_end_clean();

        /** Return processed output */
        return $output;
    }
}

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
//$this->parameters->get('view_show_page_view_heading', 1);
//$this->parameters->get('view_page_view_class_suffix', '');
