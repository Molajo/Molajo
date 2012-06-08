<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\MVC\Controller;

use Molajo\Application;
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
class DisplayController extends ModelController
{
	/**
	 * Add action is used to render view output for a form used to create new content
	 *
	 * @return string Rendered output
	 * @since   1.0
	 */
	public function add()
	{
		return $this->display();
	}

	/**
	 * Edit action is used to render view output for a form used to display existing content
	 *
	 * @return string Rendered output
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
	 * @return string Rendered output
	 * @since   1.0
	 */
	public function display()
	{
		$includer_type = $this->get('includer_type', '');
		$includer_name = $this->get('includer_name', '');


		$model_name = $this->get('model_name', '');
		$model_type = $this->get('model_type', '');
		$model_parameter = $this->get('model_parameter', '');
		$model_query_object = $this->get('model_query_object', 'item');

		$table_registry_name = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
/**
echo 'Table Registry Name ' . $table_registry_name . '<br />';

echo 'Includer Type '. $includer_type .'<br />';
echo 'Includer Name '. $includer_name .'<br />';

echo 'Model Name ' . $model_name . '<br />'
	. 'Model Type:  ' . $model_type . '<br />'
	. 'Table Registry Name ' . $table_registry_name . '<br />'
	. 'Model Parameter '. $model_parameter .'<br />'
	. 'Model query_object: ' . $model_query_object . '<br /><br /><br />';
*/
		if ($model_name == '') {
			$this->query_results = array();

		} else {
			$this->connect($model_name, $model_type);

			if ((int) $this->get('content_id') == 0) {
			} else {
				$this->set('id', $this->get('content_id'));
				$model_query_object = 'item';
			}

			/** Run Query */
			$this->getData($model_query_object);
		}

		$this->pagination = array();

		/**
		 *  For primary content (the extension determined in Application::Request),
		 *      save query results in the Request object for reuse by other
		 *      extensions. MolajoRequestModel retrieves data.
		 */
		if ($this->get('extension_primary') === true) {
			Services::Registry()->set('Parameters', 'query_resultset', $this->query_results);
			Services::Registry()->set('Parameters', 'query_pagination', $this->pagination);
		}

		/** no results */
		if (count($this->query_results) == 0
			&& (int) $this->get('criteria_display_view_on_no_results', 0) == 0
		) {
			return '';
		}

		if (strtolower($includer_name) == 'wrap') {
			$renderedOutput = $this->query_results;

			/** Template View */
		} else {
			$this->view_path = $this->get('template_view_path');
			$this->view_path_url = $this->get('template_view_path_url');

			$renderedOutput = $this->renderView();

			/** Mustache */
			if ($this->get('criteria_mustache', 0) == 1) {
				$renderedOutput = $this->processRenderedOutput($renderedOutput);
			}
		}

		/** Wrap template view results */
		return $this->wrapView($this->get('wrap_view_title'), $renderedOutput);
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

		$temp->wrap_view_css_id = $this->get('wrap_view_css_id');
		$temp->wrap_view_css_class = $this->get('wrap_view_css_class');

		$temp->content = $renderedOutput;

		$this->query_results[] = $temp;

		/** paths */
		$this->view_path = $this->get('wrap_view_path');
		$this->view_path_url = $this->get('wrap_view_path_url');

		return $this->renderView();
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
	protected function renderView()
	{
		/** start collecting output */
		ob_start();

		/** 1. view handles loop and event processing */
		if (file_exists($this->view_path . '/View/Custom.php')) {
			include $this->view_path . '/View/Custom.php';

		} else {

			/** 2. controller manages loop and event processing */
			$totalRows = count($this->query_results);
			if (count($this->query_results) > 0) {

				$first = true;
				foreach ($this->query_results as $this->row) {

					/** header: before any rows are processed */
					if ($first == true) {
						$first = false;
						if (file_exists($this->view_path . '/View/Header.php')) {
							include $this->view_path . '/View/Header.php';
						}
					}

					if (file_exists($this->view_path . '/View/Body.php')) {
						include $this->view_path . '/View/Body.php';
					}
				}

				/** footer: after all rows are processed */
				if (file_exists($this->view_path . '/View/Footer.php')) {
					include $this->view_path . '/View/Footer.php';
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
			. ucfirst(Services::Registry()->get('Parameters', 'theme_path_node')) . '\\Helper\\'
			. 'Theme' . ucfirst(Services::Registry()->get('Parameters', 'theme_path_node')) . 'Helper';

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
//$this->get('view_show_page_view_heading', 1);
//$this->get('view_page_view_class_suffix', '');
