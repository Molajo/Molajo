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
 * The class merely allows all model instantiation as a common gateway
 *
 * There are two basic process flows to the Model within the Molajo Application:
 *
 * 1. The first is directly related to processing the request and using the MVC
 *     architecture to either render output or execute the action action.
 *
 *   -> For rendering, the Parser and Includer gather data needed and execute
 *         the Controller action to activate the MVC.
 *
 *   -> For action actions, the Controller action is initiated in the Application class.
 *
 *  The Controller then interacts with the Model for data requests.
 *
 * 2. The second logic flow routes support queries originating in Service and Helper
 *  classes and pass through this Controller to invoke the Model, as needed.
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
	 * Constructor.
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		return parent::__construct();
	}

	/**
	 * Prepares data needed for the model using an XML table definition
	 *
	 * @param  string  $table
	 * @param  string  $type
	 *
	 * @return object
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function connect($table = '', $type = null)
	{
		if ($type == null) {
			$type = 'Table';
		}

echo 'In connect: ' . $table . ' type: ' . $type . '<br />';

		if ($table === '') {
			$this->dataSource = $this->default_data_source;

		} else {

			$table_registry_name = ucfirst(strtolower($table)) . ucfirst(strtolower($type));

			if (Services::Registry()->exists($table_registry_name) == true) {
				$this->table_registry_name = $table_registry_name;

			} else {
				$this->table_registry_name = ConfigurationService::getFile($table, $type);
			}
		}

		/* 2. Instantiate Model Class */
		$modelClass = 'Molajo\\MVC\\Model\\ReadModel';

		try {
			$this->model = new $modelClass();
		}
		catch (\Exception $e) {
			throw new \RuntimeException('Model entry failed. Error: ' . $e->getMessage());
		}

		/** 3. Model DB Properties (note: 'mock' DBO's are used for processing non-DB data, like Messages */
		$dbo = Services::Registry()->get($this->table_registry_name, 'data_source', $this->default_data_source);

		$this->model->set('db', Services::$dbo()->get('db'));
		$this->model->set('query', Services::$dbo()->getQuery());
		$this->model->set('nullDate', Services::$dbo()->get('db')->getNullDate());

		if ($dbo == 'JDatabase') {
			$dateClass = 'Joomla\\date\\JDate';
			$dateFromJDate = new $dateClass('now');
			$now = $dateFromJDate->toSql(false, Services::$dbo()->get('db'));
			$this->model->set('now', $now);
		}

