<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
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
	 * @return  object
	 * @since   1.0
	 */
	public function __construct($configuration_file = null)
	{
		/** Retrieve Site Data */
		$this->getSite($configuration_file);

		/** Retrieve Application Data */
		$this->getApplication();

		/** Defines, etc., with site paths */
		$this->setSitePaths();

		/** return */
		return $this;
	}

	/**
	 * Retrieve site configuration object from ini file
	 *
	 * @param string $configuration_file optional
	 *
	 * @return  object
	 * @throws  \Exception
	 * @since   1.0
	 */
	public function getSite($configuration_file = null)
	{
		/** File Configuration */
		if ($configuration_file === null) {
			$configuration_file = SITE_FOLDER_PATH . '/configuration.php';
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
		$m = Application::Controller()->connect('Sites');

		$m->model->set('id', (int)SITE_ID);
		    echo 'here';
		$items = $m->getData('load');
		var_dump($items);
		die;

		if ($items === false) {
			throw new \RuntimeException ('Application setSiteData() query problem');
		}

		Services::Registry()->set('Site', 'id', (int)$items['id']);
		Services::Registry()->set('Site', 'catalog_type_id', (int)$items['catalog_type_id']);
		Services::Registry()->set('Site', 'name', $items['name']);
		Services::Registry()->set('Site', 'path', $items['path']);
		Services::Registry()->set('Site', 'base_url', $items['base_url']);
		Services::Registry()->set('Site', 'description', $items['description']);

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
		if (defined('SITE_NAME')) {
		} else {
			define('SITE_NAME',
			Services::Registry()->get('Configuration', 'site_name', SITE_ID));
		}

		if (defined('SITE_CACHE_FOLDER')) {
		} else {
			define('SITE_CACHE_FOLDER',
			Services::Registry()->get('Configuration', 'cache_path', SITE_FOLDER_PATH . '/cache'));
		}

		if (defined('SITE_LOGS_FOLDER')) {
		} else {
			define('SITE_LOGS_FOLDER', SITE_FOLDER_PATH . '/'
				. Services::Registry()->get('Configuration', 'logs_path', SITE_FOLDER_PATH . '/logs'));
		}

		/** following must be within the web document folder */
		if (defined('SITE_MEDIA_FOLDER')) {
		} else {
			define('SITE_MEDIA_FOLDER', SITE_FOLDER_PATH . '/'
				. Services::Registry()->get('Configuration', 'media_path', SITE_FOLDER_PATH . '/media'));
		}
		if (defined('SITE_MEDIA_URL')) {
		} else {
			define('SITE_MEDIA_URL', BASE_URL
				. Services::Registry()->get('Configuration', 'media_url', BASE_URL . 'sites/' . SITE_ID . '/media'));
		}

		/** following must be within the web document folder */
		if (defined('SITE_TEMP_FOLDER')) {
		} else {
			define('SITE_TEMP_FOLDER', SITE_FOLDER_PATH . '/'
				. Services::Registry()->get('Configuration', 'temp_path', SITE_FOLDER_PATH . '/temp'));
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
	 * @return  boolean
	 * @since   1.0
	 */
	protected function getApplication()
	{

		if (APPLICATION == 'installation') {

			Services::Registry()->set('Application', 'id', 0);
			Services::Registry()->set('Application', 'catalog_type_id', CATALOG_TYPE_BASE_APPLICATION);
			Services::Registry()->set('Application', 'name', APPLICATION);
			Services::Registry()->set('Application', 'description', APPLICATION);
			Services::Registry()->set('Application', 'path', APPLICATION);

		} else {

			try {

				$m = Application::Controller()->connect('Applications');

				$m->model->set('id_name', APPLICATION);

				$items = $m->getData('load');

				if ($items === false) {
					throw new \RuntimeException ('Application setSiteData() query problem');
				}

				Services::Registry()->set('Application', 'id', (int)$items['id']);
				Services::Registry()->set('Application', 'catalog_type_id', (int)$items['catalog_type_id']);
				Services::Registry()->set('Application', 'name', $items['name']);
				Services::Registry()->set('Application', 'path', $items['path']);
				Services::Registry()->set('Application', 'description', $items['description']);

				/** Combine Application and Site Parameters into Configuration */
				$parameters = Services::Registry()->copy('ApplicationTable', 'ItemParameters');
				foreach ($parameters as $key => $value) {
					Services::Registry()->set('Configuration', $key, $value);
				}
				Services::Registry()->set('ApplicationTable', 'ItemParameters', array());

			} catch (\Exception $e) {
				echo 'Application will die. Exception caught in Configuration: ', $e->getMessage(), "\n";
				die;
			}
		}

		if (defined('APPLICATION_ID')) {
		} else {
			define('APPLICATION_ID', Services::Registry()->get('Application', 'id'));
		}

		return $this;
	}

	/**
	 * loadFile processes all XML configuration files for the application
	 *
	 * Usage:
	 * Services::Configuration()->loadFile('Content', 'Table');
	 *
	 * @return  mixed object or void
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public static function loadFile($file, $type = 'Application')
	{
		echo $file . '<br />';
		if ($type == 'Application') {
			$path_and_file = CONFIGURATION_FOLDER . '/' . $type . '/' . $file . '.xml';

		} else if ($type == 'Table') {

			/** If table data is already loaded in Registry, just use it */
			$registryName = ucfirst(strtolower($file)) . 'Table';
			$exists = Services::Registry()->exists($registryName);
			if ($exists === true) {
				echo 'Already have ' . $registryName . '<br />';
				return $registryName;
			}

			if (file_exists(EXTENSIONS_COMPONENTS . '/' . $file . '/Manifest.xml')) {
				$path_and_file = EXTENSIONS_COMPONENTS . '/' . $file . '/Manifest.xml';
			} else {
				$path_and_file = CONFIGURATION_FOLDER . '/' . $type . '/' . $file . '.xml';
			}

		} else {
			$path_and_file = $type . '/' . $file . '.xml';
		}

		if (file_exists($path_and_file)) {
		} else {
			echo 'File not found: ' . $path_and_file;
			throw new \RuntimeException('File not found: ' . $path_and_file);
		}

		try {
			$xml = simplexml_load_file($path_and_file);

		} catch (\Exception $e) {
			throw new \RuntimeException ('Failure reading XML File: ' . $path_and_file . ' ' . $e->getMessage());
		}

		if ($type == 'Table') {
			return ConfigurationService::processTableFile($file, $type, $path_and_file, $xml);
		} else {
			return $xml;
		}
	}

	/**
	 * processTableFile extracts XML configuration data for Tables/Models and populates Registry
	 * Returns the name of the registry
	 *
	 * @static
	 * @param $file
	 * @param string $type
	 * @param $path_and_file
	 * @param $xml
	 *
	 * @return  string
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public static function processTableFile($file, $type = 'Table', $path_and_file, $xml)
	{
		/** Table only: Process Include Code */
		$xml_string = '';

		$registryName = ucfirst(strtolower($file)) . 'Table';

		echo 'In processTableFile creating Registry: ' . $registryName . ' for file: ' . $path_and_file . '<br />';

		$exists = Services::Registry()->exists($registryName);
		if ($exists === true) {
			return $registryName;
		}

		Services::Registry()->set($registryName, 'model_name', (string)$xml['name']);
		Services::Registry()->set($registryName, 'table_name', (string)$xml['table']);

		$value = (string)$xml['primary_key'];
		if ($value == '') {
			$value = 'id';
		}

		Services::Registry()->set($registryName, 'primary_key', $value);

		$value = (string)$xml['primary_prefix'];
		if ($value == '') {
			$value = 'a';
		}
		Services::Registry()->set($registryName, 'primary_prefix', $value);

		$value = (int)$xml['get_custom_fields'];
		if ($value == 0 || $value == 1 || $value == 2) {
		} else {
			$value = 1;
		}
		Services::Registry()->set($registryName, 'get_custom_fields', $value);

		$value = (int)$xml['get_item_children'];
		if ($value == 0) {
		} else {
			$value = 1;
		}
		Services::Registry()->set($registryName, 'get_item_children', $value, '0');

		$value = (int)$xml['use_special_joins'];
		if ($value == 0) {
		} else {
			$value = 1;
		}
		Services::Registry()->set($registryName, 'use_special_joins', $value, '0');

		$value = (int)$xml['check_view_level_access'];
		if ($value == 0) {
		} else {
			$value = 1;
		}
		Services::Registry()->set($registryName, 'check_view_level_access', $value, '1');

		$value = (string)$xml['data_source'];
		if ($value == '') {
			$value = 'JDatabase';
		}
		Services::Registry()->set($registryName, 'data_source', $value, 'JDatabase');

		/** Body - No registry */

		$include = '';
		if (isset($xml->model->body['include'])) {
			$include = (string)$xml->model->body['include'];
		}
		if ($include == '') {
		} else {
			$replace_this = '<body include="' . $include . '"/>';
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$xml_string = ConfigurationService::replaceIncludeStatement(
				$include, $file, $type, $replace_this, $xml_string
			);
			$xml = simplexml_load_string($xml_string);
		}

		/** Fields  */
		$known = array('name', 'type', 'null', 'default', 'length', 'customtype',
			'minimum', 'maximum', 'identity', 'shape', 'size', 'unique', 'values');

		$include = '';
		if (isset($xml->model->table->item->fields->include['name'])) {
			$include = (string)$xml->model->table->item->fields->include['name'];
		}
		if ($include == '') {
		} else {

			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}

			$replace_this = '<include name="' . $include . '"/>';

			$xml_string = ConfigurationService::replaceIncludeStatement(
				$include, $file, $type, $replace_this, $xml_string
			);
			$xml = simplexml_load_string($xml_string);
		}

		if (isset($xml->table->item->fields->field)) {

			$fields = $xml->table->item->fields->field;
			$fieldArray = array();

			foreach ($fields as $field) {

				$attributes = get_object_vars($field);
				$fieldAttributes = ($attributes["@attributes"]);
				$fieldAttributesArray = array();

				while (list($key, $value) = each($fieldAttributes)) {

					if (in_array($key, $known)) {
					} else {
						echo 'Field attribute not known ' . $key . ' for ' . $file . '<br />';
					}
					$fieldAttributesArray[$key] = $value;
				}
				$fieldArray[] = $fieldAttributesArray;
			}
			Services::Registry()->set($registryName, 'Fields', $fieldArray);
		}

		/** Foreign Keys */

		$include = '';
		if (isset($xml->table->item->foreignkeys->foreignkey['include'])) {
			$include = (string)$xml->table->item->foreignkeys->foreignkey['include'];
		}
		if ($include == '') {
		} else {
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$replace_this = '<foreignkey include="' . $include . '"/>';
			$xml_string = ConfigurationService::replaceIncludeStatement(
				$include, $file, $type, $replace_this, $xml_string
			);
			$xml = simplexml_load_string($xml_string);
		}

		if (isset($xml->table->item->foreignkeys->foreignkey)) {

			$fks = $xml->table->item->foreignkeys->foreignkey;
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
			Services::Registry()->set($registryName, 'ForeignKeys', $fkArray);
		}

		/** Children */

		$include = '';
		if (isset($xml->table->item->children->child['include'])) {
			$include = (string)$xml->table->item->children->child['include'];
		}
		if ($include == '') {
		} else {
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$replace_this = '<child include="' . $include . '"/>';
			$xml_string = ConfigurationService::replaceIncludeStatement(
				$include, $file, $type, $replace_this, $xml_string
			);
			$xml = simplexml_load_string($xml_string);
		}

		if (isset($xml->table->item->children->child)) {

			$cs = $xml->table->item->children->child;
			$csArray = array();
			foreach ($cs as $c) {

				$chVars = get_object_vars($c);
				$chAttributes = ($chVars["@attributes"]);
				$chkAttributesArray = array();

				$chkAttributesArray['name'] = $chAttributes['name'];
				$temp = $chAttributes['join'];
				$keys = explode(':', $temp);
				$chkAttributesArray['join_to_key'] = $keys[0];
				$chkAttributesArray['this_table_key'] = $keys[1];

				$csArray[] = $chkAttributesArray;
			}
			Services::Registry()->set($registryName, 'Children', $csArray);
		}

		/** Triggers */

		$include = '';
		if (isset($xml->table->item->triggers->trigger['include'])) {
			$include = (string)$xml->table->item->triggers->trigger['include'];
		}

		if ($include == '') {
		} else {
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$replace_this = '<trigger include="' . $include . '"/>';
			$xml_string = ConfigurationService::replaceIncludeStatement(
				$include, $file, $type, $replace_this, $xml_string
			);
			$xml = simplexml_load_string($xml_string);
		}

		if (isset($xml->table->item->triggers->trigger)) {
			$triggers = $xml->table->item->triggers->trigger;
			$triggersArray = array();
			foreach ($triggers as $trigger) {
				$t = get_object_vars($trigger);
				$tAttr = ($t["@attributes"]);
				$triggersArray[] = $tAttr['name'];
			}
			Services::Registry()->set($registryName, 'Triggers', $triggersArray);
		}

		/** Item Customfields */
		$include = 'x';
		while ($include != '') {

			if (isset($xml->table->item->customfields->customfield->include['name'])) {
				$include = (string)$xml->table->item->customfields->customfield->include['name'];
			} else {
				$include = '';
				$done = true;
				break;
			}

			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}

			$replace_this = '<include name="' . $include . '"/>';

			$xml_string = ConfigurationService::replaceIncludeStatement(
				$include, $file, $type, $replace_this, $xml_string
			);

			$xml = simplexml_load_string($xml_string);
		}

		$temp = '';
		if (isset($xml->table->item->customfields)) {
			$temp = $xml->table->item->customfields;
			ConfigurationService::getCustomFields($temp, $file, $known, $registryName);
		}

		/** Component Customfields */
		$include = 'x';
		while ($include != '') {

			if (isset($xml->table->component->customfields->customfield->include['name'])) {
				$include = (string)$xml->table->component->customfields->customfield->include['name'];
			} else {
				$include = '';
				$done = true;
				break;
			}

			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}

			$replace_this = '<include name="' . $include . '"/>';

			$xml_string = ConfigurationService::replaceIncludeStatement(
				$include, $file, $type, $replace_this, $xml_string
			);

			$xml = simplexml_load_string($xml_string);
		}

		$temp = '';
		if (isset($xml->table->component->customfields)) {
			$temp = $xml->table->component->customfields;
			ConfigurationService::getCustomFields($temp, $file, $known, $registryName);
		}

		return $registryName;
	}

	/**
	 * replaceIncludeStatement retrieves the specified include file and substitutes the contents
	 * for the include statement
	 *
	 * @static
	 * @return  object
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public static function replaceIncludeStatement($include, $file, $type, $replace_this, $xml_string)
	{
		if ($type == 'Application') {
			$path_and_file = CONFIGURATION_FOLDER . '/' . $type . '/include/' . $include . '.xml';

		} else if ($type == 'Table') {
			if (file_exists(EXTENSIONS_COMPONENTS . '/' . $file . '/include/' . $include . '.xml')) {
				$path_and_file = EXTENSIONS_COMPONENTS . '/' . $file . '/include/' . $include . '.xml';
			} else {
				$path_and_file = CONFIGURATION_FOLDER . '/' . $type . '/include/' . $include . '.xml';
			}

		} else {
			$path_and_file = $type . '/include/' . $include . '.xml';
		}

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


	/**
	 * processTableFile extracts XML configuration data for Tables/Models and populates Registry
	 *
	 * @static
	 * @param $xml
	 * @param $file
	 * @param $known - list of valid field attributes
	 * @param $registryName
	 *
	 * @return  object
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public static function getCustomFields($xml, $file, $known, $registryName)
	{
		$i = 0;
		$done = false;
		while ($done == false) {

			$work = $xml->customfield[$i];
			$customfieldArray = array();

			if (isset($work['name'])) {
				$customfieldArray['name'] = (string)$work['name'];
				$customfieldArray['registry'] = ucfirst(strtolower($file)) . (string)$work['registry'];
			} else {
				$done = true;
				break;
			}

			if (isset($work->field['name'])) {
				$fields = $work->field;
			} else {
				$done = true;
				break;
			}

			$fieldArray = array();
			foreach ($fields as $field) {

				$attributes = get_object_vars($field);
				$fieldAttributes = ($attributes["@attributes"]);
				$fieldAttributesArray = array();
				while (list($key, $value) = each($fieldAttributes)) {
					if (in_array($key, $known)) {
					} else {
						echo 'Field attribute not known ' . $key . ' for ' . $file . '<br />';
					}
					$fieldAttributesArray[$key] = $value;
				}

				$fieldArray[] = $fieldAttributesArray;
			}

			$customfieldArray['fields'] = $fieldArray;

			Services::Registry()->set($registryName, $customfieldArray['registry'], $customfieldArray);

			$i++;
		}
	}
}
