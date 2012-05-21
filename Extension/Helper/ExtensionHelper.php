<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Application;
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
	 * @return  bool|object
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
	 * @return  boolean
	 * @since   1.0
	 */
	public function getRoute($extension_id)
	{
		/** Retrieve the query results */
		$row = $this->get(
			$extension_id,
			ucfirst(strtolower(Services::Registry()->get('Route', 'content_catalog_type_title'))),
			'List'
		);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0) {
			return Services::Registry()->set('Route', 'status_found', false);
		}

		/** Route Registry */
		Services::Registry()->set('Route', 'extension_title', $row['title']);
		Services::Registry()->set('Route', 'extension_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Route', 'extension_language', $row['language']);
		Services::Registry()->set('Route', 'extension_view_group_id', $row['view_group_id']);
		Services::Registry()->set('Route', 'extension_catalog_id', $row['catalog_id']);
		Services::Registry()->set('Route', 'extension_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Route', 'extension_catalog_type_title', $row['catalog_types_title']);

		Services::Registry()->set('Route', 'extension_path',
			$this->getPath((int)$row['catalog_type_id'],
				Services::Registry()->get('Route', 'extension_name_path_node'))
		);

		Services::Registry()->set('Route', 'extension_path_url',
			$this->getPathURL((int)$row['catalog_type_id'],
				Services::Registry()->get('Route', 'extension_name_path_node'))
		);

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($row['table_registry_name'], 'CustomFieldGroups');

		foreach ($customFieldTypes as $customFieldName) {
			$customFieldName = ucfirst(strtolower($customFieldName));
			Services::Registry()->merge($row['table_registry_name'] . $customFieldName, $customFieldName);
			Services::Registry()->deleteRegistry($row['table_registry_name'] . $customFieldName);
		}

