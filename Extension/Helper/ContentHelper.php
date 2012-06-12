<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Service\Services;

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
	 * @return bool|object
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
	 * @return boolean
	 * @since    1.0
	 */
	public function getMenuItemRoute()
	{
		/** Retrieve the query results */
		$item = $this->get(
			Services::Registry()->get('Parameters', 'catalog_source_id'),
			'Menuitem',
			Services::Registry()->get('Parameters', 'catalog_menuitem_type'),
			'item'
		);

		/** 404  */
		if (count($item) == 0) {
			return Services::Registry()->set('Parameters', 'status_found', false);
		}

		/** Route Registry */
		Services::Registry()->set('Parameters', 'menuitem_id', (int)$item->id);
		Services::Registry()->set('Parameters', 'menuitem_title', $item->title);
		Services::Registry()->set('Parameters', 'menuitem_translation_of_id', (int)$item->translation_of_id);
		Services::Registry()->set('Parameters', 'menuitem_language', $item->language);
		Services::Registry()->set('Parameters', 'menuitem_catalog_type_id', (int)$item->catalog_type_id);
		Services::Registry()->set('Parameters', 'menuitem_catalog_type_title', $item->catalog_types_title);
		Services::Registry()->set('Parameters', 'menuitem_modified_datetime', $item->modified_datetime);

		/** Menu Extension */
		Services::Registry()->set('Parameters', 'menu_extension_instance_id', (int)$item->extension_instances_id);
		Services::Registry()->set('Parameters', 'menu_extension_title', $item->extension_instances_title);
		Services::Registry()->set('Parameters', 'menu_extension_id', (int)$item->extensions_id);
		Services::Registry()->set('Parameters', 'menu_extension_name_path_node', $item->extensions_name);
		Services::Registry()->set('Parameters', 'menu_extension_catalog_type_id',
			(int)$item->extension_instances_catalog_type_id);

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($item->table_registry_name, 'CustomFieldGroups');

		$parmName = $item->table_registry_name . 'Parameters';

		/** Content Extension and Source */
		Services::Registry()->set('Parameters', 'extension_instance_id',
			Services::Registry()->get($parmName, 'menuitem_extension_instance_id'));
		Services::Registry()->set('Parameters', 'menuitem_source_id',
			Services::Registry()->get($parmName, 'menuitem_source_id'));

		/** Theme, Page, Template and Wrap Views */
		if ((int)Services::Registry()->get('Parameters', 'menuitem_source_id') > 0) {
			if (strtolower(Services::Registry()->get($parmName, 'request_action')) == 'display') {
				$type = 'item';
			} else {
				$type = 'form';
			}
		} else {
			$type = 'list';
		}

		/** Theme */
		Services::Registry()->set('Parameters', $type . '_theme_id',
			Services::Registry()->get($parmName, 'menuitem_theme_id'));

		/** Page */
		Services::Registry()->set('Parameters', $type . '_page_view_id',
			Services::Registry()->get($parmName, 'menuitem_page_view_id'));
		Services::Registry()->set('Parameters', $type . '_page_view_css_id',
			Services::Registry()->get($parmName, 'menuitem_page_view_css_id'));
		Services::Registry()->set('Parameters', $type . '_page_view_css_class',
			Services::Registry()->get($parmName, 'menuitem_page_view_css_class'));

		/** Template */
		Services::Registry()->set('Parameters', $type . '_template_view_id',
			Services::Registry()->get($parmName, 'menuitem_template_view_id'));
		Services::Registry()->set('Parameters', $type . '_template_view_css_id',
			Services::Registry()->get($parmName, 'menuitem_template_view_css_id'));
		Services::Registry()->set('Parameters', $type . '_template_view_css_class',
			Services::Registry()->get($parmName, 'menuitem_template_view_css_class'));

		/** Wrap */
		Services::Registry()->set('Parameters', $type . '_wrap_view_id',
			Services::Registry()->get($parmName, 'menuitem_wrap_view_id'));
		Services::Registry()->set('Parameters', $type . '_wrap_view_css_id',
			Services::Registry()->get($parmName, 'menuitem_wrap_view_css_id'));
		Services::Registry()->set('Parameters', $type . '_wrap_view_css_class',
			Services::Registry()->get($parmName, 'menuitem_wrap_view_css_class'));

		/** Model */
		Services::Registry()->set('Parameters', $type . '_model_name',
			Services::Registry()->get($parmName, 'menuitem_model_name'));
		Services::Registry()->set('Parameters', $type . '_model_type',
			Services::Registry()->get($parmName, 'menuitem_model_type'));
		Services::Registry()->set('Parameters', $type . '_model_query_object',
			Services::Registry()->get($parmName, 'menuitem_model_query_object'));

		Services::Registry()->delete($parmName, 'menuitem_theme*');
		Services::Registry()->delete($parmName, 'menuitem_page*');
		Services::Registry()->delete($parmName, 'menuitem_template*');
		Services::Registry()->delete($parmName, 'menuitem_wrap*');
		Services::Registry()->delete($parmName, 'menuitem_model*');

		Services::Registry()->copy($parmName, 'Parameters');

		/** Remaining Parameters */
		foreach ($customFieldTypes as $customFieldName) {
			$customFieldName = ucfirst(strtolower($customFieldName));

			if ($customFieldName == 'Parameters') {
			} else {
				Services::Registry()->merge($item->table_registry_name . $customFieldName, $customFieldName);
			}
			Services::Registry()->deleteRegistry($item->table_registry_name . $customFieldName);
		}

		return true;
	}

	/**
	 * Retrieve Route information for a specific Content Item
	 *
	 * Various registries used to store data definitions. For example: ArticlesContent (and ArticlesCustomfieldsContent,
	 * ArticlesContentMetadata, ArticlesContentParameters), ArticlesComponent (etc.)
	 *
	 * These registries are reused, not rebuilt
	 *
	 * @return boolean
	 * @since    1.0
	 */
	public function getRouteContent($id, $model_type, $model_name, $model_query_object)
	{
		/** Retrieve the query results */
		$item = $this->get(
			$id, $model_type, $model_name, $model_query_object
		);

		/** Save Content in Trigger Registry */
		Services::Registry()->set('Trigger', $item->table_registry_name . 'query_results', $item);

		/** 404  */
		if (count($item) == 0) {
			return Services::Registry()->set('Parameters', 'status_found', false);
		}

		/** Save in Trigger Registry for primary view */
		Services::Registry()->set('Trigger', $item->table_registry_name . 'query_results', $item);

		/** Route Registry */
		Services::Registry()->set('Parameters', 'content_id', (int)$item->id);
		Services::Registry()->set('Parameters', 'content_title', $item->title);
		Services::Registry()->set('Parameters', 'content_translation_of_id', (int)$item->translation_of_id);
		Services::Registry()->set('Parameters', 'content_language', $item->language);
		Services::Registry()->set('Parameters', 'content_catalog_type_id', (int)$item->catalog_type_id);
		Services::Registry()->set('Parameters', 'content_catalog_type_title', $item->catalog_types_title);
		Services::Registry()->set('Parameters', 'content_modified_datetime', $item->modified_datetime);

		Services::Registry()->set('Parameters', 'extension_instance_id', (int)$item->extension_instances_id);
		Services::Registry()->set('Parameters', 'extension_title', $item->extension_instances_title);
		Services::Registry()->set('Parameters', 'extension_id', (int)$item->extensions_id);
		Services::Registry()->set('Parameters', 'extension_name_path_node', $item->extensions_name);
		Services::Registry()->set('Parameters', 'extension_catalog_type_id',
			(int)$item->extension_instances_catalog_type_id);

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($item->table_registry_name, 'CustomFieldGroups');

		foreach ($customFieldTypes as $customFieldName) {

			$customFieldName = ucfirst(strtolower($customFieldName));
			Services::Registry()->merge($item->table_registry_name . $customFieldName, $customFieldName);

			/** Save for primary view */
			$array = Services::Registry()->getArray($item->table_registry_name . $customFieldName);
			Services::Registry()->set('Trigger', $item->table_registry_name . $customFieldName, $array);

			/** Delete */
			Services::Registry()->deleteRegistry($item->table_registry_name . $customFieldName);
		}

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
	 * @return boolean
	 * @since    1.0
	 */
	public function getRouteCategory()
	{
		/** Retrieve the query results */
		$item = $this->get(
			Services::Registry()->get('Parameters', 'catalog_category_id'),
			'Table',
			'Categories',
			'item'
		);

		/** Save Content in Trigger Registry */
		Services::Registry()->set('Trigger', 'RequestCategory' . 'query_results', $item);

		/** Route Registry with Category Data */
		Services::Registry()->set('Parameters', 'category_id', (int)$item->id);
		Services::Registry()->set('Parameters', 'category_title', $item->title);
		Services::Registry()->set('Parameters', 'category_translation_of_id', (int)$item->translation_of_id);
		Services::Registry()->set('Parameters', 'category_language', $item->language);
		Services::Registry()->set('Parameters', 'category_catalog_type_id', (int)$item->catalog_type_id);
		Services::Registry()->set('Parameters', 'category_catalog_type_title', $item->catalog_types_title);
		Services::Registry()->set('Parameters', 'category_modified_datetime', $item->modified_datetime);

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($item->table_registry_name, 'CustomFieldGroups');

		foreach ($customFieldTypes as $customFieldName) {
			$customFieldName = ucfirst(strtolower($customFieldName));

			/** Save for primary view */
			$array = Services::Registry()->getArray($item->table_registry_name . $customFieldName);
			Services::Registry()->set('RequestCategory', $customFieldName, $array);

			Services::Registry()->merge($item->table_registry_name . $customFieldName, $customFieldName);
			Services::Registry()->deleteRegistry($item->table_registry_name . $customFieldName);
		}

		return true;
	}

	/**
	 * Get data for content
	 *
	 * @param $id
	 * @param $model_type
	 * @param $model_name
	 * @param $model_query_object
	 *
	 * @return  array An object containing an array of data
	 * @since   1.0
	 */
	public function get($id = 0, $model_type = 'Table', $model_name = 'Content', $model_query_object = 'list')
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$results = $m->connect($model_type, $model_name);
		if ($results == false) {
			return false;
		}

		$m->set('id', (int)$id);
		$m->set('process_triggers', 1);

		$item = $m->getData($model_query_object);

		$item->table_registry_name = $m->table_registry_name;

		$item->model_name = $m->get('model_name');

		if (count($item) == 0) {
			return array();
		}

		return $item;
	}
}
