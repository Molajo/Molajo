<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Model
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class ModelService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Model
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $model;

	/**
	 * Module Name
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $model_name;

	/**
	 * Table Name
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $table_name;

	/**
	 * Table XML
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $table_xml;

	/**
	 * Primary Key
	 *
	 * @var    integer
	 * @since  1.0
	 */
	public $primary_key;

	/**
	 * DB Driver
	 *
	 * @var    integer
	 * @since  1.0
	 */
	public $dbDriver;

	/**
	 * Valid DB Options
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $db_options = array(
		'JDatabase',
		'Text'
	);

	/**
	 * Default DB
	 *
	 * @var string
	 */
	protected $default_dbDriver = 'JDatabase';

	/**
	 * Valid Query Object values
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $query_objects = array(
		'load',
		'loadResult',
		'loadResultArray',
		'loadRow',
		'loadAssoc',
		'loadObject',
		'loadRowList',
		'loadAssocList',
		'loadObjectList'
	);

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
			self::$instance = new ModelService();
		}
		return self::$instance;
	}

	/**
	 * Prepares data needed for the model
	 *
	 * Single-table queries - retrieve Table Definitions, create a model instance, and sets model properties
	 *     examples include User, Site Application, and Authorisation queries
	 *
	 * More complex queries
	 *
	 * @param  string  $table
	 *
	 * @return void
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function connect($table = null)
	{

		/** Specific table model interaction - or - complex data query  */
		if ($table === null)  {
		}  else {
			$this->setModelTable($table);
			$this->dbDriver = $this->default_dbDriver;
		}
		$dbo = $this->dbDriver;

		/* 2. Instantiate Model Class */
		$modelClass = 'Molajo\\MVC\\Model\\EntryModel';

		try {
			$this->model = new $modelClass ();
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
	 * @return  mixed
	 * @throws  \RuntimeException
	 */
	protected function setModelTable($table)
	{

		$this->table_xml = Services::Registry()->loadFile($table, 'Table');

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

		$this->dbDriver = (string)$this->table_xml['db'];
		if ($this->dbDriver === '') {
			$this->dbDriver = $this->default_dbDriver;
		}
		$dbo = $this->dbDriver;

		return;
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
	public function execute($query_object = 'loadObjectList')
	{
		if (in_array($query_object, $this->query_objects)) {
		} else {
			$query_object = 'loadObjectList';
		}

		try {
			$results = $this->model->$query_object();
		}

		catch (\Exception $e) {

			throw new \RuntimeException('Model query failed for ' .$query_object . ' Error: ' . $e->getMessage());
		}

		return $results;
	}
}
