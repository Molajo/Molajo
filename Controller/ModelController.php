<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Controller;

use Molajo\Service\Services\Configuration\ConfigurationService;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Model Controller
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class ModelController extends Controller
{
	/**
	 * Prepares data needed for the model using an XML table definition
	 *
	 * @param string $model_type
	 * @param null $model_name
	 * @param string $model_class
	 *
	 * @return bool|ModelController
	 * @since  1.0
	 *
	 * @throws \RuntimeException
	 */
	public function connect($model_type = 'Table', $model_name = null, $model_class = 'ReadModel')
	{
		$debugMessage = 'ModelController->connect '
			. ' Type ' . $model_type
			. ' Name ' . $model_name
			. ' Class: ' . $model_class;

		if ($model_name == null) {
			$this->table_registry_name = null;

			$this->set('model_type', $model_type);
			$this->set('model_name', '');
			$this->set('table_name', '#__content');
			$this->set('primary_key', 'id');
			$this->set('name_key', 'title');
			$this->set('primary_prefix', 'a');
			$this->set('get_customfields', 0);
			$this->set('get_item_children', 0);
			$this->set('use_special_joins', 0);
			$this->set('check_view_level_access', 0);
			$this->set('process_triggers', 0);
			$this->get('model_offset', 0);
			$this->get('model_count', 5);

		} else {

			$table_registry_name = ucfirst(strtolower($model_type)) . ucfirst(strtolower($model_name));

			if (Services::Registry()->exists($table_registry_name) == true) {
				$this->table_registry_name = $table_registry_name;
				$debugMessage .= ' Table Registry ' . $this->table_registry_name . ' retrieved from Registry. <br />';

			} else {
				$this->table_registry_name = ConfigurationService::getFile($model_type, $model_name);

				if ($this->table_registry_name == false) {
					$debugMessage .= ' Table Registry ' . $this->table_registry_name . ' is not defined. <br />';
					Services::Debug()->set($debugMessage, LOG_OUTPUT_QUERIES, VERBOSE);
					return false;
				}

				$debugMessage .= ' Table Registry ' . $this->table_registry_name . ' processed by ConfigurationService::getFile. ';
			}

			/** Serialize Options */
			$this->set('model_type', $model_type);
			$this->set('model_name',
				Services::Registry()->get($this->table_registry_name, 'model_name', ''));
			$this->set('table_name',
				Services::Registry()->get($this->table_registry_name, 'table', '#__content'));
			$this->set('primary_key',
				Services::Registry()->get($this->table_registry_name, 'primary_key', 'id'));
			$this->set('name_key',
				Services::Registry()->get($this->table_registry_name, 'name_key', 'title'));
			$this->set('primary_prefix',
				Services::Registry()->get($this->table_registry_name, 'primary_prefix', 'a'));
			$this->set('get_customfields',
				Services::Registry()->get($this->table_registry_name, 'get_customfields', 0));
			$this->set('get_item_children',
				Services::Registry()->get($this->table_registry_name, 'get_item_children', 0));
			$this->set('use_special_joins',
				Services::Registry()->get($this->table_registry_name, 'use_special_joins', 0));
			$this->set('check_view_level_access',
				Services::Registry()->get($this->table_registry_name, 'check_view_level_access', 0));
			$this->set('process_triggers',
				Services::Registry()->get($this->table_registry_name, 'process_triggers', 0));
			$this->set('filter_catalog_type_id',
				Services::Registry()->get($this->table_registry_name, 'filter_catalog_type_id', 0));
			$this->set('filter_check_published_status',
				Services::Registry()->get($this->table_registry_name, 'filter_check_published_status', 0));
			$this->set('data_source',
				Services::Registry()->get($this->table_registry_name, 'data_source', 'JDatabase'));
			$this->get('model_offset', 0);
			$this->get('model_count', 5);
		}

		if (Services::Registry()->get('Configuration', 'debug_output_queries_table_registry') == 0) {
		} else {
			ob_start();
			Services::Registry()->get($this->table_registry_name, '*');
			$debugMessage .= ob_get_contents();
			ob_end_clean();
		}

		Services::Debug()->set($debugMessage, LOG_OUTPUT_QUERIES, VERBOSE);

		/* 2. Instantiate Model Class */
		$modelClass = 'Molajo\\Model\\' . $model_class;

		try {
			$this->model = new $modelClass();

		} catch (\Exception $e) {
			throw new \RuntimeException('Model entry failed. Error: ' . $e->getMessage());
		}

		/** 3. Model DB Properties (note: 'mock' DBO's are used for processing non-DB data, like Messages */
		$dbo = Services::Registry()->get($this->table_registry_name, 'data_source', 'JDatabase');

		if ($dbo == false) {
			echo 'DBO for Table Registry: ' . $this->table_registry_name . ' could not be loaded. <br />';
			return false;
		}

		$this->model->set('db', Services::$dbo()->get('db'));
		$this->model->set('query', Services::$dbo()->getQuery());
		$this->model->set('null_date', Services::$dbo()->get('db')->getNullDate());
		$this->model->set('table_registry_name', $this->table_registry_name);

		if ($dbo == 'JDatabase') {
			$dateClass = 'Joomla\\date\\JDate';
			$dateFromJDate = new $dateClass('now');
			$now = $dateFromJDate->toSql(false, Services::$dbo()->get('db'));
			$this->model->set('now', $now);
		}

		return $this;
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

			Services::Debug()->set('ModelController->getData DBO '
				. $dbo . 'Query_object: '
				. $query_object . ' Model Parameter: '
				. $model_parameter,
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
			Services::Debug()->set('ModelController->getData SQL Query: <br /><br />'
					. $this->model->query->__toString(),
				LOG_OUTPUT_RENDERING, VERBOSE);
		}

		/** Retrieve query results from Model */
		$query_results = $this->model->get('query_results');

		/** Return result (single value) */
		if ($query_object == 'result' || $query_object == 'distinct') {

			if (Services::Registry()->get('Configuration', 'debug_output_queries_query_results', 0) == 1) {
				$message = 'ModelController->getData Query Results <br /><br />';
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
				$message = 'ModelController->getData Query Results <br /><br />';
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
	 * Get the list of potential triggers identified with this model (used to filter registered triggers)
	 *
	 * @param $query_object
	 *
	 * @return  void
	 * @since   1.0
	 */
	protected function getTriggerList($query_object)
	{
		if ($query_object == 'result') {
			$this->triggers = array();
			return;
		}

		if ((int)$this->get('process_triggers') == 1) {

			$this->triggers = Services::Registry()->get($this->table_registry_name, 'triggers', array());

			if (is_array($this->triggers)) {
			} else {
				if ($this->triggers == '' || $this->triggers == false || $this->triggers == null) {
					$this->triggers = array();
				} else {
					$temp = $this->triggers;
					$this->triggers = array();
					$this->triggers[] = $temp;
				}
			}

		} else {
			$this->triggers = array();
		}

		return;
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

		Services::Debug()->set('ModelController->onBeforeReadEvent '
				. $this->table_registry_name
				. ' Schedules onBeforeRead', LOG_OUTPUT_TRIGGERS, VERBOSE
		);

		$arguments = Services::Event()->schedule('onBeforeRead', $arguments, $this->triggers);

		if ($arguments == false) {
			Services::Debug()->set('ModelController->onBeforeReadEvent '
					. $this->table_registry_name
					. ' failure ', LOG_OUTPUT_TRIGGERS
			);
			return false;
		}

		Services::Debug()->set('ModelController->onBeforeReadEvent '
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

			Services::Debug()->set('ModelController->onAfterReadEvent '
					. $this->table_registry_name
					. ' Schedules onAfterRead', LOG_OUTPUT_TRIGGERS, VERBOSE
			);

			$arguments = Services::Event()->schedule('onAfterRead', $arguments, $this->triggers);

			if ($arguments == false) {
				Services::Debug()->set('ModelController->onAfterReadEvent '
						. $this->table_registry_name
						. ' failure ', LOG_OUTPUT_TRIGGERS
				);
				return false;
			}

			Services::Debug()->set('ModelController->onAfterReadEvent '
					. $this->table_registry_name
					. ' successful ', LOG_OUTPUT_TRIGGERS, VERBOSE
			);

			$this->query_results[] = $arguments['data'];
		}

		return true;
	}
}
