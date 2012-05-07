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
 * Entry
 *
 * As the name might suggest, the Entry Controller is the entry point for all Controller requests.
 * The class merely allows all processing to enter a common gateway and then flow
 * through the class structure to the intended method.
 *
 * There are two basic process flows to the Controller within the Molajo Application:
 *
 * 1. The first is directly related to processing the request and using the MVC
 *     architecture to either render output or execute the task action.
 *
 *   -> For rendering, the Parser and Includer gather data needed and execute
 *         the Controller task to activate the MVC.
 *
 *   -> For task actions, the Controller task is initiated in the Application class.
 *
 *  The Controller then interacts with the Model for data requests.
 *
 * 2. The second logic flow routes support queries originating in Service and Helper
 *  route through the Model Service class which essentially acts as a Controller
 *  to gather information and then invoke the Model, as needed.
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class EntryController extends DisplayController
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
			self::$instance = new EntryController();
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
	 * Prepares data needed for the model
	 *
	 * Single-table queries - retrieve Table Definitions, create a model instance,
	 * and sets model properties examples include User, Site Application, and
	 * Authorisation queries
	 *
	 * More complex queries
	 *
	 * @param  string  $table
	 *
	 * @return object
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function connect($table = null)
	{
		/** Specific table model interaction - or - complex data query  */
		if ($table === null) {
		} else {
			$this->setModelTable($table);
			$this->dbDriver = $this->default_dbDriver;
		}
		$dbo = $this->dbDriver;

		/* 2. Instantiate Model Class */
		$modelClass = 'Molajo\\MVC\\Model\\EntryModel';

		try {
			$this->model = new $modelClass();
		}
		catch (\Exception $e) {
			throw new \RuntimeException('Model entry failed. Error: ' . $e->getMessage());
		}

		/** 3. Set Model Properties */
		$this->model->set('model_name', $this->model_name);
		$this->model->set('table_name', $this->table_name);
		$this->model->set('table_xml', $this->table_xml);
		$this->model->set('primary_key', $this->primary_key);
		$this->model->set('primary_prefix', 'a');
		$this->model->set('get_special_fields', 1);
		$this->model->set('get_item_children', true);
		$this->model->set('use_special_joins', true);
		$this->model->set('add_acl_check', true);

		/** 4. Set DB Properties */
		$this->model->set('db', Services::$dbo()->get('db'));
		$this->model->set('query', Services::$dbo()->getQuery());
		$this->model->set('nullDate', Services::$dbo()->get('db')->getNullDate());

		$dateClass = 'Joomla\\date\\JDate';
		$dateFromJDate = new $dateClass('now');
		$now = $dateFromJDate->toSql(false, Services::$dbo()->get('db'));
		$this->model->set('now', $now);

		Services::$dbo()->getQuery()->clear();

		return $this;
	}

	/**
	 * Set model properties needed for specific table model interaction
	 *
	 * @param   $table
	 *
	 * @return  object
	 * @throws  \RuntimeException
	 */
	protected function setModelTable($table)
	{
		$this->table_xml = ConfigurationService::loadFile($table, 'Table');

		$this->model_name = (string)$this->table_xml['name'];
		if ($this->model_name == '') {
			throw new \RuntimeException('No model name for table: ' . $table);
		}

		$this->table_name = (string)$this->table_xml['table'];
		if ($this->table_name == '') {
			$this->table_name = '#__content';
		}

		$this->primary_key = (string)$this->table_xml['primary_key'];
		if ($this->primary_key === '') {
			$this->primary_key = 'id';
		}

		$this->dbDriver = (string)$this->table_xml['data_source'];
		if ($this->dbDriver === '') {
			$this->dbDriver = $this->default_dbDriver;
		}

		return $this;
	}

	/**
	 * Method to execute a model method which interacts with the data source
	 * and returns results
	 *
	 * @param  string  $query_object
	 *
	 * @return  mixed Depends on QueryObject selected
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function getData($query_object = 'loadObjectList')
	{
		if (in_array($query_object, $this->query_objects)) {
		} else {
			$query_object = 'loadObjectList';
		}

		try {
			return $this->model->$query_object();
		}

		catch (\Exception $e) {
			throw new \RuntimeException('Model query failed for ' . $query_object . ' Error: ' . $e->getMessage());
		}
	}
}
