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
 * ExtensionHelper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class ExtensionHelper
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
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new ExtensionHelper();
		}

		return self::$instance;
	}

	/**
	 * Retrieve Route information for a specific Extension
	 *
	 * @param  $extension_id
	 * @param  string $model_type
	 * @param  string $model_name
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function getExtension($extension_id, $model_type = 'Table', $model_name = 'ExtensionInstances')
	{
		Services::Registry()->set('Query', 'Current', 'Extension getExtension: ' . $extension_id);

		$item = Helpers::Extension()->get($extension_id, $model_type, $model_name);

		/** 404: routeRequest handles redirecting to error page */
		if (count($item) == 0) {
			Services::Registry()->set('Parameters', 'status_found', false);
			return false;
		}

		/** Route Registry */
		Services::Registry()->set('Parameters', 'extension_id', $item->extensions_id);
		Services::Registry()->set('Parameters', 'extension_name', $item->extensions_name);
		Services::Registry()->set('Parameters', 'extension_name_path_node', $item->extensions_name);
		Services::Registry()->set('Parameters', 'extension_title', $item->title);
		Services::Registry()->set('Parameters', 'extension_translation_of_id', (int)$item->translation_of_id);
		Services::Registry()->set('Parameters', 'extension_language', $item->language);
		Services::Registry()->set('Parameters', 'extension_view_group_id', $item->view_group_id);
		Services::Registry()->set('Parameters', 'extension_catalog_id', $item->catalog_id);
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', (int)$item->catalog_type_id);
		Services::Registry()->set('Parameters', 'extension_catalog_type_title', $item->catalog_types_title);

		Services::Registry()->set('Parameters', 'extension_path',
			$this->getPath((int)$item->catalog_type_id,
				Services::Registry()->get('Parameters', 'extension_name_path_node'))
		);

		Services::Registry()->set('Parameters', 'extension_path_url',
			$this->getPathURL((int)$item->catalog_type_id,
				Services::Registry()->get('Parameters', 'extension_name_path_node'))
		);

		/** Process each field namespace  */
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
	 * Common query for all Extensions - Merges into Parameter Registry
	 *
	 * @param $extension_id
	 * @param string $model_type
	 * @param string $model_name
	 * @param string $query_object
	 * @param string $catalog_type_id
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function get(
		$extension_id = 0, $model_type = 'Table', $model_name = 'ExtensionInstances',
		$query_object = 'item', $catalog_type_id = null)
	{
		if (Services::Registry()->get('CurrentPhase') == 'LOG_OUTPUT_ROUTING') {
			$phase = LOG_OUTPUT_ROUTING;
		} else {
			$phase = LOG_OUTPUT_RENDERING;
		}

		$controllerClass = 'Molajo\\Controller\\ReadController';
		$m = new $controllerClass();
		$m->connect($model_type, $model_name);

		if ((int)$extension_id == 0) {
		} else {
			$m->set('id', (int)$extension_id);
		}

		if ((int)$catalog_type_id == 0) {
		} else {
			$primary_prefix = $m->get('primary_prefix');
			$primary_key = $m->get('primary_key');

			$m->model->query->where($m->model->db->qn($primary_prefix . '.' . 'catalog_type_id')
				. ' = ' . (int)$catalog_type_id);
		}

		if ($query_object == 'list') {
			$m->set('model_offset', 0);
			$m->set('model_count', 999999);
			$m->set('check_view_level_access', 0);
		}

		$query_results = $m->getData($query_object);
		if ($query_results == false || $query_results == null) {

			echo 'Extension ID ' . $extension_id . '<br />';
			echo 'Model Type ' . $model_type . '<br />';
			echo 'Model Name ' . $model_name . '<br />';
			echo 'Query Object ' . $query_object . '<br />';
			echo 'Catalog Type ID ' . $catalog_type_id . '<br />';
			return false;
		}

		if ($query_object == 'item') {
			$query_results->table_registry_name = $m->table_registry_name;
			$query_results->model_name = $m->get('model_name');
			$query_results->model_type = $m->get('model_type');
		}

		return $query_results;
	}

	/**
	 * getInstanceID
	 *
	 * Retrieves Extension ID, given title
	 *
	 * @param  $catalog_type_id
	 * @param  $title
	 *
	 * @return bool|mixed
	 * @since   1.0
	 */
	public function getInstanceID($catalog_type_id, $title)
	{
		$controllerClass = 'Molajo\\Controller\\ReadController';
		$m = new $controllerClass();
		$query_results = $m->connect('Table', 'ExtensionInstances');
		if ($query_results == false) {
			return false;
		}

		$m->set('process_triggers', 0);

		$m->model->query->select($m->model->db->qn('a.id'));
		$m->model->query->where($m->model->db->qn('a.title') . ' = ' . $m->model->db->q($title));
		$m->model->query->where($m->model->db->qn('a.catalog_type_id') . ' = ' . (int)$catalog_type_id);

		return $m->getData('result');
	}

	/**
	 * getInstanceTitle
	 *
	 * Retrieves Extension Instance Title, given the extension_instance_id
	 *
	 * @param   $extension_instance_id
	 *
	 * @return bool|mixed
	 * @since   1.0
	 */
	public function getInstanceTitle($extension_instance_id)
	{
		$controllerClass = 'Molajo\\Controller\\ReadController';
		$m = new $controllerClass();
		$query_results = $m->connect('Table', 'ExtensionInstances');
		if ($query_results == false) {
			return false;
		}

		$m->set('process_triggers', 0);

		$m->model->query->select($m->model->db->qn('a.title'));
		$m->model->query->where($m->model->db->qn('a.id') . ' = ' . (int)$extension_instance_id);

		return $m->getData('result');
	}

	/**
	 * getExtensionNode
	 *
	 * Retrieves the folder node for the specific extension
	 *
	 * @param  $extension_instance_id
	 *
	 * @return bool|mixed
	 * @since   1.0
	 */
	public function getExtensionNode($extension_instance_id)
	{
		$controllerClass = 'Molajo\\Controller\\ReadController';
		$m = new $controllerClass();

		$query_results = $m->connect();
		if ($query_results == false) {
			return false;
		}

		$m->set('process_triggers', 0);

		$m->model->query->select($m->model->db->qn('a.name'));

		$m->model->query->from($m->model->db->qn('#__extensions') . ' as ' . $m->model->db->qn('a'));
		$m->model->query->from($m->model->db->qn('#__extension_instances') . ' as ' . $m->model->db->qn('b'));

		$m->model->query->where($m->model->db->qn('a.id') . ' = ' . $m->model->db->qn('b.extension_id'));
		$m->model->query->where($m->model->db->qn('b.id') . ' = ' . (int)$extension_instance_id);

		return $m->getData('result');
	}

	/**
	 * getPath
	 *
	 * Return path for Extension - make certain to send in extension name, not
	 *     extension instance title. Extensions Instances do not have to have
	 *  the same name as the Extension, itself. The Extension name is what
	 *  is used in the path statements.
	 *
	 * @param $catalog_type_id
	 * @param $node
	 *
	 * @return string
	 * @since 1.0
	 */
	public function getPath($catalog_type_id, $node)
	{
		if ($catalog_type_id == CATALOG_TYPE_EXTENSION_PAGE_VIEW) {
			return Helpers::View()->getPath($node, 'Page');

		} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
			return Helpers::View()->getPath($node, 'Template');

		} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
			return Helpers::View()->getPath($node, 'Wrap');

		}

		$type = Helpers::Extension()->getType($catalog_type_id);

		return EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node));
	}

	/**
	 * getPathURL
	 *
	 * Return URL path for Extension
	 *
	 * @param $catalog_type_id
	 * @param $node
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function getPathURL($catalog_type_id, $node)
	{
		if ($catalog_type_id == CATALOG_TYPE_EXTENSION_PAGE_VIEW) {
			return Helpers::View()->getPathURL($node, 'Page');

		} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
			return Helpers::View()->getPathURL($node, 'Template');

		} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
			return Helpers::View()->getPathURL($node, 'Wrap');

		} else {
			$type = Helpers::Extension()->getType($catalog_type_id);

			return EXTENSIONS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
		}
	}

	/**
	 * loadLanguage
	 *
	 * Loads Language Files for Extension
	 *
	 * @param null $path
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function loadLanguage($path = null)
	{
		if ($path == null) {
			$path = Services::Registry()->get('Parameters', 'extension_path');
		}

		Services::Language()->load($path);

		return true;
	}

	/**
	 * setThemePageView
	 *
	 * Determine the default theme value, given system default sequence
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function setThemePageView()
	{
		if ((int)Services::Registry()->get('Parameters', 'content_id') > 0) {
			if (strtolower(Services::Registry()->get('Parameters', 'request_action')) == 'display') {
				$type = 'item';
			} else {
				$type = 'form';
			}
		} else {
			$type = 'list';
		}

		if ($type == 'form') {
			$theme_id = Services::Registry()->get('Parameters', 'form_theme_id', 0);

			$page_view_id = Services::Registry()->get('Parameters', 'form_page_view_id', 0);
			$page_view_css_id = Services::Registry()->get('Parameters', 'form_page_view_css_id', '');
			$page_view_css_class = Services::Registry()->get('Parameters', 'form_page_view_css_class', '');

		} elseif ($type == 'item') {
			$theme_id = Services::Registry()->get('Parameters', 'item_theme_id', 0);

			$page_view_id = Services::Registry()->get('Parameters', 'item_page_view_id', 0);
			$page_view_css_id = Services::Registry()->get('Parameters', 'item_page_view_css_id', '');
			$page_view_css_class = Services::Registry()->get('Parameters', 'item_page_view_css_class', '');

		} else {
			$theme_id = Services::Registry()->get('Parameters', 'list_theme_id', 0);

			$page_view_id = Services::Registry()->get('Parameters', 'list_page_view_id', 0);
			$page_view_css_id = Services::Registry()->get('Parameters', 'list_page_view_css_id', '');
			$page_view_css_class = Services::Registry()->get('Parameters', 'list_page_view_css_class', '');
		}

		/** Set Parameters */
		Services::Registry()->set('Parameters', 'theme_id', $theme_id);

		Services::Registry()->set('Parameters', 'page_view_id', $page_view_id);
		Services::Registry()->set('Parameters', 'page_view_css_id', $page_view_css_id);
		Services::Registry()->set('Parameters', 'page_view_css_class', $page_view_css_class);

		/** Theme  */
		Helpers::Theme()->get($theme_id);

		/** Page  */
		Helpers::View()->get($page_view_id, 'Page');

		return true;
	}

	/**
	 *  setTemplateWrapModel - Determine the default Template and Wrap values
	 *
	 * @return string
	 * @since   1.0
	 */
	public function setTemplateWrapModel()
	{
		if ((int)Services::Registry()->get('Parameters', 'content_id') > 0) {
			if (strtolower(Services::Registry()->get('Parameters', 'request_action')) == 'display') {
				$type = 'item';
			} else {
				$type = 'form';
			}
		} else {
			$type = 'list';
		}

		if ($type == 'form') {
			$template_view_id = Services::Registry()->get('Parameters', 'form_template_view_id', 0);
			$template_view_css_id = Services::Registry()->get('Parameters', 'form_template_view_css_id', '');
			$template_view_css_class = Services::Registry()->get('Parameters', 'form_template_view_css_class', '');

			$wrap_view_id = Services::Registry()->get('Parameters', 'form_wrap_view_id', 0);
			$wrap_view_css_id = Services::Registry()->get('Parameters', 'form_wrap_view_css_id', '');
			$wrap_view_css_class = Services::Registry()->get('Parameters', 'form_wrap_view_css_class', '');

			$model_name = Services::Registry()->get('Parameters', 'form_model_name', '');
			$model_type = Services::Registry()->get('Parameters', 'form_model_type', '');
			$model_query_object = Services::Registry()->get('Parameters', 'form_model_query_object', '');

		} elseif ($type == 'item') {

			$template_view_id = Services::Registry()->get('Parameters', 'item_template_view_id', 0);
			$template_view_css_id = Services::Registry()->get('Parameters', 'item_template_view_css_id', '');
			$template_view_css_class = Services::Registry()->get('Parameters', 'item_template_view_css_class', '');

			$wrap_view_id = Services::Registry()->get('Parameters', 'item_wrap_view_id', 0);
			$wrap_view_css_id = Services::Registry()->get('Parameters', 'item_wrap_view_css_id', '');
			$wrap_view_css_class = Services::Registry()->get('Parameters', 'item_wrap_view_css_class', '');

			$model_name = Services::Registry()->get('Parameters', 'item_model_name', '');
			$model_type = Services::Registry()->get('Parameters', 'item_model_type', '');
			$model_query_object = Services::Registry()->get('Parameters', 'item_model_query_object', '');

		} else {
			$template_view_id = Services::Registry()->get('Parameters', 'list_template_view_id', 0);
			$template_view_css_id = Services::Registry()->get('Parameters', 'list_template_view_css_id', '');
			$template_view_css_class = Services::Registry()->get('Parameters', 'list_template_view_css_class', '');

			$wrap_view_id = Services::Registry()->get('Parameters', 'list_wrap_view_id', 0);
			$wrap_view_css_id = Services::Registry()->get('Parameters', 'list_wrap_view_css_id', '');
			$wrap_view_css_class = Services::Registry()->get('Parameters', 'list_wrap_view_css_class', '');

			$model_name = Services::Registry()->get('Parameters', 'list_model_name', '');
			$model_type = Services::Registry()->get('Parameters', 'list_model_type', '');
			$model_query_object = Services::Registry()->get('Parameters', 'list_model_query_object', '');
		}

		/** Set Parameters */
		Services::Registry()->set('Parameters', 'template_view_id', $template_view_id);
		Services::Registry()->set('Parameters', 'template_view_css_id', $template_view_css_id);
		Services::Registry()->set('Parameters', 'template_view_css_class', $template_view_css_class);

		Services::Registry()->set('Parameters', 'wrap_view_id', $wrap_view_id);
		Services::Registry()->set('Parameters', 'wrap_view_css_id', $wrap_view_css_id);
		Services::Registry()->set('Parameters', 'wrap_view_css_class', $wrap_view_css_class);

		Services::Registry()->set('Parameters', 'model_name', $model_name);
		Services::Registry()->set('Parameters', 'model_type', $model_type);
		Services::Registry()->set('Parameters', 'model_query_object', $model_query_object);

		/** Template  */
		Helpers::View()->get($template_view_id, 'Template');

		/** Wrap  */
		Helpers::View()->get($wrap_view_id, 'Wrap');

		return;
	}

	/**
	 * Retrieve the path node for a specified catalog type or
	 * it retrieves the catalog id value for the requested type
	 *
	 * @param int $catalog_type_id
	 * @param null $catalog_type
	 *
	 * @return string
	 * @since   1.0
	 */
	public function getType($catalog_type_id = 0, $catalog_type = null)
	{
		if ((int)$catalog_type_id == 0) {

			if ($catalog_type == 'Resource') {
				return CATALOG_TYPE_EXTENSION_RESOURCE;

			} elseif ($catalog_type == 'Language') {
				return CATALOG_TYPE_EXTENSION_LANGUAGE;

			} elseif ($catalog_type == 'Module') {
				return CATALOG_TYPE_EXTENSION_MODULE;

			} elseif ($catalog_type == 'Theme') {
				return CATALOG_TYPE_EXTENSION_THEME;

			} elseif ($catalog_type == 'Trigger') {
				return CATALOG_TYPE_EXTENSION_TRIGGER;

			} elseif ($catalog_type == 'Page') {
				return CATALOG_TYPE_EXTENSION_PAGE_VIEW;

			} elseif ($catalog_type == 'Template') {
				return CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW;

			} elseif ($catalog_type == 'Wrap') {
				return CATALOG_TYPE_EXTENSION_WRAP_VIEW;
			}

		} else {

			if ($catalog_type_id == CATALOG_TYPE_EXTENSION_RESOURCE) {
				return 'Resource';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_LANGUAGE) {
				return 'Language';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_MODULE) {
				return 'Module';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_THEME) {
				return 'Theme';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_TRIGGER) {
				return 'Trigger';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_PAGE_VIEW) {
				return 'Page';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
				return 'Template';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
				return 'Wrap';
			}
		}

		/** Should not be reachable */
		return '';
	}
}
