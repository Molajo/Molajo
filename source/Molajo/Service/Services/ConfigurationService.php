<?php
/**
 * @package   Molajo
 * @copyright Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service;

use Molajo\Services;

use Molajo\Application\Molajo;

use Molajo\MVC\Model\TableModel;

defined('MOLAJO') or die;

/**
 * Configuration
 *
 * @package   	Molajo
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
	 * getConfigurationData
	 *
	 * @return bool|object
	 * @since  1.0
	 */
	public function getConfigurationData($configuration_file = null)
	{
		// todo: move all the XML into here
		// design a priority arrangement for overrides
	}

	/**
	 * __construct
	 *
	 * setSitePaths executed after ConfigurationService startup to make paths available to other services
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function __construct($configuration_file = null)
	{
		$siteData = $this->getSite($configuration_file);
		foreach ($siteData as $key => $value) {
			Service::Registry()->set('Configuration', '' . $key, $value);
		}

		$data = $this->getApplication();

		$xml = simplexml_load_file(CONFIGURATION_FOLDER . '/Table/Applications.xml');

		Service::Registry()->loadField('ApplicationCustomfields', 'custom_fields',
			$data->custom_fields, $xml->custom_fields);
		Service::Registry()->loadField('ApplicationMetadata', 'meta',
			$data->metadata, $xml->metadata);
		Service::Registry()->loadField('ApplicationParameters', 'parameters',
			$data->parameters, $xml->parameter);

		/** Site Paths, Custom Fields, and Authorisation */
		Application::setSitePaths();

		return $this;
	}

	/**
	 * getSite
	 *
	 * retrieve site configuration object from ini file
	 *
	 * @param string $configuration_file optional
	 *
	 * @return object
	 * @throws \Exception
	 * @since  1.0
	 */
	public function getSite($configuration_file = null)
	{
		if ($configuration_file === null) {
			$configuration_file = SITE_FOLDER_PATH . '/configuration.php';
		}
		$configuration_class = 'SiteConfiguration';

		if (file_exists($configuration_file)) {
			require_once $configuration_file;
		} else {
			throw new \Exception('Fatal error - Application-Site Configuration File does not exist', 100);
		}

		if (class_exists($configuration_class)) {
			$site = new $configuration_class();
		} else {
			throw new \Exception('Fatal error - Configuration Class does not exist', 100);
		}

		return $site;
	}

	/**
	 * getApplicationInfo
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	protected function getApplication()
	{
		$row = new \stdClass();

		if (APPLICATION == 'installation') {

			$id = 0;
			$row->id = 0;
			$row->name = APPLICATION;
			$row->path = APPLICATION;
			$row->catalog_type_id = CATALOG_TYPE_BASE_APPLICATION;
			$row->description = '';
			$row->custom_fields = '';
			$row->parameters = '';
			$row->metadata = '';

		} else {

			try {
				$m = new TableModel('Applications');
				$m->query->where($m->db->qn('name') .
					' = ' . $m->db->quote(APPLICATION));
				$row = $m->loadObject();

				$id = $row->id;

			} catch (\Exception $e) {
				echo 'Application will die. Exception caught in ConfigurationService: ', $e->getMessage(), "\n";
				die;
			}
		}

		if (defined('APPLICATION_ID')) {
		} else {
			define('APPLICATION_ID', $id);
		}

		return $row;
	}
}
