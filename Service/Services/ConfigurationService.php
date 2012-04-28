<?php
/**
 * @package   Molajo
 * @copyright Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

use Molajo\Service\Services;

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
	 * Retrieve Site and Application data, set constants and paths
	 *
	 * Note: setSitePaths executed after Configuration startup to make paths available to other services
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
		$items = $model->query('Sites');
		foreach ($items as $item) {
			Services::Registry()->set('action_to_action_id', $item->title, (int)$item->id);
		}


		$m = new TableModel ('Sites');

		$m->query->where($m->db->qn('id') . ' = ' . (int)SITE_ID);

		$results = $m->loadAssoc();

		if ($results === false) {
			throw new \RuntimeException ('Application setSiteData() query problem');
		}

		Services::Registry()->set('Site', 'id', (int)$results['id']);
		Services::Registry()->set('Site', 'catalog_type_id', (int)$results['catalog_type_id']);
		Services::Registry()->set('Site', 'name', $results['name']);
		Services::Registry()->set('Site', 'description', $results['description']);
		Services::Registry()->set('Site', 'path', $results['path']);

		/** Registry for Custom Fields and Metadata */
		$xml = Services::Registry()->loadFile('Sites', 'Table');

		Services::Registry()->loadField(
			'SiteCustomfields',
			'custom_field',
			$results['custom_fields'],
			$xml->custom_fields
		);
		Services::Registry()->loadField(
			'SiteMetadata',
			'meta',
			$results['metadata'],
			$xml->metadata
		);
		Services::Registry()->loadField(
			'SiteParameters',
			'parameter',
			$results['parameters'],
			$xml->parameters
		);

		Services::Registry()->set('Site', 'base_url', $results['base_url']);

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
				. Services::Registry()->get('Configuration', 'temp_path',  SITE_FOLDER_PATH . '/temp'));
		}
		if (defined('SITE_TEMP_URL')) {
		} else {
			define('SITE_TEMP_URL', BASE_URL
				. Services::Registry()->get('Configuration', 'temp_url', BASE_URL . 'sites/' . SITE_ID . '/temp'));
		}

		return true;
	}

	/**
	 * getApplication
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
				echo 'Application will die. Exception caught in Configuration: ', $e->getMessage(), "\n";
				die;
			}
		}

		if (defined('APPLICATION_ID')) {
		} else {
			define('APPLICATION_ID', $id);
		}

		Services::Registry()->set('Application', 'id', (int)$row->id);
		Services::Registry()->set('Application', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('Application', 'name', $row->name);
		Services::Registry()->set('Application', 'description', $row->description);
		Services::Registry()->set('Application', 'path', $row->path);

		$xml = Services::Registry()->loadFile('Applications', 'Table');

		Services::Registry()->loadField(
			'ApplicationCustomfields',
			'custom_field',
			$row->custom_fields,
			$xml->custom_fields
		);
		Services::Registry()->loadField(
			'ApplicationMetadata',
			'meta',
			$row->metadata,
			$xml->metadata
		);
		Services::Registry()->loadField(
			'ApplicationParameters',
			'parameter',
			$row->parameters,
			$xml->parameters
		);
	}
}
