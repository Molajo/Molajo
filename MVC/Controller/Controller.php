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
     * Stores various extension-specific key/value pairs - public
     *
     * @var    array
     * @since  1.0
     */
    public $parameters = array();

    /**
     * Model Instance - Public as it is passed into Event Scheduling
     *
     * @var    object
     * @since  1.0
     */
    public $model;

    /**
     * Model Registry Name - Public as it is passed into Event Scheduling
     *
     * @var    object
     * @since  1.0
     */
    public $model_registry_name;

    /**
     * Model Type
     *
     * @var    object
     * @since  1.0
     */
    public $model_type;

    /**
     * Model Name
     *
     * @var    object
     * @since  1.0
     */
    public $model_name;

    /**
     * Model Registry
     *
     * Copy of Model Registry, passed thru events and plugins, can be changed
     *
     * @var    object
     * @since  1.0
     */
    public $model_registry = array();

    /**
     * Set of rows returned from a query
     *
     * @var    array
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
     * Used to build Create, Update, Delete data structures - public
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
     * Retrieve Site and Application data, set constants and paths
     *
     * @return  object
     * @since   1.0
     */
    public function __construct()
    {
        $this->parameters = array();
        $this->model_registry_name = null;
        $this->model_name = null;
        $this->model_type = null;
        $this->model_registry = array();
        $this->query_results = array();
        $this->row = null;
        $this->data = array();
        $this->plugins = array();

        $this->set('data_object_set', 0);

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
     * @param   string  $key
     * @param   mixed   $value
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
     * @throws  \RuntimeException
     */
    public function getModelRegistry($model_type = DATA_SOURCE_LITERAL, $model_name = null, $model_class = 'ReadModel')
    {
        $this->set('data_object_set', 0);

        if ($model_type == '') {
            $model_type = DATA_SOURCE_LITERAL;
        }

        $model_type = ucfirst(strtolower($model_type));
        $model_name = ucfirst(strtolower($model_name));
        $this->model_type = $model_type;
        $this->model_name = $model_name;
        $this->model_registry_name = $model_name . $model_type;

        if (Services::Registry()->exists($this->model_registry_name) === true) {
            $profiler_message = ' Registry ' . $this->model_registry_name . ' retrieved from Registry.';

        } else {

            $cached_output = Services::Cache()->get('Model', $this->model_registry_name);

            if ($cached_output === false) {

                ConfigurationService::getModel($model_type, $model_name);
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
     * @return  void
     * @since   1.0
     *
     * @throws  \RuntimeException
     */
    public function setDataobject()
    {
        $this->set('data_object_set', 1);

        if ($this->model_registry_name == null) {
            throw new \RuntimeException('Controller: Required value missing for $model_registry');
        }

        if (Services::Registry()->exists($this->model_registry_name) === true) {
        } else {
            throw new \RuntimeException('Controller: Load $model_registry using getModelRegistry');
        }

        $load = Services::Registry()->get($this->model_registry_name);
        $this->model_registry = array();
        if (count($load) > 0) {
            foreach ($load as $key => $value) {
                $this->model_registry[$key] = $value;
            }
        }

        $this->model_registry['model_type'] = $this->model_type;
        $this->model_registry['model_name'] = $this->model_name;
        $this->model_registry['model_registry_name'] = $this->model_registry_name;

        if (isset($this->model_registry['data_object_data_object_type'])) {
            $data_object_type = $this->model_registry['data_object_data_object_type'];

        } else {
            $this->model_registry['data_object_data_object_type'] = 'other';
            $this->model_registry['model_class'] = 'other';
            $data_object_type = $this->model_registry['data_object_data_object_type'];
        }
        $this->model_registry['data_object_type'] = $data_object_type;

        if (strtolower($data_object_type) == strtolower(DATABASE_LITERAL)) {

            if (isset($this->model_registry['model_class'])) {
            } else {
                $this->model_registry['model_class'] = 'ReadModel';
            }

            $modelClass = MODEL_CLASS . $this->model_registry['model_class'];

            try {
                $this->model = new $modelClass();

            } catch (\Exception $e) {
                throw new \Exception('Controller: Class ' . $modelClass . ' failed. Error: ' . $e->getMessage());
            }
        }

        if (isset($this->model_registry['use_pagination'])) {
        } else {
            $this->model_registry['use_pagination'] == 1;
        }
        if (isset($this->model_registry['model_offset'])) {
        } else {
            $this->model_registry['model_offset'] == 0;
        }
        if (isset($this->model_registry['model_count'])) {
        } else {
            $this->model_registry['model_count'] == 15;
        }
        if (isset($this->model_registry['criteria_catalog_type_id'])) {
        } else {
            $this->model_registry['criteria_catalog_type_id'] == 0;
        }
        if (isset($this->model_registry['criteria_extension_instance_id'])) {
        } else {
            $this->model_registry['criteria_extension_instance_id'] == 0;
        }
        if (isset($this->model_registry['criteria_catalog_page_type'])) {
        } else {
            $this->model_registry['criteria_catalog_page_type'] == 'a';
        }
        if (isset($this->model_registry['primary_prefix'])) {
        } else {
            $this->model_registry['primary_prefix'] == 'a';
        }
        if (isset($this->model_registry['process_template_plugins'])) {
        } else {
            $this->model_registry['process_template_plugins'] == 1;
        }
        if (isset($this->model_registry['template_view_model_registry'])) {
        } else {
            $this->model_registry['template_view_model_registry'] == '';
        }

        $this->onAfterSetDataobjectEvent();

        return;
    }

    /**
     * Method to execute model methods and returns results
     *
     * @param   string   $query_object - result, item, list, distinct
     *
     * @return  mixed    Depends on QueryObject selected
     * @since   1.0
     *
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

        $this->model_registry['query_object'] = $query_object;

        if ($this->model_registry['data_object'] == DATABASE_LITERAL) {
            $this->prepareQuery($this->model_registry['data_object']);
        }

        $this->getPluginList();

        $profiler_message =

            ' <br />Data Object: ' . $this->model_registry['data_object']
                . ' <br />Model Type: ' . $this->model_registry['model_type']
                . ' <br />Model Name: ' . $this->model_registry['model_name']
                . ' <br />Model Query Object: ' . $this->model_registry['query_object']
                . ' <br />Template View: ' . $this->model_registry['template_view_path_node']
                . ' <br />Process Plugins: ' . (int)$this->model_registry['process_plugins']
                . '<br /><br />';
echo $profiler_message;

        if ($this->model_registry['data_object_type'] == DATABASE_LITERAL) {

            if (count($this->plugins) > 0) {
                $this->onBeforeReadEvent();
            }

            $this->runQuery($this->model_registry['data_object']);

        } else {

            if (strtolower($this->model_registry['model_name']) == 'dummy') {
                $this->query_results = array();

            } else {
                $service_class = $this->model_registry['data_object_service_class'];
                $service_class_query_method = $this->model_registry['data_object_service_class_query_method'];

                if ($this->model_registry['model_name'] == PRIMARY_LITERAL) {
                    $method_parameter = DATA_LITERAL;

                } elseif ($this->model_registry['data_object_service_class_query_method_parameter']
                    == 'TEMPLATE_LITERAL') {
                    $method_parameter = $this->model_registry['template_view_path_node'];

                } elseif ($this->model_registry['data_object_service_class_query_method_parameter']
                    == 'MODEL_LITERAL') {
                    $method_parameter = $this->model_registry['model_name'];

                } else {
                    $method_parameter = $this->model_registry['data_object_service_class_query_method_parameter'];
                }

                if (count($this->plugins) > 0) {
                    $this->onBeforeReadEvent();
                }

                $profiler_message .= 'Class: ' . $service_class
                    . ' Method ' . $service_class_query_method
                    . ' Model Name ' . $this->model_registry['model_name']
                    . ' Method parameter ' . $method_parameter
                    . ' Query Object ' . $this->model_registry['data_object'];

                echo $profiler_message . '<br /><br />';

                $this->query_results = Services::$service_class()
                    ->$service_class_query_method(
                    $this->model_registry['model_name'],
                    $method_parameter,
                    $this->model_registry['data_object']
                );
            }
        }

        if (count($this->plugins) > 0) {
            $this->onAfterReadEvent(
                $this->model_registry['pagination_total'],
                $this->model_registry['model_offset'],
                $this->model_registry['model_count']
            );
        }

        if ($this->model_registry['data_object_type'] == DATABASE_LITERAL) {
        } else {
            return $this->query_results;
        }

        if ($this->model_registry['query_object'] == QUERY_OBJECT_RESULT
            || $this->model_registry['query_object'] == QUERY_OBJECT_DISTINCT
        ) {
            return $this->query_results;
        }

        if ($this->model_registry['query_object'] == QUERY_OBJECT_LIST) {

            if (Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_output_queries_query_results', 0) == 1) {
                $profiler_message .= 'DisplayController->getData Query Results <br /><br />';

                ob_start();
                echo '<pre>';
                var_dump($this->query_results);
                echo '</pre><br /><br />';

                $profiler_message .= ob_get_contents();
                ob_end_clean();
echo $profiler_message;
                die;
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
     * Get the list of potential plugins identified with this model (used to filter registered plugins)
     *
     * @return  void
     * @since   1.0
     */
    protected function getPluginList()
    {
        $this->plugins = array();

        if (defined('APPLICATION_ID')) {
        } else {
            return;
        }

        if ($this->model_registry['data_object'] == QUERY_OBJECT_RESULT) {
            return;
        }

        $dPlugins = array();
        if ((int)$this->model_registry['process_plugins'] == 1) {

            $dPlugins = $this->model_registry['plugins'];

            if (is_array($dPlugins)) {
            } else {
                $dPlugins = array();
            }
        }

        $tPlugins = array();
        if ((int)$this->model_registry['process_template_plugins']) {

            if ((int) $this->model_registry['process_template_plugins'] == 0) {
                $temp = array();

            } else {
                $tPlugins = Services::Registry()->get(
                    $this->model_registry['process_template_plugins'], 'plugins', array());

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

        $page_type = $this->model_registry['criteria_catalog_page_type'];
        if ($page_type == '') {
        } else {
            $temp[] = 'Pagetype' . strtolower($page_type);
        }

        $template = $this->model_registry['template_view_path_node'];
        if ($template == '') {
        } else {
            $temp[] = $template;
        }

        if ((int)$this->model_registry['process_plugins'] == 0 && count($temp) == 0) {
            $this->plugins = array();
            return;
        }

        $temp[] = 'Application';

        $this->plugins = array_unique($temp);

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
        if (isset($this->model_registry['primary_key_value'])) {
            $primary_key_value = (int)$this->model_registry['primary_key_value'];
        } else {
            $primary_key_value = 0;
        }
        $this->model_registry['primary_key_value'] = $primary_key_value;

        if (isset($this->model_registry['name_key_value'])) {
            $name_key_value = $this->model_registry['name_key_value'];
        } else {
            $name_key_value = '';
        }
        $this->model_registry['name_key_value'] = $name_key_value;

        if ($this->model_registry['data_object'] == QUERY_OBJECT_ITEM
            || $this->model_registry['data_object'] == QUERY_OBJECT_RESULT
        ) {
        } else {
            $primary_key_value = 0;
            $name_key_value = '';
        }
        $this->model_registry['primary_key_value'] == $primary_key_value;
        $this->model_registry['name_key_value'] == $name_key_value;

        $this->model->setBaseQuery(
            $this->model_registry[strtolower(FIELDS_LITERAL)],
            $this->model_registry['table_name'],
            $this->model_registry['primary_prefix'],
            $this->model_registry['primary_key'],
            $this->model_registry['primary_key_value'],
            $this->model_registry['name_key'],
            $this->model_registry['name_key_value'],
            $this->model_registry['data_object'],
            $this->model_registry['model_registry_name']
        );

        if ((int)$this->model_registry['check_view_level_access'] == 1) {
            $this->model->checkPermissions(
                $this->model_registry['primary_prefix'],
                $this->model_registry['primary_key_value'],
                $this->model_registry['data_object']
            );
        }

        if ((int)$this->model_registry['use_special_joins'] == 1) {
            $joins = $this->model_registry['joins'];
            if (count($joins) > 0) {
                $this->model->useSpecialJoins(
                    $joins,
                    $this->model_registry['primary_prefix'],
                    $this->model_registry['data_object']
                );
            }
        }

        $this->model->setModelCriteria(
            $this->get('criteria_catalog_type_id'),
            $this->get('criteria_extension_instance_id'),
            $this->get('primary_prefix')
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
                $this->model_registry['data_object'],
                $this->model_registry['model_offset'],
                $this->model_registry['model_count'],
                $this->model_registry['use_pagination']
            )
        );

        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_output_queries_sql') == 1) {
            Services::Profiler()->set(
                'Controller runQuery: <br /><br />' . $this->model->query->__toString(),
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

        if ($this->model_registry['data_object'] == QUERY_OBJECT_RESULT
            || $this->model_registry['data_object'] == QUERY_OBJECT_DISTINCT) {

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
                $this->model_registry['data_object']
            );

        return;
    }

    /**
     * Adds Custom Fields and Children to Query Results
     *
     * @param   $query_results
     *
     * @return  bool
     * @since   1.0
     */
    public function addCustomFields($query_results, $external = 0)
    {
        if (count($this->model_registry['data_object']) > 0) {
        } else {
            return false;
        }

        $q = array();

        foreach ($query_results as $results) {

            if ((int)$this->model_registry['get_customfields'] == 0) {
            } else {
                $customFieldTypes = Services::Registry()->get(
                    $this->model_registry['model_registry_name'],
                    CUSTOMFIELDGROUPS_LITERAL
                );

                if (count($customFieldTypes) == 0 || $customFieldTypes == null) {
                } else {

                    foreach ($customFieldTypes as $customFieldName) {

                        $results =
                            $this->model->addCustomFields(
                                $this->model_registry['model_registry_name'],
                                $customFieldName,
                                Services::Registry()->get(
                                    $this->model_registry['model_registry_name'],
                                    $customFieldName
                                ),
                                $this->model_registry['get_customfields'],
                                $results,
                                $this->model_registry['data_object']
                            );
                    }
                }

                if ((int)$this->model_registry['get_item_children'] == 1) {

                    $children = $this->model_registry['Children'];

                    if (count($children) > 0) {
                        $results = $this->model->addItemChildren(
                            $children,
                            (int)$this->model_registry['id'],
                            $results
                        );
                    }
                }
            }

            $q[] = $results;
        }

        /** Just hijacking this to build registry special fields for specific extension (from saved extension registry) */
        /** todo: figure out what the heck i meant by that comment. */
        if ($external == 1) {
            if (is_array($q)) {
                return $q[0];
            }
            return $q;
        }

        return $q;
    }

    /**
     * Schedule Event onAfterSetDataobject Event - for post-connection, data object specific logic
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onAfterSetDataobjectEvent()
    {
        $arguments = array(
            'model_registry' => $this->model_registry,
            'model' => $this->model
        );

        $arguments = Services::Event()->scheduleEvent('onAfterSetDataobject', $arguments, $this->plugins);

        if ($arguments === false) {
            throw new \Exception('Controller: onAfterSetDataobject Failed for Data Object: '
                . $this->model_registry['data_object']);
        }

        if (isset($arguments['model_registry_name'])) {
            $this->parameters = $arguments['model_registry_name'];
        }

        if (isset($arguments['model'])) {
            $this->model = $arguments['model'];
        }

        return true;
    }

    /**
     * Schedule Event onBeforeRead Event - modify query and parameters for rendering
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onBeforeReadEvent()
    {
        if (count($this->plugins) == 0
            || (int)$this->model_registry['process_plugins'] == 0
        ) {
            return true;
        }

        $arguments = array(
            'model_registry' => $this->model_registry['model_registry_name'],
            'parameters' => $this->parameters,
            'model_name' => $this->model_registry['model_name'],
            'model_type' => $this->model_registry['model_type'],
            'db' => $this->model->db,
            'query' => $this->model->query,
            'null_date' => $this->model->null_date,
            'now' => $this->model->now
        );

        $arguments = Services::Event()->scheduleEvent('onBeforeRead', $arguments, $this->plugins);

        if ($arguments === false) {
            return false;
        }

        if (isset($arguments[strtolower(PARAMETERS_LITERAL)])) {
            $this->parameters = $arguments[strtolower(PARAMETERS_LITERAL)];
        }
        if (isset($arguments['db'])) {
            $this->model->db = $arguments['db'];
        }
        if (isset($arguments['query'])) {
            $this->model->db = $arguments['query'];
        }
        if (isset($arguments['null_date'])) {
            $this->model->db = $arguments['null_date'];
        }
        if (isset($arguments['now'])) {
            $this->model->db = $arguments['now'];
        }

        return true;
    }

    /**
     * Schedule Event onAfterRead Event - could update parameters and query_results objects
     *
     * @return  bool
     * @since   1.0
     */
    protected function onAfterReadEvent()
    {
        if (count($this->plugins) == 0
            || (int)$this->model_registry['process_plugins'] == 0
        ) {
            return true;
        }

        $items = $this->query_results;
        $this->query_results = array();

        $this->parameters['model_offset'] = $this->model_registry['model_offset'];
        $this->parameters['model_count'] = $this->model_registry['model_count'];
        $this->parameters['pagination_total'] = $this->model_registry['pagination_total'];

        $first = true;

        if (count($items) == 0) {
        } else {
            foreach ($items as $item) {

                $arguments = array(
                    'first' => true,
                    'model_registry' => $this->model_registry,
                    'parameters' => $this->parameters,
                    'data' => $this->query_results
                );

                $this->parameters = array();
                $this->model_registry = array();
                $this->query_results = array();

                $arguments = Services::Event()->scheduleEvent('onAfterRead', $arguments, $this->plugins);

                if ($arguments === false) {
                    return false;
                }

                if (isset($arguments['model_registry'])) {
                    $this->model_registry = $arguments['model_registry'];
                }

                if (isset($arguments[strtolower(PARAMETERS_LITERAL)])) {
                    $this->parameters = $arguments[strtolower(PARAMETERS_LITERAL)];
                }

                if (isset($arguments['data'])) {
                    $this->query_results = $arguments['data'];
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
     * @return  bool
     * @since   1.0
     */
    protected function onAfterReadallEvent()
    {
        $arguments = array(
            'model_registry' => $this->model_registry,
            'parameters' => $this->parameters,
            'data' => $this->query_results
        );

        $this->parameters = array();
        $this->model_registry = array();
        $this->query_results = array();

        $arguments = Services::Event()->scheduleEvent('onAfterReadall', $arguments, $this->plugins);

        if ($arguments === false) {
            return false;
        }

        if (isset($arguments['model_registry'])) {
            $this->model_registry = $arguments['model_registry'];
        }

        if (isset($arguments[strtolower(PARAMETERS_LITERAL)])) {
            $this->parameters = $arguments[strtolower(PARAMETERS_LITERAL)];
        }

        if (isset($arguments['data'])) {
            $this->query_results = $arguments['data'];
        }

        return true;
    }
}
