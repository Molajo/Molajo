<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Install;

use Molajo\Controller\CreateController;
use Molajo\Service\Services;

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
	 * NOTE: This will become the Extensions Component
	 *
	 * @param $extension_name
	 * @param $model_name
	 * @param $source_path
	 * @param $destination_path
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function extension($extension_name, $model_name, $source_path = null, $destination_path = null)
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
