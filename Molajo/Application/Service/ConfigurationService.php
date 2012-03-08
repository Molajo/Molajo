<?php
/**
 * @package	 Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license	 GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;
use Joomla\registry\Registry;

defined('MOLAJO') or die;

/**
 * Configuration
 *
 * @package	 Molajo
 * @subpackage  Service
 * @since	   1.0
 */
Class ConfigurationService

{
	/**
	 * Static instance
	 *
	 * @var	object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Configuration for Site and Application
	 *
	 * @static
	 * @var	$connection
	 * @since  1.0
	 */
	protected $configuration = array();

	/**
	 * Custom Fields
	 *
	 * @static
	 * @var	$custom_fields
	 * @since  1.0
	 */
	protected $custom_fields = array();

	/**
	 * Metadata
	 *
	 * @static
	 * @var	$metadata
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
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new ConfigurationService();
		}
		return self::$instance;
	}

	/**
	 * __construct
	 *
	 * @return  object
	 * @throws  RuntimeException
	 * @since   1.0
	 */
	protected function __construct()
	{
		return $this->connect();
	}

	/**
	 * connect
	 *
	 * @return mixed
	 * @throws Exception
	 * @since 1.0
	 */
	public function connect($configuration_file = null)
	{
		$this->configuration = new Registry();
		$siteData = new Registry();

		/** Site Configuration: php file */
		$siteData = $this->getSite($configuration_file);
		foreach ($siteData as $key => $value) {
			$this->set($key, $value);
		}

		/** Application Table entry for each application - parameters field has config */
		$appConfig = $this->getApplicationInfo();

		$this->metadata = new Registry();
		$this->metadata->loadString($appConfig->metadata);

		$this->custom_fields = new Registry;
		$this->custom_fields->loadString($appConfig->custom_fields);

		// todo: amy check this after the interface is working and not test data
		$parameters = substr($appConfig->parameters, 1, strlen($appConfig->parameters) - 2);
		$parameters = substr($parameters, 0, strlen($parameters) - 1);
		$parmArray = array();
		$parmArray = explode(',', $parameters);
		foreach ($parmArray as $entry) {
			$pair = explode(':', $entry);
			$key = substr(trim($pair[0]), 1, strlen(trim($pair[0])) - 2);
			if (trim($pair[0]) == '') {
			} else {
				$value = substr(trim($pair[1]), 1, strlen(trim($pair[1])) - 2);
				$this->set($key, $value);
			}
		}
		return $this;
	}

	/**
	 * get
	 *
	 * Retrieves a parameter value from the site/application configuration file
	 *
	 * Example usage:
	 * $row->title = Service::Configuration()->get('site_title', 'Molajo');
	 *
	 * @param  string  $key
	 * @param  string  $default
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function get($key, $default = null)
	{
		return $this->configuration->get($key, $default);
	}

	/**
	 * set
	 *
	 * Sets a value in the Site/Application Configuration
	 *
	 * Example usage:
	 * Service::Configuration()->set('sef', 1);
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function set($key, $value = null)
	{
		return $this->configuration->set($key, $value);
	}

	/**
	 * getSite
	 *
	 * retrieve site configuration object from ini file
	 *
	 * @param string $configuration_file optional
	 *
	 * @return object
	 * @throws RuntimeException
	 * @since  1.0
	 */
	protected function getSite($configuration_file = null)
	{
		if ($configuration_file === null) {
			$configuration_file = SITE_FOLDER_PATH . '/configuration.php';
		}
		$configuration_class = 'SiteConfiguration';

		if (file_exists($configuration_file)) {
			require_once $configuration_file;
		} else {
			throw new Exception('Fatal error - Application-Site Configuration File does not exist');
		}

		if (class_exists($configuration_class)) {
			$site = new $configuration_class();
		} else {
			throw new Exception('Fatal error - Configuration Class does not exist');
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

		if (MOLAJO_APPLICATION == 'installation') {

			$id = 0;
			$row->id = 0;
			$row->name = MOLAJO_APPLICATION;
			$row->path = MOLAJO_APPLICATION;
			$row->asset_type_id = MOLAJO_ASSET_TYPE_BASE_APPLICATION;
			$row->description = '';
			$row->custom_fields = '';
			$row->parameters = '';
			$row->metadata = '';

		} else {

			$class = 'Molajo\\Application\\MVC\\Model\\ApplicationsModel';
			$m = new $class();
			var_dump($m);
			die;
			$m->query->where($m->db->qn('name') .
				' = ' . $m->db->q(MOLAJO_APPLICATION));
			$result = $m->loadObject();

			$row->id = $result->id;
			$id = $result->id;
			$row->name = $result->name;
			$row->path = $result->path;
			$row->asset_type_id = $result->asset_type_id;
			$row->description = $result->description;
			$row->custom_fields = $result->custom_fields;
			$row->parameters = $result->parameters;
			$row->metadata = $result->metadata;
		}

		if (defined('MOLAJO_APPLICATION_ID')) {
		} else {
			define('MOLAJO_APPLICATION_ID', $id);
		}
		return $row;
	}
}

