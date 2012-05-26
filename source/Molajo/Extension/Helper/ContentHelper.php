<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Application;
use Molajo\Service\Services;
use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * Content Helper
 *
 * @package      Molajo
 * @subpackage   Helper
 * @since        1.0
 */
Class ContentHelper
{
	/**
	 * Static instance
	 *
	 * @var     object
	 * @since   1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return  bool|object
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new ContentHelper();
		}

		return self::$instance;
	}


	/**
	 * Retrieves the Menu Item Route information
	 *
	 * Various registries used to store data definitions. For example: ArticlesContent (and ArticlesCustomfieldsContent,
	 * ArticlesContentMetadata, ArticlesContentParameters), ArticlesComponent (etc.)
	 *
	 * These registries are reused, not rebuilt
	 *
	 * @return    boolean
	 * @since    1.0
	 */
	public function getMenuItem()
	{

	}

	/**
	 * Retrieve Route information for a specific Content Item
	 *
	 * Various registries used to store data definitions. For example: ArticlesContent (and ArticlesCustomfieldsContent,
	 * ArticlesContentMetadata, ArticlesContentParameters), ArticlesComponent (etc.)
	 *
	 * These registries are reused, not rebuilt
	 *
	 * @return    boolean
	 * @since    1.0
	 */
	public function getRoute()
	{
		/** Retrieve the query results */
		$row = $this->get(
			Services::Registry()->get('Parameters', 'catalog_source_id'),
			'Item',
			ucfirst(strtolower(Services::Registry()->get('Parameters', 'catalog_type')))
		);

		/** 404  */
		if (count($row) == 0) {
			return Services::Registry()->set('Parameters', 'status_found', false);
		}

		/** Route Registry */
		Services::Registry()->set('Parameters', 'content_id', (int)$row['id']);
		Services::Registry()->set('Parameters', 'content_title', $row['title']);
		Services::Registry()->set('Parameters', 'content_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Parameters', 'content_language', $row['language']);
		Services::Registry()->set('Parameters', 'content_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Parameters', 'content_catalog_type_title', $row['catalog_types_title']);
		Services::Registry()->set('Parameters', 'content_modified_datetime', $row['modified_datetime']);

		Services::Registry()->set('Parameters', 'extension_instance_id', (int)$row['extension_instances_id']);
		Services::Registry()->set('Parameters', 'extension_title', $row['extension_instances_title']);
		Services::Registry()->set('Parameters', 'extension_id', (int)$row['extensions_id']);
		Services::Registry()->set('Parameters', 'extension_name_path_node', $row['extensions_name']);
		Services::Registry()->set('Parameters', 'extension_catalog_type_id',
			(int)$row['extension_instances_catalog_type_id']);

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($row['table_registry_name'], 'CustomFieldGroups');

		foreach ($customFieldTypes as $customFieldName) {
			$customFieldName = ucfirst(strtolower($customFieldName));
			Services::Registry()->merge($row['table_registry_name'] . $customFieldName, $customFieldName);
			Services::Registry()->deleteRegistry($row['table_registry_name'] . $customFieldName);
		}

//Services::Registry()->get('ArticlesItem', '*');

		return true;
	}

	/**
	 * Retrieve Route information for a specific Category
	 *
	 * Creates the following Registries (ex. Articles content) containing datasource information for this category.
	 *
	 * ContentCategories, ContentCategoriesCustomfields, ContentCategoriesMetadata, ContentCategoriesParameters
	 *
	 * Merges into Route and Parameters Registries
	 *
	 * @return    boolean
	 * @since    1.0
	 */
	public function getRouteCategory()
	{
		/** Retrieve the query results */
		$row = $this->get(
			Services::Registry()->get('Parameters', 'catalog_category_id'),
			'Item',
			'Categories'
		);

		/** 404 */
		if (count($row) == 0) {
			return Services::Registry()->set('Parameters', 'status_found', false);
		}

		/** Route Registry with Category Data */
		Services::Registry()->set('Parameters', 'category_id', (int)$row['id']);
		Services::Registry()->set('Parameters', 'category_title', $row['title']);
		Services::Registry()->set('Parameters', 'category_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Parameters', 'category_language', $row['language']);
		Services::Registry()->set('Parameters', 'category_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Parameters', 'category_catalog_type_title', $row['catalog_types_title']);
		Services::Registry()->set('Parameters', 'category_modified_datetime', $row['modified_datetime']);

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($row['table_registry_name'], 'CustomFieldGroups');

		foreach ($customFieldTypes as $customFieldName) {
			$customFieldName = ucfirst(strtolower($customFieldName));
			Services::Registry()->merge($row['table_registry_name'] . $customFieldName, $customFieldName);
			Services::Registry()->deleteRegistry($row['table_registry_name'] . $customFieldName);
		}

//Services::Registry()->get('CategoriesItem', '*');

		return true;
	}

	/**
	 * Get data for content
	 *
	 * @return  mixed    An object containing an array of data
	 * @since   1.0
	 */
	public function get($id, $type = null, $datasource = null)
	{
		if ($type == null) {
			$type = 'Content';
		}

		$m = Application::Controller()->connect($datasource, $type);
		$m->model->set('id', (int)$id);
		$row = $m->getData('load');

		$row['table_registry_name'] = $m->model->table_registry_name;
		$row['model_name'] = $m->model->model_name;

		if (count($row) == 0) {
			return array();
		}

		return $row;
	}
}
