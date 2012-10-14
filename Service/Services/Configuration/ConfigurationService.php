<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
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
	 * @param   null $configuration_file
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function __construct($configuration_file = null)
	{
		$this->getFieldProperties();

		$this->getSite($configuration_file);

		$this->getApplication();

		$this->setSitePaths();

		$this->getActions();

		return $this;
	}

	/**
	 * getFile locates file, reads it, and return the XML
	 *
	 * Usage:
	 * Services::Configuration()->getFile('Application', 'defines');
	 *
	 * or - in classes where usage can happen before the service is activated:
	 * ConfigurationService::getFile($model_type, $model_name);
	 *
	 * @static
	 * @param string $model_name
	 * @param string $model_type
	 *
	 * @return object $xml
	 * @since  1.0
	 *
	 * @throws \RuntimeException
	 */
	public static function getFile($model_type, $model_name)
	{
		$path_and_file = ConfigurationService::locateFile($model_type, $model_name);
		if ($path_and_file === false) {
			// FAIL
		}

		$xml_string = ConfigurationService::readXMLFile($path_and_file);

		return simplexml_load_string($xml_string);
	}

	/**
	 * getModel loads registry for requested model configuration
	 *
	 * Usage:
	 * Services::Configuration()->getModel('Resource', 'Articles');
	 *
	 * or - in classes where usage can happen before the service is activated:
	 *
	 * ConfigurationService::getModel($model_type, $model_name);
	 *
	 * @static
	 * @param string $model_name
	 * @param string $model_type
	 *
	 * @return string Name of the Model Registry object
	 * @since  1.0
	 *
	 * @throws \RuntimeException
	 */
	public static function getModel($model_type, $model_name)
	{
		$registryName = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

		if (class_exists('Services')) {
			$exists = Services::Registry()->exists($registryName);
			if ($exists === true) {
				return $registryName;
			}
		}

		$path_and_file = ConfigurationService::locateFile($model_type, $model_name);
		if ($path_and_file === false) {
			// FAIL
		}

		$xml_string = ConfigurationService::readXMLFile($path_and_file);

		$results = ConfigurationService::getIncludeCode($xml_string, $model_name);

		$xml = simplexml_load_string($results);
		if (isset($xml->model)) {
			$xml = $xml->model;
		} else {
			// FAIL
		}

		Services::Registry()->createRegistry($registryName);

		ConfigurationService::inheritDefinition($registryName, $xml);

		ConfigurationService::setModelRegistry($registryName, $xml);

		$attr = array();
		foreach (self::$valid_field_attributes as $type) {
			$attr[] = array('fields', 'field', self::$valid_field_attributes);
			$attr[] = array('joins', 'join', self::$valid_join_attributes);
			$attr[] = array('foreignkeys', 'foreignkey', self::$valid_foreignkey_attributes);
			$attr[] = array('criteria', 'where', self::$valid_criteria_attributes);
			$attr[] = array('children', 'child', self::$valid_children_attributes);
			$attr[] = array('plugins', 'plugin', self::$valid_plugin_attributes);
			$attr[] = array('values', 'value', self::$valid_value_attributes);
		}

		for ($i = 0; $i < count($attr); $i++) {
			ConfigurationService::setElementsRegistry($registryName, $xml, $attr[$i][0], $attr[$i][1], $attr[$i][2]);
		}

		ConfigurationService::getCustomFields($xml, $registryName);

		return $registryName;
	}

	/**
	 * Read XML file and return results
	 *
	 * @static
	 * @param  $path_and_file
	 *
	 * @return bool|object
	 * @since  1.0
	 * @throws \RuntimeException
	 */
	protected static function readXMLFile($path_and_file)
	{
		if (file_exists($path_and_file)) {
		} else {
			echo 'Error in ConfigurationService. File not found for ' . $path_and_file;
			return false;
			//throw new \RuntimeException('File not found: ' . $path_and_file);
		}

		try {
			return file_get_contents($path_and_file);

		} catch (\Exception $e) {
			throw new \RuntimeException ('Failure reading File: ' . $path_and_file . ' ' . $e->getMessage());
		}
	}

	/**
	 * locateFile uses override and default locations to find the file requested
	 *
	 * Usage:
	 * Services::Configuration()->locateFile('Application', 'defines');
	 *
	 * @return  mixed object or void
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	protected static function locateFile($model_type, $model_name)
	{
		/** 1. Initialization */
		$model_type = trim(ucfirst(strtolower($model_type)));
		$model_name = trim(ucfirst(strtolower($model_name)));

		$path = '';

		/** 2. Single location */
		if (in_array($model_type, array('Application', 'Dbo', 'System', 'Language', 'Service', 'Resource'))) {

			if (in_array($model_type, array('Application', 'Dbo'))) {
				$path = CONFIGURATION_FOLDER . '/' . $model_type . '/' . $model_name . '.xml';
			}
			if ($model_type == 'System') {
				$path = CONFIGURATION_FOLDER . '/' . $model_type . '/' . $model_name . '/Configuration.xml';
			}
			if ($model_type == 'Language') {
				$path = EXTENSIONS . '/Language/' . $model_name . '/Configuration.xml';
			}
			if ($model_type == 'Service') {
				$path = MOLAJO_FOLDER . '/Service/Services/' . $model_name . '/Configuration.xml';
			}
			if ($model_type == 'Resource') {
				$path = EXTENSIONS . '/Resource/' . $model_name . '/Configuration.xml';
			}
			if (file_exists($path)) {
				return $path;
			}
		}

		/** 3. Overrides */
		$modeltypeArray = Services::Registry()->get('Fields', 'Modeltypes');

		if (in_array($model_type, $modeltypeArray)) {
		} else {
			echo '<br />Error found in Configuration Service. Model Type: ' . $model_type . ' is not valid ';
			echo '<br />Also sent in was Model Name' . $model_name;
			die;
			return false;
		}

		$extension_path = false;
		if (Services::Registry()->exists('Parameters', 'extension_path')) {
			$extension_path = Services::Registry()->get('Parameters', 'extension_path');
		}

		$primary_extension_path = false;
		if (Services::Registry()->exists('RouteParameters')) {
			$primary_extension_path = Services::Registry()->get('RouteParameters', 'extension_path', '');
		}

		$theme_path = false;
		if (Services::Registry()->exists('Parameters', 'theme_path')) {
			$theme_path = Services::Registry()->get('Parameters', 'theme_path');
		}

		$page_view_path = false;
		if (Services::Registry()->exists('Parameters', 'page_view_path')) {
			$page_view_path = Services::Registry()->get('Parameters', 'page_view_path');
		}

		$template_view_path = false;
		if (Services::Registry()->exists('Parameters', 'template_view_path')) {
			$template_view_path = Services::Registry()->get('Parameters', 'template_view_path');
		}

		if (in_array($model_type, array('Datalist', 'Table'))) {

			if ($extension_path === false) {
			} else {
				$path = $extension_path . '/' . $model_type . '/' . $model_name . '.xml';
				if (file_exists($path)) {
					return $path;
				}
			}
			if ($primary_extension_path === false) {
			} else {
				$path = $primary_extension_path . '/' . $model_type . '/' . $model_name . '.xml';
				if (file_exists($path)) {
					return $path;
				}
			}

			$path = EXTENSIONS . '/Resource/' . $model_name . '/' . $model_type . '.xml';
			if (file_exists($path)) {
				return $path;
			}

			$path = EXTENSIONS . '/' . $model_type . '/' . $model_name . '.xml';
			if (file_exists($path)) {
				return $path;
			}

			$path = CONFIGURATION_FOLDER . '/' . $model_type . '/' . $model_name . '.xml';
			if (file_exists($path)) {
				return $path;
			}
		}

		if ($model_type == 'Menuitem') {
			$path = EXTENSIONS . '/' . $model_type . '/' . $model_name . '/Configuration.xml';
			if (file_exists($path)) {
				return $path;
			}
		}

		/** 4. Look first in Distro, then Core */
		if ($model_type == 'Theme') {
			$path = EXTENSIONS . '/' . $model_type . '/' . $model_name . '/Configuration.xml';
			if (file_exists($path)) {
				return $path;
			}
			$path = MOLAJO_FOLDER . '/' . $model_type . '/' . $model_name . '/Configuration.xml';
			if (file_exists($path)) {
				return $path;
			}
		}

		/** 5. Look in Theme, Primary Resource, Distro, then Core */
		if (in_array($model_type, array('Page', 'Template', 'Wrap'))) {
			if ($theme_path === false) {
			} else {
				$path = $theme_path . '/View/' . $model_type . '/' . $model_name . '/Configuration.xml';
				if (file_exists($path)) {
					return $path;
				}
			}
			if ($primary_extension_path === false) {
			} else {
				$path = $primary_extension_path . '/View/' . $model_type . '/' . $model_name . '/Configuration.xml';
				if (file_exists($path)) {
					return $path;
				}
			}

			$path = EXTENSIONS . '/View/' . $model_type . '/' . $model_name . '/Configuration.xml';
			if (file_exists($path)) {
				return $path;
			}

			$path = MOLAJO_FOLDER . '/MVC/View/' . $model_type . '/' . $model_name . '/Configuration.xml';
			if (file_exists($path)) {
				return $path;
			}
		}

		/** 6. Look in Extension, Theme, Page, Template, Distro, then Core */
		if ($model_type == 'Plugin') {
			if ($primary_extension_path === false) {
			} else {
				$path = $primary_extension_path . '/' . $model_type . '/' . $model_name . '/Configuration.xml';
				if (file_exists($path)) {
					return $path;
				}
			}
			if ($theme_path === false) {
			} else {
				$path = $theme_path . '/' . $model_type . '/' . $model_name . '/Configuration.xml';
				if (file_exists($path)) {
					return $path;
				}
			}

			if ($page_view_path === false) {
			} else {
				$path = $page_view_path . '/' . $model_type . '/' . $model_name . '/Configuration.xml';
				if (file_exists($path)) {
					return $path;
				}
			}

			if ($template_view_path === false) {
			} else {
				$path = $template_view_path . '/' . $model_type . '/' . $model_name . '/Configuration.xml';
				if (file_exists($path)) {
					return $path;
				}
			}

			$path = EXTENSIONS . '/' . $model_type . '/' . $model_name . '/Configuration.xml';
			if (file_exists($path)) {
				return $path;
			}

			$path = MOLAJO_FOLDER . '/' . $model_type . '/' . $model_name . '/Configuration.xml';
			if (file_exists($path)) {
				return $path;
			}
		}

		throw new \RuntimeException('File not found for Model Type: ' . $model_type . ' Name: ' . $model_name);
	}

	/**
	 * getIncludeCode parses the xml string repeatedly until all include statements have been processed
	 *
	 * @static
	 * @param $xml_string
	 *
	 * @return mixed
	 * @throws \RuntimeException
	 * @since  1.0
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
					$path_and_file = CONFIGURATION_FOLDER . '/field/' . $include . '.xml';
				} else {
					$path_and_file = CONFIGURATION_FOLDER . '/include/' . $include . '.xml';
				}

				if (file_exists($path_and_file)) {
				} else {
					throw new \RuntimeException('Include file for ' . $include . ' not found: ' . $path_and_file);
				}

				try {
					$withThis = file_get_contents($path_and_file);

				} catch (\Exception $e) {

					throw new \RuntimeException (
						'Failure reading XML Include file: ' . $path_and_file . ' ' . $e->getMessage()
					);
				}

				$xml_string = str_replace($replaceThis, $withThis, $xml_string);

				$i++;
			}
		}

		return $xml_string;
	}

	/**
	 * Retrieves base Model Registry data and stores it to the datasource registry
	 *
	 * @static
	 * @param   $registryName
	 * @param   $xml
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	protected static function setModelRegistry($registryName, $xml)
	{

		$modelArray = Services::Registry()->get('Fields', 'Modelattributes');

		foreach ($xml->attributes() as $key => $value) {
			if (in_array($key, $modelArray)) {
				Services::Registry()->set($registryName, $key, (string)$value);
			} else {
				echo 'Failure in ConfigurationService::setModelRegistry for Registry: '
					. $registryName . ' Invalid Attribute: ' . $key;
				die;
			}
		}

		Services::Registry()->set($registryName, 'model_name',
			Services::Registry()->get($registryName, 'name'));

		return true;
	}

	/**
	 * setElementsRegistry
	 *
	 * @static
	 * @param  $registryName
	 * @param  $xml
	 * @param  $plural
	 * @param  $singular
	 * @param  $valid_attributes
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected static function setElementsRegistry($registryName, $xml, $plural, $singular, $valid_attributes)
	{
		if (isset($xml->table->$plural->$singular)) {
		} else {
			return true;
		}

		$set = $xml->table->$plural->$singular;
		$itemArray = array();

		foreach ($set as $item) {

			$attributes = get_object_vars($item);

			$itemAttributes = ($attributes["@attributes"]);
			$itemAttributesArray = array();

			foreach ($itemAttributes as $key => $value) {

				if (in_array($key, $valid_attributes)) {
				} else {
					echo ucfirst($plural) . ' Attribute not known ' . $key . ' for ' . $registryName . '<br />';
				}
				$itemAttributesArray[$key] = $value;
			}

			if ($plural == 'plugins') {
				foreach ($itemAttributesArray as $item)  {
					$itemArray[] = $item;
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

			Services::Registry()->set($registryName, $plural, $joins);

			Services::Registry()->set($registryName, 'JoinFields', $selects);

		} elseif ($plural == 'values') {

			$valuesArray = array();

			if (count($itemArray) > 0) {

				foreach ($itemArray as $value) {

					if (is_array($value)) {
						$row = $value;
					} else {
						$valueVars = get_object_vars($value);
						$row = ($valueVars["@attributes"]);
					}

					$temp = new \stdClass();

					$temp->id = $row['id'];
					$temp->value = $row['value'];

					$valuesArray[] = $temp;
				}
				Services::Registry()->set($registryName, 'values', $valuesArray);
			}

		} else {
			Services::Registry()->set($registryName, $plural, $itemArray);
		}

 		return true;
	}

	/**
	 * setJoinFields - processes one set of join field definitions, updating the registry
	 *
	 * @static
	 * @param  $itemArray
	 *
	 * @return  array
	 * @since   1.0
	 */
	protected static function setJoinFields($modelJoinArray)
	{
		$joinArray = array();
		$joinSelectArray = array();

		$joinModel = ucfirst(strtolower($modelJoinArray['model']));
		$joinRegistry = $joinModel . 'Table';

		if (Services::Registry()->exists($joinRegistry) === false) {
			$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
			$connect = new $controllerClass();

			$results = $connect->connect('Table', $joinModel);
			if ($results === false) {
				return false;
			}
		}

		$fields = Services::Registry()->get($joinRegistry, 'fields');

		$table = Services::Registry()->get($joinRegistry, 'table');

		$joinArray['table'] = $table;

		$alias = (string)$modelJoinArray['alias'];
		if (trim($alias) == '') {
			$alias = substr($table, 3, strlen($table));
		}
		$joinArray['alias'] = trim($alias);

		$select = (string)$modelJoinArray['select'];
		$joinArray['select'] = $select;

		$selectArray = explode(',', $select);

		if ((int) count($selectArray) > 0) {
			foreach ($selectArray as $s) {
				foreach ($fields as $joinSelectArray) {
					if ($joinSelectArray['name'] == $s) {
						$joinSelectArray['as_name'] = trim($alias) . '_' . trim($s);
						$joinSelectArray['alias'] = $alias;
						$joinSelectArray['table'] = $table;
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
	 * @param $xml
	 * @param $registryName
	 *
	 * @return object
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	protected static function getCustomFields($xml, $registryName)
	{
		$customFieldsArray = array();

		/** Process Custom Fields defined within the model */
		if (count($xml->customfields->customfield) > 0) {

			foreach ($xml->customfields->customfield as $custom_field) {

				$name = (string)$custom_field['name'];
				$results = ConfigurationService::getCustomFieldsSpecificGroup ($registryName, $custom_field);
				if ($results === false) {
				}  else {

					$fieldArray = $results[0];
					$fieldNames = $results[1];

					ConfigurationService::inheritCustomFieldsSpecificGroup (
						$registryName, $name, $fieldArray, $fieldNames);

					$customFieldsArray[] = $name;
				}
			}
		}

		/** Include Inherited Groups not matching existing groups */
		$exists = Services::Registry()->exists($registryName, 'CustomFieldGroups');

		if ($exists === true) {
			$inherited = Services::Registry()->get($registryName, 'CustomFieldGroups');

			if (is_array($inherited) && count($inherited) > 0) {
				foreach($inherited as $name) {

					if (in_array($name, $customFieldsArray)) {
					} else {
						$results = ConfigurationService::inheritCustomFieldsSpecificGroup ($registryName, $name);
						if ($results === false) {
						} else {
							$customFieldsArray[] = $name;
						}
					}
				}
			}
		}

		Services::Registry()->set($registryName, 'CustomFieldGroups', array_unique($customFieldsArray));

		return;
	}

	/**
	 * getCustomFieldsSpecificGroup
	 *
	 * @static
	 * @param $registryName
	 * @param $customfield
	 *
	 * @return array
	 */
	protected static function getCustomFieldsSpecificGroup ($registryName, $customfield)
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
					echo 'Field attribute not known ' . $key2 . ':' . $value2 . ' for ' . $registryName . '<br />';
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
	 * inheritCustomFieldsSpecificGroup - inherited fields are merged in with those specifically defined in model
	 *
	 * @static
	 * @param $registryName
	 * @param $name
	 * @param $fieldArray
	 * @param $fieldNames
	 *
	 * @return array
	 * @since  1.0
	 */
	protected static function inheritCustomFieldsSpecificGroup (
		$registryName, $name, $fieldArray = array(), $fieldNames = array())
	{

		$inherit = array();
		$available = Services::Registry()->get($registryName, $name, array());

		if (count($available) > 0) {
			foreach ($available as $row) {
				foreach ($row as $field => $fieldvalue) {
					if ($field == 'name') {
						if (in_array($fieldvalue, $fieldNames)) {
						} else {
							$row['field_inherited'] = 1;
							$fieldArray[] = $row;
							$fieldNames[] = $fieldvalue;
						}
					}
				}
			}
		}

		if (is_array($fieldArray) && count($fieldArray) == 0) {
			Services::Registry()->set($registryName, $name, array());
			return false;
		}

		Services::Registry()->set($registryName, $name, $fieldArray);

		return $name;
	}

	/**
	 * Inheritance checking and setup  <model name="XYZ" extends="ThisTable"/>
	 *
	 * @static
	 * @param  $registryName
	 * @param  $xml
	 *
	 * @return void
	 * @since  1.0
	 */
	protected static function inheritDefinition($registryName, $xml)
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

		$modelArray = Services::Registry()->get('Fields', 'Modeltypes');

		$extends_model_name = '';
		$extends_model_type = '';
		foreach ($modelArray as $modeltype) {
			if (ucfirst(strtolower(substr($extends, strlen($extends) - strlen($modeltype), strlen($modeltype)))) == $modeltype) {
				$extends_model_name = ucfirst(strtolower(substr($extends, 0, strlen($extends) - strlen($modeltype))));
				$extends_model_type = $modeltype;
				break;
			}
		}

		if ($extends_model_name == '') {
			$extends_model_name = ucfirst(strtolower($extends));
			$extends_model_type = 'Table';
		}

		$inheritRegistryName = $extends_model_name . $extends_model_type;

		/** Load the file and build registry - IF - the registry is not already loaded */
		if (Services::Registry()->exists($inheritRegistryName) === true) {

		} else {
			$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
			$m = new $controllerClass();
			$results = $m->connect($extends_model_type, $extends_model_name);
			if ($results === false) {
				return false;
			}
		}

		/** Begin with inherited model */
		Services::Registry()->copy($inheritRegistryName, $registryName);

		return;
	}

	/** Site and Application start up logic follows */

	/**
	 * Retrieve valid field properties: modeltype, datatype, attribute, and datalist
	 *
	 * @return object
	 * @throws \Exception
	 * @since   1.0
	 */
	protected function getFieldProperties()
	{
		Services::Registry()->createRegistry('Fields');

		if (file_exists(CONFIGURATION_FOLDER . '/Application/Fields.xml')) {
		} else {
			//throw error
		}
		$xml = simplexml_load_string(file_get_contents(CONFIGURATION_FOLDER . '/Application/Fields.xml'));

		ConfigurationService::loadFieldProperties($xml, 'modelattributes', 'modelattribute');
		ConfigurationService::loadFieldProperties($xml, 'modeltypes', 'modeltype');
		ConfigurationService::loadFieldProperties($xml, 'datatypes', 'datatype');

		ConfigurationService::loadFieldProperties($xml, 'queryelements', 'queryelement');
		$list = Services::Registry()->get('Fields', 'queryelements');
		foreach ($list as $item) {
			$field = explode(',', $item);
			ConfigurationService::loadFieldProperties($xml, $field[0], $field[1]);
		}

		self::$valid_field_attributes = Services::Registry()->get('Fields', 'fields');
		self::$valid_join_attributes = Services::Registry()->get('Fields', 'joins');
		self::$valid_foreignkey_attributes = Services::Registry()->get('Fields', 'foreignkeys');
		self::$valid_criteria_attributes = Services::Registry()->get('Fields', 'criterion');
		self::$valid_children_attributes = Services::Registry()->get('Fields', 'children');
		self::$valid_plugin_attributes = Services::Registry()->get('Fields', 'plugins');
		self::$valid_value_attributes = Services::Registry()->get('Fields', 'values');

		$datalistsArray = array();
		$datalistsArray = ConfigurationService::loadDatalists($datalistsArray, CONFIGURATION_FOLDER . '/Datalist');
		$datalistsArray = ConfigurationService::loadDatalists($datalistsArray, EXTENSIONS . '/Resource');
		$datalistsArray = ConfigurationService::loadDatalists($datalistsArray, CONFIGURATION_FOLDER . '/System');
		sort($datalistsArray);
		$datalistsArray = array_unique($datalistsArray);

		Services::Registry()->set('Fields', 'Datalists', $datalistsArray);

		return;
	}

	/**
	 * loadFieldProperties
	 *
	 * @param   $xml
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

		Services::Registry()->set('Fields', $plural, $typeArray);

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
	 */
	protected function loadDatalists($datalistsArray, $folder)
	{
		$dirRead = dir($folder);
		$path = $dirRead->path;
		while (false !== ($entry = $dirRead->read())) {
			if (is_dir($path . '/' . $entry)) {
			} else {
				$datalistsArray[] = substr($entry, 0, strlen($entry) - 4);
			}
		}
		$dirRead->close();

		return $datalistsArray;
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
	protected function getSite($configuration_file = null)
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
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$m = new $controllerClass();

		$results = $m->connect('Table', 'Site');
		if ($results === false) {
			return false;
		}

		$m->set('id', (int)SITE_ID);
		$item = $m->getData('item');

		if ($item === false) {
			throw new \RuntimeException ('Site getSite() query problem');
		}

		Services::Registry()->set('Configuration', 'site_id', (int)$item->id);
		Services::Registry()->set('Configuration', 'site_catalog_type_id', (int)$item->catalog_type_id);
		Services::Registry()->set('Configuration', 'site_name', $item->name);
		Services::Registry()->set('Configuration', 'site_path', $item->path);
		Services::Registry()->set('Configuration', 'site_base_url', $item->base_url);
		Services::Registry()->set('Configuration', 'site_description', $item->description);

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
			Services::Registry()->set('Configuration', 'application_catalog_type_id', CATALOG_TYPE_APPLICATION);
			Services::Registry()->set('Configuration', 'application_name', APPLICATION);
			Services::Registry()->set('Configuration', 'application_description', APPLICATION);
			Services::Registry()->set('Configuration', 'application_path', APPLICATION);

		} else {

			try {
				$profiler_service = 0;
				$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
				$m = new $controllerClass();

				$results = $m->connect('Table', 'Application');
				if ($results === false) {
					return false;
				}
				$m->set('name_key_value', APPLICATION);

				$item = $m->getData('item');
				if ($item === false) {
					throw new \RuntimeException ('Application getApplication() query problem');
				}

				Services::Registry()->set('Configuration', 'application_id', (int)$item->id);
				Services::Registry()->set('Configuration', 'application_catalog_type_id',
					(int)$item->catalog_type_id);
				Services::Registry()->set('Configuration', 'application_name', $item->name);
				Services::Registry()->set('Configuration', 'application_path', $item->path);
				Services::Registry()->set('Configuration', 'application_description', $item->description);

					/** Combine Application and Site Parameters into Configuration */
				$parameters = Services::Registry()->getArray('ApplicationTableParameters');
				$profiler_service = 0;

				foreach ($parameters as $key => $value) {

					if (substr($key, 0, strlen('jdatabase')) == 'jdatabase') {
					} else {

						$existing = Services::Registry()->get('Configuration', $key);

						if ($existing === 0 || trim($existing) == '' || $existing === null || $existing === false) {

							if ($value === 0 || trim($value) == '' || $value === null) {
							} else {
								Services::Registry()->set('Configuration', $key, $value);
							}
						}
					}
				}

				/** Application Metadata */
				$metadata = Services::Registry()->getArray('ApplicationTableMetadata');
				foreach ($metadata as $key => $value) {
					Services::Registry()->set('Configuration', 'metadata_' . $key, $value);
				}

				Services::Registry()->delete('Configuration', 'jdatabase*');

			} catch (\Exception $e) {
				echo 'Application will die. Exception caught in Configuration: ', $e->getMessage(), "\n";
				die;
			}
		}

		Services::Registry()->sort('Configuration');

		if ((int) Services::Registry()->get('Configuration', 'profiler_service') == 1) {
			Services::Profiler()->initiate();
		}

		Services::Cache()->initialise();

		return $this;
	}

	/**
	 * Establish media, cache, log, etc., locations for site for application use
	 *
	 * Called out of the Configurations Class construct - paths needed in startup process for other services
	 *
	 * @return mixed
	 * @since  1.0
	 */
	protected function setSitePaths()
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
			define('SITE_CACHE_FOLDER', SITE_BASE_PATH
				. '/' . Services::Registry()->get('Configuration', 'system_cache_folder', 'cache'));
		}
		if (defined('SITE_LOGS_FOLDER')) {
		} else {

			define('SITE_LOGS_FOLDER', SITE_BASE_PATH
				. '/' . Services::Registry()->get('Configuration', 'system_logs_folder', 'logs'));
		}

		/** following must be within the web document folder */
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

		/** following must be within the web document folder */
		if (defined('SITE_TEMP_FOLDER')) {
		} else {
			define('SITE_TEMP_FOLDER', SITE_BASE_PATH
				. '/' . Services::Registry()->get('Configuration', 'system_temp_folder', SITE_BASE_PATH . '/temp'));
		}
		if (defined('SITE_TEMP_URL')) {
		} else {
			define('SITE_TEMP_URL', SITE_BASE_URL_RESOURCES
				. '/' . Services::Registry()->get('Configuration', 'system_temp_url', 'temp'));
		}

		return true;
	}

	/**
	 * Get action ids and values to load into registry (to save a read on various plugins)
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function getActions()
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$m = new $controllerClass();
		$results = $m->connect('Table', 'Actions');
		if ($results === false) {
			return false;
		}

		$items = $m->getData('list');

		if ($items === false) {
			throw new \RuntimeException ('Application getApplication() getActions Query failed');
		}

		Services::Registry()->createRegistry('Actions');

		foreach ($items as $item) {
			Services::Registry()->set('Actions', $item->title, (int)$item->id);
		}

		return true;
	}
}
