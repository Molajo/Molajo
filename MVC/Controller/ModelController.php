<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\MVC\Controller;

use Molajo\Service\Services\Configuration\ConfigurationService;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Model Controller
 *
 * Interacts with the ReadModel using these indicators serialized in $this->parameters
 *
 * model_name
 *     Ex., Articles, used with custom fields to create registry with data, ex. ArticlesParameters
 *
 * table_name
 *     Ex., #__content, used in the physical database query
 *
 * primary_key
 *     Ex., id, used to indicate single item requests
 *
 * name_key
 *     Ex., title or username, used to retrieve single item by unique value, not primary key
 *
 * primary_prefix
 *     Ex. "a", used in query development
 *
 * Processing Indicators:
 *
 * get_customfields
 *     0: do not retrieve custom fields
 *     1: retrieve fields
 *     2: retrieve and return as "normal" columns
 *
 * get_item_children
 *     0: no
 *     1: yes - executes a new read for additional data, query results return as column
 *
 * use_special_joins
 *     0: no
 *     1: yes - adds joins defined by model
 *
 * check_view_level_access
 *     0: no
 *     1: yes - adds joins to catalog and primary table, verifies view access
 *
 * process_triggers
 *     0: no
 *     1: yes - list of specific database triggers for this data source
 *
 * db
 *     typically 'JDatabase', but can be other data sources, like Messages, Registry, and Assets
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class ModelController extends Controller
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new ModelController();
		}

		return self::$instance;
	}

	/**
	 * Prepares data needed for the model using an XML table definition
	 *
	 * @param string $model_name
	 * @param string $model_type
	 *
	 * @return object
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function connect($model_type = 'Table', $model_name = null)
	{
//echo '<br />In connect: Type: ' . $model_type . ' $name: ' . $model_name. '<br />';

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

		} else {

			$table_registry_name = ucfirst(strtolower($model_type)) . ucfirst(strtolower($model_name));

			if (Services::Registry()->exists($table_registry_name) == true) {
				$this->table_registry_name = $table_registry_name;

			} else {
				$this->table_registry_name = ConfigurationService::getFile($model_type, $model_name);
				if ($this->table_registry_name == false) {
					echo '<br />ModelController: Table registry: ' . $this->table_registry_name . ' could not be defined. <br />';
					return false;
				}
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
		}

		/* 2. Instantiate Model Class */
		$modelClass = 'Molajo\\MVC\\Model\\ReadModel';

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

		if (Services::Registry()->get($this->table_registry_name, 'data_source', 'JDatabase') == 'JDatabase') {
		} else {
			$model_parameter = null;
			if ($this->get('model_parameter') == '') {
			} else {
				$model_parameter = $this->get('model_parameter');
			}
			/**
			echo 'DBO '.$dbo.'<br />';
			echo $query_object.'<br />';
			echo $model_parameter.'<br />';
			 */
			$this->query_results = $this->model->$query_object($model_parameter);
			return $this->query_results; //must directly return for non-DisplayController calls
		}

		/** Only JDatabase queries follow */
		if (in_array($query_object, array('result', 'item', 'list', 'distinct'))) {
		} else {
			$query_object = 'list';
		}

		/** Retrieve list of potential $triggers for this model (result type does not use events) */
		$triggers = $this->getTriggerList($query_object);

		/** Base query */
		if ($query_object == 'item') {
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
		if (count($triggers) > 0) {
			$this->onBeforeReadEvent($triggers);
		}

		/** Executes Query */
		$this->model->getQueryResults(
			Services::Registry()->get($this->table_registry_name, 'Fields'),
			$query_object,
			$this->get('model_offset', 0),
			$this->get('model_count', 5)
		);

		/** Retrieve query results from Model */
		$query_results = $this->model->get('query_results');

		if (count($query_results) > 0) {

			/** Return result (single value) */
			if ($query_object == 'result' || $query_object == 'distinct') {
				return $query_results;
			}

			/** No results */
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

					/** Process each field namespace  */
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

				/** Retrieve Child Objects  */
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
		if (count($triggers) > 0) {
			$this->onAfterReadEvent($triggers);
		}
		/**
		echo $query_object.'<br />';
		echo '<pre>';
		var_dump($query_results);
		echo '</pre>';
		 */
		/** Return List */
		if ($query_object == 'list') {
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
	 * @return array
	 * @since   1.0
	 */
	protected function getTriggerList($query_object)
	{
		if ($query_object == 'result') {
			return array();
		}

		if ((int)$this->get('process_triggers') == 1) {

			$triggers = Services::Registry()->get($this->table_registry_name, 'triggers', array());

			if (is_array($triggers)) {
			} else {
				if ($triggers == '' || $triggers == false || $triggers == null) {
					$triggers = array();
				} else {
					$temp = $triggers;
					$triggers = array();
					$triggers[] = $temp;
				}
			}

		} else {
			$triggers = array();
		}

		return $triggers;
	}

	/**
	 * Schedule onBeforeRead Event - could update model and parameter objects
	 *
	 * @param array $triggers
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function onBeforeReadEvent($triggers = array())
	{
		if (count($triggers) == 0
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

		$arguments = Services::Event()->schedule('onBeforeRead', $arguments, $triggers);
		if ($arguments == false) {
			return false;
		}

		/** Process results */
		$this->parameters = $arguments['parameters'];
		$this->model->query = $arguments['query'];

		return true;
	}

	/**
	 * Schedule onAfterRead Event - could update parameters and query_results objects
	 *
	 * @param array $triggers
	 *
	 * @return bool
	 * @since   1.0
	 */
	protected function onAfterReadEvent($triggers = array())
	{
		/** Prepare input */
		if (count($triggers) == 0
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
				'query_results' => $item,
				'model_name' => $this->get('model_name')
			);

			$arguments = Services::Event()->schedule('onAfterRead', $arguments, $triggers);

			if ($arguments == false) {
				return false;
			}

			$this->query_results[] = $arguments['query_results'];
		}

		return true;
	}
}
