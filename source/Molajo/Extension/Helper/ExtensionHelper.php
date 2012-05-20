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
			'Component'
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

		return;
	}

	/**
	 * Retrieve Route information for a specific Extension
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function getIncludeExtension($extension_id, $catalog_type_id, $model_name = null)
	{
		/** Retrieve catalog type, given the key value */
		$type = Helpers::Extension()->getType($catalog_type_id);

		/** Retrieve the query results */
		$row = $this->get($extension_id, $model_name, $type);

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

		$m->model->query->from($m->model->db->qn('#__extensions').' as '.$m->model->db->qn('a'));
		$m->model->query->from($m->model->db->qn('#__extension_instances').' as '.$m->model->db->qn('b'));

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
	 * Finalize the Template and Wrap selections for the request
	 *
	 * @return  null
	 * @since   1.0
	 */
	public function finalizeParameters($source_id = 0, $action = 'display')
	{
		$getTemplate = true;

		$template_view_id = (int)Services::Registry()->get('Parameters', 'template_view_id', 0);
		$template_view_title = Services::Registry()->get('Parameters', 'template_view_title', '');

		if ($template_view_id == 0) {
		} else {
			$template_view_title = Helpers::Extension()->getInstanceTitle((int)$template_view_id);
			if ($template_view_title == false) {
			} else {
				$getTemplate = false;
				Services::Registry()->set('Parameters', 'wrap_view_title', $template_view_title);
			}

		}

		if ($template_view_title == '') {
		} else {
			if ($template_view_id == 0) {
				$template_view_id = Helpers::Extension()->getInstanceID(
					CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW,
					$template_view_title
				);
				if ($template_view_id == false) {
				} else {
					$getTemplate = false;
					Services::Registry()->set('Parameters', 'template_view_id', $template_view_id);
				}
			} else {
				$getTemplate = false;
			}
		}

		$getWrap = true;

		$wrap_view_id = (int)Services::Registry()->get('Parameters', 'wrap_view_id', 0);
		$wrap_view_title = Services::Registry()->get('Parameters', 'wrap_view_title', '');

		if ($wrap_view_id == 0) {
		} else {
			$wrap_view_title = Helpers::Extension()->getInstanceTitle((int)$wrap_view_id);

			if ($wrap_view_title == false) {
			} else {
				$getWrap = false;
				Services::Registry()->set('Parameters', 'wrap_view_title', $wrap_view_title);
			}

		}

		if ($wrap_view_title == '') {
		} else {
			if ($wrap_view_id == 0) {
				$wrap_view_id = Helpers::Extension()->getInstanceID(
					CATALOG_TYPE_EXTENSION_WRAP_VIEW,
					$wrap_view_title
				);
				if ($wrap_view_id == false) {
				} else {
					$getWrap = false;
					Services::Registry()->set('Parameters', 'wrap_view_id', $wrap_view_id);
				}
			} else {
				$getWrap = false;
			}
		}

		if ($action == 'add' || $action == 'edit') {

			Services::Registry()->set('Parameters', 'template_view', 'form');

			if ($getTemplate == true) {
				Services::Registry()->set('Parameters', 'template_view_id',
					Services::Registry()->get('Parameters', 'form_template_view_id'));
				Services::Registry()->set('Parameters', 'template_view_css_id',
					Services::Registry()->get('Parameters', 'form_template_view_css_id'));
				Services::Registry()->set('Parameters', 'template_view_css_class',
					Services::Registry()->get('Parameters', 'form_template_view_css_class'));
			}

			if ($getWrap == true) {
				Services::Registry()->set('Parameters', 'wrap_view_id',
					Services::Registry()->get('Parameters', 'form_wrap_view_id'));
				Services::Registry()->set('Parameters', 'wrap_view_css_id',
					Services::Registry()->get('Parameters', 'form_wrap_view_css_id'));
				Services::Registry()->set('Parameters', 'wrap_view_css_class',
					Services::Registry()->get('Parameters', 'form_wrap_view_css_class'));
			}

			$model_name = Services::Registry()->get('Parameters', 'form_model_name', '');
			$model_type = Services::Registry()->get('Parameters', 'form_model_type', '');
			$model_query_object = Services::Registry()->get('Parameters', 'form_model_query_object', '');


		} else if ((int)$source_id == 0) {

			Services::Registry()->set('Parameters', 'template_view', 'list');

			if ($getTemplate == true) {
				Services::Registry()->set('Parameters', 'template_view_id',
					Services::Registry()->get('Parameters', 'list_template_view_id'));
				Services::Registry()->set('Parameters', 'template_view_css_id',
					Services::Registry()->get('Parameters', 'list_template_view_css_id'));
				Services::Registry()->set('Parameters', 'template_view_css_class',
					Services::Registry()->get('Parameters', 'list_template_view_css_class'));
			}

			if ($getWrap == true) {
				Services::Registry()->set('Parameters', 'wrap_view_id',
					Services::Registry()->get('Parameters', 'list_wrap_view_id'));
				Services::Registry()->set('Parameters', 'wrap_view_css_id',
					Services::Registry()->get('Parameters', 'list_wrap_view_css_id'));
				Services::Registry()->set('Parameters', 'wrap_view_css_class',
					Services::Registry()->get('Parameters', 'list_wrap_view_css_class'));
			}

			$model_name = Services::Registry()->get('Parameters', 'list_model_name', '');
			$model_type = Services::Registry()->get('Parameters', 'list_model_type', '');
			$model_query_object = Services::Registry()->get('Parameters', 'list_model_query_object', '');


		} else {

			Services::Registry()->set('Parameters', 'template_view', 'item');

			if ($getTemplate == true) {

			}

			if ($getWrap == true) {

			}

			$model_name = Services::Registry()->get('Parameters', 'model_name', '');
			$model_type = Services::Registry()->get('Parameters', 'model_type', '');
			$model_query_object = Services::Registry()->get('Parameters', 'model_query_object', '');
		}

		if ($model_name == '') {
			$model_name = Services::Registry()->get('Include', 'extension_title');
		}
		Services::Registry()->set('Parameters', 'model_name', $model_name);

		if ($model_type == '') {
			$model_type = 'Table';
		}
		Services::Registry()->set('Parameters', 'model_type', $model_type);

		if ($action == 'add' || $action == 'edit') {
			if ($model_query_object == '') {
				$model_query_object = 'load';
			}

		} else if ((int)$source_id == 0) {
			if ($model_query_object == '') {
				$model_query_object = 'load';
			}

		} else {
			if ($model_query_object == '') {
				$model_query_object = 'getData';
			}
		}
		Services::Registry()->set('Parameters', 'model_query_object', $model_query_object);

		Helpers::TemplateView()->get(Services::Registry()->get('Parameters', 'template_view_id', 0));

		Helpers::WrapView()->get(Services::Registry()->get('Parameters', 'wrap_view_id', 0));

		/** Remove parameters not needed */
		Services::Registry()->delete('Parameters', 'list_template_view_id');
		Services::Registry()->delete('Parameters', 'list_template_view_css_id');
		Services::Registry()->delete('Parameters', 'list_template_view_css_class');
		Services::Registry()->delete('Parameters', 'list_wrap_view_id');
		Services::Registry()->delete('Parameters', 'list_wrap_view_css_id');
		Services::Registry()->delete('Parameters', 'list_wrap_view_css_class');

		Services::Registry()->delete('Parameters', 'form_template_view_id');
		Services::Registry()->delete('Parameters', 'form_template_view_css_id');
		Services::Registry()->delete('Parameters', 'form_template_view_css_class');
		Services::Registry()->delete('Parameters', 'form_wrap_view_id');
		Services::Registry()->delete('Parameters', 'form_wrap_view_css_id');
		Services::Registry()->delete('Parameters', 'form_wrap_view_css_class');

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
