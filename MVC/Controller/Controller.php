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
     * Stores various extension-specific key/value pairs - public
     *
     * @var    array
     * @since  1.0
     */
    public $parameters = array();

    /**
     * Model Instance - Public as it is passed into events
     *
     * @var    object
     * @since  1.0
     */
    public $model;

    /**
     * Model Registry - Public as it is passed into events
     *
     * @var    object
     * @since  1.0
     */
    public $model_registry;

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
    public function getModelRegistry($model_type = 'Datasource', $model_name = null, $model_class = 'ReadModel')
    {
        $this->set('data_object_set', 0);

        if ($model_type == '') {
            $model_type = 'Datasource';
        }
        if ($model_class == '') {
            $model_class = 'ReadModel';
        }

        $model_type = ucfirst(strtolower($model_type));
        $model_name = ucfirst(strtolower($model_name));
        $model_registry = $model_name . $model_type;

        $this->set('model_type', $model_type);
        $this->set('model_name', $model_name);
        $this->set('model_registry', $model_registry);

echo 'Type: ' . $model_type . ' Name: ' . $model_name . ' Registry: ' . $model_registry . '<br />';
        $profiler_message = '';

        if (Services::Registry()->exists($model_registry) === true) {
            $profiler_message = ' Registry ' . $model_registry . ' retrieved from Registry. <br />';

        } else {

            $cached_output = Services::Cache()->get('Model', $model_registry);

            if ($cached_output === false) {

                ConfigurationService::getModel($model_type, $model_name);
                $cache_it = Services::Registry()->getArray($model_registry, false);
                Services::Cache()->set('Model', $model_registry, $cache_it);
                $profiler_message = ' Registry ' . $model_registry . ' processed by ConfigurationService';

            } else {

                Services::Registry()->createRegistry($model_registry);
                Services::Registry()->loadArray($model_registry, $cached_output);
                $profiler_message = ' Registry ' . $model_registry . ' loaded from Cache. ';
            }
        }

        if (Services::Registry()->get('Configuration', 'profiler_output_queries_table_registry') == 0) {
        } else {
            ob_start();
            Services::Registry()->get($model_registry, '*');
            $profiler_message .= ob_get_contents();
            ob_end_clean();
        }

        Services::Profiler()->set($profiler_message, LOG_OUTPUT_QUERIES, VERBOSE);

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

        $registry = Services::Registry()->getArray($this->get('model_registry'));
        $data_object = Services::Registry()->get($this->get('model_registry'), 'data_object');
        $this->set('data_object', ucfirst(strtolower($data_object)));

        if ($data_object == 'Database') {
            $defaults = Services::Registry()->get('Fields', 'ModelattributesDefaults');
            foreach (Services::Registry()->get('Fields', 'Modelattributes') as $key) {
                if (isset($registry[$key])) {
                    $this->set($key, $registry[$key]);
                } else {
                    $this->set($key, $defaults[$key]);
                }
            }

        }  else {
            $defaults = Services::Registry()->get('Fields', 'DataObjectAttributeDefaults');
            foreach (Services::Registry()->get('Fields', 'DataObjectAttributes') as $key) {
                if (isset($registry[$key])) {
                    $this->set($key, $registry[$key]);
                } else {
                    $this->set($key, $defaults[$key]);
                }
            }
        }

        $modelClass = 'Molajo\\MVC\\Model\\' . $this->get('model_class', 'ReadModel');

        try {
            $this->model = new $modelClass();

        } catch (\Exception $e) {
            throw new \RuntimeException('Model entry failed. Error: ' . $e->getMessage());
        }

        if ($this->model->get('data_object') === null
            || $this->model->get('data_object') === false
        ) {
            $this->model->set('data_object', 'Database');
        }

        if ($this->get('data_object') == 'Database') {
        } else {
            $this->model->db = NULL;
            $this->model->query = NULL;
            $this->model->null_date = NULL;
            $this->model->now = NULL;
            return;
        }

        if ($this->model->get('service_class', 'Database') == 'Database') {

            $service_class = $this->get('service_class', 'Database');

            $this->model->db = Services::$service_class()->connect();

            $this->model->set('query', Services::$service_class()->getQuery());
            $this->model->set('null_date', $this->model->db->getNullDate());

            try {
                $this->model->set('now', Services::Date()->getDate());


            } catch (\Exception $e) {
                // ignore error due to Date Service activation later in sequence
                $this->model->set('now', $this->model->get('null_date'));
            }
        }

        return;
    }

    /**
     * Method to execute model methods and returns results
     *
     * @param    string   $query_object - result, item, list, distinct (for datalist)
     *
     * @return   mixed    Depends on QueryObject selected
     * @since    1.0
     *
     * @throws   \RuntimeException
     */
    public function getData($query_object = QUERY_OBJECT_LIST)
    {
        if ($this->get('data_object_set') === 0) {
            $this->setDataobject();
        }

        if ($this->get('data_object') === false
            || $this->get('data_object') === null
        ) {
            echo 'Data Object for Model Registry: '
                . $this->get('model_registry') . ' could not be loaded. <br />';
            //throw error
            die;
        }

        $query_object = strtolower($query_object);

        $this->set('pagination_total', 0);

        if (in_array(
            $query_object,
            array(QUERY_OBJECT_RESULT, QUERY_OBJECT_ITEM, QUERY_OBJECT_LIST, QUERY_OBJECT_DISTINCT)
        )
        ) {
        } else {
            $query_object = QUERY_OBJECT_LIST;
        }

        if ($this->get('data_object') == 'Database') {
            $this->prepareQuery($query_object);
        }

        $this->getPluginList($query_object);
        $profiler_message =
            ' <br />Data Object: ' . $this->get('data_object')
                . ' <br />Model Type: ' . $this->get('model_type')
                . ' <br />Model Name: ' . $this->get('model_name')
                . ' <br />Model Query Object: ' . $this->get('model_query_object')
                . ' <br />Non-DB Services Class: ' . $this->get('service_class')
                . ' <br />Non-DB Services Method: ' . $this->get('service_class_query_method')
                . ' <br />Non-DB Services Method Parameter: ' . $this->get('service_class_query_method_parameter')
                . ' <br />Registry Entry (Datalist parameter): ' . $this->get('registry_entry')
                . ' <br />Template View: ' . $this->get('template_view_path_node')
                . ' <br />Process Plugins: ' . (int)$this->get('process_plugins') . '<br /><br />';
echo $profiler_message;

        if ($this->get('data_object') == 'Database') {

            if (count($this->plugins) > 0) {
                $this->onBeforeReadEvent();
            }

            $this->runQuery($query_object);

        } else {

            if (strtolower($this->get('model_name')) == 'dummy') {
                $this->query_results = array();

            } else {
                $method_parameter = NULL;
                $service_class = $this->get('service_class');
                $service_class_query_method = $this->get('service_class_query_method');
                $service_class_query_method_parameter = $this->get('service_class_query_method_parameter');

                if ($service_class_query_method_parameter == 'REGISTRY_ENTRY') {
                    $method_parameter = $this->get('registry_entry');

                } elseif ($service_class_query_method_parameter == 'TEMPLATE_VIEW_NAME') {
                    $method_parameter = $this->get('template_view_path_node');

                } else {
                    $method_parameter = $service_class_query_method_parameter;
                }

                if (strtolower($this->get('model_name')) == 'parameters') {
                    $query_object = QUERY_OBJECT_ITEM;
                }

                if (count($this->plugins) > 0) {
                    $this->onBeforeReadEvent();
                }

                $this->query_results = Services::$service_class()
                    ->$service_class_query_method(
                        $this->get('model_name'),
                        $method_parameter,
                        $query_object
                    );

                if (strtolower($this->get('template_view_path_node')) == 'commentsxxx') {
                    echo $query_object;
                    echo '<pre>';
                    echo count($this->query_results);
                    var_dump($this->query_results);
                    echo '</pre>';
                }
            }
        }

        if (count($this->plugins) > 0) {
            $this->onAfterReadEvent(
                $this->get('pagination_total'),
                $this->get('model_offset'),
                $this->get('model_count')
            );
        }

        if (strtolower($this->get('template_view_path_node')) == 'gridorderingXXX') {
            echo '<pre>';
            echo count($this->query_results);
            echo '</pre>';
        }

        if ($this->get('data_object') == 'Database') {
        } else {
            return $this->query_results;
        }

        if ($query_object == QUERY_OBJECT_RESULT
            || $query_object == QUERY_OBJECT_DISTINCT
        ) {
            return $this->query_results;
        }

        if ($query_object == QUERY_OBJECT_LIST) {

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
     * @param    $query_object
     *
     * @return   void
     * @since    1.0
     */
    protected function getPluginList($query_object)
    {
        $this->plugins = array();

        if (defined('APPLICATION_ID')) {
        } else {
            return;
        }

        if ($query_object == QUERY_OBJECT_RESULT) {
            return;
        }

        $dataSourcePlugins = array();
        if ((int)$this->get('process_plugins') == 1) {

            $dataSourcePlugins = Services::Registry()->get($this->get('model_registry'), 'plugins', array());

            if (is_array($dataSourcePlugins)) {
            } else {
                $dataSourcePlugins = array();
            }
        }

        $templatePlugins = array();
        if ((int)$this->get('process_template_plugins') == 1) {

            if ($this->get('template_view_model_registry') == $this->get('model_registry')) {
                $temp = array();

            } else {
                $templatePlugins = Services::Registry()->get(
                    $this->get('template_view_model_registry'),
                    'plugins',
                    array()
                );

                if (is_array($templatePlugins)) {
                } else {
                    $templatePlugins = array();
                }
            }
        }

        $temp = array_merge($dataSourcePlugins, $templatePlugins);
        if (is_array($temp)) {
        } else {
            $temp = array();
        }

        $page_type = $this->get('catalog_page_type');
        if ($page_type == '') {
        } else {
            $temp[] = 'Pagetype' . strtolower($page_type);
        }

        $template = $this->get('template_view_path_node');
        if ($template == '') {
        } else {
            $temp[] = $template;
        }

        if ((int)$this->get('process_plugins') == 0
            && count($temp) == 0
        ) {
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
     * @param   string  $query_object
     *
     * @return  bool
     * @since   1.0
     */
    protected function prepareQuery($query_object = QUERY_OBJECT_LIST)
    {
        if ($query_object == QUERY_OBJECT_ITEM
            || $query_object == QUERY_OBJECT_RESULT
        ) {

            $id_key_value = (int)$this->get('id', 0);
            $name_key_value = (string)$this->get('name_key_value', '');

        } else {
            $id_key_value = 0;
            $name_key_value = '';
        }

        $this->model->setBaseQuery(
            Services::Registry()->get($this->get('model_registry'), 'Fields'),
            $this->get('table_name'),
            $this->get('primary_prefix'),
            $this->get('primary_key'),
            $id_key_value,
            $this->get('name_key'),
            $name_key_value,
            $query_object,
            Services::Registry()->get($this->get('model_registry'), 'Criteria')
        );

        if ((int)$this->get('check_view_level_access') == 1) {
            $this->model->checkPermissions(
                $this->get('primary_prefix'),
                $this->get('primary_key'),
                $query_object
            );
        }

        if ((int)$this->get('use_special_joins') == 1) {
            $joins = Services::Registry()->get($this->get('model_registry'), 'Joins');
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
     * @param   string  $query_object
     *
     * @return  bool
     * @since   1.0
     */
    protected function runQuery($query_object = QUERY_OBJECT_LIST)
    {
        if ($this->get('model_offset') == 0
            && $this->get('model_count') == 0) {

            if ($query_object == QUERY_OBJECT_RESULT) {
                $this->set('model_offset', 0);
                $this->get('model_count', 1);
                $this->get('use_pagination', 0);

            } elseif ($query_object == QUERY_OBJECT_DISTINCT) {
                $this->set('model_offset', 0);
                $this->set('model_count', 999999);
                $this->set('use_pagination', 0);

            } else {
                $this->set('model_offset', 0);
                $this->set('model_count', 15);
                $this->set('use_pagination', 1);
            }
        }

        $this->set(
            'pagination_total',
            (int)$this->model->getQueryResults(
                $query_object,
                $this->get('model_offset'),
                $this->get('model_count'),
                $this->get('use_pagination')
            )
        );

        if (Services::Registry()->get('Configuration', 'profiler_output_queries_sql') == 1) {
            Services::Profiler()->set(
                'DisplayController->getData SQL Query: <br /><br />'
                    . $this->model->query->__toString(),
                LOG_OUTPUT_RENDERING
            );
        }

        /** Retrieve query results from Model */
        $query_results = $this->model->get('query_results');

//echo '<br /><br /><pre>';
//echo $this->model->query->__toString();
//echo '<br /><br />';
//var_dump($query_results);
//echo '</pre><br /><br />';

        if ($query_object == QUERY_OBJECT_RESULT || $query_object == QUERY_OBJECT_DISTINCT) {

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
     * @param   $query_results
     * @param   $query_object
     *
     * @return  bool
     * @since   1.0
     */
    public function addCustomFields($query_results, $query_object, $external = 0)
    {
        if (count($query_results) > 0) {
        } else {
            return false;
        }

        $q = array();

        foreach ($query_results as $results) {

            if ((int)$this->get('get_customfields') == 0) {
            } else {

                $customFieldTypes = Services::Registry()->get($this->get('model_registry'), 'CustomFieldGroups');

                if (count($customFieldTypes) == 0 || $customFieldTypes == null) {
                } else {

                    foreach ($customFieldTypes as $customFieldName) {
                        $results =
                            $this->model->addCustomFields(
                                $this->get('model_registry'),
                                $customFieldName,
                                Services::Registry()->get($this->get('model_registry'), $customFieldName),
                                $this->get('get_customfields'),
                                $results,
                                $query_object
                            );
                    }
                }

                if ((int)$this->get('get_item_children') == 1) {

                    $children = Services::Registry()->get($this->get('model_registry'), 'Children');

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
     * Schedule Event onBeforeRead Event - could update model and parameter objects
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

        $arguments = array(
            'model_registry' => $this->get('model_registry'),
            'db' => $this->model->db,
            'query' => $this->model->query,
            'null_date' => $this->model->null_date,
            'now' => $this->model->now,
            'parameters' => $this->parameters,
            'model_name' => $this->get('model_name'),
            'model_type' => $this->get('model_type')
        );

        Services::Profiler()->set(
            'DisplayController->onBeforeReadEvent '
                . $this->get('model_registry')
                . ' Schedules onBeforeRead',
            LOG_OUTPUT_PLUGINS,
            VERBOSE
        );

        $arguments = Services::Event()->scheduleEvent('onBeforeRead', $arguments, $this->plugins);

        if ($arguments === false) {
            Services::Profiler()->set(
                'DisplayController->onBeforeReadEvent '
                    . $this->get('model_registry')
                    . ' failure ',
                LOG_OUTPUT_PLUGINS
            );

            return false;
        }

        Services::Profiler()->set(
            'DisplayController->onBeforeReadEvent '
                . $this->get('model_registry')
                . ' successful ',
            LOG_OUTPUT_PLUGINS,
            VERBOSE
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
     * Schedule Event onAfterRead Event - could update parameters and query_results objects
     *
     * @return  bool
     * @since   1.0
     */
    protected function onAfterReadEvent()
    {
        if (count($this->plugins) == 0
            || (int)$this->get('process_plugins') == 0
            || count($this->query_results) == 0
        ) {
            return true;
        }

        $items = $this->query_results;
        $this->query_results = array();

        $this->parameters['model_offset'] = $this->get('model_offset');
        $this->parameters['model_count'] = $this->get('model_count');
        $this->parameters['pagination_total'] = $this->get('pagination_total');

        $first = true;

        if (count($items) == 0 || $items === false || $items === null) {
        } else {
            foreach ($items as $item) {

                $arguments = array(
                    'model_registry' => $this->get('model_registry'),
                    'parameters' => $this->parameters,
                    'data' => $item,
                    'model_name' => $this->get('model_name'),
                    'model_type' => $this->get('model_type'),
                    'first' => $first
                );

                Services::Profiler()->set(
                    'DisplayController->onAfterReadEvent '
                        . $this->get('model_registry')
                        . ' Schedules onAfterRead',
                    LOG_OUTPUT_PLUGINS,
                    VERBOSE
                );

                $arguments = Services::Event()->scheduleEvent('onAfterRead', $arguments, $this->plugins);

                if ($arguments === false) {

                    Services::Profiler()->set(
                        'DisplayController->onAfterRead '
                            . $this->get('model_registry')
                            . ' failure ',
                        LOG_OUTPUT_PLUGINS
                    );

                    return false;
                }

                Services::Profiler()->set(
                    'DisplayController->onAfterReadEvent '
                        . $this->get('model_registry')
                        . ' successful ',
                    LOG_OUTPUT_PLUGINS,
                    VERBOSE
                );

                $this->parameters = $arguments['parameters'];
                $this->query_results[] = $arguments['data'];
                $first = false;
            }
        }

        $arguments = array(
            'model_registry' => $this->get('model_registry'),
            'parameters' => $this->parameters,
            'data' => $this->query_results,
            'model_type' => $this->get('model_type'),
            'model_name' => $this->get('model_name')
        );

        Services::Profiler()->set(
            'DisplayController->onAfterReadEventAll '
                . $this->get('model_registry')
                . ' Schedules onAfterReadall',
            LOG_OUTPUT_PLUGINS,
            VERBOSE
        );

        $arguments = Services::Event()->scheduleEvent('onAfterReadall', $arguments, $this->plugins);

        if ($arguments === false) {
            Services::Profiler()->set(
                'DisplayController->onAfterReadall '
                    . $this->get('model_registry')
                    . ' failure ',
                LOG_OUTPUT_PLUGINS
            );

            return false;
        }

        Services::Profiler()->set(
            'DisplayController->onAfterReadEventAll '
                . $this->get('model_registry')
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
