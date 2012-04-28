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
	static $default_db = 'JDatabase';

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
	 * Retrieve Table Definitions, create a model instance, and set model properties
	 *
	 * @param  string  $table
	 *
	 * @return void
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function connect ($table = 'Content')
	{
		/** 1. Get definition file for table */
		$table_xml = Services::Registry()->loadFile($table, 'Table');

		$model_name = (string)$table_xml['name'];
		if ($model_name == '') {
			throw new \RuntimeException('No model name for table: ' . $table);
		}

		$table_name = (string)$table_xml['table'];
		if ($table_name == '') {
			$table_name = '#__content';
		}

		$primary_key = (string)$table_xml['primary_key'];
		if ($primary_key === '') {
			$primary_key = 'id';
		}

		$dbDriver = (string)$table_xml['db'];
		if ($dbDriver === '') {
			$dbDriver = $this->default_dbDriver;
		}

		/* 2. Instantiate Model Class */
		$modelClass = 'Molajo\\MVC\\Model\\TableModel';

		try {
			$this->model = new $modelClass ();
		}
		catch (\Exception $e) {
			throw new \RuntimeException('Model entry failed for ' . $table . 'Error: ' . $e->getMessage());
		}

		/** 3. Model Properties */
		$this->model->set('model_name', $model_name);
		$this->model->set('table', $table_name);
		$this->model->set('table_xml', $table_xml);
		$this->model->set('primary_key', $primary_key);
		$this->model->set('primary_prefix', 'a');

		$this->model->set('db', Services::$dbDriver()->get('db'));
		$this->model->set('query', Services::$dbDriver()->getQuery());
		$this->model->set('nullDate', Services::$dbDriver()->get('db')->getNullDate());
		$this->model->set('now', Services::$dbDriver()->get('db')->getDateFormat());
		$this->model->set('query_results', array());
		$this->model->set('pagination', array());

		Services::$dbDriver()->getQuery()->clear();

		return $this;

	}

	/**
	 * Method to execute a query and return results
	 *
	 * @param  string  $query_object
	 *
	 * @return  mixed Depends on QueryObject selected
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function execute ($query_object = 'loadObjectList')
	{
		if (in_array($query_object, $this->query_objects)) {
		} else {
			$query_object = 'loadObjectList';
		}

		try {
			$results = $this->model->$query_object();
		}

		catch (\Exception $e) {

			throw new \RuntimeException('Model query failed for '
				. $this->model->get('table') . 'Error: ' . $e->getMessage());
		}

		echo '<pre>';
//		var_dump($results);
		echo '</pre>';

		return $results;
	}
}
