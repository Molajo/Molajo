<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\MVC\Controller;

use Molajo\Extension\Helpers;
use Molajo\Service\Services;
use Mustache\Mustache;

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
	 * Constructor.
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		return parent::__construct();
	}


	/**
	 * Add action is used to render view output for a form used to create new content
	 *
	 * @return  string  Rendered output
	 * @since   1.0
	 */
	public function add()
	{
		return $this->display();
	}

	/**
	 * Edit action is used to render view output for a form used to display existing content
	 *
	 * @return  string  Rendered output
	 * @since   1.0
	 */
	public function edit()
	{
		$results = parent::checkoutItem();

		if ($results === false) {
			//
		}

		return $this->display();
	}

	/**
	 * Display action is used to render view output
	 *
	 * @return  string  Rendered output
	 * @since   1.0
	 */
	public function display()
	{
		/** Primary Query */
		if ($this->parameters['query_object'] == 'none') {
			$this->query_results = array();
			$this->pagination = array();

		} else {
			$x = $this->parameters['query_object'];
			$this->query_results = $this->model->$x();

			/** Pagination (if needed) */
			$this->pagination = $this->model->getPagination();
		}

		/**
		 *  For primary content (the extension determined in Application::Request),
		 *      save query results in the Request object for reuse by other
		 *      extensions. MolajoRequestModel retrieves data.
		 */
		if (Services::Registry()->get('Parameters', 'extension_primary') === true) {
			Services::Registry()->set('Primary', 'query_resultset', $this->query_results);
			Services::Registry()->set('Primary', 'query_pagination', $this->pagination);
//Services::Registry()->set('Primary', 'query_state', $this->model_state);
		}

		/** no results */
		if (count($this->query_results) == 0
			&& Services::Registry()->get('Parameters', 'display_view_on_no_results') == 1
		) {
			//return '';
		}

		/** render template view */
		$this->view_path = Services::Registry()->get('TemplateView', 'path');
		$this->view_path_url = Services::Registry()->get('TemplateView', 'path_url');

		$renderedOutput = $this->renderView(Services::Registry()->get('TemplateView', 'title'));

		/** Mustache */
		if (Services::Registry()->get('Parameters', 'mustache', 1) == 1) {
			$renderedOutput = $this->processRenderedOutput($renderedOutput);
		}

		/** render wrap view around template view results */
		return $this->wrapView(Services::Registry()->get('WrapView', 'title'), $renderedOutput);
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
		$this->query_results = array();

		$temp = new \stdClass();
		$temp->wrap_view_css_id = Services::Registry()->get('WrapView', 'wrap_view_css_id');
		$temp->wrap_view_css_class = Services::Registry()->get('WrapView', 'wrap_view_css_class');
		$temp->content = $renderedOutput;

		$this->query_results[] = $temp;

		/** paths */
		$this->view_path = Services::Registry()->get('WrapView', 'path');
		$this->view_path_url = Services::Registry()->get('WrapView', 'path_url');

		/** render wrap */
		return $this->renderView(Services::Registry()->get('WrapView', 'title'), 'Wrap');
	}

	/**
	 * renderView
	 *
	 * Depending on the files within view/view-type/view-name/View/*.*:
	 *
	 * 1. Include a single Custom.php file to process all query results in $this->query_results
	 *
	 * 2. Include Header.php, Body.php, and/or Footer.php views for Molajo to
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
		if (file_exists($this->view_path . '/View/Custom.php')) {
			include $this->view_path . '/View/Custom.php';

		} else {

			/** 2. controller manages loop and event processing */
			$totalRows = count($this->query_results);
			foreach ($this->query_results as $this->row) {

				/** header: before any rows are processed */
				if ($rowCount == 1) {

					if (isset($this->row->event->beforeRenderView)) {
						echo $this->row->event->beforeRenderView;
					}

					if (file_exists($this->view_path . '/View/Header.php')) {
						include $this->view_path . '/View/Header.php';
					}
				}

				/** body: once for each row */
				if ($this->row == null) {
				} else {

					if (isset($this->row->event->beforeRenderViewItem)) {
						echo $this->row->event->beforeRenderViewItem;
					}

					if (file_exists($this->view_path . '/View/Body.php')) {
						include $this->view_path . '/View/Body.php';
					}

					if (isset($this->row->event->afterRenderViewItem)) {
						echo $this->row->event->afterRenderViewItem;
					}

					$rowCount++;
				}
			}

			/** footer: after all rows are processed */
			if ($rowCount > $totalRows) {

				if (file_exists($this->view_path . '/View/Footer.php')) {
					include $this->view_path . '/View/Footer.php';

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
	 * todo: create a trigger action here to invoke template tools, like mustache.
	 *
	 * processRenderedOutput
	 *
	 * Passes the rendered output and the entire resultset into the
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

		/** Theme Specific Mustache Helper or Molajo Mustache Helper */
		$helperClass = 'Molajo\\Extension\\Theme\\'
			. ucfirst(Services::Registry()->get('Theme', 'title')) . '\\Helper\\'
			. 'Theme' . ucfirst(Services::Registry()->get('Theme', 'title')) . 'Helper';

		if (\class_exists($helperClass)) {
			$h = new $helperClass();

		} else {
			$helperClass = 'Molajo\\Extension\\Helper\\MustacheHelper';
			$h = new $helperClass();
		}

		/** Push in Parameters */
		$h->parameters = $this->parameters;
		$h->items = $this->query_results;
		/** Push in model results */
		$totalRows = count($this->query_results);

		if (($this->query_results) == false) {
		     $totalRows = 0;
		}


		if (is_object($this->query_results)) {

			if ($totalRows > 0) {
				foreach ($this->query_results as $this->row) {

					$item = new \stdClass ();
					$pairs = get_object_vars($this->row);
					foreach ($pairs as $key => $value) {
						$item->$key = $value;
					}

					$new_query_results[] = $item;

				}
			}

			/** Load -- Associative Array */
		} else {
			$new_query_results = $this->query_results;
		}

		/** Pass in Rendered Output and Helper Class Instance */
		ob_start();
		echo $h->render($template);
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
//Services::Registry()->get('Parameters', 'view_show_page_view_heading', 1);
//Services::Registry()->get('Parameters', 'view_page_view_class_suffix', '');
