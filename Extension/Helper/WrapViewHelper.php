<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
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
	 * get
	 *
	 * Get requested wrap_view data
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function get($wrap_view_id = 0)
	{
		if ($wrap_view_id == 0) {
			$wrap_view_id = $this->setDefault();
		}

		Services::Registry()->set('Parameters', 'wrap_view_id', (int)$wrap_view_id);

		$node = Helpers::Extension()->getExtensionNode((int)$wrap_view_id);

		Services::Registry()->set('Parameters', 'wrap_view_path_node', $node);

		Services::Registry()->set('Parameters', 'wrap_view_path', $this->getPath($node));
		Services::Registry()->set('Parameters', 'wrap_view_path_include', $this->getPath($node) . '/index.php');
		Services::Registry()->set('Parameters', 'wrap_view_path_url', $this->getPathURL($node));

		/** Retrieve the query results */
		$row = Helpers::Extension()->get($wrap_view_id, 'WrapViews', 'Table');

		/** 500: not found */
		if (count($row) == 0) {

			/** System Default */
			$wrap_view_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_WRAP_VIEW, 'Default');

			/** System default */
			Services::Registry()->set('Parameters', 'wrap_view_id', (int)$wrap_view_id);

			$node = Helpers::Extension()->getExtensionNode((int)$wrap_view_id);

			Services::Registry()->set('Parameters', 'wrap_view_path_node', $node);

			Services::Registry()->set('Parameters', 'wrap_view_path', $this->getPath($node));
			Services::Registry()->set('Parameters', 'wrap_view_path_include', $this->getPath($node) . '/index.php');
			Services::Registry()->set('Parameters', 'wrap_view_path_url', $this->getPathURL($node));

			$row = Helpers::Extension()->get($wrap_view_id, 'WrapView');

			if (count($row) == 0) {
				Services::Error()->set(500, 'View not found');
				return false;
			}
		}

		Services::Registry()->set('Parameters', 'wrap_view_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Parameters', 'wrap_view_language', $row['language']);
		Services::Registry()->set('Parameters', 'wrap_view_view_group_id', $row['view_group_id']);
		Services::Registry()->set('Parameters', 'wrap_view_catalog_id', $row['catalog_id']);
		Services::Registry()->set('Parameters', 'wrap_view_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Parameters', 'wrap_view_catalog_type_title', $row['catalog_types_title']);

		return;
	}

	/**
	 *  setDefault
	 *
	 *  Determine the default wrap_view value, given system default sequence
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function setDefault()
	{
		$wrap_view_id = Services::Registry()->get('Parameters', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		$wrap_view_id = Services::Registry()->get('UserParameters', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		$wrap_view_id = Services::Registry()->get('Configuration', 'wrap_view_id', 0);
		if ((int)$wrap_view_id == 0) {
		} else {
			return $wrap_view_id;
		}

		return Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_WRAP_VIEW, 'System'); //99
	}

	/**
	 * getPath
	 *
	 * Return path for selected WrapView
	 *
	 * @param $wrap_view_name
	 * @return bool|string
	 */
	public function getPath($node)
	{
		if (file_exists(EXTENSIONS_VIEWS . '/Wrap/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
			return EXTENSIONS_VIEWS . '/Wrap/' . ucfirst(strtolower($node));
		}

		return false;
	}

	/**
	 * getPathURL
	 *
	 * Return path for selected WrapView
	 *
	 * @return bool|string
	 * @since 1.0
	 */
	public function getPathURL($node)
	{
		if (file_exists(EXTENSIONS_VIEWS . '/Wrap/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
			return EXTENSIONS_VIEWS_URL . '/Wrap/' . ucfirst(strtolower($node));
		}

		return false;
	}
}
