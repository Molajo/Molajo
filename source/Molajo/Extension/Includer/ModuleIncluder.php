<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Includer;

use Molajo\Extension\Helpers;
use Molajo\Service\Services;
use Molajo\Extension\Includer;

defined('MOLAJO') or die;

/**
 * Module
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class ModuleIncluder extends Includer
{
	/**
	 * getExtension
	 *
	 * Retrieve extension information using either the ID or the name
	 *
	 * @return bool
	 * @since 1.0
	 */
	protected function getExtension()
	{
		$this->set(
			'extension_catalog_type_id',
			CATALOG_TYPE_EXTENSION_MODULE
		);
		$results = parent::getExtension();

		if ($results === false) {
			if (Services::Registry()->get('Configuration', 'debug', 0) == 1) {
				Services::Debug()->set('ModuleIncluder::getExtension');
				Services::Debug()->set('Module not found: ' . $this->get('extension_instance_name'));
			}
			return false;
		}

		$this->set(
			'extension_path',
			ModuleHelper::getPath(
				strtolower($this->get('extension_instance_name')))
		);

		$this->set('extension_type', 'module');

		return true;
	}

	/**
	 * import
	 *
	 * Extension classes, if existing
	 *
	 * @since 1.0
	 */
	protected function importClasses()
	{
		$load = new LoadHelper();
		$name = ucfirst($this->get('extension_instance_name'));
		$name = str_replace(array('-', '_'), '', $name);
		$name = 'Molajo' . $name;

		/** Controller */
		if (file_exists($this->get('extension_path') . '/controller.php')) {
			$load->requireClassFile(
				$this->get('extension_path') . '/controller.php',
				$name . 'ModuleController');
		}

		/** Model */
		if (file_exists($this->get('extension_path') . '/model.php')) {
			$load->requireClassFile(
				$this->get('extension_path') . '/model.php',
				$name . 'ModuleModel');
		}
	}

	/**
	 * loadMedia
	 *
	 * Loads Media Files for Site, Application, User, and Theme
	 *
	 * @return  boolean  True, if the file has successfully loaded.
	 * @since   1.0
	 */
	protected function loadMedia()
	{
		parent::loadMedia(
			EXTENSIONS_MODULES_URL . '/' . $this->get('extension_instance_name'),
			SITE_MEDIA_URL . '/' . $this->get('extension_instance_name'),
			Services::Registry()->get('Configuration', 'media_priority_module', 400)
		);
	}
}
