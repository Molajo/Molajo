<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Application;
use Molajo\Service\Services;

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

		Services::Registry()->set('Extension', 'id', (int)$row->id);
		Services::Registry()->set('Route', 'extension_instances_id', (int)$row->id);
		Services::Registry()->set('Extension', 'title', $row->title);
		Services::Registry()->set('Extension', 'translation_of_id', $row->translation_of_id);
		Services::Registry()->set('Extension', 'language', $row->language);
		Services::Registry()->set('Extension', 'view_group_id', $row->view_group_id);
		Services::Registry()->set('Extension', 'catalog_id', $row->catalog_id);
		Services::Registry()->set('Extension', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('Extension', 'catalog_type_title', $row->catalog_type_title);
		Services::Registry()->set('Extension', 'path', $this->getPath((int)$row->catalog_type_id, $row->title));
		Services::Registry()->set('Extension', 'path_url', $this->getPathURL((int)$row->catalog_type_id, $row->title));

		/** Load special fields for specific extension */
		$xml = Services::Configuration()->loadFile(ucfirst(strtolower(Services::Registry()->get('Content', 'catalog_type_title'))), 'Table');
		$row = Services::Configuration()->populateCustomFields($xml->extension, $row, 1);

		return;
	}


	/**
	 * Retrieve Route information for a specific Extension
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function getIncludeExtension($extension_id)
	{
		/** Retrieve the query results */
		$row = $this->get($extension_id);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0) {
			Services::Registry()->set('Parameter', 'status_found', false);
			return false;
		}

		Services::Registry()->set('Parameters', 'extension_instance_id', (int)$row->id);
		Services::Registry()->set('Parameters', 'extension_instance_title', $row->title);
		Services::Registry()->set('Parameters', 'extension_translation_of_id', $row->translation_of_id);
		Services::Registry()->set('Parameters', 'extension_language', $row->language);

		Services::Registry()->set('Parameters', 'extension_catalog_id', $row->catalog_id);
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('Parameters', 'extension_catalog_type_title', $row->catalog_type_title);

		Services::Registry()->set('Parameters', 'extension_view_group_id', $row->view_group_id);

		Services::Registry()->set('Parameters', 'extension_path', $this->getPath((int)$row->catalog_type_id, $row->title));
		Services::Registry()->set('Parameters', 'extension_path_url', $this->getPathURL((int)$row->catalog_type_id, $row->title));

		Services::Registry()->set('Parameters', 'extension_primary', false);

		$xml = Services::Configuration()->loadFile(
			'Manifest', Services::Registry()->get('Parameters', 'extension_path')
		);
		if ($xml == false) {
			return;
		}

		$row = Services::Configuration()->populateCustomFields($xml->config, $row, 1);

		$parameters = Services::Registry()->get('ExtensionParameters');
		foreach ($parameters as $key => $value) {
			Services::Registry()->set('Parameters', $key, $value);
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
	public function get($extension_id = 0, $catalog_type_id = 0)
	{
		$m = Application::Controller()->connect('ExtensionInstances');

		$m->model->set('id', (int)$extension_id);

		$m->model->set('get_special_fields', 1);
		$m->model->set('get_item_children', false);
		$m->model->set('use_special_joins', false);
		$m->model->set('check_view_level_access', false);

		/**
		 *  a. Extensions Instances Table
		 */
		$m->model->query->select($m->model->db->qn('a.id'));
		$m->model->query->select($m->model->db->qn('a.catalog_type_id'));
		$m->model->query->select($m->model->db->qn('a.title'));
		$m->model->query->select($m->model->db->qn('a.parameters'));
		$m->model->query->select($m->model->db->qn('a.metadata'));
		$m->model->query->select($m->model->db->qn('a.custom_fields'));
		$m->model->query->select($m->model->db->qn('a.translation_of_id'));
		$m->model->query->select($m->model->db->qn('a.language'));

		$m->model->query->from($m->model->db->qn('#__extension_instances') . ' as a');

		$m->model->query->where($m->model->db->qn('a.extension_id') . ' > 0 ');

		$m->model->query->where(
			'((' . $m->model->db->qn('a.id') . '= ' . (int)$extension_id . ')'
				. ' OR (' . $m->model->db->qn('a.catalog_type_id') . ' = ' . (int)$catalog_type_id
				. ' AND 0 = ' . (int)$extension_id . '))'
		);

		$m->model->query->where($m->model->db->qn('a.status') . ' > ' . STATUS_UNPUBLISHED);
		$m->model->query->where('(' . $m->model->db->qn('a.start_publishing_datetime') . ' = ' .
				$m->model->db->q($m->model->nullDate) .
				' OR ' . $m->model->db->qn('a.start_publishing_datetime') . ' <= ' . $m->model->db->q($m->model->now) . ')'
		);
		$m->model->query->where('(' . $m->model->db->qn('a.stop_publishing_datetime') . ' = ' .
				$m->model->db->q($m->model->nullDate) .
				' OR ' . $m->model->db->qn('a.stop_publishing_datetime') . ' >= ' . $m->model->db->q($m->model->now) . ')'
		);

		/** Catalog Join and View Access Check */
		Services::Authorisation()
			->setQueryViewAccess(
			$m->model->query,
			$m->model->db,
			array('join_to_prefix' => 'a',
				'join_to_primary_key' => 'id',
				'catalog_prefix' => 'b_catalog',
				'select' => true
			)
		);

		/** b_catalog_types. Catalog Types Table  */
		$m->model->query->select($m->model->db->qn('b_catalog_types.title') . ' as catalog_type_title');
		$m->model->query->from($m->model->db->qn('#__catalog_types') . ' as b_catalog_types');
		$m->model->query->where($m->model->db->qn('b_catalog.catalog_type_id') . ' = ' . $m->model->db->qn('b_catalog_types.id'));

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
		//echo $m->model->query->__toString();

		$row = $m->getData('loadObject');

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
			$path = Services::Registry()->get('Extension', 'path');
		}
		$path .= '/language';


		if (Services::Filesystem()->folderExists($path)) {
		} else {
			echo 'does not exist'.$path.'<br />';
			return false;
		}

		Services::Language()->load($path, Services::Language()->get('tag'), false, false);

		return true;
	}
}
