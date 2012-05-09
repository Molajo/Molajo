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
	 * __construct
	 *
	 * Class constructor.
	 *
	 * @param  string $name
	 * @param  string $type
	 * @param  array  $items (used for event processing includes, only)
	 *
	 * @return  null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null, $items = null)
	{
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', CATALOG_TYPE_EXTENSION_MODULE);
		return parent::__construct($name, $type, $items);
	}

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
		Services::Registry()->deleteRegistry('ExtensionParameters');

		/** Retrieve Extension Instances ID */
		Services::Registry()->set('Parameters', 'extension_instance_id',
			Helpers::Extension()->getInstanceID(
				Services::Registry()->get('Parameters', 'extension_catalog_type_id'),
				Services::Registry()->get('Parameters', 'extension_instance_name')
			)
		);

		/**  Retrieve Extension Data and set Extension Parameter values */
		$response = Helpers::Extension()->getIncludeExtension(
			Services::Registry()->get('Parameters', 'extension_instance_id')
		);

		return $response;
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
			EXTENSIONS_MODULES_URL . '/' . Services::Registry()->get('Parameters', 'extension_instance_name'),
			SITE_MEDIA_URL . '/' . Services::Registry()->get('Parameters', 'extension_instance_name'),
			Services::Registry()->get('Configuration', 'media_priority_module', 400)
		);
	}
}
