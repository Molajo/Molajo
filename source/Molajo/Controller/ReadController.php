<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Controller;

use Molajo\Application;
use Molajo\Service\Services;
use Molajo\Controller\Controller;

defined('MOLAJO') or die;

/**
 * Display
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class ReadController extends Controller
{

	/**
	 * Display action is used to render view output
	 *
	 * @return  string Rendered output
	 * @since   1.0
	 */
	public function execute()
	{
		/**
		if ($action == 'edit') {
		} elseif ($action == 'edit') {
		} elseif ($action == 'display') {
		}
		 */

		if ($this->get('model_name', '') == '') {
			$this->query_results = array();

		} else {
			$this->connect($this->get('model_type'), $this->get('model_name'));

			if ((int)$this->get('content_id') == 0) {
//todo end up with: 1. result, 2. item, 3. list(dbo needs to change - add parameter for specific query, don't hijack
			} elseif (strtolower($this->get('model_type', '')) == 'dbo') {

			} else {
				$this->set('id', $this->get('content_id'));
				$this->set('model_query_object', 'item');
			}

			/** Run Query */
			$this->getData($this->get('model_query_object', 'item'));

			if (Services::Registry()->get('Configuration', 'debug_output_queries_query_results', 0) == 1) {

				$debug_message = 'ReadController->execute '
					. ' <br />Includer: ' . $this->get('includer_type', '')
					. ' <br />Model Type: ' . $this->get('model_type', '')
					. ' <br />Model Type: ' . $this->get('model_type', '')
					. ' <br />Model Name: ' . $this->get('model_name', '')
					. ' <br />Model Parameter: ' . $this->get('model_parameter', '')
					. ' <br />Model Query Object: ' . $this->get('model_query_object', '')
					. ' <br />Template Path: ' . $this->get('template_view_path', '')
					. ' <br />Wrap Path: ' . $this->get('wrap_view_path', '');

				ob_start();
				echo '<pre>';
				var_dump($this->query_results);
				echo '</pre>';
				$debug_message .= ob_get_contents();
				ob_end_clean();

				Services::Debug()->set('ReadController->onAfterReadEvent ' . $debug_message
					. ' Schedules onAfterRead', LOG_OUTPUT_TRIGGERS, VERBOSE);
			}
		}

//todo - move this into a trigger?
		$this->pagination = array();

		/** no results */
		if (count($this->query_results) == 0
			&& (int)$this->get('criteria_display_view_on_no_results', 0) == 0
		) {
			return '';
		}

		if (strtolower($this->get('includer_name', '')) == 'wrap') {
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
			 */
			if ($this->get('extension_primary') == true) {
				Services::Registry()->set('RouteParameters', 'query_resultset', $this->query_results);
				Services::Registry()->set('RouteParameters', 'query_pagination', $this->pagination);
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
	 * Method to execute a model method which interacts with the data source and returns results
	 *
	 * @param string $query_object - result, item, or list
	 *
	 * @return mixed Depends on QueryObject selected
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function getData($query_object = 'list')
	{
		$dbo = Services::Registry()->get($this->table_registry_name, 'data_source', 'JDatabase');

		if ($dbo == 'JDatabase') {
		} else {

			$model_parameter = null;

			if ($this->get('model_parameter') == '') {
			} else {
				$model_parameter = $this->get('model_parameter');
			}

			Services::Debug()->set('ReadController->getData '
					. ' DBO: ' . $dbo
					. ' Query Object: ' . $query_object
					. ' Model Parameter: ' . $model_parameter,
				LOG_OUTPUT_QUERIES, VERBOSE);

			if (strtolower($query_object) == 'getdummy') {
				$this->query_results = array();
			} else {

				$this->query_results = $this->model->$query_object($model_parameter);
			}

			return $this->query_results; //must directly return for non-ReadController calls
		}

		/** Only JDatabase queries follow */
		if (in_array($query_object, array('result', 'item', 'list', 'distinct'))) {
		} else {
			$query_object = 'list';
		}

		/** Retrieve list of potential $this->triggers for this model (result type does not use events) */
		$this->getTriggerList($query_object);

		/** Base query */
		if ($query_object == 'item' || $query_object == 'result') {
			$id_key = (int)$this->get('id', 0);
			$name_key_value = (string)$this->get('name_key_value', '');

		} else {
			$id_key = 0;
			$name_key_value = '';
		}

		/** Establishes the Field values (if not already set) and the primary from table */
		$this->model->setBaseQuery(
			Services::Registry()->get($this->table_registry_name, 'Fields'),
			$this->get('table_name'),
			$this->get('primary_prefix'),
			$this->get('primary_key'),
			$id_key,
			$this->get('name_key'),
			$name_key_value,
			$query_object
		);

		/** Passes query object to Authorisation Services to append ACL query elements */
		if ((int)$this->get('check_view_level_access') == 1) {
			$this->model->addACLCheck(
				$this->get('primary_prefix'),
				$this->get('primary_key'),
				$query_object
			);
		}

		/** Adds Select, From and Where query elements for Joins */
		if ((int)$this->get('use_special_joins') == 1) {
			$joins = Services::Registry()->get($this->table_registry_name, 'Joins');
			if (count($joins) > 0) {
				$this->model->useSpecialJoins(
					$joins,
					$this->get('primary_prefix'),
					$query_object
				);
			}
		}

		/** Schedule onBeforeRead Event */
		if (count($this->triggers) > 0) {
			$this->onBeforeReadEvent();
		}

		$offset = $this->get('model_offset', 0);
		$count = $this->get('model_count', 0);

		if ($offset == 0 && $count == 0) {
			if ($query_object == 'result') {
				$offset = 0;
				$count = 1;
			} elseif ($query_object == 'distinct' || $query_object = 'getListdata') {
				$offset = $this->get('model_offset', 0);
				$count = $this->get('model_count', 9999);
			} else {
				$offset = $this->get('model_offset', 0);
				$count = $this->get('model_count', 10);
			}
		}

		$this->model->getQueryResults(
			Services::Registry()->get($this->table_registry_name, 'Fields'),
			$query_object,
			$offset,
			$count
		);

		if (Services::Registry()->get('Configuration', 'debug_output_queries_sql', 0) == 1) {
			Services::Debug()->set('ReadController->getData SQL Query: <br /><br />'
					. $this->model->query->__toString(),
				LOG_OUTPUT_RENDERING, VERBOSE);
		}

		/** Retrieve query results from Model */
		$query_results = $this->model->get('query_results');

		/** Return result (single value) */
		if ($query_object == 'result' || $query_object == 'distinct') {

			if (Services::Registry()->get('Configuration', 'debug_output_queries_query_results', 0) == 1) {
				$message = 'ReadController->getData Query Results <br /><br />';
				ob_start();
				echo '<pre>';
				var_dump($query_results);
				echo '</pre><br /><br />';
				$message .= ob_get_contents();
				ob_end_clean();
				Services::Debug()->set($message, LOG_OUTPUT_QUERIES, VERBOSE);
			}

			return $query_results;
		}

		/** No results */
		if (count($query_results) > 0) {
		} else {
			return false;
		}

		/** Iterate through results to process special fields and requests for additional queries for child objects */
		$q = array();

		foreach ($query_results as $results) {

			/** Load Special Fields */
			if ((int)$this->get('get_customfields') == 0) {
			} else {

				$customFieldTypes = Services::Registry()->get($this->table_registry_name, 'CustomFieldGroups');

				if (count($customFieldTypes) == 0 || $customFieldTypes == null) {
				} else {

					/** Process each field namespace */
					foreach ($customFieldTypes as $customFieldName) {

						$results =
							$this->model->addCustomFields(
								$this->table_registry_name,
								$customFieldName,
								Services::Registry()->get($this->table_registry_name, $customFieldName),
								$this->get('get_customfields'),
								$results
							);

					}
				}

				/** Retrieve Child Objects */
				if ((int)$this->get('get_item_children') == 1) {

					$children = Services::Registry()->get($this->table_registry_name, 'Children');

					if (count($children) > 0) {
						$results = $this->model->addItemChildren(
							$children,
							(int)$this->get('id', 0),
							$results
						);
					}
				}
			}

			$q[] = $results;
		}

		$this->query_results = $q;

		/** Schedule onAfterRead Event */
		if (count($this->triggers) > 0) {
			$this->onAfterReadEvent();
		}

		/** Return List */
		if ($query_object == 'list') {

			if (Services::Registry()->get('Configuration', 'debug_output_queries_query_results', 0) == 1) {
				$message = 'ReadController->getData Query Results <br /><br />';
				ob_start();
				echo '<pre>';
				var_dump($query_results);
				echo '</pre><br /><br />';
				$message .= ob_get_contents();
				ob_end_clean();
				Services::Debug()->set($message, LOG_OUTPUT_QUERIES, VERBOSE);
			}

			return $this->query_results;
		}

		/** Return Item */
		return $this->query_results[0];
	}

	/**
	 * Schedule onBeforeRead Event - could update model and parameter objects
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function onBeforeReadEvent()
	{
		if (count($this->triggers) == 0
			|| (int)$this->get('process_triggers') == 0
		) {
			return true;
		}

		/** Schedule onBeforeRead Event */
		$arguments = array(
			'table_registry_name' => $this->table_registry_name,
			'db' => $this->model->db,
			'query' => $this->model->query,
			'null_date' => $this->model->null_date,
			'now' => $this->model->now,
			'parameters' => $this->parameters,
			'model_name' => $this->get('model_name')
		);

		Services::Debug()->set('ReadController->onBeforeReadEvent '
				. $this->table_registry_name
				. ' Schedules onBeforeRead', LOG_OUTPUT_TRIGGERS, VERBOSE
		);

		$arguments = Services::Event()->schedule('onBeforeRead', $arguments, $this->triggers);

		if ($arguments == false) {
			Services::Debug()->set('ReadController->onBeforeReadEvent '
					. $this->table_registry_name
					. ' failure ', LOG_OUTPUT_TRIGGERS
			);
			return false;
		}

		Services::Debug()->set('ReadController->onBeforeReadEvent '
				. $this->table_registry_name
				. ' successful ', LOG_OUTPUT_TRIGGERS, VERBOSE
		);

		/** Process results */
		$this->model->query = $arguments['query'];
		$this->parameters = $arguments['parameters'];

		return true;
	}

	/**
	 * Schedule onAfterRead Event - could update parameters and query_results objects
	 *
	 * @return bool
	 * @since   1.0
	 */
	protected function onAfterReadEvent()
	{
		/** Prepare input */
		if (count($this->triggers) == 0
			|| (int)$this->get('process_triggers') == 0
		) {
			return true;
		}

		/** Process each item, on at a time */
		$items = $this->query_results;
		$this->query_results = array();

		foreach ($items as $item) {

			$arguments = array(
				'table_registry_name' => $this->table_registry_name,
				'parameters' => $this->parameters,
				'data' => $item,
				'model_name' => $this->get('model_name')
			);

			Services::Debug()->set('ReadController->onAfterReadEvent '
					. $this->table_registry_name
					. ' Schedules onAfterRead', LOG_OUTPUT_TRIGGERS, VERBOSE
			);

			$arguments = Services::Event()->schedule('onAfterRead', $arguments, $this->triggers);

			if ($arguments == false) {
				Services::Debug()->set('ReadController->onAfterReadEvent '
						. $this->table_registry_name
						. ' failure ', LOG_OUTPUT_TRIGGERS
				);
				return false;
			}

			Services::Debug()->set('ReadController->onAfterReadEvent '
					. $this->table_registry_name
					. ' successful ', LOG_OUTPUT_TRIGGERS, VERBOSE
			);

			$this->query_results[] = $arguments['data'];
		}

		return true;
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
			'data' => $this->query_results,
			'model_name' => $this->get('model_name')
		);

//		Services::Debug()->set('ReadController->onBeforeViewRender Schedules onBeforeViewRender', LOG_OUTPUT_TRIGGERS);

		$arguments = Services::Event()->schedule('onBeforeViewRender', $arguments);
		if ($arguments == false) {
//			Services::Debug()->set('ReadController->onBeforeViewRender Schedules onBeforeViewRender', LOG_OUTPUT_TRIGGERS);
			return false;
		}

//		Services::Debug()->set('ReadController->onBeforeViewRender Schedules onBeforeViewRender', LOG_OUTPUT_TRIGGERS);

		$this->query_results = $arguments['data'];

		return true;
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

//		Services::Debug()->set('ReadController->onAfterViewRender Schedules onAfterViewRender', LOG_OUTPUT_TRIGGERS);

		$arguments = Services::Event()->schedule('onAfterViewRender', $arguments);
		if ($arguments == false) {
//			Services::Debug()->set('ReadController->onAfterViewRender Schedules onAfterViewRender', LOG_OUTPUT_TRIGGERS);
			return false;
		}

//		Services::Debug()->set('ReadController->onAfterViewRender Schedules onAfterViewRender', LOG_OUTPUT_TRIGGERS);

		$rendered_output = $arguments['rendered_output'];

		return $rendered_output;
	}
}
