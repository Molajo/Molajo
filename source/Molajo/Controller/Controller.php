<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Controller;

use Molajo\Service\Services\Configuration\ConfigurationService;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Controller
 *
 * @package      Molajo
 * @subpackage   Controller
 * @since        1.0
 */
class Controller
{

	/**
	 * User object, custom fields and parameters
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $user;

	/**
	 * Stores various extension-specific key/value pairs
	 *
	 * Public as it is passed into triggered events
	 *
	 * @var    array
	 * @since  1.0
	 */
	public $parameters = array();

	/**
	 * Model Instance
	 *
	 * Public as it is passed into triggered events
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $model;

	/**
	 * Registry containing Table Configuration from XML
	 *
	 * Public as it is passed into triggered events
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $table_registry_name;

	/**
	 * Set of rows returned from a query
	 *
	 * Public as it is passed into triggered events
	 *
	 * @var    array()
	 * @since  1.0
	 */
	protected $query_results = array();

	/**
	 * Single item from the $query_results
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $row;

	/**
	 * Public as it is passed into triggered events
	 *
	 * @var    array
	 * @since  1.0
	 */
	public $data = array();

	/**
	 * Triggers specified in the table registry for the model
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $triggers = array();

	/**
	 * Get the current value (or default) of the specified Model property
	 *
	 * @param string $key     Property
	 * @param mixed  $default Value
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function get($key, $default = null)
	{
		if (isset($this->parameters[$key])) {
			return $this->parameters[$key];
		}

		$this->parameters[$key] = $default;

		return $this->parameters[$key];
	}

	/**
	 * Set the value of a Model property
	 *
	 * @param string $key   Property
	 * @param mixed  $value Value
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function set($key, $value = null)
	{
		$this->parameters[$key] = $value;

		return $this;
	}

	/**
	 * Prepares data needed for the model using an XML table definition
	 *
	 * @param  string $model_type
	 * @param  null   $model_name
	 * @param  string $model_class
	 *
	 * @return bool
	 * @since  1.0
	 *
	 * @throws \RuntimeException
	 */
	public function connect($model_type = 'Table', $model_name = null, $model_class = 'ReadModel')
	{
		$debugMessage = 'ReadController->connect '
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
					Services::Profiler()->set($debugMessage, LOG_OUTPUT_QUERIES, VERBOSE);
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

		Services::Profiler()->set($debugMessage, LOG_OUTPUT_QUERIES, VERBOSE);

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

}
