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
namespace Molajo\Controller;

use Molajo\Application;
use Molajo\Service\Services;

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
	 * @return  string Rendered output
	 * @since   1.0
	 */
	public function add()
	{
		return $this->display();
	}

	/**
	 * Edit action is used to render view output for a form used to display existing content
	 *
	 * @return  string Rendered output
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
	 * @return  string Rendered output
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

		if ($model_name == '') {
			$this->query_results = array();

		} else {
			$this->connect($model_type, $model_name);

			if ((int)$this->get('content_id') == 0) {
//todo end up with: 1. result, 2. item, 3. list(dbo needs to change - add parameter for specific query, don't hijack
			} elseif (strtolower($model_type) == 'dbo') {

			} else {
				$this->set('id', $this->get('content_id'));
				$model_query_object = 'item';
			}

			/** Run Query */
			$this->getData($model_query_object);
		}

//todo - move this into a trigger?
		$this->pagination = array();

		/** no results */
		if (count($this->query_results) == 0
			&& (int)$this->get('criteria_display_view_on_no_results', 0) == 0
		) {
			return '';
		}

		if (strtolower($includer_name) == 'wrap') {
			$rendered_output = $this->query_results;

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
			 * todo: simplify all of the various dbo's into application-wide and single-view storage
			 */
			if ($this->get('extension_primary') == true) {
				Services::Registry()->set('Parameters', 'query_resultset', $this->query_results);
				Services::Registry()->set('Parameters', 'query_pagination', $this->pagination);
			}

			/** Render View */
			$rendered_output = $this->renderView();

			/** Trigger After-View Render Event */
			$rendered_output = $this->onAfterViewRender($rendered_output);
		}

		/** Wrap template view results */
		return $this->wrapView($this->get('wrap_view_title'), $rendered_output);
	}

	/**
	 * wrapView
	 *
	 * @param  $view
	 * @param  $rendered_output
	 *
	 * @return string
	 * @since  1.0
	 */
	public function wrapView($view, $rendered_output)
	{
//todo: decide if not passing wraps thru view triggers will come back and bite us in the ass.
		$this->query_results = array();

		$temp = new \stdClass();

		$this->set('view_css_id', $this->get('wrap_view_css_id'));
		$this->set('view_css_class', $this->get('wrap_view_css_class'));

		$temp->content = $rendered_output;

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
	 * @since  1.0
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
	 * @return  string
	 * @since   1.0
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
	protected function onAfterViewRender($rendered_output)
	{
		return $rendered_output;
		if ((int)$this->get('process_triggers') == 0) {
			return $rendered_output;
		}

		/** Process the entire query_results set */
		$arguments = array(
			'table_registry_name' => $this->table_registry_name,
			'parameters' => $this->parameters,
			'rendered_output' => $rendered_output,
			'model_name' => $this->get('model_name')
		);

		$arguments = Services::Event()->schedule('onAfterViewRender', $arguments);

		if ($arguments == false) {
			return false;
		}

		$rendered_output = $arguments['rendered_output'];

		return $rendered_output;
	}
}
