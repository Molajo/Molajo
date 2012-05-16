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
	public function connect($table = '', $type = null)
	{
		if ($type == null) {
			$type = 'Table';
		}
		//echo 'connect '.$table.'<br />';

		/** Specific table model interaction - or - complex data query  */
		if ($table === '') {
			$this->dataSource = $this->default_dataSource;
		} else {
			$this->table_registry_name = ConfigurationService::getFile($table, $type);

			//echo '<pre>';
			//var_dump(Services::Registry()->get($this->table_registry_name));
			//echo '</pre>';

		}

		/* 2. Instantiate Model Class */
		$modelClass = 'Molajo\\MVC\\Model\\EntryModel';

		try {
			$this->model = new $modelClass();
		}
		catch (\Exception $e) {
			throw new \RuntimeException('Model entry failed. Error: ' . $e->getMessage());
		}

		/** Set Model Properties */
		$this->model->set('table_registry_name', $this->table_registry_name);
		$this->model->set('model_name', Services::Registry()->get($this->table_registry_name, 'model_name'));
		$this->model->set('table_name', Services::Registry()->get($this->table_registry_name, 'table_name'));
		$this->model->set('primary_key', Services::Registry()->get($this->table_registry_name, 'primary_key'));
		$this->model->set('id', Services::Registry()->get($this->table_registry_name, 'id'));
		$this->model->set('primary_prefix', Services::Registry()->get($this->table_registry_name, 'primary_prefix'));
		$this->model->set('name_key', Services::Registry()->get($this->table_registry_name, 'name_key'));
		$this->model->set('id_name', Services::Registry()->get($this->table_registry_name, 'id_name'));

		/** 4. Set DB Properties */
		$dbo = Services::Registry()->get($this->table_registry_name, 'data_source');

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
	public function getData($query_object = 'loadObjectList', $view_requested = false)
	{
		//** Need to know if there is a view, or not */
		if (in_array($query_object, $this->query_objects)) {
		} else {
			$query_object = 'loadObjectList';
		}

		try {

			if ($view_requested == true) {
				$this->parameters['query_object'] = $query_object;
				return $this->display();

			} else {
				return $this->model->$query_object();
			}

		} catch (\Exception $e) {
			throw new \RuntimeException('Model query failed for ' . $query_object . ' Error: ' . $e->getMessage());
		}
	}
}
