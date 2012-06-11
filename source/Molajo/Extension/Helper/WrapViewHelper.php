<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Service\Services;
use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * WrapView Helper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class WrapViewHelper
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
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new WrapViewHelper();
		}

		return self::$instance;
	}

	/**
	 * get - Get requested wrap_view data
	 *
	 * @param int $wrap_view_id
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function get($wrap_view_id = 0)
	{
		Services::Registry()->set('Parameters', 'wrap_view_id', (int)$wrap_view_id);

		$node = Helpers::Extension()->getExtensionNode((int)$wrap_view_id);

		Services::Registry()->set('Parameters', 'wrap_view_path_node', $node);

		Services::Registry()->set('Parameters', 'wrap_view_path', $this->getPath($node, 'Parameters'));
		Services::Registry()->set('Parameters', 'wrap_view_path_url', $this->getPathURL($node, 'Parameters'));

		/** Retrieve the query results */
		$item = Helpers::Extension()->get($wrap_view_id, 'Wrap', $node);

		/** Not found: get system default */
		if (count($item) == 0) {

			/** System Default */
			$wrap_view_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_WRAP_VIEW, 'Default');

			/** System default */
			Services::Registry()->set('Parameters', 'wrap_view_id', (int)$wrap_view_id);

			$node = Helpers::Extension()->getExtensionNode((int)$wrap_view_id);

			Services::Registry()->set('Parameters', 'wrap_view_path_node', $node);

			Services::Registry()->set('Parameters', 'wrap_view_path', $this->getPath($node, 'Parameters'));
			Services::Registry()->set('Parameters', 'wrap_view_path_url', $this->getPathURL($node, 'Parameters'));

			$item = Helpers::Extension()->get($wrap_view_id, 'Wrap', $node);

			if (count($item) == 0) {
				Services::Error()->set(500, 'View not found');
				return false;
			}
		}

		Services::Registry()->set('Parameters', 'wrap_view_title', $item->title);
		Services::Registry()->set('Parameters', 'wrap_view_translation_of_id', (int)$item->translation_of_id);
		Services::Registry()->set('Parameters', 'wrap_view_language', $item->language);
		Services::Registry()->set('Parameters', 'wrap_view_view_group_id', $item->view_group_id);
		Services::Registry()->set('Parameters', 'wrap_view_catalog_id', $item->catalog_id);
		Services::Registry()->set('Parameters', 'wrap_view_catalog_type_id', (int)$item->catalog_type_id);
		Services::Registry()->set('Parameters', 'wrap_view_catalog_type_title', $item->catalog_types_title);

		Services::Registry()->set('Parameters', 'wrap_view_table_registry_name', $item->table_registry_name);

		/** Merge in each custom field namespace  */
		$customFieldTypes = Services::Registry()->get($item->table_registry_name, 'CustomFieldGroups');

		if (count($customFieldTypes) > 0) {
			foreach ($customFieldTypes as $customFieldName) {
				$customFieldName = ucfirst(strtolower($customFieldName));
				Services::Registry()->merge($item->table_registry_name . $customFieldName, $customFieldName);
				Services::Registry()->deleteRegistry($item->table_registry_name . $customFieldName);
			}
		}

		return true;
	}

	/**
	 * getPath
	 *
	 * Return path for selected Template View
	 *
	 * Expects known path for Theme and Extension
	 *
	 * @param $node
	 *
	 * @return bool|string
	 * @since  1.0
	 */
	public function getPath($node)
	{
		$plus = '/View/Wrap/' . ucfirst(strtolower($node));

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/Configuration.xml')) {
			return Services::Registry()->get('Parameters', 'theme_path') . $plus;
		}

		/** 2. Extension */
		if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . $plus . '/Configuration.xml')) {
			return Services::Registry()->get('Parameters', 'extension_path') . $plus;
		}

		/** 3. View */
		if (file_exists(EXTENSIONS_VIEWS . '/Wrap/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
			return EXTENSIONS_VIEWS . '/Wrap/' . ucfirst(strtolower($node));
		}

		/** 4. MVC */
		if (file_exists(MVC . $plus . '/Configuration.xml')) {
			return MVC . $plus;
		}

		return false;
	}

	/**
	 * getURLPath - Return URL path for selected Template View
	 *
	 * @param $node
	 *
	 * @return bool|string
	 * @since 1.0
	 */
	public function getPathURL($node = false)
	{
		$plus = '/View/Wrap/' . ucfirst(strtolower($node));

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/Configuration.xml')) {
			return Services::Registry()->get('Parameters', 'theme_path_url') . $plus;
		}

		/** 2. Extension */
		if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . $plus . '/Configuration.xml')) {
			return Services::Registry()->get('Parameters', 'extension_path_url') . $plus;
		}

		/** 3. View */
		if (file_exists(EXTENSIONS_VIEWS . '/Wrap/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
			return EXTENSIONS_VIEWS_URL . '/Wrap/' . ucfirst(strtolower($node));
		}

		/** 4. MVC */
		if (file_exists(MVC . $plus . '/Configuration.xml')) {
			return MVC_URL . $plus;
		}

		return false;
	}
}
