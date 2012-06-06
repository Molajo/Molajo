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
	 * Creates the following Registries (ex. Articles component) containing datasource information for this component.
	 *
	 * ArticlesComponent, ArticlesComponentCustomfields, ArticlesComponentMetadata, ArticlesComponentParameters
	 *
	 * Merges into Route and Parameters Registries
	 *
	 * @param $extension_id
	 * @param string $model_name
	 * @param string $model_type
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function getExtension($extension_id, $model_name = 'ExtensionInstances', $model_type = 'Table')
	{
		/** Retrieve the query results */
		$item = Helpers::Extension()->get($extension_id, $model_name, $model_type);

		/** 500: not found */
		if (count($item) == 0) {
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

		/**
		echo '<br /><br /><br />';
		echo 'In getExtension ROW <br />';
		echo '<pre>';
		var_dump($item);
		echo '</pre>';
		 */

		return true;
	}

	/**
	 * Common query for all Extensions
	 *
	 * Merges into Parameter Registry
	 *
	 * @param $extension_id
	 * @param string $model_name
	 * @param string $model_type
	 *
	 * @return array
	 * @since   1.0
	 */
	public function get($extension_id, $model_name = 'ExtensionInstances', $model_type = 'Table')
	{

//echo '<br />' . $extension_id . ' Name: ' . $model_name . ' Type: ' . $model_type . '<br />';
	//. ' query_object: ' . $model_query_object . '<br />';

		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect($model_name, $model_type);

		$m->set('id', (int)$extension_id);
		$m->set('process_triggers', 0);

		$item = $m->getData('item');

		$item->table_registry_name = $m->table_registry_name;
		$item->model_name = $m->get('model_name');

		/** 404: routeRequest handles redirecting to error page */
		if (count($item) == 0) {
			return Services::Registry()->set('Parameters', 'status_found', false);
		}

		return $item;
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
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect('ExtensionInstances');

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
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect('ExtensionInstances');

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
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect();

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
			return Helpers::PageView()->getPath($node);

		} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
			return Helpers::Template()->getPath($node);

		} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
			return Helpers::WrapView()->getPath($node);

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
			return Helpers::PageView()->getPathURL($node);

		} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
			return Helpers::Template()->getPathURL($node);

		} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
			return Helpers::WrapView()->getPathURL($node);

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
		$path .= '/Language';

		if (Services::Filesystem()->folderExists($path)) {
		} else {
			return true;
		}

		Services::Language()->load($path, Services::Language()->get('tag'), false, false);

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
		Helpers::PageView()->get($page_view_id);

		return true;
	}

	/**
	 *  setThemePageView
	 *
	 *  Determine the default theme value, given system default sequence
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
		Helpers::TemplateView()->get($template_view_id);

		/** Wrap  */
		Helpers::WrapView()->get($wrap_view_id);

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

			if ($catalog_type == 'Component') {
				return CATALOG_TYPE_EXTENSION_COMPONENT;

			} elseif ($catalog_type == 'Formfield') {
				return CATALOG_TYPE_EXTENSION_FORMFIELDS;

			} elseif ($catalog_type_id == 'Module') {
				return CATALOG_TYPE_EXTENSION_MODULE;

			} elseif ($catalog_type == 'Theme') {
				return CATALOG_TYPE_EXTENSION_THEME;

			} elseif ($catalog_type == 'Trigger') {
				return CATALOG_TYPE_EXTENSION_TRIGGER;

			} elseif ($catalog_type == 'Pageview') {
				return CATALOG_TYPE_EXTENSION_PAGE_VIEW;

			} elseif ($catalog_type == 'Templateview') {
				return CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW;

			} elseif ($catalog_type == 'Wrapview') {
				return CATALOG_TYPE_EXTENSION_WRAP_VIEW;
			}

		} else {

			if ($catalog_type_id == CATALOG_TYPE_EXTENSION_COMPONENT) {
				return 'Component';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_FORMFIELDS) {
				return 'Formfield';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_MODULE) {
				return 'Module';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_THEME) {
				return 'Theme';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_TRIGGER) {
				return 'Trigger';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_PAGE_VIEW) {
				return 'Pageview';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
				return 'Templateview';

			} elseif ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
				return 'Wrapview';
			}
		}

		/** Should not be reachable */
		return '';
	}
}
