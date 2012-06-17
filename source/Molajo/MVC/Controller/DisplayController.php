<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 *
 *
if ($table_registry_name == 'XYZ') {
echo '<br /><br />' . $model_name . '<br /><br />';
echo 'Table Registry Name ' . $table_registry_name . '<br />';

echo 'Includer Type ' . $includer_type . '<br />';
echo 'Includer Name ' . $includer_name . '<br />';

echo 'Model Type: ' . $model_type . '<br />'
. 'Model Name:  ' . $model_name . '<br />'
. 'Table Registry Name ' . $table_registry_name . '<br />'
. 'Model Parameter ' . $model_parameter . '<br />'
. 'Model query_object: ' . $model_query_object . '<br /><br /><br />';
}
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

		$model_type = $this->get('model_type', '');
		$model_name = $this->get('model_name', '');
		$model_parameter = $this->get('model_parameter', '');
		$model_query_object = $this->get('model_query_object', 'item');

		$table_registry_name = ucfirst(strtolower($model_type)) . ucfirst(strtolower($model_name));

		$this->getTriggerList($model_query_object);

		if ($model_name == '') {
			$this->query_results = array();

		} else {
			$this->connect($model_type, $model_name);

			if ((int)$this->get('content_id') == 0) {

			} elseif (strtolower($model_type) == 'dbo') {

			} else {
				$this->set('id', $this->get('content_id'));
				$model_query_object = 'item';
			}

			/** Run Query */
			$this->getData($model_query_object);
		}

		$this->pagination = array();

		/** no results */
		if (count($this->query_results) == 0
			&& (int)$this->get('criteria_display_view_on_no_results', 0) == 0
		) {
			return '';
		}

		if (strtolower($includer_name) == 'wrap') {
			$renderedOutput = $this->query_results;

		} else {

			/** Template View */
			$this->set('view_css_id', $this->get('template_view_css_id'));
			$this->set('view_css_class', $this->get('template_view_css_class'));

			$this->view_path = $this->get('template_view_path');
			$this->view_path_url = $this->get('template_view_path_url');

			/** Trigger Pre-View Render Event */
			$this->onBeforeViewRender();

			/**
			 *  For primary content (the extension determined in Application::Request),
			 *      save query results in the Request object for reuse by other
			 *      extensions.
			 *
			 * todo: simplify all of the various dbo's into 'long term' and one view storage
			 */
			if ($this->get('extension_primary') == true) {
				Services::Registry()->set('Parameters', 'query_resultset', $this->query_results);
				Services::Registry()->set('Parameters', 'query_pagination', $this->pagination);
			}

			/** Render View */
			$renderedOutput = $this->renderView();

			/** Trigger After-View Render Event */
			$this->onAfterViewRender($renderedOutput);
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

		$this->set('view_css_id', $this->get('wrap_view_css_id'));
		$this->set('view_css_class', $this->get('wrap_view_css_class'));

		$temp->content = $renderedOutput;

		$this->query_results[] = $temp;

		/** paths */
		$this->view_path = $this->get('wrap_view_path');
		$this->view_path_url = $this->get('wrap_view_path_url');

		return $this->renderView();
	}

	/**
	 * Schedule onBeforeViewRender Event - could update query_results objects
	 *
	 * @return bool
	 * @since   1.0
	 */
	protected function onBeforeViewRender()
	{
		if ((int)$this->get('process_triggers') == 0) {
			return true;
		}

		/** Process the entire query_results set */
		$arguments = array(
			'table_registry_name' => $this->table_registry_name,
			'parameters' => $this->parameters,
			'query_results' => $this->query_results,
			'model_name' => $this->get('model_name')
		);

		$arguments = Services::Event()->schedule('onBeforeViewRender', $arguments);

		if ($arguments == false) {
			return false;
		}

		$this->query_results = $arguments['query_results'];

		return true;
	}

	/**
	 * renderView
	 *
	 * Depending on the files within view/view-type/view-name/View/*.*:
	 *
	 * 1. Include a single Custom.php file to process all query results in $this->query_results
	 *
	 * 2. Include Header.php, Body.php, and/or Footer.php views for Molajo to
	 *  perform the looping, injecting $row into each of the three views
	 *
	 * On no query results
	 *
	 * @return string
	 * @since 1.0
	 */
	protected function renderView()
	{
//todo think about empty queryresults processing when parameter set to true (custom and footer?)
//todo think about the result, item, and list processing - get dbo's in shape, triggers
//todo when close to done - do encoding - bring in filters - how?

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

					/** body: once for each row */
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
	 * Schedule onAfterViewRender Event - can update rendered results
	 *
	 * Position where mustache and Twig can process on rendered results
	 *
	 * @return bool
	 * @since   1.0
	 */
	protected function onAfterViewRender($renderedOutput)
	{
		if ((int)$this->get('process_triggers') == 0) {
			return true;
		}

/** Mustache */
//		if ($this->get('mustache', 0) == 1) {
//			$renderedOutput = $this->processRenderedOutput($renderedOutput);
//		}

		/** Process the entire query_results set */
		$arguments = array(
			'table_registry_name' => $this->table_registry_name,
			'parameters' => $this->parameters,
			'rendered_output' => $renderedOutput,
			'model_name' => $this->get('model_name')
		);

		$arguments = Services::Event()->schedule('onAfterViewRender', $arguments);

		if ($arguments == false) {
			return false;
		}

		$renderedOutput = $arguments['renderedOutput'];

		return $renderedOutput;
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
