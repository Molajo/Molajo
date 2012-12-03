<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
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
     * Model Registry Name
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry_name;

    /**
     * Model Instance - db, query connection, date defaults, etc.
     *
     * @var    object
     * @since  1.0
     */
    public $model;

    /**
     * Model Registry - data source/object fields and definitions
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = array();

    /**
     * Stores an array of key/value Parameters settings
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * Set of rows returned from a query
     *
     * @var    array
     * @since  1.0
     */
    protected $query_results = array();

    /**
     * Single set of $query_results - used in CUD operations
     *
     * @var    object
     * @since  1.0
     */
    protected $row;

    /**
     * Used to build Create, Update, Delete data structures - public
     *
     * @var    array
     * @since  1.0
     */
    protected $data = array();

    /**
     * Plugins specified in the table registry for the model registry
     *
     * @var    array
     * @since  1.0
     */
    protected $plugins = array();

    /**
     * Used to ensure all getData requests have first been processed by getDataobject
     *
     * @var    boolean
     * @since  1.0
     */
    protected $data_object_set;


    /**
     * Used in DisplayView to render output (file path for includes)
     *
     * @var    boolean
     * @since  1.0
     */
    protected $view_path;

    /**
     * Used in DisplayView to render output (URL for assets)
     *
     * @var    boolean
     * @since  1.0
     */
    protected $view_path_url;

    /**
     * Rendered Output
     *
     * @var    boolean
     * @since  1.0
     */
    protected $rendered_output;

    /**
     * List of Controller Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'model_registry_name',
        'model',
        'model_registry',
        'parameters',
        'query_results',
        'row',
        'data',
        'plugins',
        'rendered_output',
        'data_object_set',
        'view_path',
        'view_path_url'
    );

    /**
     * All properties are used in the controller and passed into the model and events
     *
     * Exceptions: data_object_set and model_registry_name. both of which proved a temporary
     *      state value between the getModelRegistry and setDataobject methods
     */
    public function __construct()
    {
        /** Temporary and Internal */
        $this->model_registry_name = null;
        $this->data_object_set = 0;

        /** Shared with Model and Passed into Events/Plugins */
        $this->parameters = array();
        $this->model = array();
        $this->model_registry = array();
        $this->query_results = array();
        $this->row = null;
        $this->data = array();
        $this->get('plugins', array());

        return $this;
    }

    /**
     * Get the current value (or default) of the specified Model property
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key, $default = null, $property = '')
    {
//        echo 'GET $key ' . $key . ' ' . ' Property ' . $property . '<br />';

        if (in_array($key, $this->property_array) && $property == '') {
            $value = $this->$key;
            return $value;
        }

        if ($property == 'parameters') {
            if (isset($this->parameters[$key])) {
                return $this->parameters[$key];
            }
            $this->parameters[$key] = $default;
            return $this->parameters[$key];
        }

        if ($property == 'model_registry') {
            if (isset($this->model_registry[$key])) {
                return $this->model_registry[$key];
            }
            $this->model_registry[$key] = $default;
            return $this->model_registry[$key];
        }

        if ($property == 'model') {
            return $this->model->$key;
        }

        if (isset($this->$key)) {
            return $this->$key;
        }

        throw new \OutOfRangeException('Controller: ' .
            ' attempting to get value for unknown key: ' . $key);
    }

    /**
     * Set the value of a Model property
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null, $property = '')
    {
//echo 'SET $key ' . $key . ' ' . ' Property ' . $property . '<br />';

        if (in_array($key, $this->property_array) && $property == '') {
            $this->$key = $value;
            return $this->$key;
        }

        if ($property == 'parameters') {
            $this->parameters[$key] = $value;
            return $this->parameters[$key];
        }

        if ($property == 'model_registry') {
            $this->model_registry[$key] = $value;
            return $this->model_registry[$key];
        }

        if ($property == 'model') {
            $this->model->$key = $value;
            return $this->model->$key;
        }

        throw new \OutOfRangeException('Controller: '
            . ' is attempting to set value for unknown property: ' . $key);
    }

    /**
     * Prepares data needed for the model using the model registry
     *
     * @param   string  $model_type
     * @param   null    $model_name
     *
     * @return  bool
     * @since   1.0
     *
     * @throws  \RuntimeException
     */
    public function getModelRegistry($model_type = DATA_SOURCE_LITERAL, $model_name = null)
    {
        $this->set('data_object_set', 0);

        if ($model_type === null) {
            $model_type = DATA_SOURCE_LITERAL;
        }
        $model_type = ucfirst(strtolower($model_type));
        $this->set('model_type', $model_type, 'model_registry');

        $model_name = ucfirst(strtolower($model_name));
        $this->set('model_name', $model_name, 'model_registry');

        $this->set('model_registry_name', $model_name . $model_type, 'model_registry');
        $this->model_registry_name = $this->get('model_registry_name', '', 'model_registry');

        unset($model_name);
        unset($model_type);

        if (Services::Registry()->exists($this->model_registry_name) === true) {
            $profiler_message = ' Registry ' . $this->model_registry_name . ' retrieved from Registry.';

        } else {
            $cached_output = Services::Cache()->get('Model', $this->model_registry_name);

            if ($cached_output === false) {

                ConfigurationService::getModel(
                    $this->get('model_type', '', 'model_registry'),
                    $this->get('model_name', '', 'model_registry')
                );

                $cache_it = Services::Registry()->getArray($this->model_registry_name, false);
                Services::Cache()->set('Model', $this->model_registry_name, $cache_it);
                $profiler_message = ' Registry ' . $this->model_registry_name . ' processed by Configuration Service';

            } else {
                Services::Registry()->createRegistry($this->model_registry_name);
                Services::Registry()->loadArray($this->model_registry_name, $cached_output);
                $profiler_message = ' Registry ' . $this->model_registry_name . ' loaded from Cache. ';
            }
        }

        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_output_queries_table_registry') == 0) {
        } else {
            ob_start();
            Services::Registry()->get($this->model_registry_name, '*');
            $profiler_message .= ob_get_contents();
            ob_end_clean();
        }

        Services::Profiler()->set($profiler_message, PROFILER_QUERIES, VERBOSE);

        return $this;
    }

    /**
     * Connects to the Dataobject
     *
     *  getModelRegistry retrieves the Model Registry from the Configuration, Cache, or the Registry
     *
     * @return  void
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function setDataobject()
    {
        $this->set('data_object_set', 1);

        if ($this->get('model_registry_name') === null) {
            throw new \RuntimeException('Controller: Required value missing for $model_registry');
        }

        if (Services::Registry()->exists($this->get('model_registry_name')) === true) {
        } else {
            throw new \RuntimeException('Controller: Load $model_registry using getModelRegistry');
        }

        $load = Services::Registry()->get($this->get('model_registry_name'));

        $this->set('model_registry', array());

        if (count($load) > 0) {
            foreach ($load as $key => $value) {
                $this->set($key, $value, 'model_registry');
            }
        }

        $this->set('model_registry_name', $this->get('model_registry_name'), 'model_registry');

        if (isset($this->model_registry['data_object_data_object_type'])) {

        } else {
            $this->set('data_object_data_object_type', 'other', 'model_registry');
            $this->set('model_class', 'other', 'model_registry');
        }
        $data_object_type = $this->get('data_object_data_object_type', null, 'model_registry');

        $this->set('data_object_type', $data_object_type, 'model_registry');

        if (strtolower($data_object_type) == strtolower(DATABASE_LITERAL)) {

            if ($this->get('model_class', null, 'model_registry') === null) {
                $this->set('model_class', 'ReadModel', 'model_registry');
            }

            $modelClass = MODEL_CLASS . $this->get('model_class', null, 'model_registry');

            try {
                $this->model = new $modelClass();

            } catch (\Exception $e) {
                throw new \Exception('Controller: Class ' . $modelClass . ' failed. Error: ' . $e->getMessage());
            }
        }

        if ($this->get('use_pagination', null, 'model_registry') === null) {
            $this->set('use_pagination', 1, 'model_registry');
        }
        if ($this->get('model_offset', null, 'model_registry') === null) {
            $this->set('model_offset', 0, 'model_registry');
        }
        if ($this->get('model_count', null, 'model_registry') === null) {
            $this->set('model_count', 15, 'model_registry');
        }
        if ($this->get('primary_prefix', null, 'model_registry') === null) {
            $this->set('primary_prefix', 'a', 'model_registry');
        }
        if ($this->get('use_pagination', null, 'model_registry') === null) {
            $this->set('use_pagination', 1, 'model_registry');
        }
        if ($this->get('template_view_model_registry', null, 'model_registry') === null) {
            $this->set('template_view_model_registry', 1, 'model_registry');
        }

        $this->onConnectDatabaseEvent();

        return;
    }

    /**
     * Method to execute model methods and returns results
     *
     * @param   string   $query_object - result, item, list, distinct
     *
     * @return  mixed    Depends on QueryObject selected
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function getData($query_object = QUERY_OBJECT_LIST)
    {
        if ($this->get('data_object_set') == 0) {
            $this->setDataobject();
        }

        $query_object = strtolower($query_object);

        if (in_array(
            $query_object,
            array(QUERY_OBJECT_RESULT, QUERY_OBJECT_ITEM, QUERY_OBJECT_LIST, QUERY_OBJECT_DISTINCT)
        )
        ) {
        } else {
            $query_object = QUERY_OBJECT_LIST;
        }

        $this->set('query_object', $query_object, 'model_registry');

        if ($this->get('data_object', DATABASE_LITERAL, 'model_registry') == DATABASE_LITERAL) {
            $this->prepareQuery($this->get('query_object', '', 'model_registry'));
        }

        $profiler_message =
            ' <br />Data Object: ' . $this->get('data_object', DATABASE_LITERAL, 'model_registry')
                . ' <br />Model Type: ' . $this->get('model_type', DATA_SOURCE_LITERAL, 'model_registry')
                . ' <br />Model Name: ' . $this->get('model_name', '', 'model_registry')
                . ' <br />Model Query Object: ' . $this->get('query_object', '', 'model_registry')
                . ' <br />Template View: ' . $this->get('template_view_path_node', '', 'parameters')
                . ' <br />Process Plugins: ' . (int)$this->get('process_plugins', 1, 'model_registry')
                . '<br /><br />';

        if ($this->get('data_object_type', DATABASE_LITERAL, 'model_registry') == DATABASE_LITERAL) {

            if (count($this->get('plugins', array())) > 0) {
                $this->onBeforeReadEvent();
            }

            $this->runQuery($this->get('query_object', '', 'model_registry'));

        } else {

            if (strtolower($this->get('model_name', '', 'model_registry')) == 'dummy') {
                $this->query_results = array();

            } else {
                $service_class = $this->get('data_object_service_class', DATABASE_LITERAL, 'model_registry');
                $service_class_query_method = $this->get(
                    'data_object_service_class_query_method',
                    '',
                    'model_registry'
                );

                if ($this->get('model_name', '', 'model_registry') == PRIMARY_LITERAL) {
                    $method_parameter = DATA_LITERAL;

                } elseif ($this->get('data_object_service_class_query_method_parameter', '', 'model_registry')
                    == 'TEMPLATE_LITERAL'
                ) {
                    $method_parameter = $this->get('template_view_path_node', '', 'parameters');

                } elseif ($this->get('data_object_service_class_query_method_parameter', '', 'model_registry')
                    == 'MODEL_LITERAL'
                ) {
                    $method_parameter = $this->get('model_name', '', 'model_registry');

                } else {
                    $method_parameter = $this->get(
                        'data_object_service_class_query_method_parameter',
                        '',
                        'model_registry'
                    );
                }

                if (count($this->get('plugins', array())) > 0) {
                    $this->onBeforeReadEvent();
                }

                $profiler_message .= 'Class: ' . $service_class
                    . ' Method ' . $service_class_query_method
                    . ' Model Name ' . $this->get('model_name', '', 'model_registry')
                    . ' Method parameter ' . $method_parameter
                    . ' Query Object ' . $this->get('query_object', '', 'model_registry');

//echo $profiler_message . '<br /><br />';

                $this->query_results = Services::$service_class()
                    ->$service_class_query_method(
                    $this->get('model_name', '', 'model_registry'),
                    $method_parameter,
                    $this->get('query_object', '', 'model_registry')
                );
            }
        }

        /** if (count($this->get('plugins', array())) > 0) {
        $this->onAfterReadEvent(
        $this->get('use_pagination', 1, 'model_registry'),
        $this->get('model_offset', 0, 'model_registry'),
        $this->get('model_count', 15, 'model_registry')
        );
        }
         */
        if ($this->get('data_object_type', DATABASE_LITERAL, 'model_registry') == DATABASE_LITERAL) {
        } else {
            return $this->query_results;
        }

        if ($this->get('query_object', '', 'model_registry') == QUERY_OBJECT_RESULT
            || $this->get('query_object', '', 'model_registry') == QUERY_OBJECT_DISTINCT
        ) {
            return $this->query_results;
        }

        if ($this->get('query_object', '', 'model_registry') == QUERY_OBJECT_LIST) {

            if (Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_output_queries_query_results', 0) == 1) {

                $profiler_message .= 'Controller: getData Query Results <br /><br />';

                ob_start();
                echo '<pre>';
                var_dump($this->query_results);
                echo '</pre><br /><br />';

                $profiler_message .= ob_get_contents();
                ob_end_clean();
                echo $profiler_message;

                Services::Profiler()->set($profiler_message, PROFILER_QUERIES, VERBOSE);
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
     * Get the list of potential plugins identified with this model registry
     *
     * @return  void
     * @since   1.0
     */
    protected function getPluginList()
    {
        $this->set('plugins', array());

        if (defined('DATABASE_SERVICE')) {
            return true;
        }

        if ($this->get('query_object', QUERY_OBJECT_RESULT, 'model_registry') == QUERY_OBJECT_RESULT) {
            return;
        }

        $modelPlugins = array();
        if ((int)$this->get('process_plugins', 1, 'model_registry') == 1) {

            $modelPlugins = $this->get('plugins', array(), 'model_registry');

            if (is_array($modelPlugins)) {
            } else {
                $modelPlugins = array();
            }
        }

        $templatePlugins = array();
        if ((int)$this->get('process_template_plugins', 1, 'model_registry')) {

            if ((int)$this->get('process_template_plugins', 1, 'model_registry') == 0) {
                $temp = array();

            } else {
                $templatePlugins = Services::Registry()->get(
                    $this->get('process_template_plugins', 1, 'model_registry'),
                    'plugins',
                    array()
                );

                if (is_array($templatePlugins)) {
                } else {
                    $templatePlugins = array();
                }
            }
        }

        $plugins = array_merge($modelPlugins, $templatePlugins);
        if (is_array($plugins)) {
        } else {
            $plugins = array();
        }

        $page_type = $this->get('criteria_catalog_page_type', '', 'parameters');
        if ($page_type == '') {
        } else {
            $plugins[] = 'Pagetype' . strtolower($page_type);
        }

        $template = $this->get('template_view_path_node', '', 'parameters');
        if ($template == '') {
        } else {
            $plugins[] = $template;
        }

        if ((int)$this->get('process_plugins', 1, 'model_registry') == 0 && count($plugins) == 0) {
            $this->get('plugins', array());
            return;
        }

        $plugins[] = 'Application';

        $this->set('plugins', $plugins);

        return;
    }

    /**
     * Prepare query object for standard dbo queries
     *
     * @return  bool
     * @since   1.0
     */
    protected function prepareQuery()
    {
        $this->model->setBaseQuery(
            $this->get(strtolower(FIELDS_LITERAL), array(), 'model_registry'),
            $this->get('table_name', null, 'model_registry'),
            $this->get('primary_prefix', 'a', 'model_registry'),
            $this->get('primary_key', 'id', 'model_registry'),
            $this->get('primary_key_value', 0, 'model_registry'),
            $this->get('name_key', null, 'model_registry'),
            $this->get('name_key_value', null, 'model_registry'),
            $this->get('query_object', '', 'model_registry'),
            $this->get('model_registry_name', null, 'model_registry')
        );


        if ((int)$this->get('check_view_level_access', 0, 'model_registry') == 1) {
            $this->model->checkPermissions(
                $this->get('primary_prefix', 'a', 'model_registry'),
                $this->get('primary_key', 'id', 'model_registry'),
                $this->get('query_object', '', 'model_registry')
            );
        }

        if ((int)$this->get('use_special_joins', 1, 'model_registry') == 1) {
            $joins = $this->get('joins', array(), 'model_registry');
            if (count($joins) > 0) {
                $this->model->useSpecialJoins(
                    $joins,
                    $this->get('primary_prefix', 'a', 'model_registry'),
                    $this->get('query_object', '', 'model_registry')
                );
            }
        }

        $this->model->setModelCriteria(
            $this->get('criteria_catalog_type_id', '', 'parameters'),
            $this->get('criteria_extension_instance_id', '', 'parameters'),
            $this->get('primary_prefix', 'a', 'model_registry')
        );

        return;
    }

    /**
     * Execute data retrieval query for standard requests
     *
     * @return  bool
     * @since   1.0
     */
    protected function runQuery()
    {
        $this->set(
            'pagination_total',
            (int)$this->model->getQueryResults(
                $this->get('query_object', '', 'model_registry'),
                $this->get('model_offset', 0, 'model_registry'),
                $this->get('model_count', 15, 'model_registry'),
                $this->get('use_pagination', 1, 'model_registry')
            ),
            'parameters'
        );

        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_output_queries_sql') == 1) {
            Services::Profiler()->set(
                'Controller runQuery: <br /><br />'
                    . $this->model->query->__toString(),
                PROFILER_RENDERING,
                VERBOSE
            );
        }

        /** Retrieve query results from Model */
        $query_results = $this->model->get('query_results');

        /**
        echo '<br /><br /><pre>';
        echo $this->model->query->__toString();
        echo '<br /><br />';
        var_dump($query_results);
        echo '</pre><br /><br />';
         */
        if ($this->get('query_object', '', 'model_registry') == QUERY_OBJECT_RESULT
            || $this->get('query_object', '', 'model_registry') == QUERY_OBJECT_DISTINCT
        ) {

            if (Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_output_queries_query_results') == 1) {
                $message = 'DisplayController->getData Query Result <br /><br />';
                ob_start();
                echo '<pre>';
                var_dump($query_results);
                echo '</pre><br /><br />';
                $message .= ob_get_contents();
                ob_end_clean();
                Services::Profiler()->set($message, PROFILER_QUERIES);
            }

            $this->query_results = $query_results;

            return;
        }

        $this->query_results =
            $this->addCustomFields(
                $query_results,
                $this->get('query_object', '', 'model_registry')
            );

        return;
    }

    /**
     * Adds Custom Fields and Children to Query Results
     *
     * @param   array  $query_results
     * @param   int    $external
     *
     * @return  bool
     * @since   1.0
     */
    public function addCustomFields($query_results, $external = 0)
    {
        $customFieldTypes = $this->get(strtolower(CUSTOMFIELDGROUPS_LITERAL), array(), 'model_registry');

        if (count($customFieldTypes) > 0) {
        } else {
            return $query_results;
        }

        $q = array();

        foreach ($query_results as $results) {

            if ((int)$this->get('get_customfields', 1, 'model_registry') == 0) {
            } else {

                $customFieldTypes = $this->get(strtolower(CUSTOMFIELDGROUPS_LITERAL), array(), 'model_registry');

                if (count($customFieldTypes) == 0 || $customFieldTypes == null) {
                } else {

                    foreach ($customFieldTypes as $customFieldName) {

                        $results =
                            $this->model->addCustomFields(
                                $this->get('model_registry_name', '', 'model_registry'),
                                $customFieldName,
                                $this->get($customFieldName, array(), 'model_registry'),
                                $this->get('get_customfields', 1, 'model_registry'),
                                $results,
                                $this->get('query_object', QUERY_OBJECT_ITEM, 'model_registry')
                            );
                    }
                }

                if ((int)$this->get('get_item_children', 1, 'model_registry') == 1) {

                    $children = $this->get('children', array(), 'model_registry');

                    if (count($children) > 0) {
                        $results = $this->model->addItemChildren(
                            $children,
                            (int)$this->get('primary_key_value', 0, 'model_registry'),
                            $results
                        );
                    }
                }
            }
            $q[] = $results;
        }

        /** Just hijacking this to build registry special fields for specific extension (from saved extension registry) */
        /** todo: figure out what the heck i meant by this (or, more likely, just make certain it isn't used and pull it. */
        if ($external == 1) {
            if (is_array($q)) {
                return $q[0];
            }
            return $q;
        }
        return $q;
    }

    /**
     * Schedule onConnectDatabase Event
     *
     *  - Connection to Data Object complete - the model instance and model registry passed into Event
     *
     *  - Plugins cannot be selected for the data object -- use criteria within the plugin event method
     *
     * @return  void
     * @since   1.0
     */
    protected function onConnectDatabaseEvent()
    {
        $arguments = array(
            'model' => $this->get('model'),
            'model_registry' => $this->get('model_registry'),
            'parameters' => $this->get('parameters'),
            'query_results' => array(),
            'data' => array(),
            'rendered_output' => array(),
            'include_parse_sequence' => null,
            'include_parse_exclude_until_final' => null
        );

        $arguments = Services::Event()->scheduleEvent(
            'onConnectDatabase',
            $arguments,
            $this->get('plugins', array())
        );

        $this->setPluginResultProperties($arguments);

        return;
    }

    /**
     * Schedule onBeforeRead Event
     *
     * - Model Query has been developed and is passed into the event, along with parameters and registry data
     *
     * - Good event for modifying selection criteria, like adding tag selectivity, or setting publishing criteria
     *
     * - Examples: Publishedstatus
     *
     * @return  void
     * @since   1.0
     */
    protected function onBeforeReadEvent()
    {
        if (count($this->get('plugins', array())) == 0
            || (int)$this->get('process_plugins', 1, 'model_registry') == 0
        ) {
            return;
        }

        $arguments = array(
            'model' => $this->get('model'),
            'model_registry' => $this->get('model_registry'),
            'parameters' => $this->get('parameters'),
            'query_results' => array(),
            'data' => array(),
            'rendered_output' => array(),
            'include_parse_sequence' => null,
            'include_parse_exclude_until_final' => null
        );

        $arguments = Services::Event()->scheduleEvent(
            'onBeforeRead',
            $arguments,
            $this->get('plugins', $this->get('plugins'))
        );

        $this->setPluginResultProperties($arguments);

        return;
    }

    /**
     * Schedule Event onAfterRead Event
     *
     * - After the Query executes, the results of the query are sent through the plugins, one at a time
     *  (this event -- and each of the associated plugins -- run one time for each record returned)
     *
     * - Good time to schedule content modifying plugins, like smilies or image placement.
     *      Examples: Smilies, Images, Linebreaks
     *
     * - Additional data elements can be added to the row -- codes can be expanded into textual descriptions
     *  or profile data added for author, etc.
     *      Examples: Author, CSSclassandids, Gravatar, Dateformats, Email
     *
     * - Use Event carefully as it has perhaps the most potential to negatively impact performance.
     *
     * @return  void
     * @since   1.0
     */
    protected function onAfterReadEvent()
    {
        if (count($this->get('plugins', array())) == 0
            || (int)$this->get('process_plugins', 1, 'model_registry') == 0
        ) {
            return;
        }

        $items = $this->query_results;
        $this->query_results = array();

        $first = true;

        if (count($items) == 0) {
        } else {
            foreach ($items as $item) {

                $this->set('first', $first, 'parameters');

                $arguments = array(
                    'model' => $this->get('model'),
                    'model_registry' => $this->get('model_registry'),
                    'parameters' => $this->get('parameters'),
                    'query_results' => $item,
                    'data' => array(),
                    'rendered_output' => array(),
                    'include_parse_sequence' => null,
                    'include_parse_exclude_until_final' => null
                );

                $arguments = Services::Event()->scheduleEvent(
                    'onAfterRead',
                    $arguments,
                    $this->get('plugins', $this->get('plugins'))
                );

                $this->setPluginResultProperties($arguments);

                $first = false;
            }
        }

        return;
    }

    /**
     * Schedule Event onAfterRead Event
     *
     *  - entire query results passed in as an array
     *
     *  - Good event for inserting an include statement based on the results (maybe a begin and end form)
     *      or when the entire resultset must be handled, like generating a Feed, or JSON output,
     *
     *  - Examples: CssclassandidsPlugin, Pagination, Paging, Useractivity
     *
     * @return  void
     * @since   1.0
     */
    protected function onAfterReadallEvent()
    {
        $arguments = array(
            'model' => $this->get('model'),
            'model_registry' => $this->get('model_registry'),
            'parameters' => $this->get('parameters'),
            'query_results' => $this->get('query_results'),
            'data' => array(),
            'rendered_output' => array(),
            'include_parse_sequence' => null,
            'include_parse_exclude_until_final' => null
        );

        $arguments = Services::Event()->scheduleEvent(
            'onAfterReadall',
            $arguments,
            $this->get('plugins', $this->get('plugins'))
        );

        $this->setPluginResultProperties($arguments);

        return;
    }

    /**
     * Common code for setting the controller properties, given various events
     *
     * @param   $arguments
     *
     * @return  bool
     * @since   1.0
     */
    protected function setPluginResultProperties($arguments)
    {
        if (isset($arguments['model'])) {
            $this->set('model', $arguments['model'], '');
        } else {
            $this->set('model', array(), '');
        }

        if (isset($arguments['model_registry'])) {
            $this->set('model_registry', $arguments['model_registry'], '');
        } else {
            $this->set('model_registry', array(), '');
        }

        if (isset($arguments['parameters'])) {
            $this->set('parameters', $arguments['parameters'], '');
        } else {
            $this->set('parameters', array(), '');
        }

        if (isset($arguments['query_results'])) {
            $this->set('query_results', $arguments['query_results'], '');
        } else {
            $this->set('query_results', array(), '');
        }

        if (isset($arguments['data'])) {
            $this->set('data', $arguments['data'], '');
        } else {
            $this->set('data', array(), '');
        }

        if (isset($arguments['rendered_output'])) {
            $this->set('rendered_output', $arguments['rendered_output'], '');
        } else {
            $this->set('rendered_output', array(), '');
        }

        return true;
    }
}
