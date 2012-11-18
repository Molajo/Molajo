<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\MVC\Controller;

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
	 * Public as it is passed into events
	 *
	 * @var    array
	 * @since  1.0
	 */
	public $parameters = array();

	/**
	 * Model Instance
	 *
	 * Public as it is passed into events
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $model;

	/**
	 * Registry containing Table Configuration from XML
	 *
	 * Public as it is passed into events
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $model_registry;

	/**
	 * Set of rows returned from a query
	 *
	 * @var    array()
	 * @since  1.0
	 */
	protected $query_results = array();

	/**
	 * Single set of $query_results
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $row;

	/**
	 * Used to build Create, Update, Delete data structures
	 *
	 * Public as it is passed into plugined events
	 *
	 * @var    array
	 * @since  1.0
	 */
	public $data = array();

	/**
	 * Plugins specified in the table registry for the model
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $plugins = array();

	/**
	 * Pagination: Total of rows
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $pagination_total;

	/**
	 * Pagination: Model offset
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $model_offset;

	/**
	 * Pagination: Model count
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $model_count;

	/**
	 * Pagination: Use or do not use
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $use_pagination;

	/**
	 * Get the current value (or default) of the specified Model property
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 *
	 * @return  mixed
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
	 * @param   string $key
	 * @param   mixed  $value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function set($key, $value = null)
	{
		$this->parameters[$key] = $value;

		return $this;
	}

	/**
	 * Prepares data needed for the model using the model registry
	 *
	 * @param   string  $model_type
	 * @param   null    $model_name
	 * @param   string  $model_class
	 *
	 * @return  bool
	 * @since   1.0
	 *
	 * @throws \RuntimeException
	 */
	public function getModelRegistry($model_type = 'Datasource', $model_name = null, $model_class = 'ReadModel')
	{
		$model_registry = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
		$profiler_message = '';

		if (Services::Registry()->exists($model_registry) === true) {
			$this->model_registry = $model_registry;
			$profiler_message = ' Registry ' . $this->model_registry . ' retrieved from Registry. <br />';

		} else {

			$cached_output = Services::Cache()->get('Model', $model_registry);

			if ($cached_output === false) {
				$this->model_registry = ConfigurationService::getModel($model_type, $model_name);
				$cache_it = Services::Registry()->getArray($model_registry, false);
				Services::Cache()->set('Model', $model_registry, $cache_it);
				$profiler_message = ' Registry ' . $this->model_registry . ' processed by ConfigurationService';

			} else {
				$this->model_registry = $model_registry;
				Services::Registry()->createRegistry($model_registry);
				Services::Registry()->loadArray($model_registry, $cached_output);
				$profiler_message = ' Registry ' . $this->model_registry . ' loaded from Cache. ';
			}
		}

            echo $profiler_message . '<br />';

		/** Serialize Options */
		$this->set('model_type', $model_type);

        echo 'Before ' . $model_type . ' ' . $model_name .'<br />';
		$this->set('model_name',
			Services::Registry()->get($this->model_registry, 'model_name', ''));
        echo 'After ' . $this->get('model_type') . ' ' . $this->get('model_name') .'<br />';

		$this->set('table_name',
			Services::Registry()->get($this->model_registry, 'table', '#__content'));
		$this->set('primary_key',
			Services::Registry()->get($this->model_registry, 'primary_key', 'id'));
		$this->set('name_key',
			Services::Registry()->get($this->model_registry, 'name_key', 'title'));
		$this->set('primary_prefix',
			Services::Registry()->get($this->model_registry, 'primary_prefix', 'a'));
		$this->set('get_customfields',
			Services::Registry()->get($this->model_registry, 'get_customfields', 0));
		$this->set('get_item_children',
			Services::Registry()->get($this->model_registry, 'get_item_children', 0));
		$this->set('use_special_joins',
			Services::Registry()->get($this->model_registry, 'use_special_joins', 0));
		$this->set('check_view_level_access',
			Services::Registry()->get($this->model_registry, 'check_view_level_access', 0));
		$this->set('process_plugins',
			Services::Registry()->get($this->model_registry, 'process_plugins', 0));
		$this->set('process_template_plugins', 0);
		$this->set('criteria_catalog_type_id',
			Services::Registry()->get($this->model_registry, 'catalog_type_id', 0));
		$this->set('criteria_extension_instance_id',
			Services::Registry()->get($this->model_registry, 'extension_instance_id', 0));
		$this->set('criteria_published_status',
			Services::Registry()->get($this->model_registry, 'published_status', 0));
		$this->set('use_pagination',
			Services::Registry()->get($this->model_registry, 'use_pagination', 1));
		$this->set('data_object',
			Services::Registry()->get($this->model_registry, 'data_object', 'Database'));
		$this->set('registry_entry',
			Services::Registry()->get($this->model_registry, 'registry_entry', ''));
		$this->set('model_offset',
			Services::Registry()->get($this->model_registry, 'model_offset', 0));
		$this->set('use_pagination',
			Services::Registry()->get($this->model_registry, 'use_pagination', 0));
		$this->set('model_count',
			Services::Registry()->get($this->model_registry, 'model_count', 10));

		if (Services::Registry()->get('Configuration', 'profiler_output_queries_table_registry') == 0) {
		} else {
			ob_start();
			Services::Registry()->get($this->model_registry, '*');
			$profiler_message .= ob_get_contents();
			ob_end_clean();
		}

		Services::Profiler()->set($profiler_message, LOG_OUTPUT_QUERIES, VERBOSE);

		/* 2. Instantiate Model Class */
		$modelClass = 'Molajo\\MVC\\Model\\' . $model_class;

		try {
			$this->model = new $modelClass();

		} catch (\Exception $e) {
			throw new \RuntimeException('Model entry failed. Error: ' . $e->getMessage());
		}

		return $this;
	}

    /**
     *  Connects to the Data Object, be it a Database or a Registry or Assets, etc.
     *
     *  @return  void
     *  @since   1.0
     */
    public function setDataobject()
    {
        $data_object = $this->model->get('data_object');

        if ($data_object === null || $data_object === false) {
            $data_object = 'Database';
            $this->model->set('data_object', $data_object);
        }

        $this->model->set('db', Services::$data_object()->get('db'));

        $this->model->set('query', Services::$data_object()->getQuery());

        $this->model->set('null_date', Services::$data_object()->get('db')->getNullDate());

        $this->model->set('model_registry', $this->model_registry);

//todo create an event for these
        if ($data_object == 'Database') {
            $dateClass = 'JPlatform\\date\\JDate';
            $dateFromJDate = new $dateClass('now');
            $now = $dateFromJDate->toSql(false, Services::$data_object()->get('db'));
            $this->model->set('now', $now);
        }
    }

	/**
	 * Method to execute a model method and returns results
	 *
	 * @param string $query_object - result, item, list, distinct (for listbox)
	 *
	 * @return mixed Depends on QueryObject selected
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	public function getData($query_object = 'list')
	{

        $data_object = Services::Registry()->get($this->model_registry, 'data_object');
        if ($data_object === false || $data_object === null) {
            echo 'Data Object for Model Registry: ' . $this->model_registry . ' could not be loaded. <br />';
            //throw error
            die;
        }

        if ($this->model->get('db') === null) {
            $this->setDataobject();
        }

		$query_object = strtolower($query_object);

		$this->pagination_total = 0;
		$this->model_offset = $this->get('model_offset');
		$this->model_count = $this->get('model_count');
		$this->use_pagination = $this->get('use_pagination');

        if (in_array($query_object, array('result', 'item', 'list', 'distinct'))) {
        } else {
            $query_object = 'list';
        }

        if ($data_object == 'Database') {
            $this->prepareQuery($query_object);
        }

		$this->getPluginList($query_object);

		$profiler_message =
			' <br />Model Type: ' . $this->get('model_type')
				. ' <br />Model Name: ' . $this->get('model_name')
				. ' <br />Model Query Object: ' . $this->get('model_query_object')
				. ' <br />Process Plugins: ' . (int)$this->get('process_plugins') . '<br /><br />';
echo $profiler_message . ' <br />';

		if (count($this->plugins) > 0) {
			$this->onBeforeReadEvent();
		}

		if ($data_object == 'Database') {
			$this->runQuery($query_object);

		} else {

			if (strtolower($this->get('model_name')) == 'dummy') {
				$this->query_results = array();

			} else {
                $this->query_results = $this->db->runQuery(
                    $this->get('model_type'),
                    $this->get('model_name'),
                    $query_object
                );
			}
		}

		if (count($this->plugins) > 0) {
			$this->onAfterReadEvent(
				$this->pagination_total,
				$this->model_offset,
				$this->model_count
			);
		}

		if ($data_object == 'Database') {
		} else {
			return $this->query_results;
		}

		if ($query_object == 'result' || $query_object == 'distinct') {
			return $this->query_results;
		}

		if ($query_object == 'list') {

			if (Services::Registry()->get('Configuration', 'profiler_output_queries_query_results', 0) == 1) {
				$message = 'DisplayController->getData Query Results <br /><br />';

				ob_start();
				echo '<pre>';
				var_dump($this->query_results);
				echo '</pre><br /><br />';

				$message .= ob_get_contents();
				ob_end_clean();

				Services::Profiler()->set($message, LOG_OUTPUT_QUERIES);
			}

			return $this->query_results;
		}

		if (count($this->query_results) === 0 || $this->query_results === false) {
			return array();
		}
		if (is_array($this->query_results)) {
			return $this->query_results[0];
		}

		return $this->query_results;
	}

	/**
	 * Get the list of potential plugins identified with this model (used to filter registered plugins)
	 *
	 * @param $query_object
	 *
	 * @return void
	 * @since   1.0
	 */
	protected function getPluginList($query_object)
	{
		$this->plugins = array();

		if (defined('APPLICATION_ID')) {
		} else {
			return;
		}

		if ($query_object == 'result') {
			return;
		}

		/** Model Plugins */
		$dataSourcePlugins = array();
		if ((int)$this->get('process_plugins') == 1) {

			$dataSourcePlugins = Services::Registry()->get($this->model_registry, 'plugins', array());

			if (is_array($dataSourcePlugins)) {
			} else {
				$dataSourcePlugins = array();
			}
		}

		/** Template Plugins */
		$templatePlugins = array();
		if ((int)$this->get('process_template_plugins') == 1) {

			if ($this->get('template_view_model_registry') == $this->model_registry) {
				$temp = array();

			} else {
				$templatePlugins = Services::Registry()->get(
					$this->get('template_view_model_registry'), 'plugins', array()
				);

				if (is_array($templatePlugins)) {
				} else {
					$templatePlugins = array();
				}
			}
		}

		/** Merge */
		$temp = array_merge($dataSourcePlugins, $templatePlugins);
		if (is_array($temp)) {
		} else {
			$temp = array();
		}

		/** Automatically Menuitem Type, Template Node and Application */
		$page_type = $this->get('catalog_page_type', '');
		if ($page_type == '') {
		} else {
			$temp[] = 'Pagetype' . strtolower($page_type);
		}

		$template = Services::Registry()->get('Parameters', 'template_view_path_node', '');
		if ($template == '') {
		} else {
			$temp[] = $template;
		}

		if ((int)$this->get('process_plugins') == 0
			&& count($temp) == 0) {
			$this->plugins = array();
			return;
		}

		$temp[] = 'Application';

		$temp2 = array_unique($temp);

		foreach ($temp2 as $plugin) {
			if ((int)Services::Registry()->get('Plugins', $plugin . 'Plugin') > 0) {
				$this->plugins[] = $plugin;
			}
		}

		return;
	}

	/**
	 * Prepare query object for standard dbo queries
	 *
	 * @param  string $query_object
     *
	 * @return  bool
     * @since   1.0
	 */
	protected function prepareQuery($query_object = 'list')
	{
		if ($query_object == 'item' || $query_object == 'result') {
			$id_key_value = (int)$this->get('id', 0);
			$name_key_value = (string)$this->get('name_key_value', '');

		} else {
			$id_key_value = 0;
			$name_key_value = '';
		}

		$this->model->setBaseQuery(
			Services::Registry()->get($this->model_registry, 'Fields'),
			$this->get('table_name'),
			$this->get('primary_prefix'),
			$this->get('primary_key'),
			$id_key_value,
			$this->get('name_key'),
			$name_key_value,
			$query_object,
			Services::Registry()->get($this->model_registry, 'Criteria')
		);

		if ((int)$this->get('check_view_level_access') == 1) {
			$this->model->addACLCheck(
				$this->get('primary_prefix'),
				$this->get('primary_key'),
				$query_object
			);
		}

		if ((int)$this->get('use_special_joins') == 1) {
			$joins = Services::Registry()->get($this->model_registry, 'Joins');
			if (count($joins) > 0) {
				$this->model->useSpecialJoins(
					$joins,
					$this->get('primary_prefix'),
					$query_object
				);
			}
		}

		$this->model->setModelCriteria(
			$this->get('criteria_catalog_type_id'),
			$this->get('criteria_extension_instance_id'),
			$this->get('primary_prefix')
		);

		return;
	}

	/**
	 * Execute data retrieval query for standard requests
	 *
	 * @param  string $query_object
	 * @return bool
	 */
	protected function runQuery($query_object = 'list')
	{
		$this->model_offset = $this->get('model_offset', 0);
		$this->model_count = $this->get('model_count', 10);
		$this->use_pagination = $this->get('use_pagination', 1);

		if ($this->model_offset == 0 && $this->model_count == 0) {
			if ($query_object == 'result') {
				$this->model_offset = 0;
				$this->model_count = 1;
				$this->use_pagination = 0;

			} elseif ($query_object == 'distinct') {
				$this->model_offset = 0;
				$this->model_count = 999999;
				$this->use_pagination = 0;

			} else {
				$this->model_offset = 0;
				$this->model_count = 10;
				$this->use_pagination = 1;
			}
		}

		$this->pagination_total = (int)$this->model->getQueryResults(
			$query_object, $this->model_offset, $this->model_count, $this->use_pagination);

		if (Services::Registry()->get('Configuration', 'profiler_output_queries_sql') == 1) {
			Services::Profiler()->set('DisplayController->getData SQL Query: <br /><br />'
				. $this->model->query->__toString(), LOG_OUTPUT_RENDERING);
		}

		/** Retrieve query results from Model */
		$query_results = $this->model->get('query_results');

//echo '<br /><br /><pre>';
//echo $this->model->query->__toString();
//echo '<br /><br />';
//var_dump($query_results);
//echo '</pre><br /><br />';

		/** Result */
		if ($query_object == 'result' || $query_object == 'distinct') {

			if (Services::Registry()->get('Configuration', 'profiler_output_queries_query_results') == 1) {
				$message = 'DisplayController->getData Query Result <br /><br />';
				ob_start();
				echo '<pre>';
				var_dump($query_results);
				echo '</pre><br /><br />';
				$message .= ob_get_contents();
				ob_end_clean();
				Services::Profiler()->set($message, LOG_OUTPUT_QUERIES);
			}

			$this->query_results = $query_results;

			return;
		}

		$this->query_results = $this->addCustomFields($query_results, $query_object);

		return;

	}

	/**
	 * Adds Custom Fields and Children to Query Results
	 *
	 * Called by Controller and Extension Helper (Extension Queries made once, then parameters built, as needed)
	 *
	 * @param $query_results
	 * @param $query_object
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function addCustomFields($query_results, $query_object, $external = 0)
	{
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

				$customFieldTypes = Services::Registry()->get($this->model_registry, 'CustomFieldGroups');

				if (count($customFieldTypes) == 0 || $customFieldTypes == null) {
				} else {

					/** Process each field namespace */
					foreach ($customFieldTypes as $customFieldName) {
						$results =
							$this->model->addCustomFields(
								$this->model_registry,
								$customFieldName,
								Services::Registry()->get($this->model_registry, $customFieldName),
								$this->get('get_customfields'),
								$results,
								$query_object
							);
					}
				}

				/** Retrieve Child Objects */
				if ((int)$this->get('get_item_children') == 1) {

					$children = Services::Registry()->get($this->model_registry, 'Children');

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

		/** Just hijacking this to build registry special fields for specific extension (from saved extension registry) */
		if ($external == 1) {
			if (is_array($q)) {
				return $q[0];
			}
			return $q;
		}

		return $q;
	}

	/**
	 * Schedule onBeforeRead Event - could update model and parameter objects
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	protected function onBeforeReadEvent()
	{
		if (count($this->plugins) == 0
			|| (int)$this->get('process_plugins') == 0
		) {
			return true;
		}

		/** Schedule onBeforeRead Event */
		$arguments = array(
			'model_registry' => $this->model_registry,
			'db' => $this->model->db,
			'query' => $this->model->query,
			'null_date' => $this->model->null_date,
			'now' => $this->model->now,
			'parameters' => $this->parameters,
			'model_name' => $this->get('model_name'),
			'model_type' => $this->get('model_type')
		);

		Services::Profiler()->set('DisplayController->onBeforeReadEvent '
				. $this->model_registry
				. ' Schedules onBeforeRead', LOG_OUTPUT_PLUGINS, VERBOSE
		);

		$arguments = Services::Event()->schedule('onBeforeRead', $arguments, $this->plugins);

		if ($arguments === false) {
			Services::Profiler()->set('DisplayController->onBeforeReadEvent '
					. $this->model_registry
					. ' failure ', LOG_OUTPUT_PLUGINS
			);

			return false;
		}

		Services::Profiler()->set('DisplayController->onBeforeReadEvent '
				. $this->model_registry
				. ' successful ', LOG_OUTPUT_PLUGINS, VERBOSE
		);

		/** Process results */
		if (isset($arguments['query'])) {
			$this->model->query = $arguments['query'];
		}
		if (isset($arguments['parameters'])) {
			$this->parameters = $arguments['parameters'];
		}

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
		if (count($this->plugins) == 0
			|| (int)$this->get('process_plugins') == 0
			|| count($this->query_results) == 0
		) {
			return true;
		}

		/** Process each item, one at a time */
		$items = $this->query_results;
		$this->query_results = array();

		$this->parameters['model_offset'] = $this->model_offset;
		$this->parameters['model_count'] = $this->model_count;
		$this->parameters['pagination_total'] = $this->pagination_total;

		$first = true;

		if (count($items) == 0 || $items === false || $items === null) {
		} else {
			foreach ($items as $item) {

				$arguments = array(
					'model_registry' => $this->model_registry,
					'parameters' => $this->parameters,
					'data' => $item,
					'model_name' => $this->get('model_name'),
					'model_type' => $this->get('model_type'),
					'first' => $first
				);

				Services::Profiler()->set(
					'DisplayController->onAfterReadEvent '
						. $this->model_registry
						. ' Schedules onAfterRead',
					LOG_OUTPUT_PLUGINS,
					VERBOSE
				);

				$arguments = Services::Event()->schedule('onAfterRead', $arguments, $this->plugins);

				if ($arguments === false) {
					Services::Profiler()->set(
						'DisplayController->onAfterRead '
							. $this->model_registry
							. ' failure ',
						LOG_OUTPUT_PLUGINS
					);

					return false;
				}

				Services::Profiler()->set(
					'DisplayController->onAfterReadEvent '
						. $this->model_registry
						. ' successful ',
					LOG_OUTPUT_PLUGINS,
					VERBOSE
				);

				$this->parameters = $arguments['parameters'];
				$this->query_results[] = $arguments['data'];
				$first = false;
			}
		}

		/** onAfterReadall - Passes the entire query_results through the plugin */
		$arguments = array(
			'model_registry' => $this->model_registry,
			'parameters' => $this->parameters,
			'data' => $this->query_results,
			'model_type' => $this->get('model_type'),
			'model_name' => $this->get('model_name')
		);

		Services::Profiler()->set(
			'DisplayController->onAfterReadEventAll '
				. $this->model_registry
				. ' Schedules onAfterReadall',
			LOG_OUTPUT_PLUGINS,
			VERBOSE
		);

		$arguments = Services::Event()->schedule('onAfterReadall', $arguments, $this->plugins);

		if ($arguments === false) {
			Services::Profiler()->set(
				'DisplayController->onAfterReadall '
					. $this->model_registry
					. ' failure ',
				LOG_OUTPUT_PLUGINS
			);

			return false;
		}

		Services::Profiler()->set(
			'DisplayController->onAfterReadEventAll '
				. $this->model_registry
				. ' successful ',
			LOG_OUTPUT_PLUGINS,
			VERBOSE
		);

		if (isset($arguments['parameters'])) {
			$this->parameters = $arguments['parameters'];
		} else {
			$this->parameters = array();
		}
		if (isset($arguments['data'])) {
			$this->query_results = $arguments['data'];
		} else {
			$this->query_results = array();
		}

		return true;
	}
}
