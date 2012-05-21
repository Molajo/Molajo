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
	 *
	 * @return  null
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
	 * Retrieve extension information using either the ID or the name
	 *
	 * @return bool
	 * @since 1.0
	 */
	protected function getExtension($extension_id = null)
	{
		$extension_id = Helpers::Module()->get(Services::Registry()->get('Include', 'extension_title'));

		return parent::getExtension($extension_id);
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
			EXTENSIONS_MODULES_URL . '/' . Services::Registry()->get('Include', 'extension_title'),
			SITE_MEDIA_URL . '/' . Services::Registry()->get('Include', 'extension_title'),
			Services::Registry()->get('Configuration', 'media_priority_module', 400)
		);
	}
}
