<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
	 * @param string $name
	 * @param string $type
	 *
	 * @return null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null)
	{
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', CATALOG_TYPE_EXTENSION_MODULE);

		return parent::__construct($name, $type);
	}

	/**
	 * getExtension
	 *
	 * Retrieve extension information after looking up the ID in the extension-specific includer
	 *
	 * @return bool
	 * @since 1.0
	 */
	protected function getExtension()
	{
		Services::Registry()->set('Parameters', 'extension_instance_id',
			Helpers::Extension()->getInstanceID(
				Services::Registry()->get('Parameters', 'extension_catalog_type_id'),
				Services::Registry()->get('Parameters', 'extension_title')
			)
		);

		$response = Helpers::Extension()->getExtension(
			Services::Registry()->get('Parameters', 'extension_instance_id'),
			'Module',
			ucfirst(strtolower(Services::Registry()->get('Parameters', 'extension_title')))
		);

		if ($response === false) {
			Services::Error()->set(500, 'Extension not found');
		}

		return;
	}

	/**
	 * loadMedia
	 *
	 * Loads Media Files for Site, Application, User, and Theme
	 *
	 * @return boolean True, if the file has successfully loaded.
	 * @since   1.0
	 */
	protected function loadMedia()
	{
		parent::loadMedia(
			EXTENSIONS_MODULES_URL . '/' . Services::Registry()->get('Parameters', 'template_view_path_url'),
			SITE_MEDIA_URL . '/' . Services::Registry()->get('Parameters', 'extension_title'),
			Services::Registry()->get('Configuration', 'media_priority_module', 400)
		);
	}
}
