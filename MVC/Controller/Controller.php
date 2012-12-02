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
     * Model Registry Name - only used to share data between getModelRegistry and setDataobject
     *  property is unset in setDataobject - all model data should be accessed via the $model_registry
     *
     * @var    object
     * @since  1.0
     */
    private $model_registry_name;

    /**
     * Stores an array of key/value Parameters settings
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * Model Instance - db, query connection, date defaults, etc.
     *
     * @var    object
     * @since  1.0
     */
    protected $model;

    /**
     * Model Registry - data source/object fields and definitions
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = array();

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
     * @var  boolean
     */
    protected $data_object_set;

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
    public function get($key, $default = null, $property = 'parameters')
    {
        if ($property == 'parameters') {
            if (isset($this->parameters[$key])) {
                return $this->parameters[$key];
            }

            $this->parameters[$key] = $default;

            return $this->parameters[$key];

        } elseif ($property == 'model_registry') {

            if (isset($this->model_registry[$key])) {
                return $this->model_registry[$key];
            }

            $this->model_registry[$key] = $default;

            return $this->model_registry[$key];

        }

        if (isset($this->$key)) {
            return $this->$key;
        }

        $this->$key = $default;

        return $this->$key;
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
    public function set($key, $value = null, $property = 'parameters')
    {
        if ($property == 'parameters') {
            $this->parameters[$key] = $value;

        } elseif ($property == 'model_registry') {
            $this->model_registry[$key] = $value;

        } else {
            $this->$key = $value;
        }

        return $this;
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

        if ($this->model_registry_name === null) {
            throw new \RuntimeException('Controller: Required value missing for $model_registry');
        }

        if (Services::Registry()->exists($this->model_registry_name) === true) {
        } else {
            throw new \RuntimeException('Controller: Load $model_registry using getModelRegistry');
        }

        $load = Services::Registry()->get($this->model_registry_name);

        $this->set('model_registry', array());

        if (count($load) > 0) {
            foreach ($load as $key => $value) {
                $this->set($key, $value, 'model_registry');
            }
        }

        $this->set('model_registry_name', $this->model_registry_name, 'model_registry');

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

        unset($this->model_registry_name);

        $this->onAfterSetDataobjectEvent();
        Services::Registry()->get($this->model_registry_name, '*');
        die;

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
            $this->prepareQuery($this->get('query_object', QUERY_OBJECT_LIST, 'model_registry'));
        }

        $this->getPluginList();

        $profiler_message =

            ' <br />Data Object: ' . $this->get('data_object', DATABASE_LITERAL, 'model_registry')
                . ' <br />Model Type: ' . $this->get('model_type', DATA_SOURCE_LITERAL, 'model_registry')
                . ' <br />Model Name: ' . $this->get('model_name', '', 'model_registry')
                . ' <br />Model Query Object: ' . $this->get('query_object', QUERY_OBJECT_LIST, 'model_registry')
                . ' <br />Template View: ' . $this->parameters['template_view_path_node']
                . ' <br />Process Plugins: ' . (int)$this->get('process_plugins', 1, 'model_registry')
                . '<br /><br />';

        if ($this->get('data_object_type', DATABASE_LITERAL, 'model_registry') == DATABASE_LITERAL) {

            if (count($this->get('plugins', array())) > 0) {
                $this->onBeforeReadEvent();
            }

            $this->runQuery($this->get('data_object', DATABASE_LITERAL, 'model_registry'));

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
                    $method_parameter = $this->parameters['template_view_path_node'];

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
                    . ' Query Object ' . $this->get('data_object', DATABASE_LITERAL, 'model_registry');

                echo $profiler_message . '<br /><br />';

                $this->query_results = Services::$service_class()
                    ->$service_class_query_method(
                    $this->get('model_name', '', 'model_registry'),
                    $method_parameter,
                    $this->get('data_object', DATABASE_LITERAL, 'model_registry')
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

        if ($this->get('data_object_type', DATABASE_LITERAL, 'model_registry') == DATABASE_LITERAL) {
        } else {
            return $this->query_results;
        }

        if ($this->get('query_object', QUERY_OBJECT_LIST, 'model_registry') == QUERY_OBJECT_RESULT
            || $this->get('query_object', QUERY_OBJECT_LIST, 'model_registry') == QUERY_OBJECT_DISTINCT
        ) {
            return $this->query_results;
        }

        if ($this->get('query_object', QUERY_OBJECT_LIST, 'model_registry') == QUERY_OBJECT_LIST) {

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

        if (defined('APPLICATION_ID')) {
        } else {
            return;
        }

        if ($this->get('data_object', DATABASE_LITERAL, 'model_registry') == QUERY_OBJECT_RESULT) {
            return;
        }

        $dPlugins = array();
        if ((int)$this->get('process_plugins', 1, 'model_registry') == 1) {

            $dPlugins = $this->get('plugins', array(), 'model_registry');

            if (is_array($dPlugins)) {
            } else {
                $dPlugins = array();
            }
        }

        $tPlugins = array();
        if ((int)$this->get('process_template_plugins', 1, 'model_registry')) {

            if ((int)$this->get('process_template_plugins', 1, 'model_registry') == 0) {
                $temp = array();

            } else {
                $tPlugins = Services::Registry()->get(
                    $this->get('process_template_plugins', 1, 'model_registry'),
                    'plugins',
                    array()
                );

                if (is_array($tPlugins)) {
                } else {
                    $tPlugins = array();
                }
            }
        }

        $temp = array_merge($dPlugins, $tPlugins);
        if (is_array($temp)) {
        } else {
            $temp = array();
        }

        $page_type = $this->parameters['criteria_catalog_page_type'];
        if ($page_type == '') {
        } else {
            $temp[] = 'Pagetype' . strtolower($page_type);
        }

        $template = $this->parameters['template_view_path_node'];
        if ($template == '') {
        } else {
            $temp[] = $template;
        }

        if ((int)$this->get('process_plugins', 1, 'model_registry') == 0 && count($temp) == 0) {
            $this->get('plugins', array());
            return;
        }

        $temp[] = 'Application';

        $this->get('plugins', array());

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
        $primary_key_value = (int)$this->get('primary_key_value', 0, 'model_registry');
        $name_key_value = $this->get('name_key_value', '', 'model_registry');

        if ($this->get('data_object', DATABASE_LITERAL, 'model_registry') == QUERY_OBJECT_ITEM
            || $this->get('data_object', DATABASE_LITERAL, 'model_registry') == QUERY_OBJECT_RESULT
        ) {
        } else {
            $primary_key_value = 0;
            $name_key_value = '';
        }

        $this->set('primary_key_value', $primary_key_value, 'model_registry');
        $this->set('name_key_value', $name_key_value, 'model_registry');

        $this->model->setBaseQuery(
            $this->get(strtolower(FIELDS_LITERAL), array(), 'model_registry'),
            $this->get('table_name', null, 'model_registry'),
            $this->get('primary_prefix', 'a', 'model_registry'),
            $this->get('primary_key', 'id', 'model_registry'),
            $this->get('primary_key_value', 0, 'model_registry'),
            $this->get('name_key', null, 'model_registry'),
            $this->get('name_key_value', null, 'model_registry'),
            $this->get('data_object', DATABASE_LITERAL, 'model_registry'),
            $this->get('model_registry_name', null, 'model_registry')
        );

        if ((int)$this->model_registry['check_view_level_access'] == 1) {
            $this->model->checkPermissions(
                $this->get('primary_prefix', 'a', 'model_registry'),
                $this->get('primary_key_value', 0, 'model_registry'),
                $this->get('data_object', DATABASE_LITERAL, 'model_registry')
            );
        }

        if ((int)$this->get('use_special_joins', 1, 'model_registry') == 1) {
            $joins = $this->get('joins', array(), 'model_registry');
            if (count($joins) > 0) {
                $this->model->useSpecialJoins(
                    $joins,
                    $this->get('primary_prefix', 'a', 'model_registry'),
                    $this->get('data_object', DATABASE_LITERAL, 'model_registry')
                );
            }
        }

        $this->model->setModelCriteria(
            $this->get('criteria_catalog_type_id'),
            $this->get('criteria_extension_instance_id'),
            $this->get('primary_prefix', 'a', 'model_registry')
        );

        echo $this->model->query->__toString();
        die;
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
        echo '<pre>';
        var_dump($this->model_registry);
        echo '</pre>';

        $this->set(
            'pagination_total',
            (int)$this->model->getQueryResults(
                $this->get('data_object', DATABASE_LITERAL, 'model_registry'),
                $this->get('model_offset', 0, 'model_registry'),
                $this->get('model_count', 15, 'model_registry'),
                $this->model_registry['use_pagination']
            )
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

        echo '<br /><br /><pre>';
        echo $this->model->query->__toString();
        echo '<br /><br />';
        var_dump($query_results);
        echo '</pre><br /><br />';

        if ($this->get('data_object', DATABASE_LITERAL, 'model_registry') == QUERY_OBJECT_RESULT
            || $this->get('data_object', DATABASE_LITERAL, 'model_registry') == QUERY_OBJECT_DISTINCT
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
                $this->get('data_object', DATABASE_LITERAL, 'model_registry')
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
        if (count($this->get('data_object', DATABASE_LITERAL, 'model_registry')) > 0) {
        } else {
            return false;
        }

        $q = array();

        foreach ($query_results as $results) {

            if ((int)$this->get('get_customfields', 1, 'model_registry') == 0) {
            } else {

                $customFieldTypes = $this->model_registry[CUSTOMFIELDGROUPS_LITERAL];

                if (count($customFieldTypes) == 0 || $customFieldTypes == null) {
                } else {

                    foreach ($customFieldTypes as $customFieldName) {
                        $results =
                            $this->model->addCustomFields(
                                $customFieldName,
                                $this->model_registry[$customFieldName],
                                $this->get('get_customfields', 1, 'model_registry'),
                                $results,
                                $this->get('data_object', DATABASE_LITERAL, 'model_registry')
                            );
                    }
                }

                if ((int)$this->get('get_item_children', 1, 'model_registry') == 1) {

                    $children = $this->get('Children', array(), 'model_registry');

                    if (count($children) > 0) {
                        $results = $this->model->addItemChildren(
                            $children,
                            (int)$this->get('primary_key', 0, 'model_registry'),
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
     * Schedule onAfterSetDataobject Event
     *
     *  - Connection to Data Object complete - the model instance and model registry passed into Event
     *
     *  - Plugins cannot be selected for the data object -- use criteria within the plugin event method
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onAfterSetDataobjectEvent()
    {
        $arguments = array(
            'model_registry' => $this->get('model_registry'),
            'model' => $this->get('model'),
            'parameters' => $this->get('parameters')
        );

        $arguments = Services::Event()->scheduleEvent('onAfterSetDataobject', $arguments, array());

        if ($arguments === false) {
            throw new \Exception('Controller: onAfterSetDataobject Failed for Data Object: '
                . $this->get('data_object', DATABASE_LITERAL, 'model_registry'));
        }

        if (isset($arguments['parameters'])) {
            $this->set('parameters', $arguments['parameters']);
        }
        if (isset($arguments['model'])) {
            $this->set('model', $arguments['model']);
        }
        if (isset($arguments['model_registry'])) {
            $this->set('model_registry', $arguments['model_registry']);
        }

        return true;
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
     * @return  boolean
     * @since   1.0
     */
    protected function onBeforeReadEvent()
    {
        if (count($this->get('plugins', array())) == 0
            || (int)$this->get('process_plugins', 1, 'model_registry') == 0
        ) {
            return true;
        }

        $arguments = array(
            'model_registry' => $this->get('model_registry'),
            'model' => $this->get('model'),
            'parameters' => $this->get('parameters')
        );

        $arguments = Services::Event()->scheduleEvent('onBeforeRead', $arguments, array());

        if ($arguments === false) {
            throw new \Exception('Controller: onBeforeRead Failed for Model Registry: '
                . $this->get('model_registry', DATABASE_LITERAL, 'model_registry'));
        }

        if (isset($arguments['parameters'])) {
            $this->set('parameters', $arguments['parameters']);
        }
        if (isset($arguments['model'])) {
            $this->set('model', $arguments['model']);
        }
        if (isset($arguments['model_registry'])) {
            $this->set('model_registry', $arguments['model_registry']);
        }

        return true;
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
     * @return  bool
     * @since   1.0
     */
    protected function onAfterReadEvent()
    {
        if (count($this->get('plugins', array())) == 0
            || (int)$this->get('process_plugins', 1, 'model_registry') == 0
        ) {
            return true;
        }

        $items = $this->query_results;
        $this->query_results = array();

        $first = true;

        if (count($items) == 0) {
        } else {
            foreach ($items as $item) {

                $arguments = array(
                    'model_registry' => $this->get('model_registry'),
                    'model' => $this->get('model'),
                    'parameters' => $this->get('parameters'),
                    'first' => $first
                );

                $arguments = Services::Event()->scheduleEvent(
                    'onAfterRead', $arguments, $this->get('plugins', array()));

                if ($arguments === false) {
                    return false;
                }

                if (isset($arguments['parameters'])) {
                    $this->set('parameters', $arguments['parameters']);
                }
                if (isset($arguments['model'])) {
                    $this->set('model', $arguments['model']);
                }
                if (isset($arguments['model_registry'])) {
                    $this->set('model_registry', $arguments['model_registry']);
                }

                $first = false;
            }
        }
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
     * @return  bool
     * @since   1.0
     */
    protected function onAfterReadallEvent()
    {
        $arguments = array(
            'model_registry' => $this->get('model_registry'),
            'model' => $this->get('model'),
            'parameters' => $this->get('parameters')
        );

        $this->set('parameters', array());
        $this->set('model_registry', array());
        $this->set('query_results', array());


        $arguments = Services::Event()->scheduleEvent(
            'onAfterReadall', $arguments, $this->get('plugins', array()));

        if ($arguments === false) {
            return false;
        }

        if (isset($arguments['parameters'])) {
            $this->set('parameters', $arguments['parameters']);
        }
        if (isset($arguments['model'])) {
            $this->set('model', $arguments['model']);
        }
        if (isset($arguments['model_registry'])) {
            $this->set('model_registry', $arguments['model_registry']);
        }

        return true;
    }
}
