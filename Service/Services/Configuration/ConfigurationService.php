<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Configuration;

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
		$m = Services::Model()->connect('Sites');

		$m->model->set('id', (int)SITE_ID);
		$m->model->set('get_item_children', false);
		$m->model->set('use_special_joins', false);
		$m->model->set('add_acl_check', false);

		$items = $m->execute('load');

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

				$m = Services::Model()->connect('Applications');

				$m->model->set('id_name', APPLICATION);
				$m->model->set('name_field', 'name');
				$m->model->set('get_item_children', false);
				$m->model->set('use_special_joins', false);
				$m->model->set('add_acl_check', false);

				$items = $m->execute('load');

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
	 * loadFile is the isolated point in the application where all XML configuration files are read
	 *   That includes XML for tables, services, and the application, along with service startup
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
		if ($type == 'Application' || $type == 'Table') {
			$path_and_file = CONFIGURATION_FOLDER . '/' . $type . '/' . $file . '.xml';
		} else {
			$path_and_file = $type . '/' . $file . '.xml';
		}

		if (file_exists($path_and_file)) {
		} else {
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

		/** Process Include Code */
		$xml_string = '';

		/** <filters include="value">  */
		$include = '';
		$filters = $xml->filters;
		$include = (string)$filters['include'];
		if ($include == '') {
		} else {
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$replace_this = '<filters include="' . $include . '"/>';
			$xml_string = Services::Configuration()->processIncludeFile($include, $replace_this, $xml_string);
		}

		/** <foreignkeys include="ContentForeignkeys"/> */
		$include = '';
		$foreignkeys = $xml->foreignkeys;
		$include = (string)$foreignkeys['include'];
		if ($include == '') {
		} else {
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$replace_this = '<foreignkeys include="' . $include . '"/>';
			$xml_string = Services::Configuration()->processIncludeFile($include, $replace_this, $xml_string);
		}

		/** <triggers include="ContentTrigger"/> */
		$include = '';
		$triggers = $xml->triggers;
		$include = (string)$triggers['include'];
		if ($include == '') {
		} else {
			if ($xml_string == '') {
				$xml_string = file_get_contents($path_and_file);
			}
			$replace_this = '<triggers include="' . $include . '"/>';
			$xml_string = Services::Configuration()->processIncludeFile($include, $replace_this, $xml_string);
		}

		if ($xml_string == '') {
			return $xml;
		} else {
			return simplexml_load_string($xml_string);
		}
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
	public static function processIncludeFile($include, $replace_this, $xml_string)
	{
		$path_and_file = CONFIGURATION_FOLDER . '/table/include/' . $include . '.xml';

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
}
