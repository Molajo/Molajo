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
	public function extension()
	{
		$results = $this->testTrigger();
	}

	public function testCreateExtension($extension_name, $model_name, $source_path = null, $destination_path = null)
	{
		/** Process results */


		// ($extension_name, $model_name, $source_path = null, $destination_path = null)
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
	 * testTrigger
	 *
	 * @return bool
	 */
	public function testTrigger()
	{

		$controller = new CreateController();
		$table_registry_name = 'ArticlesTable';

		$triggers = array();
		$triggers[] = 'Create';

		$query_results = array();
		$data = new \stdClass();
		$data->id = 333;
		$data->title = 'Test';
		$data->catalog_type_id = 1050;
		$query_results[] = $data;

		$parameters = array('create_extension' => 1,
			'create_sample_data' => 1);

		/** Schedule onAfterCreate Event */
		$arguments = array(
			'table_registry_name' => $table_registry_name,
			'db' => '',
			'data' => $query_results,
			'parameters' => $parameters,
			'model_name' => 'Articles'
		);

		$arguments = Services::Event()->schedule('onAfterCreate', $arguments, $triggers);

		var_dump($arguments);
		die;
		if ($arguments == false) {
			return false;
		}
	}


}
