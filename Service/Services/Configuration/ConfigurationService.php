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
	 * @return object $xml
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

		$xml_string = ConfigurationService::getIncludeCode($xml_string);

		$xml = simplexml_load_string($xml_string);
		if (isset($xml->model)) {
			$xml = $xml->model;
		} else {
			// FAIL
		}

		Services::Registry()->createRegistry($registryName);

		ConfigurationService::inheritDefinition($registryName, $xml);

		ConfigurationService::setModelRegistry($registryName, $xml);

		$attributes = array();
		$attributes[] = array('fields', 'field');
		$attributes[] = array('joins', 'join');
		$attributes[] = array('foreignkeys', 'foreignkey');
		$attributes[] = array('criteria', 'where');
		$attributes[] = array('children', 'child');
		$attributes[] = array('plugins', 'plugin');
		$attributes[] = array('values', 'value');

		for ($i = 0; $i < count($attributes); $i++) {
			ConfigurationService::setElementsRegistry($registryName, $xml, $attributes[$i][0], $attributes[$i][1]);
		}

		if (isset($xml->customfields)) {
			ConfigurationService::getCustomFields($xml->customfields, $model_name, $registryName);
		}

		return $registryName;
	}

	/**
	 * Read XML file and return results
	 *
	 * @static
	 * @param $path_and_file
	 * @return bool|object
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
	 * @throws \RuntimeException
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
	public static function getIncludeCode($xml_string)
	{
		if (trim($xml_string) == '') {
			return $xml_string;
		}

		$replaceThis = array();
		$withThis = array();
		$pattern = '/<include (.*)="(.*)"\/>/';

		$done = false;
		while ($done === false) {

			preg_match_all($pattern, $xml_string, $matches);
			if (count($matches[1]) == 0) {
				break;
			}

			$i = 0;
			foreach ($matches[1] as $match) {

				$replaceThis[] = $matches[0][$i];

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
					$withThis[] = file_get_contents($path_and_file);

				} catch (\Exception $e) {
					throw new \RuntimeException (
						'Failure reading XML Include file: ' . $path_and_file . ' ' . $e->getMessage()
					);
				}

				$i++;
			}

			$text = str_replace($replaceThis, $withThis, $xml_string);
			$xml_string = $text;
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
	public static function setModelRegistry($registryName, $xml)
	{
		foreach ($xml->attributes() as $key => $value) {
			Services::Registry()->set($registryName, $key, (string)$value);
		}

		Services::Registry()->set($registryName, 'model_name',
			Services::Registry()->get($registryName, 'name'));

		return true;
	}

	/**
	 * setElementsRegistry
	 *
	 * @static
	 * @param   $registryName
	 * @param   $xml
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public static function setElementsRegistry($registryName, $xml, $plural, $singular)
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

				if (in_array($key, self::$valid_field_attributes)) {
				} else {
					echo $singular . ' Attribute not known ' . $key . ' for ' . $registryName . '<br />';
				}

				switch ($plural) {
					case 'fields':
						$itemAttributesArray[$key] = $value;
						break;

					case 'joins': //model, alias, select, joinwith, jointo
						$itemAttributesArray[$key] = $value;
						break;

					case 'foreignkeys': //name, source_id, source_model and required
						$itemAttributesArray[$key] = $value; //done
						break;

					case 'criteria': //name, connector, value
						$itemAttributesArray[] = $value;
						break;

					case 'children': // name and join
						$itemAttributesArray[] = $value;
						break;

					case 'plugins': //name
						$itemAttributesArray[] = $value;
						break;

					case 'values': //id value
						$itemAttributesArray[] = $value;

						$values = $value;
						$valuesArray = array();
						foreach ($values as $value) {
							$t = get_object_vars($value);
							$tXXX = ($t["@attributes"]);

							$temp = new \stdClass();

							$temp->id = $tXXX['id'];
							$temp->value = $tXXX['value'];

							$valuesArray[] = $temp;
						}
						Services::Registry()->set($registryName, 'values', $valuesArray);
						break;
				}


			}
			$itemArray[] = $itemAttributesArray;
		}

		if ($plural == 'joins') {
			$joins = array();
			$selects = array();

			echo ' PRIOR TO LOOP <br />';
			echo '<pre>';
			var_dump($itemArray);
			echo '</pre>';

			for ($i = 0; $i < count($itemArray); $i++) {

				echo ' in loop <br />';
				echo '<pre>';
				var_dump($itemArray[$i]);
				echo '</pre>';

				$temp = ConfigurationService::setJoinFields($itemArray[$i]);
				$joins[] = $temp[0];
				$selects[] = $temp[1];
			}
			echo '<pre>After loop';
			var_dump($joins);
			echo '</pre>';

			echo '<pre>';
			var_dump($selects);
			echo '</pre>';

			die;
		} else {

			Services::Registry()->set($registryName, $plural, $itemArray);
		}
//		echo '<pre>';
//		Services::Registry()->get($registryName, $plural);
//		echo '</pre>';

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
				echo $joinRegistry;
				die;
				return false;
			}
		}
		    echo 'after connect ' . $joinRegistry . '<br />';
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
		foreach ($selectArray as $s) {
			foreach ($fields as $joinSelectArray) {
				if ($joinSelectArray['name'] == $s) {
					$joinSelectArray['as_name'] = trim($alias) . '_' . trim($s);
					$joinSelectArray['alias'] = $alias;
					$joinSelectArray['table'] = $table;
				}
			}
		}

		$joinArray['jointo'] = (string)$modelJoinArray['jointo'];
		$joinArray['joinwith'] = (string)$modelJoinArray['joinwith'];
		die;
		return array($joinArray, $fields);
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
				$name = (string)$customfield['name'];
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
						echo 'Field attribute not known ' . $key2 . ':' . $value2 . ' for ' . $registryName . '<br />';
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
	 * Inheritance checking and setup  <model name="XYZ" extends="ThisTable"/>
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

		$parentRegistryName = $extends_model_name . $extends_model_type;

		/** Load the file and build registry - IF - the registry is not already loaded */
		if (Services::Registry()->exists($parentRegistryName) == true) {
		} else {

			$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
			$m = new $controllerClass();
			$results = $m->connect($extends_model_type, $extends_model_name);
			if ($results == false) {
				return false;
			}
		}

		/** Begin with inherited model */
		Services::Registry()->copy($parentRegistryName, $registryName);

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
		/** 1. Initialize Registry */
		Services::Registry()->createRegistry('Fields');

		/** 2. Verify File Exists */
		if (file_exists(CONFIGURATION_FOLDER . '/Application/Fields.xml')) {
		} else {
			//throw error
		}
		$xml = simplexml_load_string(file_get_contents(CONFIGURATION_FOLDER . '/Application/Fields.xml'));

		/** 3. Load Valid Modeltypes */
		if (isset($xml->modeltypes->modeltype)) {
		} else {
			//throw error
		}
		$modeltypes = $xml->modeltypes->modeltype;
		$modeltypeArray = array();
		foreach ($modeltypes as $modeltype) {
			$modeltypeArray[] = (string)$modeltype;
		}

		Services::Registry()->set('Fields', 'Modeltypes', $modeltypeArray);

		/** 4. Load Valid Field Datatypes */
		if (isset($xml->datatypes->datatype)) {
		} else {
			//throw error
		}
		$datatypes = $xml->datatypes->datatype;
		$datatypeArray = array();
		foreach ($datatypes as $datatype) {
			$datatypeArray[] = (string)$datatype;
		}

		Services::Registry()->set('Fields', 'Datatypes', $datatypeArray);

		/** 5. Load Valid Field Properties */
		if (isset($xml->attributes->attribute)) {
		} else {
			//throw error
		}
		$attributes = $xml->attributes->attribute;
		$attributeArray = array();
		foreach ($attributes as $attribute) {
			$attributeArray[] = (string)$attribute;
		}

		Services::Registry()->set('Fields', 'Attributes', $attributeArray);
		self::$valid_field_attributes = $attributeArray;

		/** 6. Load Valid Datalists */
		$datalistsArray = array();
		$dirRead = dir(CONFIGURATION_FOLDER . '/Datalist');
		$path = $dirRead->path;
		while (false !== ($entry = $dirRead->read())) {
			if (is_dir($path . '/' . $entry)) {
			} else {
				$datalistsArray[] = substr($entry, 0, strlen($entry) - 4);
			}
		}
		$dirRead->close();

		/** 7. Load Datalists from Resources */
		$dirRead = dir(EXTENSIONS . '/Resource');
		$path = $dirRead->path;
		while (false !== ($entry = $dirRead->read())) {
			if (is_dir($path . '/' . $entry)) {
				if (substr($entry, 0, 1) == '.') {
				} else {
					$datalistsArray[] = $entry;
				}
			}
		}
		$dirRead->close();

		/** 8. Load Datalists from System */
		$dirRead = dir(CONFIGURATION_FOLDER . '/System');
		$path = $dirRead->path;
		while (false !== ($entry = $dirRead->read())) {
			if (is_dir($path . '/' . $entry)) {
				if (substr($entry, 0, 1) == '.') {
				} else {
					$datalistsArray[] = $entry;
				}
			}
		}
		$dirRead->close();

		/** Sort and unique */
		sort($datalistsArray);
		$datalistsArray = array_unique($datalistsArray);

		Services::Registry()->set('Fields', 'Datalists', $datalistsArray);

		return;
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
		if ($results == false) {
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
			Services::Registry()->set('Configuration', 'application_catalog_type_id', CATALOG_TYPE_BASE_APPLICATION);
			Services::Registry()->set('Configuration', 'application_name', APPLICATION);
			Services::Registry()->set('Configuration', 'application_description', APPLICATION);
			Services::Registry()->set('Configuration', 'application_path', APPLICATION);

		} else {

			try {
				$profiler = 0;
				$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
				$m = new $controllerClass();

				$results = $m->connect('Table', 'Application');
				if ($results == false) {
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

		if ((int)$profiler == 1) {
			Services::Profiler()->initiate();
		}

		if ((int)$cache == 1 && class_exists(Services)) {
			Services::Cache()->startCache();
			Services::Registry()->set('cache', true);
		} else {
			Services::Registry()->set('cache', false);
		}

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
		if ($results == false) {
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

		return;
	}
}
