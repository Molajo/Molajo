<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

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
		Services::Registry()->set('Extension', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('Extension', 'title', $row->title);
		Services::Registry()->set('Extension', 'parameters', $row->parameters);
		Services::Registry()->set('Extension', 'metadata', $row->metadata);

		$xml = Services::Configuration()->loadFile(ucfirst(strtolower($row->title)), 'Table');

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
		$m = Services::Model()->connect('ExtensionInstances');

		$m->model->set('id', (int)$extension_id);

		$m->model->set('get_special_fields', 0);
		$m->model->set('get_item_children', false);
		$m->model->set('use_special_joins', false);
		$m->model->set('add_acl_check', true);

		/**
		 *  a. Extensions Instances Table
		 */
		$m->model->query->select($m->model->db->qn('a.id'));
		$m->model->query->select($m->model->db->qn('a.catalog_type_id'));
		$m->model->query->select($m->model->db->qn('a.title'));
		$m->model->query->select($m->model->db->qn('a.parameters'));
		$m->model->query->select($m->model->db->qn('a.metadata'));
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

		$row = $m->execute('loadObject');

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
		$m = Services::Model()->connect('ExtensionInstances');

		$m->model->query->select($m->model->db->qn('id'));
		$m->model->query->where($m->model->db->qn('title') . ' = ' . $m->model->db->q($title));
		$m->model->query->where($m->model->db->qn('catalog_type_id') . ' = ' . (int)$catalog_type_id);

		return $m->execute('loadResult');
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
		$m = Services::Model()->connect('ExtensionInstances');

		$m->model->query->select($m->model->db->qn('title'));
		$m->model->query->where($m->model->db->qn('id') . ' = ' . (int)$extension_instance_id);

		return $m->execute('loadResult');
	}

	/**
	 * formatNameForClass
	 *
	 * Extension names can include dashes (or underscores). This method
	 * prepares the name for use as a component of a classname.
	 *
	 * @param $extension_name
	 *
	 * @return string
	 * @since  1.0
	 */
	public function formatNameForClass($extension_name)
	{
		return ucfirst(str_replace(array('-', '_'), '', $extension_name));
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
			return Helper::Component()->getPath($name);
		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_MODULE) {
			return Helper::Module()->getPath($name);
		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_THEME) {
			return Helper::Theme()->getPath($name);
		} else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_TRIGGER) {
			return Helper::Trigger()->getPath($name);
		}
		return false;
	}

	/**
	 * loadLanguage
	 *
	 * Loads Language Files for extension
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function loadLanguage($path)
	{
		$path .= '/language';

		if (Services::Filesystem()->folderExists($path)) {
		} else {
			return false;
		}

		Services::Language()->load($path, Services::Language()->get('tag'), false, false);

		return true;
	}
}
