<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Configuration;

use Molajo\Application;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Configuration
 *
 * @package     Molajo
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
     * Valid Field Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected static $valid_field_attributes;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance($configuration_file = null)
    {
        if (empty(self::$instance)) {
            self::$instance = new ConfigurationService($configuration_file);
        }

        return self::$instance;
    }

    /**
     * Retrieve Site and Application data, set constants and paths
     *
     * @return object
     * @since   1.0
     */
    public function __construct($configuration_file = null)
    {
        /** Initialize list of valid field attributes */
        self::$valid_field_attributes = array('name', 'as_name', 'alias', 'default',
            'file',	'form', 'identity', 'length', 'minimum', 'maximum', 'null',
            'required', 'shape', 'size', 'table', 'type', 'unique', 'values');

        /** Retrieve Site Data */
        $this->getSite($configuration_file);

        /** Retrieve Application Data */
        $this->getApplication();

        /** Defines, etc., with site paths */
        $this->setSitePaths();

        /** Retrieves and stores Action Table pairs in Registry */
        $this->getActions();

        /** return */

        return $this;
    }

    /**
     * Retrieve site configuration object from ini file
     *
     * @param string $configuration_file optional
     *
     * @return object
     * @throws \Exception
     * @since   1.0
     */
    public function getSite($configuration_file = null)
    {

        if ($configuration_file === null) {
            $configuration_file = SITE_BASE_PATH . '/configuration.php';
        }
        $configuration_class = 'SiteConfiguration';

        if (file_exists($configuration_file)) {
            require_once $configuration_file;

        } else {
            throw new \Exception('Fatal error - Site Configuration File does not exist', 100);
        }

        if (class_exists($configuration_class)) {
            $siteData = new $configuration_class();

        } else {
            throw new \Exception('Fatal error - Configuration Class does not exist', 100);
        }

        foreach ($siteData as $key => $value) {
            Services::Registry()->set('Configuration', $key, $value);
        }

        /** Retrieve Sites Data from DB */
        $controllerClass = 'Molajo\\Controller\\Controller';
        $m = new $controllerClass();

        $results = $m->connect('Table', 'Sites');

        if ($results == false) {
            return false;
        }

        $m->set('id', (int) SITE_ID);

        $item = $m->getData('item');

        if ($item === false) {
            throw new \RuntimeException ('Site getSite() query problem');
        }

        Services::Registry()->set('Configuration', 'site_id', (int) $item->id);
        Services::Registry()->set('Configuration', 'site_catalog_type_id', (int) $item->catalog_type_id);
        Services::Registry()->set('Configuration', 'site_name', $item->name);
        Services::Registry()->set('Configuration', 'site_path', $item->path);
        Services::Registry()->set('Configuration', 'site_base_url', $item->base_url);
        Services::Registry()->set('Configuration', 'site_description', $item->description);

        return true;
    }

    /**
     * Establish media, cache, log, etc., locations for site for application use
     *
     * Called out of the Configurations Class construct - paths needed in startup process for other services
     *
     * @return mixed
     * @since  1.0
     */
    public function setSitePaths()
    {
        /** Base URLs for Site and Application */
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
            define('SITE_CACHE_FOLDER',
            Services::Registry()->get('Configuration', 'cache_path', SITE_BASE_PATH . '/cache'));
        }

        if (defined('SITE_LOGS_FOLDER')) {
        } else {
            define('SITE_LOGS_FOLDER', SITE_BASE_PATH . '/'
                . Services::Registry()->get('Configuration', 'logs_path', SITE_BASE_PATH . '/logs'));
        }

        /** following must be within the web document folder */
        if (defined('SITE_MEDIA_FOLDER')) {
        } else {
            define('SITE_MEDIA_FOLDER', SITE_BASE_PATH . '/'
                . Services::Registry()->get('Configuration', 'media_path', SITE_BASE_PATH . '/media'));
        }
        if (defined('SITE_MEDIA_URL')) {
        } else {
            define('SITE_MEDIA_URL', BASE_URL . 'Molajo/Site/' . SITE_ID . '/' . Services::Registry()->get('Configuration', 'media_url'));
        }

        /** following must be within the web document folder */
        if (defined('SITE_TEMP_FOLDER')) {
        } else {
            define('SITE_TEMP_FOLDER', SITE_BASE_PATH . '/'
                . Services::Registry()->get('Configuration', 'temp_path', SITE_BASE_PATH . '/temp'));
        }
        if (defined('SITE_TEMP_URL')) {
        } else {
            define('SITE_TEMP_URL', BASE_URL
                . Services::Registry()->get('Configuration', 'temp_url', BASE_URL . 'sites/' . SITE_ID . '/temp'));
        }

        return true;
    }

    /**
     * Get the application data and store it in the registry, combine with site data for configuration
     *
     * @return boolean
     * @since   1.0
     */
    protected function getApplication()
    {

        if (APPLICATION == 'installation') {

            Services::Registry()->set('Configuration', 'application_id', 0);
            Services::Registry()->set('Configuration', 'application_catalog_type_id', CATALOG_TYPE_BASE_APPLICATION);
            Services::Registry()->set('Configuration', 'application_name', APPLICATION);
            Services::Registry()->set('Configuration', 'application_description', APPLICATION);
            Services::Registry()->set('Configuration', 'application_path', APPLICATION);

        } else {

            try {
                $profiler = 0;
                $controllerClass = 'Molajo\\Controller\\Controller';
                $m = new $controllerClass();
                $results = $m->connect('Table', 'Applications');
                if ($results == false) {
                    return false;
                }

                $m->set('name_key_value', APPLICATION);

                $item = $m->getData('item');

                if ($item === false) {
                    throw new \RuntimeException ('Application getApplication() query problem');
                }

                Services::Registry()->set('Configuration', 'application_id', (int) $item->id);
                Services::Registry()->set('Configuration', 'application_catalog_type_id',
                    (int) $item->catalog_type_id);
                Services::Registry()->set('Configuration', 'application_name', $item->name);
                Services::Registry()->set('Configuration', 'application_path', $item->path);
                Services::Registry()->set('Configuration', 'application_description', $item->description);

                /** Combine Application and Site Parameters into Configuration */
                $parameters = Services::Registry()->get('ApplicationsTableParameters');
                foreach ($parameters as $key => $value) {
                    Services::Registry()->set('Configuration', $key, $value);
                    if (strtolower($key) == 'profiler') {
                        $profiler = $value;
                    }
                    if (strtolower($key) == 'cache') {
                        $cache = $value;
                    }
                }

            } catch (\Exception $e) {
                echo 'Application will die. Exception caught in Configuration: ', $e->getMessage(), "\n";
                die;
            }
        }

        if (defined('APPLICATION_ID')) {
        } else {
            define('APPLICATION_ID', Services::Registry()->get('Configuration', 'application_id'));
        }

        Services::Registry()->sort('Configuration');

        if ((int) $profiler == 1) {
            Services::Profiler()->initiate();
        }

        if ((int) $cache == 1) {
            Services::Cache()->startCache();
            Services::Registry()->set('cache', true);
        } else {
            Services::Registry()->set('cache', false);
        }

        return $this;
    }

    /**
     * Get action ids and values to load into registry (to save a read on various triggers)
     *
     * @return boolean
     * @since   1.0
     */
    protected function getActions()
    {
        $controllerClass = 'Molajo\\Controller\\Controller';
        $m = new $controllerClass();
        $results = $m->connect('Table', 'Actions');
        if ($results == false) {
            return false;
        }

        $items = $m->getData('list');

        if ($items === false) {
            throw new \RuntimeException ('Application getApplication() getActions Query failed');
        }

        Services::Registry()->createRegistry('Actions');

        foreach ($items as $item) {
            Services::Registry()->set('Actions', $item->title, (int) $item->id);
        }

        return;
    }

    /**
     * getFile processes all XML configuration files for the application
     *
     * Usage:
     * Services::Configuration()->getFile('Application', 'defines');
     *
     * or - in classes where usage can happen before the service is activated:
     *
     * ConfigurationService::getFile($model_type, $model_name);
     *
     * @static
     * @param $model_name
     * @param string $model_type - Application, Table, Module, Theme, Page, Template, Wrap
     *
     * @return object $xml
     * @since  1.0
     *
     * @throws \RuntimeException
     */
    public static function getFile($model_type, $model_name)
    {

        /** Use existing registry values, if existing */
        $registry = ConfigurationService::checkRegistryExists($model_type, $model_name);
        if ($registry == false) {
        } else {
            return $registry;
        }

        $registryName = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

        /** Or, use cache, if available */
        //if (Services::Registry()->get('cache') == true) {

        //	Services::Registry()->createRegistry($registryName);
        //	if (Services::Cache()->exists(md5($registryName), 'registry')) {
        //		Services::Registry()->createRegistry($registryName);
        //		Services::Registry()->loadArray($registryName, Services::Cache()->get($registryName, 'registry'));
        //		echo 'loading  '.$registry.' from cache<br />';
        //		return $registry;
        //	}
        //}

        /** Using application location structure, locate file */
        $results = ConfigurationService::locateFile($model_type, $model_name);

        if (file_exists($results)) {
            $path_and_file = $results;
        } else {
            echo 'Error in ConfigurationService. File not found for '
                . ' Model Type:' . $model_type
                . ' Model Name: ' . $model_name;

            return false;
            //throw new \RuntimeException('File not found: ' . $path_and_file);
        }

        /** Read XML file */
        try {
            $xml = simplexml_load_file($path_and_file);

        } catch (\Exception $e) {
            throw new \RuntimeException ('Failure reading XML File: ' . $path_and_file . ' ' . $e->getMessage());
        }

        /** now process it. */
        if (strtolower($model_type) == 'application') {
            return $xml;
        }

        /** Create and Populate Registry */
        Services::Registry()->createRegistry($model_name);

        /** Using Extends allows inheritance of another Model */
        ConfigurationService::inheritDefinition($registryName, $xml);

        /** Extensions are within an <extension></extension> group */
        if (isset($xml->model)) {
            $xml = $xml->model;
        }

        /** Set Model Properties */
        ConfigurationService::setModelRegistry($registryName, $xml);

        /** Table Registry: Fields, Joins, Foreign Keys, Filters, etc. */
        $xmlArray = ConfigurationService::setTableRegistry(
            $registryName, $xml, '', $path_and_file, $model_name);

        /** Custom Fields use type "customfield" <field name="xyz" type="customfield"/> */
        ConfigurationService::setSpecialFieldsRegistry(
            $registryName, $xml, '', $path_and_file, $model_name);

        /** Save in Cache */
        //if (Services::Registry()->get('cache') == true) {
        //	Services::Cache()->set(md5($registryName), Services::Registry()->getArray($registryName), 'registry');
        //}

        return $registryName;
    }

    /**
     * Determine if data already exists in the registry, if so, reuse
     *
     * @static
     * @param $model_name
     * @param $model_type
     *
     * @return bool|string
     * @since  1.0
     */
    public static function checkRegistryExists($model_type, $model_name)
    {
        if (strtolower($model_type) == 'application') {
            return false;
        }

        $registryName = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
        $exists = Services::Registry()->exists($registryName);

        if ($exists === true) {
            return $registryName;
        }

        return false;
    }

    /**
     * locateFile uses override and default locations to find the file requested
     *
     * Usage:
     * Services::Configuration()->locateFile('Application', 'defines');
     *
     * @return mixed object or void
     * @since   1.0
     * @throws \RuntimeException
     */
    public static function locateFile($model_type, $model_name)
    {
        $model_type = trim(ucfirst(strtolower($model_type)));
        $model_name = trim(ucfirst(strtolower($model_name)));
        $model_name_type = $model_name . $model_type;

        if ($model_type == 'Language') {
            return $model_name . '/' . 'Configuration.xml';
        }
        if ($model_name == 'Language') {
            return EXTENSIONS . '/Language/Configuration.xml';
        }

        if ($model_type == 'Application') {
            return CONFIGURATION_FOLDER . '/Application/' . $model_name . '.xml';
        }

        if ($model_type == 'Dbo') {
            return CONFIGURATION_FOLDER . '/Dbo/' . $model_name . '.xml';
        }

        /** Validate Model Types */
        $array = explode(',', 'Table,Dbo,Listbox,Menuitem,Theme,Page,Template,Wrap,Trigger');
        if (in_array($model_type, $array)) {
        } else {
            echo 'Error found in Configuration Service. Model Type: ' . $model_type . ' is not valid ';

            return false;
        }

        /** 1. Menu Item */
        if ($model_type == 'Menuitem'
            || $model_name == 'Menuitem'
            || substr($model_name, 0, 8) == 'Menuitem'
        ) {
            return ConfigurationService::locateFileMenuitem($model_type, $model_name);
        }

        /** 2. Current Extension */
        $extension_path = false;
        $extension_name = '';
        if (Services::Registry()->exists('Parameters', 'extension_path')) {
            $extension_path = Services::Registry()->get('Parameters', 'extension_path');
            $extension_name = Services::Registry()->get('Parameters', 'extension_name_path_node');

        } else {
            /** Extension path not available until after the first content read - use Catalog data */
            if (Services::Registry()->exists('Parameters', 'catalog_type')) {
                $catalog_type = Services::Registry()->get('Parameters', 'catalog_type');
                $extension_path = EXTENSIONS_RESOURCES . '/' . ucfirst(strtolower($catalog_type));
                $extension_name = $catalog_type;
            }
        }


        if ($extension_path == false) {
        } else {
            /** ex. Resource/Article/Configuration.xml */
            if (file_exists($extension_path . '/Configuration.xml')
                && strtolower($extension_name) == strtolower($model_name)
            ) {
                return $extension_path . '/Configuration.xml';
            }
            /** ex. Resource/Article/ContentTable.xml or Resource/Article/DefaultTemplate.xml */
            if (file_exists($extension_path . '/' . $model_name_type . '.xml')
            ) {
                return $extension_path . '/' . $model_name_type . '.xml';
            }
        }

        /** 3. Primary Resource (if not current extension) */
        if (Services::Registry()->exists('RouteParameters')) {
            $primary_extension_path = Services::Registry()->get('RouteParameters', 'extension_path', '');
            $primary_extension_name = Services::Registry()->get('RouteParameters', 'extension_name_path_node');
            if ($primary_extension_path == $extension_path
                || $primary_extension_path == ''
            ) {
            } else {

                /** ex. Resource/Article/Configuration.xml */
                if (file_exists($primary_extension_path . '/Configuration.xml')
                    && strtolower($primary_extension_name) == strtolower($model_name)
                ) {
                    return $primary_extension_path . '/Configuration.xml';
                }

                /** ex. Resource/Article/ContentTable.xml or Resource/Article/DefaultTemplate.xml */
                if (file_exists($primary_extension_path . '/' . $model_name_type . '.xml')
                ) {
                    return $primary_extension_path . '/' . $model_name_type . '.xml';
                }
            }
        }

        /** 4. The Manifest for the model type/name itself */
        if ($model_type == 'Menuitem' || $model_type == 'Trigger'
            || $model_type == 'Theme' || $model_type == 'Page'
            || $model_type == 'Template' || $model_type == 'Wrap'
        ) {

            if ($model_type == 'Page' || $model_type == 'Template' || $model_type == 'Wrap') {
                $path_parameter = strtolower($model_type) . '_view_path';
            } else {
                $path_parameter = strtolower($model_type) . '_path';
            }

            if (Services::Registry()->exists('Parameters', $path_parameter)) {
                $extension_path = Services::Registry()->get('Parameters', $path_parameter);
                if (file_exists($extension_path . '/' . 'Configuration.xml')) {
                    return $extension_path . '/' . 'Configuration.xml';
                }
            }
        }

        /** 5. Any Resource (in case of delete or using a resource in a non-request position, etc.) */
        $folders = Services::Filesystem()->folderFolders(EXTENSIONS_RESOURCES, $filter = '',
            $recurse = false, $fullpath = false, $exclude = array('.git'));

        foreach ($folders as $folder) {
            if (strtolower($folder) == strtolower($model_name)) {
                if (file_exists(EXTENSIONS_RESOURCES . '/' . $folder . '/Configuration.xml')) {
                    return EXTENSIONS_RESOURCES . '/' . $folder . '/Configuration.xml';
                }
            }
        }

        /** 6. Application Configuration folder is the last */
        if (file_exists(CONFIGURATION_FOLDER . '/' . $model_type . '/' . $model_name . '.xml')) {
            return CONFIGURATION_FOLDER . '/' . $model_type . '/' . $model_name . '.xml';
        }

        return false;
    }

    /**
     * locateFileMenuitem uses override and default locations to find the file requested
     *
     * Usage:
     * Services::Configuration()->locateFileMenuitem('Menuitem', 'grid');
     *
     * @return mixed object or void
     * @since   1.0
     * @throws \RuntimeException
     */
    public static function locateFileMenuitem($model_type, $model_name)
    {
        $model_type = trim(ucfirst(strtolower($model_type)));
        $model_name = trim(ucfirst(strtolower($model_name)));
        $model_name_type = $model_name . $model_type;

        /** 1. Current Extension */
        $extension_path = false;
        $extension_name = '';
        if (Services::Registry()->exists('Parameters', 'extension_path')) {
            $extension_path = Services::Registry()->get('Parameters', 'extension_path');
            $extension_name = Services::Registry()->get('Parameters', 'extension_name_path_node');

            /** ex. Resource/Article/GridMenuitem.xml */
            if (file_exists($extension_path . '/' . $model_name_type . '.xml')
            ) {
                return $extension_path . '/' . $model_name_type . '.xml';
            }
        }

        /** 2. Primary Resource (if not current extension) */
        if (Services::Registry()->exists('RouteParameters')) {
            $primary_extension_path = Services::Registry()->get('RouteParameters', 'extension_path', '');
            $primary_extension_name = Services::Registry()->get('RouteParameters', 'extension_name_path_node');
            if ($primary_extension_path == $extension_path
                || $primary_extension_path == ''
            ) {
            } else {
                /** ex. Resource/Article/GridMenuitem.xml */
                if (file_exists($primary_extension_path . '/' . $model_name_type . '.xml')
                ) {
                    return $primary_extension_path . '/' . $model_name_type . '.xml';
                }
            }
        }

        /** 3. Ex Collection or Dashboard */
        if (file_exists(EXTENSIONS . '/Menuitem/' . $model_name . '/Configuration.xml')) {
            return EXTENSIONS . '/Menuitem/' . $model_name . '/Configuration.xml';
        }

        /** 4. ex. Resource or Template */
        if (file_exists(EXTENSIONS . '/Menuitemtype/' . $model_name . '.xml')) {
            return EXTENSIONS . '/Menuitemtype/' . $model_name . '.xml';
        }

        /** 4. ex. Resource or Template */
        if (file_exists(CONFIGURATION_FOLDER . '/Table/' . $model_name . '.xml')) {
            return CONFIGURATION_FOLDER . '/Table/' . $model_name . '.xml';
        }

        /** 5. Menuitem Default */

        return EXTENSIONS . '/Menuitem/Configuration.xml';

    }

    /**
     * Retrieves base Model Registry data and stores it to the datasource registry
     *
     * @static
     * @param  $registryName
     * @param  $xml
     * @return mixed
     */
    public static function setModelRegistry($registryName, $xml)
    {
        foreach ($xml->attributes() as $key => $value) {
            Services::Registry()->set($registryName, $key, (string) $value);
        }

        Services::Registry()->set($registryName, 'model_name',
            Services::Registry()->get($registryName, 'name'));

        return;
    }

    /**
     * Inheritance checking and setup
     *
     * @static
     * @param  $registryName
     * @param  $xml
     *
     * @return void
     * @since  1.0
     */
    public static function inheritDefinition($registryName, $xml)
    {
        /** Inheritance: <extension type="Resource" version="1.0" extends="Content"> */
        $extends = false;
        $type = '';
        foreach ($xml->attributes() as $key => $value) {
            if ($key == 'extends') {
                $extends = (string) $value;
            } elseif ($key == 'type') {
                $type = (string) $value;
            }
        }

        /** No Inheritance */
        if ($extends == false) {
            return;
        }

        /** Can only inherit a Table definition */
        $parentRegistryName = strtolower($extends . 'Table');

        /** Load the file and build registry - IF - the registry is not already loaded */
        if (Services::Registry()->exists($parentRegistryName) == true) {
        } else {
            //if not, load it.
            $controllerClass = 'Molajo\\Controller\\Controller';
            $m = new $controllerClass();
            $results = $m->connect('Table', $extends);
            if ($results == false) {
                return false;
            }
        }

        /** Copy parent to child for start - will be overwritten for child definitions */
        Services::Registry()->copy($parentRegistryName, $registryName);

        return;
    }

    /**
     * Processes Table attributes: fields, joins, foreign keys, children and triggers
     *
     * @static
     * @param $registryName
     * @param $xml
     * @param $xml_string
     * @param $path_and_file
     * @param $model_name
     * @return array
     */
    public static function setTableRegistry(
        $registryName, $xml, $xml_string, $path_and_file, $model_name)
    {
        /** Process table includes */
        $include = '';

        if (isset($xml->table->include['name'])) {
            $include = (string) $xml->table->include['name'];
        }
        if ($include == '') {
        } else {

            if ($xml_string == '') {
                $xml_string = file_get_contents($path_and_file);
            }

            $replace_this = '<include name="' . $include . '"/>';

            $xml_string = ConfigurationService::replaceIncludeStatement(
                $include, $model_name, $replace_this, $xml_string
            );

            $xml = simplexml_load_string($xml_string);
        }

        /** Process each type */
        $xmlArray = ConfigurationService::setTableFieldsRegistry(
            $registryName, $xml, $xml_string, $path_and_file, $model_name
        );

        $xmlArray = ConfigurationService::setTableJoinsRegistry(
            $registryName, $xmlArray[0], $xmlArray[1], $path_and_file, $model_name
        );

        $xmlArray = ConfigurationService::setTableForeignKeysRegistry(
            $registryName, $xmlArray[0], $xmlArray[1], $path_and_file, $model_name
        );

        $xmlArray = ConfigurationService::setTableChildrenRegistry(
            $registryName, $xmlArray[0], $xmlArray[1], $path_and_file, $model_name
        );

        $xmlArray = ConfigurationService::setTableTriggersRegistry(
            $registryName, $xmlArray[0], $xmlArray[1], $path_and_file, $model_name
        );

        return $xmlArray;
    }

    /**
     * setTableFieldsRegistry
     *
     * @static
     * @param $registryName
     * @param $xml
     * @param $xml_string
     * @param $path_and_file
     * @param $model_name
     * @return array
     */
    public static function setTableFieldsRegistry(
        $registryName, $xml, $xml_string, $path_and_file, $model_name)
    {
        $include = '';

        if (isset($xml->table->fields->include['name'])) {
            $include = (string) $xml->table->fields->include['name'];
        }
        if ($include == '') {
        } else {

            if ($xml_string == '') {
                $xml_string = file_get_contents($path_and_file);
            }

            $replace_this = '<include name="' . $include . '"/>';

            $xml_string = ConfigurationService::replaceIncludeStatement(
                $include, $model_name, $replace_this, $xml_string
            );
            $xml = simplexml_load_string($xml_string);
        }

        if (isset($xml->table->fields->field)) {

            $fields = $xml->table->fields->field;
            $fieldArray = array();

            foreach ($fields as $field) {

                $attributes = get_object_vars($field);
                $fieldAttributes = ($attributes["@attributes"]);
                $fieldAttributesArray = array();

                foreach ($fieldAttributes as $key => $value) {

                    if (in_array($key, self::$valid_field_attributes)) {
                    } else {
                        echo 'Field attribute not known ' . $key . ' for ' . $model_name . '<br />';
                    }
                    $fieldAttributesArray[$key] = $value;
                }
                $fieldArray[] = $fieldAttributesArray;
            }

            Services::Registry()->set($registryName, 'fields', $fieldArray);
        }

        return array($xml, $xml_string);
    }

    /**
     * setTableJoinsRegistry
     *
     * @static
     * @param $registryName
     * @param $xml
     * @param $xml_string
     * @param $path_and_file
     * @param $model_name
     *
     * @return array
     * @since  1.0
     */
    public static function setTableJoinsRegistry(
        $registryName, $xml, $xml_string, $path_and_file, $model_name)
    {

        $include = '';
        if (isset($xml->table->joins->include['name'])) {
            $include = (string) $xml->table->joins->include['name'];
        }
        if ($include == '') {
        } else {

            if ($xml_string == '') {
                $xml_string = file_get_contents($path_and_file);
            }
            $replace_this = '<include name="' . $include . '"/>';

            $xml_string = ConfigurationService::replaceIncludeStatement(
                $include, $model_name, $replace_this, $xml_string
            );
            $xml = simplexml_load_string($xml_string);
        }

        if (isset($xml->table->joins->join)) {
            $jXML = $xml->table->joins->join;

            $join_fields_select = array();

            $jArray = array();
            foreach ($jXML as $joinItem) {

                $joinVars = get_object_vars($joinItem);
                $joinAttributes = ($joinVars["@attributes"]);
                $joinAttributesArray = array();

                $joinModel = (string) $joinAttributes['model'];

                $joinFields = array();

                /** Load Registry for Table Joined too -- so that field attributes can be used */
                $joinRegistry = strtolower($joinModel . 'Table');

                /** Load the file and build registry - IF - the registry is not already loaded */
                if (Services::Registry()->exists($joinRegistry) == true) {
                } else {
                    //if not, load it.
                    $controllerClass = 'Molajo\\Controller\\Controller';
                    $m = new $controllerClass();
                    $results = $m->connect('Table', $joinModel);
                }

                /** Load inherited definitions */
                $tempFields = Services::Registry()->get($joinRegistry, 'fields', array());
                $table = Services::Registry()->get($joinRegistry, 'table');
                $joinAttributesArray['table'] = $table;

                $alias = (string) $joinAttributes['alias'];
                if (trim($alias) == '') {
                    $alias = substr($table, 3, strlen($table));
                }
                $joinAttributesArray['alias'] = trim($alias);

                $select = (string) $joinAttributes['select'];
                $joinAttributesArray['select'] = $select;
                $selectArray = explode(',', $select);

                foreach ($selectArray as $x) {

                    foreach ($tempFields as $t) {
                        if ($t['name'] == $x) {
                            $t['as_name'] = trim($alias) . '_' . trim($x);
                            $t['alias'] = $alias;
                            $t['table'] = $table;
                            $join_fields_select[] = $t;
                        }
                    }
                }

                $joinAttributesArray['jointo'] = (string) $joinAttributes['jointo'];
                $joinAttributesArray['joinwith'] = (string) $joinAttributes['joinwith'];

                $jArray[] = $joinAttributesArray;
            }

            Services::Registry()->set($registryName, 'Joins', $jArray);

            Services::Registry()->set($registryName, 'JoinFields', $join_fields_select);
        }

        return array($xml, $xml_string);
    }

    /**
     * setTableForeignKeysRegistry
     *
     * @static
     * @param $registryName
     * @param $xml
     * @param $xml_string
     * @param $path_and_file
     * @param $model_name
     * @return array
     * @since  1.0
     */
    public static function setTableForeignKeysRegistry(
        $registryName, $xml, $xml_string, $path_and_file, $model_name)
    {
        $include = '';
        if (isset($xml->table->foreignkeys->include['name'])) {
            $include = (string) $xml->table->foreignkeys->include['name'];
        }
        if ($include == '') {
        } else {
            if ($xml_string == '') {
                $xml_string = file_get_contents($path_and_file);
            }

            $replace_this = '<include name="' . $include . '"/>';

            $xml_string = ConfigurationService::replaceIncludeStatement(
                $include, $model_name, $replace_this, $xml_string
            );
            $xml = simplexml_load_string($xml_string);
        }

        if (isset($xml->table->foreignkeys->foreignkey)) {

            $fks = $xml->table->foreignkeys->foreignkey;
            $fkArray = array();

            foreach ($fks as $fk) {

                $attributes = get_object_vars($fk);
                $fkAttributes = ($attributes["@attributes"]);
                $fkAttributesArray = array();

                $fkAttributesArray['name'] = $fkAttributes['name'];
                $fkAttributesArray['source_id'] = $fkAttributes['source_id'];
                $fkAttributesArray['source_model'] = $fkAttributes['source_model'];
                $fkAttributesArray['required'] = $fkAttributes['required'];

                $fkArray[] = $fkAttributesArray;
            }
            Services::Registry()->set($registryName, 'foreignkeys', $fkArray);
        }

        return array($xml, $xml_string);
    }

    /**
     * setTableChildrenRegistry
     *
     * @static
     * @param $registryName
     * @param $xml
     * @param $xml_string
     * @param $path_and_file
     * @param $model_name
     * @return array
     */
    public static function setTableChildrenRegistry(
        $registryName, $xml, $xml_string, $path_and_file, $model_name)
    {
        $include = '';
        if (isset($xml->table->children->include['name'])) {
            $include = (string) $xml->table->children->include['name'];
        }
        if ($include == '') {
        } else {
            if ($xml_string == '') {
                $xml_string = file_get_contents($path_and_file);
            }

            $replace_this = '<include name="' . $include . '"/>';

            $xml_string = ConfigurationService::replaceIncludeStatement(
                $include, $model_name, $replace_this, $xml_string
            );
            $xml = simplexml_load_string($xml_string);
        }

        if (isset($xml->table->children->child)) {

            $cs = $xml->table->children->child;
            $csArray = array();
            foreach ($cs as $c) {

                $chVars = get_object_vars($c);
                $chAttributes = ($chVars["@attributes"]);
                $chkAttributesArray = array();

                $chkAttributesArray['name'] = $chAttributes['name'];
                $chkAttributesArray['join'] = $chAttributes['join'];

                $csArray[] = $chkAttributesArray;
            }
            Services::Registry()->set($registryName, 'children', $csArray);
        }

        return array($xml, $xml_string);
    }

    /**
     * setTableTriggersRegistry
     *
     * @static
     * @param $registryName
     * @param $xml
     * @param $xml_string
     * @param $path_and_file
     * @param $model_name
     * @return array
     * @since  1.0
     */
    public static function setTableTriggersRegistry(
        $registryName, $xml, $xml_string, $path_and_file, $model_name)
    {
        $include = '';
        if (isset($xml->table->triggers->include['name'])) {
            $include = (string) $xml->table->triggers->include['name'];
        }

        if ($include == '') {
        } else {
            if ($xml_string == '') {
                $xml_string = file_get_contents($path_and_file);
            }

            $replace_this = '<include name="' . $include . '"/>';

            $xml_string = ConfigurationService::replaceIncludeStatement(
                $include, $model_name, $replace_this, $xml_string
            );
            $xml = simplexml_load_string($xml_string);
        }

        if (isset($xml->table->triggers->trigger)) {
            $triggers = $xml->table->triggers->trigger;
            $triggersArray = array();
            foreach ($triggers as $trigger) {
                $t = get_object_vars($trigger);
                $tAttr = ($t["@attributes"]);
                $triggersArray[] = $tAttr['name'];
            }
            Services::Registry()->set($registryName, 'triggers', $triggersArray);
        }

        return array($xml, $xml_string);
    }

    /**
     * Retrieves base Model Registry data and stores it to the datasource registry
     *
     * @static
     * @param $registryName
     * @param $xml
     * @param $xml_string
     * @param $path_and_file
     * @param $model_name
     * @return mixed
     */
    public static function setSpecialFieldsRegistry(
        $registryName, $xml, $xml_string, $path_and_file, $model_name)
    {

        $fieldTypes = 0;

        while ($fieldTypes < 99) {

            /** Process one field type at a time ex. parameters, metadata, customfield */
            if (isset($xml->customfields->customfield[$fieldTypes])) {

            } else {
                $fieldTypes = 9999;
                break;
            }

            $done = false;
            while ($done == false) {

                /** Process one include code statement at a time per fieldtype */
                if (isset($xml->customfields->customfield[$fieldTypes]->include['name'])) {
                    $include = (string) $xml->customfields->customfield[$fieldTypes]->include['name'];

                    if ($xml_string == '') {
                        $xml_string = file_get_contents($path_and_file);
                    }

                    $replace_this = '<include name="' . $include . '"/>';

                    $xml_string = ConfigurationService::replaceIncludeStatement(
                        $include, $model_name, $replace_this, $xml_string
                    );

                    $xml = simplexml_load_string($xml_string);
                } else {
                    $done = true;
                }
            }
            $fieldTypes++;
        }

        /** Now that all include code has been retrieved, process custom fields */
        if (isset($xml->customfields)) {
            ConfigurationService::getCustomFields(
                $xml->customfields,
                $model_name,
                $registryName
            );
        }

        return;
    }

    /**
     * processTableFile extracts XML configuration data for Tables/Models and populates Registry
     *
     * @static
     * @param $xml
     * @param $model_name
     * @param $registryName
     *
     * @return object
     * @since   1.0
     * @throws \RuntimeException
     */
    public static function getCustomFields(
        $xml, $model_name, $registryName)
    {

        $i = 0;
        $continue = true;
        $customFieldsArray = array();

        while ($continue == true) {

            if (isset($xml->customfield[$i]->field)) {
                $customfield = $xml->customfield[$i];
            } else {
                $continue = false;
                break;
            }

            $name = '';

            /** Next field  */
            if (isset($customfield['name'])) {
                $name = (string) $customfield['name'];
            }

            /** Load inherited definitions */
            $inherit = Services::Registry()->get($registryName, $name, array());
            $inheritFields = array();
            if (count($inherit) > 0) {
                foreach ($inherit as $row) {
                    foreach ($row as $field => $fieldvalue) {
                        if ($field == 'name') {
                            $inheritFields[] = $fieldvalue;
                        }
                    }
                }
            }
            $doNotInheritFields = array();

            /** Current fieldset processing */
            $fieldArray = array();

            /** Retrieve Field Attributes for each field */
            foreach ($customfield->field as $key1 => $value1) {

                $attributes = get_object_vars($value1);
                $fieldAttributes = ($attributes["@attributes"]);
                $fieldAttributesArray = array();

                foreach ($fieldAttributes as $key2 => $value2) {

                    if (in_array($key2, self::$valid_field_attributes)) {
                    } else {
                        echo 'Field attribute not known ' . $key2 . ':' . $value2
                            . ' for ' . $model_name . '<br />';
                    }

                    if ($key2 == 'name') {
                        if (in_array($value2, $inheritFields)) {
                            $doNotInheritFields[] = $value2;
                        }

                    }
                    $fieldAttributesArray[$key2] = $value2;
                }

                $fieldArray[] = $fieldAttributesArray;
            }

            if (count($inherit) > 0) {
                foreach ($inherit as $row) {
                    if (in_array($row['name'], $doNotInheritFields)) {
                    } else {
                        $fieldArray[] = $row;
                    }
                }
            }

            Services::Registry()->set($registryName, $name, $fieldArray);

            /** Track Registry names for all customfields */
            $exists = Services::Registry()->exists($registryName, 'CustomFieldGroups');

            if ($exists === true) {
                $temp = Services::Registry()->get($registryName, 'CustomFieldGroups');
            } else {
                $temp = array();
            }

            if (is_array($temp)) {
            } else {
                if ($temp == '') {
                    $temp = array();
                } else {

                    $hold = $temp;
                    $temp = array();
                    $temp[] = $hold;
                }
            }

            $temp[] = $name;

            Services::Registry()->set($registryName, 'CustomFieldGroups', array_unique($temp));

            $i++;
        }

        return;
    }

    /**
     * replaceIncludeStatement
     *
     * @static
     * @param $include
     * @param $model_name
     * @param $replace_this
     * @param $xml_string
     * @return mixed
     * @throws \RuntimeException
     */
    public static function replaceIncludeStatement(
        $include, $model_name, $replace_this, $xml_string)
    {
        $path_and_file = CONFIGURATION_FOLDER . '/include/' . $include . '.xml';

        if (file_exists($path_and_file)) {
        } else {
            throw new \RuntimeException('Include file not found: ' . $path_and_file);
        }

        try {
            $with_this = file_get_contents($path_and_file);

            return str_replace($replace_this, $with_this, $xml_string);

        } catch (\Exception $e) {
            throw new \RuntimeException (
                'Failure reading XML Include file: ' . $path_and_file . ' ' . $e->getMessage()
            );
        }
    }
}
