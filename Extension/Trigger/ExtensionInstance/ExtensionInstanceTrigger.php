<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Extensioninstance;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Controller\CreateController;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Extension Instances
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ExtensioninstanceTrigger extends ContentTrigger
{

	/**
	 * Pre-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{

		if ($this->query_results->catalog_type_id >= CATALOG_TYPE_EXTENSION_BEGIN
			AND $this->query_results->catalog_type_id <= CATALOG_TYPE_EXTENSION_END
		) {
		} else {
			return true;
		}

		/** Ensure no other entry exists for this Name/Catalog Type */
		$controllerClass = 'Molajo\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect('Table', 'ExtensionInstances');

		$primary_prefix = $m->get('primary_prefix', 'a');

		$m->set('get_customfields', '0');
		$m->set('get_item_children', '0');
		$m->set('use_special_joins', '0');
		$m->set('check_view_level_access', '0');

		$m->model->query->select($m->model->db->qn($primary_prefix) . '.' . $m->model->db->qn('id'));
		$m->model->query->where($m->model->db->qn($primary_prefix) . '.' . $m->model->db->qn('title')
			. ' = ' . $m->model->db->q($this->query_results->title));
		$m->model->query->where($m->model->db->qn($primary_prefix) . '.' . $m->model->db->qn('catalog_type_id')
			. ' = ' . (int)$this->query_results->catalog_type_id);

		$id = $m->getData('result');

		if ((int)$id > 0) {
			//name already exists
			return false;
		}

		return true;
	}

	/**
	 * Post-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterCreate()
	{
		if ($this->query_results->catalog_type_id >= CATALOG_TYPE_EXTENSION_BEGIN
			AND $this->query_results->catalog_type_id <= CATALOG_TYPE_EXTENSION_END
		) {
		} else {
			return true;
		}

		/** Extension ID */
		$id = $this->query_results->id;
		if ((int) $id == 0) {
			return false;
		}

		/** Site Extension Instances */
		$controller = new CreateController();

		$data = new \stdClass();
		$data->site_id = SITE_ID;
		$data->extension_instance_id = $id;
		$data->model_name = 'SiteExtensionInstances';

		$controller->data = $data;

		$results = $controller->create();
		if ($results === false) {
			//install failed
			return false;
		}

		/** Application Extension Instances */
		$controller = new CreateController();

		$data = new \stdClass();
		$data->application_id = APPLICATION_ID;
		$data->extension_instance_id = $id;
		$data->model_name = 'ApplicationExtensionInstances';

		$controller->data = $data;

		$results = $controller->create();
		if ($results === false) {
			//install failed
			return false;
		}

		/** Catalog */
		$controller = new CreateController();

		$data = new \stdClass();
		$data->catalog_type_id = Services::Registry()->get($this->table_registry_name, 'catalog_type_id');
		$data->source_id = $id;
		$data->view_group_id = 1;
		$data->extension_instance_id = $id;
		$data->model_name = 'Catalog';

		$controller->data = $data;

		$catalog_id = $controller->create();
		if ($results === false) {
			//install failed
			return false;
		}

		/** Catalog Activity */

		/** Permissions */
	}

	/**
	 * Pre-read processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeRead()
	{
		return true;
	}

	/**
	 * Post-read processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		return true;
	}

	/**
	 * On after route
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		return true;
	}

	/**
	 * Pre-update processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeUpdate()
	{
		return true;
	}

	/**
	 * Post-update processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterUpdate()
	{
		return true;
	}

	/**
	 * Pre-delete processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeDelete()
	{
		return true;
	}

	/**
	 * Post-delete processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterDelete()
	{
		return true;
	}
}
