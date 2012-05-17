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
	 * @return  boolean
	 * @since   1.0
	 */
	public function getRoute($extension_id)
	{
		/** Retrieve the query results */
		$row = $this->get($extension_id);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0) {
			return Services::Registry()->set('Route', 'status_found', false);
		}

		Services::Registry()->set('Route', 'extension_title', $row['title']);
		Services::Registry()->set('Route', 'extension_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Route', 'extension_language', $row['language']);
		Services::Registry()->set('Route', 'extension_view_group_id', $row['view_group_id']);
		Services::Registry()->set('Route', 'extension_catalog_id', $row['catalog_id']);
		Services::Registry()->set('Route', 'extension_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Route', 'extension_catalog_type_title', $row['catalog_type_title']);
		Services::Registry()->set('Route', 'extension_path',
			$this->getPath((int)$row['catalog_type_id'], $row['title']));
		Services::Registry()->set('Route', 'extension_path_url',
			$this->getPathURL((int)$row['catalog_type_id'], $row['title']));

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($row['table_registry_name'], 'CustomFieldGroups');
		foreach ($customFieldTypes as $customFieldName) {

			$customFieldName = ucfirst(strtolower($customFieldName));

			if ('Extensioninstances' . $customFieldName == 'Extensioninstances' . 'Parameters') {
				Services::Registry()->merge(
					'Extensioninstances' . $customFieldName,
					'Parameters'
				);
			}

			if ('Extensioninstances' . $customFieldName == 'Extensioninstances' . 'Metadata') {
				Services::Registry()->merge(
					'Extensioninstances' . $customFieldName,
					'Metadata'
				);
			}

			Services::Registry()->deleteRegistry('Extensioninstances' . $customFieldName);
		}

