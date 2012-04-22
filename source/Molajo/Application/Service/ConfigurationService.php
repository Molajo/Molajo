<?php
/**
 * @package   Molajo
 * @copyright Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Molajo\Application\Services;

use Molajo\Application\Molajo;

use Molajo\Application\MVC\Model\TableModel;

defined('MOLAJO') or die;

/**
 * Configuration
 *
 * @package   Molajo
 * @subpackage  Service
 * @since           1.0
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
	 * Configuration for Site and Application
	 *
	 * @static
	 * @var    $connection
	 * @since  1.0
	 */
	protected $configuration = array();

	/**
	 * Custom Fields
	 *
	 * @static
	 * @var    $custom_fields
	 * @since  1.0
	 */
	protected $custom_fields = array();

	/**
	 * Metadata
	 *
	 * @static
	 * @var    $metadata
	 * @since  1.0
	 */
	protected $metadata = array();

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
			Services::Registry()->set('Configuration\\' . $key, $value);
		}

		$data = $this->getApplicationInfo();

		$xml = simplexml_load_file(APPLICATIONS_MVC . '/Model/Table/Applications.xml');

		$this->registry('Configuration\\', $data, 'parameters', 'parameter', $xml);

		/** Site Paths, Custom Fields, and Authorisation */
		Molajo::Application()->setSitePaths();

		return $this;
	}

	/**
	 * Initialise and populate registry object with xml
	 *
	 * @param $namespace
	 * @param $source
	 * @param $field_group
	 * @param $field_name
	 * @param $xml
	 */
	protected function registry($namespace, $data, $field_group, $field_name, $xml)
	{
		$registry = Services::Registry()->initialise();
		$registry->loadString($data->$field_group, 'JSON');

		if (isset($xml->$field_group->$field_name)) {
			foreach ($xml->$field_group->$field_name as $cf) {

				$name = (string)$cf['name'];
				$dataType = (string)$cf['filter'];
				$null = (string)$cf['null'];
				$default = (string)$cf['default'];
				$values = (string)$cf['values'];

				if ($default == '') {
					$val = $registry->get($name, null);
				} else {
					$val = $registry->get($name, $default);
				}

				Services::Registry()->set($namespace . $name, $val);
			}
		}
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
	public function getApplicationInfo()
	{
		$row = new \stdClass();

		if (APPLICATION == 'installation') {

			$id = 0;
			$row->id = 0;
			$row->name = APPLICATION;
			$row->path = APPLICATION;
			$row->asset_type_id = ASSET_TYPE_BASE_APPLICATION;
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