//Services::Registry()->get('ArticlesList', '*');

		return;
	}

	/**
	 * Retrieve Route information for a specific Extension
	 *
	 * @param int $extension_id
	 * @param int $catalog_type_id
	 * @param null $model_name ('Articles', 'Comments')
	 *
	 * @return bool
	 *
	 * @since   1.0
	 */
	public function getIncludeExtension($extension_id, $catalog_type_id, $model_name = null)
	{
		/** Retrieve catalog type, given the key value */
		$type = Helpers::Extension()->getType($catalog_type_id);

		/** Retrieve the query results */
		$row = $this->get(
			$extension_id,
			$model_name,
			$type
		);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0) {
			Services::Registry()->set('Parameter', 'status_found', false);
			return false;
		}

		/** Route Registry */
		Services::Registry()->set('Include', 'extension_title', $row['title']);
		Services::Registry()->set('Include', 'extension_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Include', 'extension_language', $row['language']);
		Services::Registry()->set('Include', 'extension_view_group_id', $row['view_group_id']);
		Services::Registry()->set('Include', 'extension_catalog_id', $row['catalog_id']);
		Services::Registry()->set('Include', 'extension_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Include', 'extension_catalog_type_title', $row['catalog_types_title']);

		Services::Registry()->set('Include', 'extension_path',
			$this->getPath((int)$row['catalog_type_id'],
				Services::Registry()->get('Include', 'extension_name_path_node'))
		);

		Services::Registry()->set('Include', 'extension_path_url',
			$this->getPathURL((int)$row['catalog_type_id'],
				Services::Registry()->get('Include', 'extension_name_path_node'))
		);

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($row['table_registry_name'], 'CustomFieldGroups');

		foreach ($customFieldTypes as $customFieldName) {
			$customFieldName = ucfirst(strtolower($customFieldName));
			Services::Registry()->merge($row['table_registry_name'] . $customFieldName, $customFieldName);
			Services::Registry()->deleteRegistry($row['table_registry_name'] . $customFieldName);
		}

		return;
	}

	/**
	 * get
	 *
	 * Retrieves Extension data from the extension and extension instances
	 * Verifies access for user, application and site
	 *
	 * @param   $catalog_type_id
	 * @param   $extension
	 *
	 * @return  bool|mixed
	 * @since   1.0
	 */
	public function get($extension_id = 0, $model = 'ExtensionInstances', $type = null)
	{
		$m = Application::Controller()->connect($model, $type);
		$m->model->set('id', (int)$extension_id);
		$row = $m->getData('load');

		$row['table_registry_name'] = $m->model->table_registry_name;
		$row['model_name'] = $m->model->model_name;

		if (count($row) == 0) {
			return array();
		}

		return $row;
	}

	/**
	 * getInstanceID
	 *
	 * Retrieves Extension ID, given title
	 *
	 * @param  $catalog_type_id
	 * @param  $title
	 *
	 * @return  bool|mixed
	 * @since   1.0
	 */
	public function getInstanceID($catalog_type_id, $title)
	{
		$m = Application::Controller()->connect('ExtensionInstances');

		$m->model->query->select($m->model->db->qn('id'));
		$m->model->query->where($m->model->db->qn('title') . ' = ' . $m->model->db->q($title));
		$m->model->query->where($m->model->db->qn('catalog_type_id') . ' = ' . (int)$catalog_type_id);

		return $m->getData('loadResult');
	}

	/**
	 * getInstanceTitle
	 *
	 * Retrieves Extension Instance Title, given the extension_instance_id
	 *
	 * @param   $extension_instance_id
	 *
	 * @return  bool|mixed
	 * @since   1.0
	 */
	public function getInstanceTitle($extension_instance_id)
	{
		$m = Application::Controller()->connect('ExtensionInstances');

		$m->model->query->select($m->model->db->qn('title'));
		$m->model->query->where($m->model->db->qn('id') . ' = ' . (int)$extension_instance_id);

		return $m->getData('loadResult');
	}

	/**
	 * getExtensionNode
	 *
	 * Retrieves the folder node for the specific extension
	 *
	 * @param  $catalog_type_id
	 * @param  $extension_instance_id
	 *
	 * @return  bool|mixed
	 * @since   1.0
	 */
	public function getExtensionNode($extension_instance_id)
	{
		$m = Application::Controller()->connect();

		$m->model->query->select($m->model->db->qn('a.name'));

		$m->model->query->from($m->model->db->qn('#__extensions') . ' as ' . $m->model->db->qn('a'));
		$m->model->query->from($m->model->db->qn('#__extension_instances') . ' as ' . $m->model->db->qn('b'));

		$m->model->query->where($m->model->db->qn('a.id') . ' = ' . $m->model->db->qn('b.extension_id'));
		$m->model->query->where($m->model->db->qn('b.id') . ' = ' . (int)$extension_instance_id);

		return $m->getData('loadResult');
	}

	/**
	 * getPath
	 *
	 * Return path for Extension - make certain to send in extension name, not
	 *     extension instance title. Extensions Instances do not have to have
	 *  the same name as the Extension, itself. The Extension name is what
	 *  is used in the path statements.
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function getPath($catalog_type_id, $node)
	{
		if ($catalog_type_id == CATALOG_TYPE_EXTENSION_PAGE_VIEW) {
			return Helpers::Page()->getPath($node);

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
			return Helpers::Template()->getPath($node);

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
			return Helpers::Wrap()->getPath($node);

		} else {
			$type = Helpers::Extension()->getType($catalog_type_id);
			return EXTENSIONS . '/' . $type . '/' . ucfirst(strtolower($node));
		}
	}

	/**
	 * getPathURL
	 *
	 * Return URL path for Extension
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function getPathURL($catalog_type_id, $node)
	{
		if ($catalog_type_id == CATALOG_TYPE_EXTENSION_PAGE_VIEW) {
			return Helpers::Page()->getPathURL($node);

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
			return Helpers::Template()->getPathURL($node);

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
			return Helpers::Wrap()->getPathURL($node);

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
	 * @return  boolean
	 * @since   1.0
	 */
	public function loadLanguage($path = null)
	{
		if ($path == null) {
			$path = Services::Registry()->get('Include', 'extension_path');
		}
		if ($path == null) {
			$path = Services::Registry()->get('Request', 'extension_path');
		}
		$path .= '/Language';

		if (Services::Filesystem()->folderExists($path)) {
		} else {
			echo 'does not exist' . $path . '<br />';
			echo '<pre>';
			var_dump(Services::Registry()->get('Include'));
			return false;
		}

		Services::Language()->load($path, Services::Language()->get('tag'), false, false);

		return true;
	}

	/**
	 *  setThemePageView
	 *
	 *  Determine the default theme value, given system default sequence
	 *
	 * @return  string
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

		} else if ($type == 'item') {

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
		Helpers::Theme()->get($theme_id, 'Parameters');

		/** Page  */
		Helpers::PageView()->get($page_view_id, 'Parameters');

		return;
	}

	/**
	 *  setThemePageView
	 *
	 *  Determine the default theme value, given system default sequence
	 *
	 * @return  string
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

		} else if ($type == 'item') {

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
		Helpers::TemplateView()->get($template_view_id, 'Parameters');

		/** Wrap  */
		Helpers::WrapView()->get($wrap_view_id, 'Parameters');

		return;
	}

	/**
	 * Retrieve the path node for a specified catalog type or
	 * it retrieves the catalog id value for the requested type
	 *
	 * @param $catalog_type_id
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function getType($catalog_type_id = 0, $catalog_type = null)
	{
		if ((int)$catalog_type_id == 0) {

			if ($catalog_type == 'Component') {
				return CATALOG_TYPE_EXTENSION_COMPONENT;

			} else if ($catalog_type == 'Formfield') {
				return CATALOG_TYPE_EXTENSION_FORMFIELDS;

			} else if ($catalog_type_id == 'Module') {
				return CATALOG_TYPE_EXTENSION_MODULE;

			} else if ($catalog_type == 'Theme') {
				return CATALOG_TYPE_EXTENSION_THEME;

			} else if ($catalog_type == 'Trigger') {
				return CATALOG_TYPE_EXTENSION_TRIGGER;

			} else if ($catalog_type == 'Pageview') {
				return CATALOG_TYPE_EXTENSION_PAGE_VIEW;

			} else if ($catalog_type == 'Templateview') {
				return CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW;

			} else if ($catalog_type == 'Wrapview') {
				return CATALOG_TYPE_EXTENSION_WRAP_VIEW;
			}

		} else {

			if ($catalog_type_id == CATALOG_TYPE_EXTENSION_COMPONENT) {
				return 'Component';

			} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_FORMFIELDS) {
				return 'Formfield';

			} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_MODULE) {
				return 'Module';

			} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_THEME) {
				return 'Theme';

			} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_TRIGGER) {
				return 'Trigger';

			} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_PAGE_VIEW) {
				return 'Pageview';

			} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
				return 'Templateview';

			} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
				return 'Wrapview';
			}
		}
	}
}
