<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

use Joomla\database\JDatabaseDriver;

use Molajo\Service\Services;

use Molajo\MVC\Model\TableModel;

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
	protected $model;

	/**
	 * Valid Query Object values
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $query_objects = array(
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
	 * Method to edit input, instantiate a model instance, set model properties,
	 * execute a query and return results
	 *
	 * @param  string  $table
	 * @param  string  $id
	 * @param  string  $query_object
	 * @param  string  $dbClass
	 *
	 * $query_object options:
	 *
	 * $item = loadObjectList
	 * foreach ($items as $item) {
	 *     echo $item->example;
	 * }
	 *
	 * @return  mixed Depends on QueryObject selected
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function connect (
		$table = 'Content', $dbClass = 'Database')
	{

		/**
		 * Step 1. Editing and data preparation to use Model
		 */

		/** Get definition file for table */
		$table_xml = Services::Registry()->loadFile($table, 'Table');

		/** Retrieve Model Name from Table Definition */
		$model_name = (string)$table_xml['name'];
		if ($model_name == '') {
			throw new \RuntimeException('No model name in XML: ' . $table);
		}

		/** Retrieve name of Table */
		$table_name = (string)$table_xml['table'];

		if ($table_name == '') {
			$table_name = '#__content';
		}

		/** Primary Key */
		$primary_key = (string)$table_xml['primary_key'];
		if ($primary_key === '') {
			$primary_key = 'id';
		}

		/* Model Class */
		$modelClass = 'MVC\\Model\\' . ucfirst(strtolower($table)) . 'Model';

		/* Query Object */
		if (in_array($query_object, $this->query_objects)) {
		} else {
			$query_object = 'loadObjectList';
		}

		/**
		 * Step 2. Instantiate the Model Class
		 */
		try {
			/** instantiate model */
			$this->model = new $modelClass ($table = null, $id = null);

			/* Get results */
			$items = $this->model->$query_object();
		}
		catch (\Exception $e) {
			throw new \RuntimeException('Model entry failed for ' . $table . 'Error: ' . $e->getMessage());
		}

		/**
		 * Step 3. Set Model Properties
		 */
		$this->model->set('model_name', $model_name);
		$this->model->set('table', $table);
		$this->model->set('table_xml', $table_xml);
		$this->model->set('primary_key', $primary_key);
		$this->model->set('id', (int) $id);
		$this->model->set('primary_prefix', 'a');

		$this->model->set('db', Services::$dbClass()->get('db'));
		$this->model->set('query', Services::$dbClass()->getQuery());
		$this->model->set('nullDate', Services::$dbClass()->getNullDate());
		$this->model->set('now', date("Y-m-d") . ' 00:00:00');
		$this->model->set('query_results', array());
		$this->model->set('pagination', array());

		return;

		/**
		if ((int) Application::Service()->get('DebugService', 0) == 0) {
		} else {
		Services::Debug()->set('Model Construct '.$name. ' '.$model_name);
		}
		 */
	}

	/**
	 * Method to execute a query and return results
	 *
	 * @param  string  $id
	 * @param  string  $query_object
	 *
	 * $query_object options:
	 *
	 * $item = loadObjectList
	 * foreach ($items as $item) {
	 *     echo $item->example;
	 * }
	 *
	 * @return  mixed Depends on QueryObject selected
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function query ($id = null, $query_object = 'loadObjectList')
	{
		try {
			/* Get results */
			$items = $this->model->$query_object();
		}
		catch (\Exception $e) {
			throw new \RuntimeException('Model query failed for ' . $table . 'Error: ' . $e->getMessage());
		}
		echo '<pre>';
		var_dump($items);
		echo '</pre>';
		die;
		//return $items;
	}
}
