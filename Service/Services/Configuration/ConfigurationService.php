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
			Services::Registry()->set('SiteParameters', $key, $value);
		}

		/** Retrieve Sites Data from DB */
		$m = Application::Controller()->connect('Sites');

		$m->model->set('id', (int)SITE_ID);
		$m->model->set('get_item_children', false);
		$m->model->set('use_special_joins', false);
		$m->model->set('check_view_level_access', false);

		$items = $m->getData('load');

		if ($items === false) {
			throw new \RuntimeException ('Application setSiteData() query problem');
		}

		Services::Registry()->set('Site', 'id', (int)$items['id']);
		Services::Registry()->set('Site', 'catalog_type_id', (int)$items['catalog_type_id']);
		Services::Registry()->set('Site', 'name', $items['name']);
		Services::Registry()->set('Site', 'description', $items['description']);
		Services::Registry()->set('Site', 'path', $items['path']);
		Services::Registry()->set('Site', 'base_url', $items['base_url']);

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
				$m->model->set('name_field', 'name');
				$m->model->set('get_item_children', false);
				$m->model->set('use_special_joins', false);
				$m->model->set('check_view_level_access', false);

				$items = $m->getData('load');

				if ($items === false) {
					throw new \RuntimeException ('Application setSiteData() query problem');
				}

				Services::Registry()->set('Application', 'id', (int)$items['id']);
				Services::Registry()->set('Application', 'catalog_type_id', (int)$items['catalog_type_id']);
				Services::Registry()->set('Application', 'name', $items['name']);
				Services::Registry()->set('Application', 'description', $items['description']);
				Services::Registry()->set('Application', 'path', $items['path']);

				Services::Registry()->copy('ApplicationParameters', 'Configuration');

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
	 * loadFile is the point in the application where all XML configuration files are read
	 *
	 * Usage:
	 * Services::Configuration()->loadFile('Content', 'Table');
	 *
	 * todo: add php spl priority for loading and a little more thinking on API options (ini? json?)
	 *
	 * @return  object
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public static function loadFile($file, $type = 'Application')
	{
		if ($type == 'Application') {
			$path_and_file = CONFIGURATION_FOLDER . '/' . $type . '/' . $file . '.xml';

		} else if ($type == 'Table') {

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
			die;
			throw new \RuntimeException('File not found: ' . $path_and_file);
		}

		try {
			$xml = simplexml_load_file($path_and_file);

		} catch (\Exception $e) {
			throw new \RuntimeException ('Failure reading XML File: ' . $path_and_file . ' ' . $e->getMessage());
		}

		if ($type == 'Table') {
		} else {
			return $xml;
		}

		/** Table only: Process Include Code */
		$xml_string = '';

		/** <body include="XYZ">  */
		$include = '';
		$include = (string)$xml->config->body['include'];
		if (isset($xml->config->body['include'])) {
			$include = (string)$xml->config->body['include'];
		}
		if ($include == '') {
		} else {
			$replace_this = '<body include="' . $include . '"/>';
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$xml_string = Services::Configuration()->processIncludeFile($include, $type, $replace_this, $xml_string);
			$xml = simplexml_load_string($xml_string);
		}

		/** <filters include="XYZ">  */
		$include = '';
		if (isset($xml->config->table->item->filters['include'])) {
			$include = (string)$xml->config->table->item->filters['include'];
		}
		if ($include == '') {
		} else {
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$replace_this = '<filters include="' . $include . '"/>';
			$xml_string = Services::Configuration()->processIncludeFile($include, $type, $replace_this, $xml_string);
		}

		/** <foreignkeys include="XYZ"/> */
		$include = '';
		if (isset($xml->config->table->item->foreignkeys['include'])) {
			$include = (string)$xml->config->table->item->foreignkeys['include'];
		}
		if ($include == '') {
		} else {
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$replace_this = '<foreignkeys include="' . $include . '"/>';
			$xml_string = Services::Configuration()->processIncludeFile($include, $type, $replace_this, $xml_string);
			$xml = simplexml_load_string($xml_string);
		}

		/** <children include="XYZ"/> */
		$include = '';
		if (isset($xml->config->table->item->children['include'])) {
			$include = (string)$xml->config->table->item->children['include'];
		}
		if ($include == '') {
		} else {
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$replace_this = '<children include="' . $include . '"/>';
			$xml_string = Services::Configuration()->processIncludeFile($include, $type, $replace_this, $xml_string);
			$xml = simplexml_load_string($xml_string);
		}

		/** <triggers include="XYZ"/> */
		$include = '';
		if (isset($xml->config->table->item->triggers['include'])) {
			$include = (string)$xml->config->table->item->triggers['include'];
		}
		if ($include == '') {
		} else {
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			echo 'asdfasfdas';
			$replace_this = '<triggers include="' . $include . '"/>';
			$xml_string = Services::Configuration()->processIncludeFile($include, $type, $replace_this, $xml_string);
			$xml = simplexml_load_string($xml_string);
		}

		/** Item Customfields <element include="XYZ"/> */
		$temp = '';
		if (isset($xml->config->table->item->customfields)) {
			$temp = $xml->config->table->item->customfields;
		}

		$include = 'x';
		$i = 0;
		while ($include != '') {

			$include = '';
			if (isset($temp->customfield[$i]->element['include'])) {
				$include = (string)$temp->customfield[$i]->element['include'];
			}

			if ($include == '') {
				$done = true;
				break;
			} else {

				if ($xml_string == '') {
					$xml_string = file_get_contents($path_and_file);
				}

				$replace_this = '<element include="' . $include . '"/>';
				$xml_string = Services::Configuration()->processIncludeFile($include, $type, $replace_this, $xml_string);
				$xml = simplexml_load_string($xml_string);
			}
			$i++;
		}

		/** Component Customfields <element include="XYZ"/> */
		$temp = '';
		if (isset($xml->config->table->component->customfields)) {
			$temp = $xml->config->table->component->customfields;
		}

		$include = 'x';
		$i = 0;
		while ($include != '') {

			$include = '';
			if (isset($temp->customfield[$i]->element['include'])) {
				$include = (string)$temp->customfield[$i]->element['include'];
			}

			if ($include == '') {
				$done = true;
				break;
			} else {

				if ($xml_string == '') {
					$xml_string = file_get_contents($path_and_file);
				}

				$replace_this = '<element include="' . $include . '"/>';
				$xml_string = Services::Configuration()->processIncludeFile($include, $type, $replace_this, $xml_string);
				$xml = simplexml_load_string($xml_string);
			}
			$i++;
		}

		return $xml;
	}

	/**
	 * includeFile retrieves the specified include file and substitutes it in for the include statement
	 *
	 * Usage:
	 * Services::Configuration()->includeFile($includename);
	 *
	 * @return  object
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public static function processIncludeFile($include, $type, $replace_this, $xml_string)
	{
		$path_and_file = CONFIGURATION_FOLDER . '/' . $type . '/include/' . $include . '.xml';

		if (file_exists($path_and_file)) {
		} else {
			throw new \RuntimeException('Include file not found: ' . $path_and_file);
		}

		try {
			$with_this = file_get_contents($path_and_file);
			return str_replace($replace_this, $with_this, $xml_string);

		} catch (\Exception $e) {
			throw new \RuntimeException ('Failure reading XML Include file: ' . $path_and_file . ' ' . $e->getMessage());
		}
	}

	/**
	 * addSpecialFields
	 *
	 * Method used in load sequence to optionally expand special fields
	 * for Item, either into the Registry or so that the fields can be used
	 * normally
	 *
	 * $param $fields xml string beginning with the fields section (literals fields, extension or category)
	 * $data  associative array containing custom fields
	 * $retrieval_method - 1: populate registry or 2: return as columns in $data associative array
	 *
	 * @return  array
	 * @since   1.0
	 */
	public static function addSpecialFields($fields, $queryResults, $retrieval_method)
	{
		/**
		echo '<pre>';
		var_dump($fields);
		echo '</pre>';
		 */
		if (count($fields->field) > 0) {
		} else {
			return $queryResults;
		}

		/** Process each field namespace  */
		foreach ($fields->field as $ns) {

			$field_name = (string)$ns['name'];

			$namespace = (string)$ns['registry'];

			if ((is_array($queryResults) && isset($queryResults[$field_name]))
				|| (is_object($queryResults) && isset($queryResults->$field_name))
			) {

				if (is_array($queryResults)) {
					$jsonData = $queryResults[$field_name];
				} else {
					$jsonData = $queryResults->$field_name;
				}

				$custom_field = json_decode($jsonData);

				$elementArray = array();

				/** Place field names into named pair array */
				$lookup = array();

				if (count($custom_field) > 0) {
					foreach ($custom_field as $key => $value) {
						$lookup[$key] = $value;
					}
				}

				if (count($ns->element) > 0) {

					foreach ($ns->element as $element) {

						$name = (string)$element['name'];
						$name = strtolower($name);
						$dataType = (string)$element['filter'];
						$null = (string)$element['null'];
						$default = (string)$element['default'];
						$values = (string)$element['values'];

						if ($default == '') {
							$default = null;
						}

						/** Use value, if exists, or defined default */
						if (isset($lookup[$name])) {
							$setValue = $lookup[$name];
						} else {
							$setValue = $default;
						}

						/** Filter Input and Save the Registry */
						//$set = $this->filterInput($name, $set, $dataType, $null, $default);

						if ($retrieval_method == 2) {
							if (is_array($queryResults)) {
								$queryResults[$name] = $setValue;
							} else {
								$queryResults->$name = $setValue;
							}
						} else {
							Services::Registry()->set($namespace, $name, $setValue);
						}
					}
				}
			}
		}

		return $queryResults;
	}

	/**
	 * filterInput
	 *
	 * @param   string  $name         Name of input field
	 * @param   string  $field_value  Value of input field
	 * @param   string  $dataType     Datatype of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	protected function filterInput(
		$name, $value, $dataType, $null = null, $default = null)
	{

		try {
			$value = Services::Filter()
				->filter(
				$value,
				$dataType,
				$null,
				$default
			);

		} catch (\Exception $e) {
			//todo: errors
			echo $e->getMessage() . ' ' . $name;
		}

		return $value;
	}
}
