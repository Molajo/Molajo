<?php
/**
 * Primary Controller
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\MVC\Controller;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Primary controller responsible to retrieve configuration for model registries, interact with models,
 * data objects, and perform event scheduling for data object connectivity and before and after read.
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
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
     * Single set of $query_results and used in create, update, delete operations
     *
     * @var    object
     * @since  1.0
     */
    protected $row;

    /**
     * Plugins specified in the table registry for the model registry
     *
     * @var    array
     * @since  1.0
     */
    protected $plugins = array();

    /**
     * Rendered Output
     *
     * @var    boolean
     * @since  1.0
     */
    protected $rendered_output;

    /**
     * Used to ensure dataobject is connected to the database
     *
     * @var    boolean
     * @since  1.0
     */
    protected $connect_database_set;

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
     * List of Controller Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'model',
        'model_registry',
        'model_registry_name',
        'parameters',
        'query_results',
        'row',
        'rendered_output',
        'view_path',
        'view_path_url',
        'connect_database_set',
        'plugins'
    );

    /**
     * Most properties are used in the controller and passed into the model and events
     */
    public function __construct()
    {
        /** Temporary and Internal */
        $this->set('model_registry_name', null);
        $this->set('connect_database_set', 0);

        /** Shared with Model and Passed into Events/Plugins */
        $this->set('parameters', array());
        $this->set('model', array());
        $this->set('model_registry', array());
        $this->set('query_results', array());
        $this->set('row', array());
        $this->get('plugins', array());

        return $this;
    }

    /**
     * Get the current value (or default) of the specified Model property
     *
     * @param   string  $key
     * @param   mixed   $default
     * @param   string  $property
     *
     * @return  mixed
     * @throws  \OutOfRangeException
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
     * @param   string  $value
     * @param   string  $property
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
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
     * getModelRegistry retrieves Model Registry data first by seeing if it's already available in the Registry,
     *  if not, then seeing if it is available in Cache, and finally by building it from source in Configuration
     *
     * @param   string  $model_type
     * @param   null    $model_name
     * @param   int     $connect
     * @param   null    $parameter_registry
     *
     * @return  object  model_registry
     * @since   1.0
     *
     * @throws  \RuntimeException
     */
    public function getModelRegistry(
        $model_type = 'datasource',
        $model_name = null,
        $connect = 0,
        $parameter_registry = null
    ) {
        $this->set('connect_database_set', 0);

        if ($model_type === null) {
            $model_type = 'datasource';
        }
        if ($parameter_registry === null) {
            $parameter_registry = 'parameters';
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

                Services::Configuration()->getModel(
                    $this->get('model_type', '', 'model_registry'),
                    $this->get('model_name', '', 'model_registry'),
                    $parameter_registry
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

        $this->set('model_registry', Services::Registry()->get($this->model_registry_name));

        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            Services::Profiler()->set($profiler_message, 'Queries');
        }

        /** Only to ensure this redundant data is the same - it is a handy data element to retain */
        $this->set('model_registry_name', $this->model_registry_name, 'model_registry');

        if (isset($this->model_registry['data_object_data_object_type'])) {

        } else {
            $this->set('data_object_data_object_type', 'other', 'model_registry');
            $this->set('model_class', 'other', 'model_registry');
        }
        $data_object_type = $this->get('data_object_data_object_type', null, 'model_registry');

        $this->set('data_object_type', $data_object_type, 'model_registry');

        if ($connect == 1) {
            $this->connectDatabase();
        }

        return $this->get('model_registry');
    }

    /**
     * Method to connect data object to database for query development and execution
     *
     * @return  void
     * @since   1.0
     * @throws  \Exception
     */
    public function connectDatabase()
    {
        $this->set('connect_database_set', 1);

        if (strtolower($this->get('data_object_type', '', 'model_registry')) == 'database') {

            if ($this->get('model_class', null, 'model_registry') === null) {
                $this->set('model_class', 'ReadModel', 'model_registry');
            }

            $modelClass = MODEL_NAMESPACE . $this->get('model_class', null, 'model_registry');

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
        if ($this->get('connect_database_set') == 0) {
            $this->connectDatabase();
        }

        $this->getPluginList();

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

        if (defined('PROFILER_ON') && PROFILER_ON === true) {

            $profiler_message =
                ' <br />Data Object: ' . $this->get('data_object', 'Database', 'model_registry')
                    . ' <br />Model Type: ' . $this->get('model_type', 'datasource', 'model_registry')
                    . ' <br />Model Name: ' . $this->get('model_name', '', 'model_registry')
                    . ' <br />Model Registry Name: ' . $this->get('model_registry_name')
                    . ' <br />Model Query Object: ' . $this->get('query_object', '', 'model_registry')
                    . ' <br />Template View: ' . $this->get('template_view_path_node', '', 'parameters')
                    . ' <br />Process Plugins: ' . (int)$this->get('process_plugins', 1, 'model_registry')
                    . '<br /><br />';

            Services::Profiler()->set($profiler_message, 'Queries');
        }

        if ($this->get('data_object', 'Database', 'model_registry') == 'Database') {
            $this->prepareQuery($this->get('query_object', '', 'model_registry'));
        }

        if ($this->get('data_object', 'Database', 'model_registry') == 'Database') {

            if (count($this->get('plugins', array())) > 0) {
                $this->onBeforeReadEvent();
            }

            $this->runQuery($this->get('query_object', '', 'model_registry'));

        } else {

            if (strtolower($this->get('model_name', '', 'model_registry')) == 'dummy') {
                $this->query_results = array();

            } else {

                $service_class              = $this->get('service_class', 'Database', 'model_registry');
                $service_class_query_method = $this->get('service_class_query_method', '', 'model_registry');

                if ($this->get('model_name', '', 'model_registry') == 'Primary') {
                    $method_parameter = 'Data';

                } elseif ($this->get('service_class_query_method_parameter', '', 'model_registry')
                    == 'Template'
                ) {
                    $method_parameter = $this->get('template_view_path_node', '', 'parameters');

                } elseif ($this->get('service_class_query_method_parameter', '', 'model_registry')
                    == 'Model'
                ) {
                    $method_parameter = $this->get('model_name', '', 'model_registry');

                } else {
                    $method_parameter = $this->get(
                        'service_class_query_method_parameter',
                        '',
                        'model_registry'
                    );
                }

                if (count($this->get('plugins', array())) > 0) {
                    $this->onBeforeReadEvent();
                }

                if (defined('PROFILER_ON') && PROFILER_ON === true) {
                    $profiler_message .= 'Class: ' . $service_class
                        . ' Method ' . $service_class_query_method
                        . ' Model Name ' . $this->get('model_name', '', 'model_registry')
                        . ' Method parameter ' . $method_parameter
                        . ' Query Object ' . $this->get('query_object', '', 'model_registry');

                    Services::Profiler()->set($profiler_message, 'Queries');
                }

                $this->query_results = Services::$service_class()
                    ->$service_class_query_method(
                    $this->get('model_name', '', 'model_registry'),
                    $method_parameter,
                    $this->get('query_object', '', 'model_registry')
                );
            }
        }

        if (count($this->get('plugins', array())) > 0) {
            $this->onAfterReadEvent(
                $this->get('use_pagination', 1, 'model_registry'),
                $this->get('model_offset', 0, 'model_registry'),
                $this->get('model_count', 15, 'model_registry')
            );
        }

        if ($this->get('data_object_type', 'Database', 'model_registry') == 'Database') {
        } else {
            return $this->query_results;
        }

        if ($this->get('query_object', '', 'model_registry') == QUERY_OBJECT_RESULT
            || $this->get('query_object', '', 'model_registry') == QUERY_OBJECT_DISTINCT
        ) {
            return $this->query_results;
        }

        if ($this->get('query_object', '', 'model_registry') == QUERY_OBJECT_LIST) {

            if (defined('PROFILER_ON') && PROFILER_ON === true) {
                $profiler_message .= 'Controller: getData Query Results <br /><br />';

                ob_start();
                echo '<pre>';
                var_dump($this->query_results);
                echo '</pre><br /><br />';

                $profiler_message .= ob_get_contents();
                ob_end_clean();
                echo $profiler_message;

                Services::Profiler()->set($profiler_message, 'Queries', 1);
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
     * Prepare query object for standard dbo queries
     *
     * @return  bool
     * @since   1.0
     */
    protected function prepareQuery()
    {
        //Services::Registry()->get('ActionsDatasource', '*');

        $key = $this->get('primary_key_value', 0, 'model_registry');

        if ($key === 0) {
            $key = $this->get('criteria_source_id', 0, 'parameters');
        }

        $this->set('primary_key_value', $key, 'model_registry');

        $this->model->setBaseQuery(
            $this->get('fields', array(), 'model_registry'),
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

        if ($this->get('model_registry_name') == 'ExtensioninstancesDatasource') {
        echo '<br /><br /><pre>';
        $this->get('model_registry_name');
        echo '<br /><br /><pre>';
        echo $this->model->query->__toString();
        echo '<br /><br />';
        }

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

        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            Services::Profiler()->set(
                'Controller runQuery: <br /><br />'
                    . $this->model->query->__toString(),
                'Queries',
                1
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

            if (defined('PROFILER_ON') && PROFILER_ON === true) {

                $message = 'DisplayController->getData Query Result <br /><br />';

                ob_start();
                echo '<pre>';
                var_dump($query_results);
                echo '</pre><br /><br />';
                $message .= ob_get_contents();
                ob_end_clean();

                Services::Profiler()->set($message, 'Queries', 1);
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
    public function addCustomFields($query_results)
    {
        $custom_field_types = $this->get('customfieldgroups', array(), 'model_registry');

        if (is_array($custom_field_types)) {
        } else {
            $custom_field_types = array();
        }

        $q = array();
        foreach ($query_results as $row) {


            if (count($custom_field_types) > 0) {
                foreach ($custom_field_types as $field_type) {
                    $row =
                        $this->model->addCustomFields(
                            $this->get('model_registry_name', '', 'model_registry'),
                            $field_type,
                            $this->get($field_type, array(), 'model_registry'),
                            $this->get('get_customfields', 1, 'model_registry'),
                            $row,
                            $this->get('query_object', QUERY_OBJECT_ITEM, 'model_registry')
                        );
                }
            }

            if ((int)$this->get('get_item_children', 1, 'model_registry') == 1) {

                $children = $this->get('children', array(), 'model_registry');

                if (count($children) > 0) {
                    $row = $this->model->addItemChildren(
                        $children,
                        $this->get('primary_key_value', 0, 'model_registry'),
                        $row
                    );
                }
            }

            $q[] = $row;
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
            'model'                             => $this->get('model'),
            'model_registry'                    => $this->get('model_registry'),
            'model_registry_name'               => $this->get('model_registry_name'),
            'parameters'                        => $this->get('parameters'),
            'query_results'                     => array(),
            'row'                               => null,
            'rendered_output'                   => null,
            'view_path'                         => null,
            'view_path_url'                     => null,
            'plugins'                           => $this->get('plugins'),
            'class_array'                       => array(),
            'include_parse_sequence'            => array(),
            'include_parse_exclude_until_final' => array()
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
     * Get the list of potential plugins identified with this model registry
     *
     * @return  void
     * @since   1.0
     */
    protected function getPluginList()
    {
        $this->set('plugins', array());

        if (defined('ROUTE')) {
        } else {
            return;
        }

        if ($this->get('query_object', '', 'model_registry') == QUERY_OBJECT_RESULT) {
            return;
        }

        $modelPlugins = array();
        if ((int)$this->get('process_plugins', 1, 'model_registry') > 0) {

            $modelPlugins = $this->get('plugins', array(), 'model_registry');

            if (is_array($modelPlugins)) {
            } else {
                $modelPlugins = array();
            }
        }

        $templatePlugins = array();

        if ((int)$this->get('process_template_plugins', 1, 'model_registry') > 0) {

            $name = $this->get('template_view_path_node', '', 'parameters');

            if ($name == '') {
            } else {
                $templatePlugins = Services::Registry()->get(ucfirst(strtolower($name)) . 'Templates', 'plugins');

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

        $page_type = $this->get('catalog_page_type', '', 'parameters');
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
        if (defined('ROUTE')) {
        } else {
            return;
        }

        if (count($this->get('plugins', array())) == 0
            || (int)$this->get('process_plugins', 1, 'model_registry') == 0
        ) {
            return;
        }

        $arguments = array(
            'model'                             => $this->get('model'),
            'model_registry'                    => $this->get('model_registry'),
            'model_registry_name'               => $this->get('model_registry_name'),
            'parameters'                        => $this->get('parameters'),
            'query_results'                     => array(),
            'row'                               => null,
            'rendered_output'                   => null,
            'view_path'                         => null,
            'view_path_url'                     => null,
            'plugins'                           => $this->get('plugins'),
            'class_array'                       => array(),
            'include_parse_sequence'            => array(),
            'include_parse_exclude_until_final' => array()
        );

        $arguments = Services::Event()->scheduleEvent(
            'onBeforeRead',
            $arguments,
            $this->get('plugins')
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

        $rows                = $this->query_results;
        $this->query_results = array();

        $first = true;

        if (count($rows) == 0) {
        } else {
            foreach ($rows as $row) {

                $this->set('first', $first, 'parameters');

                $arguments = array(
                    'model'                             => $this->get('model'),
                    'model_registry'                    => $this->get('model_registry'),
                    'model_registry_name'               => $this->get('model_registry_name'),
                    'parameters'                        => $this->get('parameters'),
                    'query_results'                     => array(),
                    'row'                               => $row,
                    'rendered_output'                   => null,
                    'view_path'                         => null,
                    'view_path_url'                     => null,
                    'plugins'                           => $this->get('plugins'),
                    'class_array'                       => array(),
                    'include_parse_sequence'            => array(),
                    'include_parse_exclude_until_final' => array()
                );

                $arguments = Services::Event()->scheduleEvent(
                    'onAfterRead',
                    $arguments,
                    $this->get('plugins')
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
        if (defined('ROUTE')) {
        } else {
            return;
        }

        $arguments = array(
            'model'                             => $this->get('model'),
            'model_registry'                    => $this->get('model_registry'),
            'model_registry_name'               => $this->get('model_registry_name'),
            'parameters'                        => $this->get('parameters'),
            'query_results'                     => $this->get('query_results'),
            'row'                               => null,
            'rendered_output'                   => null,
            'view_path'                         => null,
            'view_path_url'                     => null,
            'plugins'                           => $this->get('plugins'),
            'class_array'                       => array(),
            'include_parse_sequence'            => array(),
            'include_parse_exclude_until_final' => array()
        );

        $arguments = Services::Event()->scheduleEvent(
            'onAfterReadall',
            $arguments,
            $this->get('plugins')
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

        if (isset($arguments['model_registry_name'])) {
            $this->set('model_registry_name', $arguments['model_registry_name'], '');
        } else {
            $this->set('model_registry_name', '');
        }

        if (isset($arguments['parameters'])) {
            $this->set('parameters', $arguments['parameters'], '');
        } else {
            $this->set('parameters', array(), '');
        }

        if (isset($arguments['row']) && $arguments['row'] !== null) {
            $this->query_results[] = $arguments['row'];
            $this->set('query_results', $this->query_results, '');
            $this->set('row', $arguments['row'], '');

        } elseif (isset($arguments['query_results'])) {
            $this->set('query_results', $this->query_results, '');
            $this->set('row', null, '');

        } else {
            $this->set('query_results', array(), '');
            $this->set('row', null, '');
        }

        if (isset($arguments['rendered_output'])) {
            $this->set('rendered_output', $arguments['rendered_output'], '');
        } else {
            $this->set('rendered_output', array(), '');
        }

        if (isset($arguments['plugins'])) {
            $this->set('plugins', $arguments['plugins'], '');
        } else {
            $this->set('plugins', array(), '');
        }

        return true;
    }
}