// it's a new instance - this should not be needed=>Services::$dbo()->getQuery()->clear();

		return $this;
	}

	/**
	protected $query_objects = array(
	'result',
	'item',
	'list',
	'loadResultArray',
	'loadRow',
	'loadAssoc',
	'loadObject',
	'loadRowList',
	'loadAssocList',
	'loadObjectList',
	'getAssets',
	'getMessages',
	'getParameters',
	'none'
	);
	 */

	/**
	 * Method to execute a model method which interacts with the data source and returns results
	 *
	 * @param  string  $query_object - result, item, or list
	 *
	 * @return  mixed Depends on QueryObject selected
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function getData($query_object = 'list')
	{
		if (in_array($query_object, array('result', 'item', 'list'))) {
		} else {
			$query_object = 'list';
		}
echo $query_object.'<br />';

		/** Retrieve list of potential $triggers for this model (result type does not use events) */
		$triggers = $this->getTriggerList($query_object);

		/** Schedule onBeforeRead Event */
		if (count($triggers) > 0) {
			$this->onBeforeReadEvent($triggers);
		}

		/** Execute Read Query */
		/** clear previous db activity */
		//$this->model->query->clear();

		/** Base query */
		if ($query_object == 'item') {
			$id_key = (int)$this->get('id', 0);
			$name_key = (string)$this->get('name_key');

		} else {
			$id_key = 0;
			$name_key = '';
		}

		$this->model->setBaseQuery(
			Services::Registry()->get($this->table_registry_name, 'Fields'),
			Services::Registry()->get($this->table_registry_name, 'table_name'),
			Services::Registry()->get($this->table_registry_name, 'primary_prefix'),
			Services::Registry()->get($this->table_registry_name, 'primary_key'),
			$id_key,
			$name_key,
			Services::Registry()->get($this->table_registry_name, 'id_name')
		);

		/** Add ACL Checking */
		if ((int)Services::Registry()->get($this->table_registry_name, 'check_view_level_access', 0) == 1) {
			$this->model->addACLCheck(
				Services::Registry()->get($this->table_registry_name, 'primary_prefix'),
				Services::Registry()->get($this->table_registry_name, 'primary_key')
			);
		}

		/** Check Published */
		if ((int)Services::Registry()->get($this->table_registry_name, 'check_published', 0) == 1) {
			$this->model->addPublishedCheck();
		}

		/** Joins */
		if ((int)Services::Registry()->get($this->table_registry_name, 'use_special_joins', 0) == 1) {
			$joins = Services::Registry()->get($this->table_registry_name, 'Joins');
			if (count($joins) > 0) {
				$this->model->useSpecialJoins(
					$joins,
					Services::Registry()->get($this->table_registry_name, 'primary_prefix')
				);
			}
		}

		/** Execute Query */
		$this->model->getQueryResults(Services::Registry()->get($this->table_registry_name, 'Fields'));

		/** Return Query Results */
		$query_results = $this->model->get('query_results');
		$this->query_results = array();

		if (count($query_results) > 0) {
		} else {
			return;
		}

		foreach ($query_results as $results) {

			/** Load Special Fields */
			if (((int) Services::Registry()->get($this->table_registry_name, 'get_customfields', 0) == 0)
				|| ((int)Services::Registry()->get($this->table_registry_name, 'CustomFieldGroups') == 0)) {

			} else {

				$customFieldTypes = Services::Registry()->get($this->table_registry_name, 'CustomFieldGroups');

				if (count($customFieldTypes) == 0 || $customFieldTypes == null) {
				} else {

					/** Process each field namespace  */
					foreach ($customFieldTypes as $customFieldName) {

						$results =
							$this->model->addCustomFields(
								Services::Registry()->get($this->table_registry_name, 'model_name'),
								$customFieldName,
								Services::Registry()->get($this->table_registry_name, $customFieldName),
								Services::Registry()->get($this->table_registry_name, 'get_customfields'),
								$results
							);
					}
				}

				/** Retrieve Child Objects  */
				if ((int)Services::Registry()->get($this->table_registry_name, 'get_item_children') == 1) {

					$children = Services::Registry()->get($this->table_registry_name, 'Children');

					if (count($children) > 0) {
						$results = $this->model->addItemChildren(
								$children,
								(int)$this->get('id', 0),
								$results
						);
					}
				}
				$this->query_results[] = $results;
			}
		}

		echo '<pre>';
		var_dump($this->query_results);
		echo '</pre>';

		/** Schedule onAfterRead Event */
		if (count($triggers) > 0) {
			$this->onAfterReadEvent($triggers);
		}

		/** Return Results */
		if ($query_object == 'list') {;
			return $this->query_results;
		} else if ($query_object == 'item') {
			return $this->query_results[0];
		} else {
			return $result;
		}
	}

	/**
	 * Get the list of potential triggers identified with this model (used to filter registered triggers)
	 *
	 * @return  array
	 * @since   1.0
	 */
	protected function getTriggerList($query_object)
	{
		if ($query_object == 'result') {
			return array();
		}

		if ((int)Services::Registry()->get($this->table_registry_name, 'process_triggers') == 1) {

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
	 * @return  boolean
	 * @since   1.0
	 */
	protected function onBeforeReadEvent($triggers = array())
	{
		/** Prepare input */
		if (count($triggers) == 0) {
			return false;
		}

		/** Schedule onBeforeRead Event */
		$arguments = array(
			'table_registry_name' => $this->table_registry_name,
			'parameters' => $this->parameters,
			'model' => $this->model
		);

		$arguments = Services::Event()->schedule('onBeforeRead', $arguments, $triggers);
		if ($arguments == false) {
			return false;
		}

		/** Process results */
		$this->parameters = $arguments['parameters'];
		$this->model = $arguments['model'];

		return true;
	}

	/**
	 * Schedule onAfterRead Event - could update parameters and query_results objects
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function onAfterReadEvent($triggers = array())
	{
		if (count($triggers) == 0) {
			return false;
		}

		/** Process each item, on at a time */
		$items = $this->query_results;
		$this->query_results = array();

		foreach ($items as $item) {

			$arguments = array(
				'table_registry_name' => $this->table_registry_name,
				'parameters' => $this->parameters,
				'model' => $this->model,
				'query_results' => $item
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