/**
		echo '<pre>';
		var_dump(Services::Registry()->get('Route'));
		echo '</pre>';
		echo '<pre>';
		var_dump(Services::Registry()->get('Parameters'));
		echo '</pre>';
		echo '<pre>';
		var_dump(Services::Registry()->get('Metadata'));
		echo '</pre>';
		die;
*/
		return;
	}

	/**
	 * Retrieve Route information for a specific Extension
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function getIncludeExtension($extension_id, $model_name = null, $type = null)
	{
		/** Retrieve the query results */
		$row = $this->get($extension_id, $model_name, $type);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0) {
			Services::Registry()->set('Parameter', 'status_found', false);
			return false;
		}

		Services::Registry()->set('Include', 'extension_id', (int)$row['id']);
		Services::Registry()->set('Include', 'extension_title', $row['title']);
		Services::Registry()->set('Include', 'extension_translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Include', 'extension_language', $row['language']);

		Services::Registry()->set('Include', 'extension_catalog_id', (int)$row['catalog_id']);
		Services::Registry()->set('Include', 'extension_catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Include', 'extension_catalog_type_title', $row['catalog_type_title']);

		Services::Registry()->set('Include', 'extension_view_group_id', (int)$row['view_group_id']);

		Services::Registry()->set('Include', 'extension_path',
			$this->getPath((int)$row['catalog_type_id'], $row['title']));
		Services::Registry()->set('Include', 'extension_path_url',
			$this->getPathURL((int)$row['catalog_type_id'], $row['title']));

		Services::Registry()->set('Include', 'extension_primary', false);

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($row['table_registry_name'], 'CustomFieldGroups');

		foreach ($customFieldTypes as $customFieldName) {

			$customFieldName = ucfirst(strtolower($customFieldName));

			if ($customFieldName == 'Parameters') {
				Services::Registry()->merge(
					$row['table_registry_name'].'Parameters', 'Parameters'
				);
			}

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
	public function get($extension_id = 0, $model = null, $type = null)
	{
		if ($model == null) {
			$model = 'ExtensionInstances';
		}

		$m = Application::Controller()->connect($model, $type);

		/**
		 *  a. Extensions Instances Table
		 */
		$m->model->query->select($m->model->db->qn('a.id'));
		$m->model->query->select($m->model->db->qn('a.catalog_type_id'));
		$m->model->query->select($m->model->db->qn('a.title'));
		$m->model->query->select($m->model->db->qn('a.parameters'));
		$m->model->query->select($m->model->db->qn('a.metadata'));
		$m->model->query->select($m->model->db->qn('a.customfields'));
		$m->model->query->select($m->model->db->qn('a.translation_of_id'));
		$m->model->query->select($m->model->db->qn('a.language'));

		$m->model->query->from($m->model->db->qn('#__extension_instances') . ' as a');

		$m->model->query->where($m->model->db->qn('a.extension_id') . ' > 0 ');

		$m->model->query->where($m->model->db->qn('a.id') . '= ' . (int)$extension_id);

		$m->model->query->where($m->model->db->qn('a.status') . ' > ' . STATUS_UNPUBLISHED);
		$m->model->query->where('(' . $m->model->db->qn('a.start_publishing_datetime') . ' = ' .
				$m->model->db->q($m->model->nullDate) .
				' OR ' . $m->model->db->qn('a.start_publishing_datetime') . ' <= ' . $m->model->db->q($m->model->now) . ')'
		);
		$m->model->query->where('(' . $m->model->db->qn('a.stop_publishing_datetime') . ' = ' .
				$m->model->db->q($m->model->nullDate) .
				' OR ' . $m->model->db->qn('a.stop_publishing_datetime') . ' >= ' . $m->model->db->q($m->model->now) . ')'
		);

		/** b_catalog_types. Catalog Types Table  */
		$m->model->query->select($m->model->db->qn('b_catalog_types.title') . ' as catalog_type_title');
		$m->model->query->from($m->model->db->qn('#__catalog_types') . ' as b_catalog_types');
		$m->model->query->where($m->model->db->qn('a.catalog_type_id') . ' = ' . $m->model->db->qn('b_catalog_types.id'));

		/**
		 *  c. Application Table
		 *      Extension Instances must be enabled for the Application
		 */
		$m->model->query->from($m->model->db->qn('#__application_extension_instances') . ' as c');
		$m->model->query->where($m->model->db->qn('c.extension_instance_id') . ' = ' . $m->model->db->qn('a.id'));
		$m->model->query->where($m->model->db->qn('c.application_id') . ' = ' . APPLICATION_ID);

		/**
		 *  d. Site Table
		 *      Extension Instances must be enabled for the Site
		 */
		$m->model->query->from($m->model->db->qn('#__site_extension_instances') . ' as d');
		$m->model->query->where($m->model->db->qn('d.extension_instance_id') . ' = ' . $m->model->db->qn('a.id'));
		$m->model->query->where($m->model->db->qn('d.site_id') . ' = ' . SITE_ID);

		/**
		 *  Run Query
		 */
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
	 * Retrieves Extension Name, given the extension_instance_id
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
	 * getPath
	 *
	 * Return path for Extension
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function getPath($catalog_type_id, $name)
	{
		if ($catalog_type_id == CATALOG_TYPE_EXTENSION_COMPONENT) {
			return EXTENSIONS_COMPONENTS . '/' . ucfirst(strtolower($name));

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_FORMFIELDS) {
			return EXTENSIONS_FORMFIELDS . '/' . ucfirst(strtolower($name));

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_MODULE) {
			return EXTENSIONS_MODULES . '/' . ucfirst(strtolower($name));

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_THEME) {
			return EXTENSIONS_THEMES . '/' . ucfirst(strtolower($name));

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_TRIGGER) {
			return EXTENSIONS_TRIGGERS . '/' . ucfirst(strtolower($name));

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_PAGE_VIEW) {
			return Helpers::Page()->getPath($name);

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
			return Helpers::Template()->getPath($name);

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
			return Helpers::Wrap()->getPath($name);
		}

		return false;
	}

	/**
	 * getPathURL
	 *
	 * Return URL path for Extension
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function getPathURL($catalog_type_id, $name)
	{
		if ($catalog_type_id == CATALOG_TYPE_EXTENSION_COMPONENT) {
			return EXTENSIONS_COMPONENTS_URL . '/' . ucfirst(strtolower($name));

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_FORMFIELDS) {
			return EXTENSIONS_FORMFIELDS_URL . '/' . ucfirst(strtolower($name));

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_MODULE) {
			return EXTENSIONS_MODULES_URL . '/' . ucfirst(strtolower($name));

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_THEME) {
			return EXTENSIONS_THEMES_URL . '/' . ucfirst(strtolower($name));

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_TRIGGER) {
			return EXTENSIONS_TRIGGERS_URL . '/' . ucfirst(strtolower($name));

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_PAGE_VIEW) {
			return Helpers::Page()->getPathURL($name);

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW) {
			return Helpers::Template()->getPathURL($name);

		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_WRAP_VIEW) {
			return Helpers::Wrap()->getPathURL($name);
		}

		return false;
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
			return;
			echo 'does not exist'.$path.'<br />';
			echo '<pre>';
			var_dump(Services::Registry()->get('Include'));
			return false;
		}

//todo fix Services::Language()->load($path, Services::Language()->get('tag'), false, false);
		Services::Language()->load($path, 'en-GB', false, false);

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

		$template_view_id = (int) Services::Registry()->get('Parameters', 'template_view_id', 0);
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
			}
		}


		$getWrap = true;

		$wrap_view_id = (int) Services::Registry()->get('Parameters', 'wrap_view_id', 0);
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
					CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW,
					$wrap_view_title
				);
				if ($wrap_view_id == false) {
				} else {
					$getWrap = false;
					Services::Registry()->set('Parameters', 'wrap_view_id', $wrap_view_id);
				}
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


		} else {

			Services::Registry()->set('Parameters', 'template_view', 'item');
			if ($getTemplate == true) {

			}

			if ($getWrap == true) {

			}
		}

		Helpers::TemplateView()->get();

		Helpers::WrapView()->get();

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
}
