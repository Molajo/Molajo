<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\Configuration;

use Molajo\Frontcontroller;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Configuration
 *
 * @package     Niambie
 * @subpackage  Service
 * @since       1.0
 */
Class ConfigurationService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Valid Data Object Types
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_dataobject_types;

    /**
     * Valid Data Object Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_dataobject_attributes;

    /**
     * Valid Model Types
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_model_types;

    /**
     * Valid Model Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_model_attributes;

    /**
     * Valid Data Types
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_data_types;

    /**
     * Valid Query Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_queryelements_attributes;

    /**
     * Valid Field Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_field_attributes;

    /**
     * Valid Join Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_join_attributes;

    /**
     * Valid Foreignkey Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_foreignkey_attributes;

    /**
     * Valid Criteria Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_criteria_attributes;

    /**
     * Valid Children Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_children_attributes;

    /**
     * Valid Plugin Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_plugin_attributes;

    /**
     * Valid Value Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_value_attributes;

    /**
     * getInstance
     *
     * @static
     * @return  bool|object
     * @since   1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ConfigurationService();
        }

        return self::$instance;
    }

    /**
     * Retrieve Site and Application data, set constants and paths
     *
     * @return  object
     * @since   1.0
     */
    public function __construct()
    {
        $this->getFieldProperties();
        $this->getApplication();
        $this->setSitePaths();

        return $this;
    }

    /**
     * Retrieve and load valid properties for fields, data models and data objects
     *
     * @return  object
     * @throws  \Exception
     * @since   1.0
     */
    protected function getFieldProperties()
    {
        Services::Registry()->createRegistry('Fields');

        $xml = ConfigurationService::getFile('Application', 'Fields');
        if ($xml === false) {
            throw new \Exception('Configuration: getFieldProperties File not found.');
        }

        ConfigurationService::loadFieldProperties($xml, 'dataobjecttypes', 'dataobjecttype');

        ConfigurationService::loadFieldPropertiesWithAttributes($xml, 'dataobjectattributes', 'dataobjectattribute');

        ConfigurationService::loadFieldProperties($xml, 'modeltypes', 'modeltype');

        ConfigurationService::loadFieldPropertiesWithAttributes($xml, 'modelattributes', 'modelattribute');

        ConfigurationService::loadFieldProperties($xml, 'datatypes', 'datatype');

        ConfigurationService::loadFieldProperties($xml, 'queryelements', 'queryelement');

        $list = Services::Registry()->get(FIELDS_LITERAL, 'queryelements');
        foreach ($list as $item) {
            $field = explode(',', $item);
            ConfigurationService::loadFieldProperties($xml, $field[0], $field[1]);
        }

        self::$valid_dataobject_types = Services::Registry()->get(FIELDS_LITERAL, 'dataobjecttypes');
        self::$valid_dataobject_attributes = Services::Registry()->get(FIELDS_LITERAL, 'dataobjectattributes');

        self::$valid_model_types = Services::Registry()->get(FIELDS_LITERAL, 'modeltypes');
        self::$valid_model_attributes = Services::Registry()->get(FIELDS_LITERAL, 'modelattributes');

        self::$valid_data_types = Services::Registry()->get(FIELDS_LITERAL, 'datatypes');
        self::$valid_field_attributes = Services::Registry()->get(FIELDS_LITERAL, FIELDS_LITERAL);

        self::$valid_join_attributes = Services::Registry()->get(FIELDS_LITERAL, 'joins');
        self::$valid_foreignkey_attributes = Services::Registry()->get(FIELDS_LITERAL, 'foreignkeys');
        self::$valid_criteria_attributes = Services::Registry()->get(FIELDS_LITERAL, 'criterion');
        self::$valid_children_attributes = Services::Registry()->get(FIELDS_LITERAL, 'children');
        self::$valid_plugin_attributes = Services::Registry()->get(FIELDS_LITERAL, 'plugins');
        self::$valid_value_attributes = Services::Registry()->get(FIELDS_LITERAL, 'values');

        $datalistsArray = array();
        $extensionArray = array();
        $datalistsArray = ConfigurationService::loadDatalists($datalistsArray, PLATFORM_MVC . '/Model/Datalist');
        $extensionArray = ConfigurationService::loadDatalists($datalistsArray, EXTENSIONS . '/Model/Datalist');
        array_merge($datalistsArray, $extensionArray);
        sort($datalistsArray);
        $datalistsArray = array_unique($datalistsArray);

        Services::Registry()->set(FIELDS_LITERAL, 'Datalists', $datalistsArray);

        return;
    }

    /**
     * loadFieldProperties
     *
     * @param   $input
     * @param   $plural
     * @param   $singular
     *
     * @return  bool
     * @since   1.0
     */
    protected function loadFieldProperties($xml, $plural, $singular)
    {
        if (isset($xml->$plural->$singular)) {
        } else {
            return false;
        }

        $types = $xml->$plural->$singular;
        if (count($types) === 0) {
            return false;
        }

        $typeArray = array();
        foreach ($types as $type) {
            $typeArray[] = (string)$type;
        }

        Services::Registry()->set(FIELDS_LITERAL, $plural, $typeArray);

        return true;
    }

    /**
     * loadFieldPropertiesWithAttributes
     *
     * @param   $input
     * @param   $plural
     * @param   $singular
     *
     * @return  bool
     * @since   1.0
     */
    protected function loadFieldPropertiesWithAttributes($xml, $plural, $singular)
    {
        if (isset($xml->$plural->$singular)) {
        } else {
            return false;
        }

        $typeArray = array();
        $typeDefaultArray = array();
        foreach ($xml->$plural->$singular as $type) {
            $typeArray[] = (string)$type['name'];
            $typeDefaultArray[(string)$type['name']] = (string)$type['default'];
        }

        Services::Registry()->set(FIELDS_LITERAL, $plural, $typeArray);
        Services::Registry()->set(FIELDS_LITERAL, $plural . 'Defaults', $typeDefaultArray);

        return true;
    }

    /**
     * loadDatalists
     *
     * @param   $datalistsArray
     * @param   $folder
     *
     * @return  array
     * @since   1.0
     * @throws  \Exception
     */
    protected function loadDatalists($datalistsArray, $folder)
    {
        try {

            $dirRead = dir($folder);

            $path = $dirRead->path;

            while (false !== ($entry = $dirRead->read())) {
                if (is_dir($path . '/' . $entry)) {
                } else {
                    $datalistsArray[] = substr($entry, 0, strlen($entry) - 4);
                }
            }

            $dirRead->close();

        } catch (\Exception $e) {
            throw new \Exception('Configuration: Cannot find Datalists file for folder: ' . $folder);
        }

        return $datalistsArray;
    }

    /**
     * Get the application data and store it in the registry
     *
     * @return  ConfigurationService
     * @since   1.0
     * @throws  \Exception
     */
    protected function getApplication()
    {
        if (APPLICATION == 'installation') {

            Services::Registry()->set('Configuration', 'application_id', 0);
            Services::Registry()->set('Configuration', 'application_catalog_type_id', CATALOG_TYPE_APPLICATION);
            Services::Registry()->set('Configuration', 'application_name', APPLICATION);
            Services::Registry()->set('Configuration', 'application_description', APPLICATION);
            Services::Registry()->set('Configuration', 'application_path', APPLICATION);

        } else {

            try {
                $profiler_service = 0;

                $controllerClass = CONTROLLER_CLASS;
                $controller = new $controllerClass();
                $controller->getModelRegistry('Datasource', 'Application', 1);
                $controller->set('name_key_value', APPLICATION, 'model_registry');
                $item = $controller->getData(QUERY_OBJECT_ITEM);
                if ($item === false) {
                    throw new \Exception ('ConfigurationService: Error executing getApplication Query');
                }

                Services::Registry()->set('Configuration', 'application_id', (int)$item->id);
                Services::Registry()->set(
                    'Configuration',
                    'application_catalog_type_id',
                    (int)$item->catalog_type_id
                );
                Services::Registry()->set('Configuration', 'application_name', $item->name);
                Services::Registry()->set('Configuration', 'application_path', $item->path);
                Services::Registry()->set('Configuration', 'application_description', $item->description);

                $profiler_service = 0;

                $parameters = Services::Registry()->getArray('ApplicationDatasourceParameters');
                foreach ($parameters as $key => $value) {
                    Services::Registry()->set('Configuration', $key, $value);
                }

                $metadata = Services::Registry()->getArray('ApplicationDatasourceMetadata');
                if (count($metadata) > 0) {
                    foreach ($metadata as $key => $value) {
                        Services::Registry()->set('Configuration', 'metadata_' . $key, $value);
                    }
                }

            } catch (\Exception $e) {
                throw new \Exception('Configuration: Exception caught in Configuration: '. $e->getMessage());
            }
        }

        Services::Registry()->sort('Configuration');
        Services::Profiler()->initialise();
        Services::Cache()->initialise();

        return $this;
    }

    /**
     * Establish media, cache, log, etc., locations for site for application use
     *
     * Called out of the Configurations Class construct - paths needed in startup process for other services
     *
     * @return  mixed
     * @since   1.0
     */
    protected function setSitePaths()
    {
        Services::Registry()->set('Configuration', 'site_base_url', BASE_URL);

        $path = Services::Registry()->get('Configuration', 'application_path', '');
        Services::Registry()->set('Configuration', 'application_base_url', BASE_URL . $path);

        if (defined('SITE_NAME')) {
        } else {
            define('SITE_NAME',
            Services::Registry()->get('Configuration', 'site_name', SITE_ID));
        }

        if (defined('SITE_CACHE_FOLDER')) {
        } else {
            define('SITE_CACHE_FOLDER', SITE_BASE_PATH
                . '/' . Services::Registry()->get('Configuration', 'system_cache_folder', 'cache'));
        }
        if (defined('SITE_LOGS_FOLDER')) {
        } else {

            define('SITE_LOGS_FOLDER', SITE_BASE_PATH
                . '/' . Services::Registry()->get('Configuration', 'system_logs_folder', 'logs'));
        }

        if (defined('SITE_MEDIA_FOLDER')) {
        } else {
            define('SITE_MEDIA_FOLDER', SITE_BASE_PATH
                . '/' . Services::Registry()->get('Configuration', 'system_media_folder', 'media'));
        }
        if (defined('SITE_MEDIA_URL')) {
        } else {
            define('SITE_MEDIA_URL', SITE_BASE_URL_RESOURCES
                . '/' . Services::Registry()->get('Configuration', 'system_media_url', 'media'));
        }

        if (defined('SITE_TEMP_FOLDER')) {
        } else {
            define('SITE_TEMP_FOLDER', SITE_BASE_PATH
                . '/' . Services::Registry()->get(
                'Configuration',
                'system_temp_folder',
                SITE_BASE_PATH . '/temp'
            ));
        }

        if (defined('SITE_TEMP_URL')) {
        } else {
            define('SITE_TEMP_URL', SITE_BASE_URL_RESOURCES
                . '/' . Services::Registry()->get('Configuration', 'system_temp_url', 'temp'));
        }

        return true;
    }

    /**
     * getFile locates file, reads it, and return the XML
     *
     * Usage:
     * Services::Configuration()->getFile('Application', 'defines');
     *
     * or - in classes where usage can happen before the service is activated:
     *      ConfigurationService::getFile($model_type, $model_name);
     *
     * @static
     * @param   string $model_name
     * @param   string $model_type
     *
     * @return  object $xml
     * @since   1.0
     */
    public static function getFile($model_type, $model_name)
    {
        $path_and_file = ConfigurationService::locateFile($model_type, $model_name);

        $xml_string = ConfigurationService::readXMLFile($path_and_file);

        return simplexml_load_string($xml_string);
    }

    /**
     * getModel loads registry for requested model resource
     *
     * Usage:
     * Services::Configuration()->getModel('Resource', 'Articles');
     *
     * or - in classes where usage can happen before the service is activated:
     *
     * ConfigurationService::getModel($model_type, $model_name);
     *
     * @static
     * @param   string  $model_name
     * @param   string  $model_type
     * @param   string  $parameter_registry
     *
     * @return  string  Name of the Model Registry object
     * @since   1.0
     *
     * @throws  \Exception
     */
    public static function getModel($model_type, $model_name, $parameter_registry)
    {
        $model_type = ucfirst(strtolower($model_type));
        $model_name = ucfirst(strtolower($model_name));
        $model_registry = $model_name . $model_type;

        if ($parameter_registry === null) {
            $parameter_registry = 'parameters';
        }

        if ($model_type == 'Dataobject') {
            return ConfigurationService::getDataobject($model_type, $model_name);
        }

        if (class_exists('Services')) {
            $exists = Services::Registry()->exists($model_registry);
            if ($exists === true) {
                return $model_registry;
            }
        }

        $path_and_file = ConfigurationService::locateFile($model_type, $model_name, $parameter_registry);
        if ($path_and_file === false) {
            throw new \Exception('Configuration: Cannot find XML file for Model Type: '
                . $model_type . ' Model Name: ' . $model_name . ' Located at ' . $path_and_file);

        }

        $xml_string = ConfigurationService::readXMLFile($path_and_file);

        $results = ConfigurationService::getIncludeCode($xml_string, $model_name);

        $xml = simplexml_load_string($results);
        if ($xml === false) {
            throw new \Exception('Configuration: getModel cannot process XML file for Model Type: '
                . $model_type . ' Model Name: ' . $model_name . ' Located at ' . $path_and_file);
        }

        if (isset($xml->model)) {
            $xml = $xml->model;
        }

        Services::Registry()->createRegistry($model_registry);

        ConfigurationService::inheritDefinition($model_registry, $xml);

        ConfigurationService::setModelRegistry($model_registry, $xml);

        Services::Registry()->set($model_registry, 'model_name', $model_name);
        Services::Registry()->set($model_registry, 'model_type', $model_type);
        Services::Registry()->set($model_registry, 'model_registry_name', $model_registry);

        $data_object = Services::Registry()->get($model_registry, 'data_object', '');

        if ($data_object == '') {
            $data_object = 'Database';
            Services::Registry()->set($model_registry, 'data_object', $data_object);
        }

        $dataObjectRegistry = ucfirst(strtolower($data_object)) . 'Dataobject';

        if (Services::Registry()->exists($dataObjectRegistry)) {
        } else {
            $controllerClass = CONTROLLER_CLASS;
            $controller = new $controllerClass();
            $controller->getModelRegistry('Dataobject', $data_object, 0);
        }

        foreach (Services::Registry()->get($dataObjectRegistry) as $key => $value) {
            Services::Registry()->set($model_registry, 'data_object_' . $key, (string)$value);
        }

        ConfigurationService::setElementsRegistry(
            $model_registry,
            $xml,
            'fields',
            'field',
            self::$valid_field_attributes
        );

        ConfigurationService::setElementsRegistry(
            $model_registry,
            $xml,
            'joins',
            'join',
            self::$valid_join_attributes
        );

        ConfigurationService::setElementsRegistry(
            $model_registry,
            $xml,
            'foreignkeys',
            'foreignkey',
            self::$valid_foreignkey_attributes
        );

        ConfigurationService::setElementsRegistry(
            $model_registry,
            $xml,
            'criteria',
            'where',
            self::$valid_criteria_attributes
        );

        ConfigurationService::setElementsRegistry(
            $model_registry,
            $xml,
            'children',
            'child',
            self::$valid_children_attributes
        );

        ConfigurationService::setElementsRegistry(
            $model_registry,
            $xml,
            'plugins',
            'plugin',
            self::$valid_plugin_attributes
        );

        ConfigurationService::setElementsRegistry(
            $model_registry,
            $xml,
            'values',
            'value',
            self::$valid_value_attributes
        );

        ConfigurationService::getCustomFields($xml, $model_registry);

        return $model_registry;
    }

    /**
     * getDataobject loads registry for requested resource
     *
     * Usage:
     * Services::Configuration()->getDataobject('Dataobject', 'Database');
     * Services::Configuration()->getDataobject('Dataobject', 'Assets');
     *
     * @static
     * @param   string $model_name
     * @param   string $model_type
     *
     * @return  string Name of the Dataobject Registry object
     * @since   1.0
     *
     * @throws  \Exception
     */
    public static function getDataobject($model_type, $model_name)
    {
        $model_registry = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

        if (class_exists('Services')) {
            $exists = Services::Registry()->exists($model_registry);
            if ($exists === true) {
                return $model_registry;
            }
        }

        $path_and_file = ConfigurationService::locateFile($model_type, $model_name);
        if ($path_and_file === false) {
            throw new \Exception('Configuration: getDataobject method Cannot find XML file for Model Type: '
                . $model_type . ' Model Name: ' . $model_name . ' Located at ' . $path_and_file);
        }

        $xml_string = ConfigurationService::readXMLFile($path_and_file);

        $results = ConfigurationService::getIncludeCode($xml_string, $model_name);

        $xml = simplexml_load_string($results);
        if ($xml === false) {
            throw new \Exception('Configuration: getDataobject cannot process XML file for Model Type: '
                . $model_type . ' Model Name: ' . $model_name . ' Located at ' . $path_and_file);
        }

        if (isset($xml->model)) {
            $xml = $xml->model;
        }

        Services::Registry()->createRegistry($model_registry);

        ConfigurationService::setDataobjectRegistry($model_registry, $xml);

        Services::Registry()->sort($model_registry);

        return $model_registry;
    }

    /**
     * getIncludeCode parses the xml string repeatedly until all include statements have been processed
     *
     * @static
     * @param   $xml_string
     *
     * @return  mixed
     * @throws  \RuntimeException
     * @since   1.0
     */
    protected static function getIncludeCode($xml_string, $model_name)
    {
        if (trim($xml_string) == '') {
            return $xml_string;
        }

        $include = '';
        $pattern = '/<include (.*)="(.*)"\/>/';

        $done = false;
        while ($done === false) {

            preg_match_all($pattern, $xml_string, $matches);
            if (count($matches[1]) == 0) {
                break;
            }

            $i = 0;
            $replaceThis = '';
            $withThis = '';

            foreach ($matches[1] as $match) {

                $replaceThis = $matches[0][$i];

                $include = $matches[2][$i];

                if (trim(strtolower($matches[1][$i])) == 'field') {
                    $path_and_file = ConfigurationService::locateFile('Field', $include);
                } else {
                    $path_and_file = ConfigurationService::locateFile('Include', $include);
                }

                $withThis = ConfigurationService::readXMLFile($path_and_file);

                $xml_string = str_replace($replaceThis, $withThis, $xml_string);

                $i++;
            }
        }

        return $xml_string;
    }

    /**
     * Store Data Object Definitions into Registry
     *
     * @static
     * @param   $DataobjectRegistry
     * @param   $xml
     *
     * @return  boolean
     * @since   1.0
     * @throws  \Exception
     */
    protected static function setDataobjectRegistry($DataobjectRegistry, $xml)
    {

        $doArray = Services::Registry()->get(FIELDS_LITERAL, 'Dataobjectattributes');

        foreach ($xml->attributes() as $key => $value) {
            if (in_array((string) $key, $doArray)) {
                Services::Registry()->set($DataobjectRegistry, $key, (string)$value);
            } else {
                throw new \Exception ('Configuration: setDataobjectRegistry encountered Invalid Dataobject Attributes '
                    . $key);
            }
        }

        Services::Registry()->set($DataobjectRegistry, 'data_object', 'Dataobject');
        Services::Registry()->set($DataobjectRegistry, 'model_type', 'Dataobject');
        Services::Registry()->set(
            $DataobjectRegistry,
            'model_name',
            Services::Registry()->get($DataobjectRegistry, 'name')
        );

        return true;
    }

    /**
     * Store Model Registry data into Registry
     *
     * @static
     * @param   $model_registry
     * @param   $xml
     *
     * @return  boolean
     * @since   1.0
     */
    protected static function setModelRegistry($model_registry, $xml)
    {
        $modelArray = Services::Registry()->get(FIELDS_LITERAL, 'Modelattributes');

        foreach ($xml->attributes() as $key => $value) {

            if (in_array($key, $modelArray)) {
                Services::Registry()->set($model_registry, $key, (string)$value);
            } else {
                throw new \Exception ('Configuration: setModelRegistry encountered Invalid Model Attribute ' . $key);
            }
        }

        Services::Registry()->set(
            $model_registry,
            'model_name',
            Services::Registry()->get($model_registry, 'name')
        );

        return true;
    }

    /**
     * Define elements for Data Model to Registry
     *
     * @static
     * @param   $model_registry
     * @param   $xml
     * @param   $plural
     * @param   $singular
     * @param   $valid_attributes
     *
     * @return  bool
     * @since   1.0
     */
    protected static function setElementsRegistry($model_registry, $xml, $plural, $singular, $valid_attributes)
    {
        if (isset($xml->table->$plural->$singular)) {
        } else {
            return true;
        }

        $set = $xml->table->$plural;

        $itemArray = array();

        foreach ($set->$singular as $item) {

            $attributes = get_object_vars($item);

            $itemAttributes = ($attributes["@attributes"]);
            $itemAttributesArray = array();

            foreach ($itemAttributes as $key => $value) {

                if (in_array($key, $valid_attributes)) {
                } else {
                    throw new \Exception ('Configuration: setElementsRegistry encountered Invalid Model Attribute '
                        . $key . ' for ' . $model_registry);
                }

                $itemAttributesArray[$key] = $value;
            }

            if ($plural == 'plugins') {
                foreach ($itemAttributesArray as $plugin) {
                    $itemArray[] = $plugin;
                }
            } else {
                $itemArray[] = $itemAttributesArray;
            }
        }

        if ($plural == 'joins') {
            $joins = array();
            $selects = array();

            for ($i = 0; $i < count($itemArray); $i++) {
                $temp = ConfigurationService::setJoinFields($itemArray[$i]);
                $joins[] = $temp[0];
                $selects[] = $temp[1];
            }

            Services::Registry()->set($model_registry, $plural, $joins);

            Services::Registry()->set($model_registry, 'JoinFields', $selects);

        } elseif ($plural == 'values') {

            $valuesArray = array();

            if (count($itemArray) > 0) {

                foreach ($itemArray as $value) {

                    if (is_array($value)) {
                        $temp_row = $value;
                    } else {
                        $valueVars = get_object_vars($value);
                        $temp_row = ($valueVars["@attributes"]);
                    }

                    $temp = new \stdClass();

                    $temp->id = $temp_row['id'];
                    $temp->value = $temp_row['value'];

                    $valuesArray[] = $temp;
                }
                Services::Registry()->set($model_registry, 'values', $valuesArray);
            }

        } else {
            Services::Registry()->set($model_registry, $plural, $itemArray);
        }

        return true;
    }

    /**
     * setJoinFields - processes one set of join field definitions, updating the registry
     *
     * @static
     * @param   $itemArray
     *
     * @return  array
     * @since   1.0
     */
    protected static function setJoinFields($modelJoinArray)
    {
        $joinArray = array();
        $joinSelectArray = array();

        $joinModel = ucfirst(strtolower($modelJoinArray['model']));
        $joinRegistry = $joinModel . 'Datasource';

        if (Services::Registry()->exists($joinRegistry) === false) {
            $controllerClass = CONTROLLER_CLASS;
            $controller = new $controllerClass();
            $controller->getModelRegistry('Datasource', $joinModel, 0);
        }

        $fields = Services::Registry()->get($joinRegistry, FIELDS_LITERAL);

        $table = Services::Registry()->get($joinRegistry, 'table_name');

        $joinArray['table_name'] = $table;

        $alias = (string)$modelJoinArray['alias'];
        if (trim($alias) == '') {
            $alias = substr($table, 3, strlen($table));
        }
        $joinArray['alias'] = trim($alias);

        $select = (string)$modelJoinArray['select'];
        $joinArray['select'] = $select;

        $selectArray = explode(',', $select);

        if ((int)count($selectArray) > 0) {

            foreach ($selectArray as $s) {

                foreach ($fields as $joinSelectArray) {

                    if ($joinSelectArray['name'] == $s) {
                        $joinSelectArray['as_name'] = trim($alias) . '_' . trim($s);
                        $joinSelectArray['alias'] = $alias;
                        $joinSelectArray['table_name'] = $table;
                    }
                }
            }
        }

        $joinArray['jointo'] = (string)$modelJoinArray['jointo'];
        $joinArray['joinwith'] = (string)$modelJoinArray['joinwith'];

        return array($joinArray, $joinSelectArray);
    }

    /**
     * getCustomFields extracts field information for all customfield groups
     *
     * @static
     * @param   $xml
     * @param   $model_registry
     *
     * @return  object
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected static function getCustomFields($xml, $model_registry)
    {
        $customFieldsArray = array();

        if (count($xml->customfields->customfield) > 0) {

            foreach ($xml->customfields->customfield as $custom_field) {

                $name = (string)$custom_field['name'];
                $results = ConfigurationService::getCustomFieldsSpecificGroup($model_registry, $custom_field);
                if ($results === false) {
                } else {

                    $fieldArray = $results[0];
                    $fieldNames = $results[1];

                    ConfigurationService::inheritCustomFieldsSpecificGroup(
                        $model_registry,
                        $name,
                        $fieldArray,
                        $fieldNames
                    );

                    $customFieldsArray[] = $name;
                }
            }
        }

        /** Include Inherited Groups not matching existing groups */
        $exists = Services::Registry()->exists($model_registry, 'Customfieldgroups');

        if ($exists === true) {
            $inherited = Services::Registry()->get($model_registry, 'Customfieldgroups');

            if (is_array($inherited) && count($inherited) > 0) {
                foreach ($inherited as $name) {

                    if (in_array($name, $customFieldsArray)) {
                    } else {
                        $results = ConfigurationService::inheritCustomFieldsSpecificGroup($model_registry, $name);
                        if ($results === false) {
                        } else {
                            $customFieldsArray[] = $name;
                        }
                    }
                }
            }
        }

        Services::Registry()->set($model_registry, 'Customfieldgroups', array_unique($customFieldsArray));

        return;
    }

    /**
     * Load Custom Fields for a specific Group -- this is called once for each custom field type for a Model
     *
     * @static
     * @param   $model_registry
     * @param   $customfield
     *
     * @return  array
     * @since   1.0
     */
    protected static function getCustomFieldsSpecificGroup($model_registry, $customfield)
    {
        $fieldArray = array();
        $fieldNames = array();

        foreach ($customfield as $key1 => $value1) {

            $attributes = get_object_vars($value1);
            $fieldAttributes = ($attributes["@attributes"]);
            $fieldAttributesArray = array();

            foreach ($fieldAttributes as $key2 => $value2) {

                if ($key2 == 'fieldset') {
                } elseif (in_array($key2, self::$valid_field_attributes)) {
                } else {
                    throw new \Exception ('Configuration: getCustomFieldsSpecificGroup Invalid Field attribute '
                        . $key2 . ':' . $value2 . ' for ' . $model_registry);
                }

                if ($key2 == 'name') {
                } else {
                    $fieldNames[] = $value2;
                }

                $fieldAttributesArray[$key2] = $value2;
            }

            $fieldAttributesArray['field_inherited'] = 0;

            $fieldArray[] = $fieldAttributesArray;
        }

        if (is_array($fieldArray) && count($fieldArray) > 0) {
        } else {
            return false;
        }

        return array($fieldArray, $fieldNames);
    }

    /**
     * Inherited fields are merged in with those specifically defined in model
     *
     * @static
     * @param   $model_registry
     * @param   $name
     * @param   $fieldArray
     * @param   $fieldNames
     *
     * @return  array
     * @since   1.0
     */
    protected static function inheritCustomFieldsSpecificGroup(
        $model_registry,
        $name,
        $fieldArray = array(),
        $fieldNames = array()
    ) {

        $inherit = array();
        $available = Services::Registry()->get($model_registry, $name, array());

        if (count($available) > 0) {

            foreach ($available as $temp_row) {

                foreach ($temp_row as $field => $fieldvalue) {

                    if ($field == 'name') {

                        if (in_array($fieldvalue, $fieldNames)) {
                        } else {
                            $temp_row['field_inherited'] = 1;
                            $fieldArray[] = $temp_row;
                            $fieldNames[] = $fieldvalue;
                        }
                    }
                }
            }
        }

        if (is_array($fieldArray) && count($fieldArray) == 0) {
            Services::Registry()->set($model_registry, $name, array());
            return false;
        }

        Services::Registry()->set($model_registry, $name, $fieldArray);

        return $name;
    }

    /**
     * Inheritance checking and setup  <model name="XYZ" extends="ThisTable"/>
     *
     * @static
     * @param   $model_registry
     * @param   $xml
     *
     * @return  void
     * @since   1.0
     */
    protected static function inheritDefinition($model_registry, $xml)
    {
        $extends = false;
        foreach ($xml->attributes() as $key => $value) {
            if ($key == 'extends') {
                $extends = (string)$value;
            }
        }
        if ($extends === false) {
            return;
        }

        $modelArray = Services::Registry()->get(FIELDS_LITERAL, 'Modeltypes');

        $extends_model_name = '';
        $extends_model_type = '';
        foreach ($modelArray as $modeltype) {
            if (ucfirst(
                strtolower(substr($extends, strlen($extends) - strlen($modeltype), strlen($modeltype)))
            ) == $modeltype
            ) {
                $extends_model_name = ucfirst(strtolower(substr($extends, 0, strlen($extends) - strlen($modeltype))));
                $extends_model_type = $modeltype;
                break;
            }
        }

        if ($extends_model_name == '') {
            $extends_model_name = ucfirst(strtolower($extends));
            $extends_model_type = 'Datasource';
        }

        $inheritModelRegistry = $extends_model_name . $extends_model_type;

        if (Services::Registry()->exists($inheritModelRegistry) === true) {

        } else {
            $controller_class = CONTROLLER_CLASS;
            $controller = new $controller_class();
            $controller->getModelRegistry($extends_model_type, $extends_model_name, 0);
        }

        Services::Registry()->copy($inheritModelRegistry, $model_registry);

        return;
    }

    /**
     * locateFile uses override and default locations to find the file requested
     *
     * Usage:
     * Services::Configuration()->locateFile('Application', 'defines');
     *
     * @param   string  $model_type
     * @param   string  $model_name
     * @param   string  $parameter_registry
     *
     * @return  string
     * @since   1.0
     * @throws  \Exception
     */
    protected static function locateFile($model_type, $model_name, $parameter_registry = null)
    {
        $model_type = trim(ucfirst(strtolower($model_type)));
        $model_name = trim(ucfirst(strtolower($model_name)));

        if ($parameter_registry === null) {
            $parameter_registry = 'parameters';
        }

        $path = false;

        if ($model_type == 'Site') {
            $path = SITES . '/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            }
            throw new \Exception ('Configuration: locateFile() Cannot find Sites XML File.');
        }

        if ($model_type == 'Dataobject') {
            $path = SITE_DATA_OBJECT_FOLDER . '/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            }
            $path = SITES_DATA_OBJECT_FOLDER . '/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            }
            $path = PLATFORM_MVC . '/' . $model_type . '/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            }
            throw new \Exception ('Configuration: locateFile() Cannot find Data Object for Model Type '
                . $model_type . ' Model Name ' . $model_name);
        }

        if ($model_type == 'Parse') {
            $path = EXTENSIONS . '/Theme/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            }
            $path = PLATFORM_FOLDER . '/Service/Services/Theme/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            }
            throw new \Exception ('Configuration: locateFile() Cannot find Parse File Model Type '
                . $model_type . ' Model Name ' . $model_name);
        }

        if ($model_type == 'Includer') {
            $path = EXTENSIONS . '/Theme/Includer/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            }
            $path = PLATFORM_FOLDER . '/Service/Services/Theme/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            }
            throw new \Exception ('Configuration: locateFile() Cannot find Theme Includer File Model Type '
                . $model_type . ' Model Name ' . $model_name);
        }

        $modeltypeArray = array('Application', 'Datalist', 'Datasource', 'Field', 'Include');
        if (in_array($model_type, $modeltypeArray)) {
            $path = EXTENSIONS . '/Model/' . $model_type . '/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            }

            $path = PLATFORM_MVC . '/Model/' . $model_type . '/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            }
            throw new \Exception ('Configuration: locateFile() Cannot find Model Type '
                . $model_type . ' Model Name ' . $model_name);
        }

        if ($model_type == 'Resource') {
            $path = EXTENSIONS . '/Resource/' . $model_name . '/Configuration.xml';
            if (file_exists($path)) {
                return $path;
            } else {
                $path = false;
            }
        }

        $modeltypeArray = array('Service');

        if (in_array($model_type, $modeltypeArray)) {

            $path = EXTENSIONS . '/' . $model_type . '/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            } else {
                $path = false;
            }
            $path = PLATFORM_FOLDER . '/' . $model_type . '/' . $model_name . '.xml';
            if (file_exists($path)) {
                return $path;
            } else {
                $path = false;
            }
        }

        $modeltypeArray = array('Theme', 'System');

        if (in_array($model_type, $modeltypeArray)) {

            $path = EXTENSIONS . '/' . $model_type . '/' . $model_name . '/' . 'Configuration.xml';
            if (file_exists($path)) {
                return $path;
            } else {
                $path = false;
            }
            $path = PLATFORM_FOLDER . '/' . $model_type . '/' . $model_name . '/' . 'Configuration.xml';
            if (file_exists($path)) {
                return $path;
            } else {
                $path = false;
            }
        }

        $extension_path = false;
        if (Services::Registry()->exists($parameter_registry, 'extension_path')) {
            $extension_path = Services::Registry()->get($parameter_registry, 'extension_path');
        }

        $primary_extension_path = false;
        if (Services::Registry()->exists('RouteParameters')) {
            $primary_extension_path = Services::Registry()->get('RouteParameters', 'extension_path', '');
        }

        $theme_path = false;
        if (Services::Registry()->exists($parameter_registry, 'theme_path')) {
            $theme_path = Services::Registry()->get($parameter_registry, 'theme_path');
        }

        $page_view_path = false;
        if (Services::Registry()->exists($parameter_registry, 'page_view_path')) {
            $page_view_path = Services::Registry()->get($parameter_registry, 'page_view_path');
        }

        $template_view_path = false;
        if (Services::Registry()->exists($parameter_registry, 'template_view_path')) {
            $template_view_path = Services::Registry()->get($parameter_registry, 'template_view_path');
        }

        $wrap_view_path = false;
        if (Services::Registry()->exists($parameter_registry, 'wrap_view_path')) {
            $wrap_view_path = Services::Registry()->get($parameter_registry, 'wrap_view_path');
        }

        /** Search for overrides before standard placement */
        $valid = array('Menuitem', 'Plugin');
        if (in_array($model_type, $valid)) {
            $path = ConfigurationService::commonSearch(
                $model_type,
                $model_name,
                $extension_path,
                $primary_extension_path,
                $theme_path,
                $page_view_path,
                $template_view_path,
                $wrap_view_path,
                '',
                'folder'
            );
            if ($path === false) {
            } else {
                return $path;
            }
        }

        $valid = array('Page', 'Template', 'Wrap');

        if (in_array($model_type, $valid)) {

            $path = ConfigurationService::commonSearch(
                $model_type,
                $model_name,
                $extension_path,
                $primary_extension_path,
                $theme_path,
                $page_view_path,
                $template_view_path,
                $wrap_view_path,
                '/View/',
                'folder'
            );

            if ($path === false) {
            } else {
                return $path;
            }
        }

        $valid = array('Datalist', 'Dataobject', 'Datasource');

        if (in_array($model_type, $valid)) {
            $path = ConfigurationService::commonSearch(
                $model_type,
                $model_name,
                $extension_path,
                $primary_extension_path,
                $theme_path,
                $page_view_path,
                $template_view_path,
                $wrap_view_path,
                '',
                'file'
            );
            if ($path === false) {
            } else {
                return $path;
            }
        }

        /** These are the Dataobjects, other than Database */
        $path = ConfigurationService::commonSearch(
            'Datasource',
            $model_type,
            $extension_path,
            $primary_extension_path,
            $theme_path,
            $page_view_path,
            $template_view_path,
            $wrap_view_path,
            '',
            'file'
        );
        if ($path === false) {
        } else {
            return $path;
        }

        throw new \Exception('File not found for Model Type: ' . $model_type . ' Name: ' . $model_name);
    }

    /**
     * Common search order for Extension Model utilizing override order
     *
     * 1. Extension/Theme/etc.
     * 2. Theme/etc.
     * 3. Template (wherever it is)
     * 4. Wrap
     * 5. Page
     * 6. Extension (current extension, ex. Resource, Menuitem, Template, etc.)
     * 7. Primary Extension (always a Resource, whether distribution or core)
     *
     * @param   $model_type
     * @param   $model_name
     * @param   $extension_path
     * @param   $primary_extension_path
     * @param   $file_or_folder
     *
     * @return  string
     * @since   1.0
     */
    protected static function commonSearch(
        $model_type,
        $model_name,
        $extension_path,
        $primary_extension_path,
        $theme_path,
        $page_view_path,
        $template_view_path,
        $wrap_view_path,
        $view_path_portion = '',
        $file_or_folder = 'file'
    ) {
        if ($view_path_portion == '') {
            $connector = '/';
            $core_connector = '/';
        } else {
            $connector = $view_path_portion;
            $core_connector = '/MVC/View/';
        }

        $path = false;

        if ($file_or_folder == 'folder') {

            if ($theme_path === false) {
            } elseif ($path === false) {
                $path = $theme_path . $connector . $model_type . '/' . $model_name . '/Configuration.xml';
                if (file_exists($path)) {
                } else {
                    $path = false;
                }
            }

            if ($template_view_path === false) {
            } elseif ($path === false) {
                $path = $template_view_path . $connector . $model_type . '/' . $model_name . '/Configuration.xml';
                if (file_exists($path)) {
                } else {
                    $path = false;
                }
            }

            if ($wrap_view_path === false) {
            } elseif ($path === false) {
                $path = $wrap_view_path . $connector . $model_type . '/' . $model_name . '/Configuration.xml';
                if (file_exists($path)) {
                } else {
                    $path = false;
                }
            }

            if ($page_view_path === false) {
            } elseif ($path === false) {
                $path = $page_view_path . $connector . $model_type . '/' . $model_name . '/Configuration.xml';
                if (file_exists($path)) {
                } else {
                    $path = false;
                }
            }
        }

        if ($file_or_folder == 'file') {

            if ($extension_path === false) {
            } elseif ($path === false) {
                $path = $extension_path . $connector . $model_type . '/' . $model_name . '.xml';
                if (file_exists($path)) {
                } else {
                    $path = false;
                }
            }

            if ($primary_extension_path === false) {
            } elseif ($path === false) {
                $path = $primary_extension_path . $connector . $model_type . '/' . $model_name . '.xml';
                if (file_exists($path)) {
                } else {
                    $path = false;
                }
            }
        }

        /** Plugin, Menuitem, Datalist */
        if ($file_or_folder == 'folder') {
            if ($path === false) {
                $path = EXTENSIONS . $connector . $model_type . '/' . $model_name . '/Configuration.xml';
                if (file_exists($path)) {
                } else {
                    $path = false;
                }
            }

            if ($path === false) {
                $path = PLATFORM_FOLDER . $core_connector . $model_type . '/' . $model_name . '/Configuration.xml';
                if (file_exists($path)) {
                } else {
                    $path = false;
                }
            }

        } else {

            if ($path === false) {
                $path = EXTENSIONS . $connector . $model_type . '/' . $model_name . '.xml';
                if (file_exists($path)) {
                } else {
                    $path = false;
                }
            }

            if ($path === false) {
                $path = PLATFORM_FOLDER . $core_connector . $model_type . '/' . $model_name . '.xml';
                if (file_exists($path)) {
                } else {
                    $path = false;
                }
            }
        }

        return $path;
    }

    /**
     * Read XML file and return results
     *
     * @static
     * @param   $path_and_file
     *
     * @return  bool|object
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected static function readXMLFile($path_and_file)
    {
        if (file_exists($path_and_file)) {
        } else {
            throw new \Exception('Configuration: readXMLFile File not found: ' . $path_and_file);
        }

        try {
            return file_get_contents($path_and_file);

        } catch (\Exception $e) {

            throw new \Exception('Configuration: readXMLFile Failure reading File: '
                . $path_and_file . ' ' . $e->getMessage());
        }
    }
}
