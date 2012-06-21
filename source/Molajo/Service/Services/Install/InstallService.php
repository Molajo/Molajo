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
	public function installExtension($extension_name, $extension_type, $source_path = null, $destination_path = null)
	{
		/** Create Extension Row */
		$controller = new CreateController();

		/** Set Field Values */
		$controller->set('model_type', 'Table');
		$controller->set('model_name', 'Extensions');

		$controller->setData('name', $extension_name);
		$results = $controller->create();
die;
		return $results;


		/** Verify ACL for User to Create Extensions */
		$connect = $m->connect('Table', 'Extensions');
		if ($connect === false) {
			return false;
		}


		/** Extension Instance */

		/** Application Extension Instances */

		/** Site Extension Instances */

		/** Catalog Entry */

		/** Catalog Activity */

		/** Permissions */

		/** Complete */
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
