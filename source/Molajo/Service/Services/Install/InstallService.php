<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Install;

use Molajo\Service\Services;
use Molajo\Controller\CreateController;

defined('MOLAJO') or die;

/**
 * Install
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class InstallService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new InstallService();
		}

		return self::$instance;
	}

	/**
	 * Install Extension
	 *
	 * @param $extension_name
	 * @param $catalog_type_id
	 * @param $source_path
	 * @param $destination_path
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function installExtension($extension_name, $model_name,
									 $source_path = null, $destination_path = null)
	{
		/** Create Extension and Extension Instances Row */
		$controller = new CreateController();

		$table_registry_name = ucfirst(strtolower($model_name)) . 'Table';

		$data = new \stdClass();
		$data->title = $extension_name;
		$data->model_name = $model_name;

		$controller->data = $data;

		$id = $controller->create();
		if ($id === false) {
			//install failed
			return false;
		}

		/** Site Extension Instances */
		$controller = new CreateController();

		$data = new \stdClass();
		$data->site_id = SITE_ID;
		$data->extension_instance_id = $id;
		$data->model_name = 'SiteExtensionInstances';

		$controller->data = $data;

		$results = $controller->create();
		if ($results === false) {
			//install failed
			return false;
		}

		/** Application Extension Instances */
		$controller = new CreateController();

		$data = new \stdClass();
		$data->application_id = APPLICATION_ID;
		$data->extension_instance_id = $id;
		$data->model_name = 'ApplicationExtensionInstances';

		$controller->data = $data;

		$results = $controller->create();
		if ($results === false) {
			//install failed
			return false;
		}

		/** Catalog */
		$controller = new CreateController();

		$data = new \stdClass();
		$data->catalog_type_id = Services::Registry()->get($table_registry_name, 'catalog_type_id');
		$data->source_id = $id;
		$data->view_group_id = 1;
		$data->extension_instance_id = $id;
		$data->model_name = 'Catalog';

		$controller->data = $data;

		$catalog_id = $controller->create();
		if ($results === false) {
			//install failed
			return false;
		}

		/** Catalog Activity */

		/** Permissions */

		/** Complete */
		return true;
	}

	/**
	 * copyFiles
	 *
	 * @param $extension_name
	 * @param $catalog_type_id
	 * @param $source_path
	 * @param $destination_path
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function copyFiles($extension_name, $extension_type, $source_path, $destination_path)
	{
		return true;
	}
}
