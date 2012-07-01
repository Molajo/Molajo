<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Extensioninstance;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Controller;
use Molajo\Controller\CreateController;
use Molajo\Controller\DeleteController;
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
	 * onBeforeCreate processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
		if ($this->data->catalog_type_id >= CATALOG_TYPE_EXTENSION_BEGIN
			AND $this->data->catalog_type_id <= CATALOG_TYPE_EXTENSION_END
		) {
		} else {
			return true;
		}

		/** Check ACL */

		/** Check if the Extension Instance already exists */
		$controllerClass = 'Molajo\\Controller\\ReadController';
		$m = new $controllerClass();
		$m->connect('Table', 'ExtensionInstances');

		$primary_prefix = $m->get('primary_prefix', 'a');

		$m->set('get_customfields', '0');
		$m->set('get_item_children', '0');
		$m->set('use_special_joins', '0');
		$m->set('check_view_level_access', '0');

		$m->model->query->select($m->model->db->qn($primary_prefix) . '.' . $m->model->db->qn('id'));
		$m->model->query->where($m->model->db->qn($primary_prefix) . '.' . $m->model->db->qn('title')
			. ' = ' . $m->model->db->q($this->data->title));
		$m->model->query->where($m->model->db->qn($primary_prefix) . '.' . $m->model->db->qn('catalog_type_id')
			. ' = ' . (int)$this->data->catalog_type_id);

		$id = $m->getData('result');

		if ((int)$id > 0) {
			//name already exists
			return false;
		}

		/** Next, see if the Extension node exists */
		$controllerClass = 'Molajo\\Controller\\ReadController';
		$m = new $controllerClass();
		$m->connect('Table', 'Extensions');

		$m->model->query->select($m->model->db->qn('a.id'));
		$m->model->query->where($m->model->db->qn('a.name')
			. ' = ' . $m->model->db->q($this->data->title));
		$m->model->query->where($m->model->db->qn('a.catalog_type_id')
			. ' = ' . (int) $this->data->catalog_type_id);

		$item = $m->getData('item');

		if ($item === false) {
		} else {
			$this->data->extension_id = $item->id;
			$this->data->catalog_type_id = $item->catalog_type_id;
			return;
		}

		/** If Extension Node does not exist */

		//todo decide if another query is warranted for verifying existence of catalog type

		/** Create a new Catalog Type */
		$controllerClass = 'Molajo\\Controller\\ReadController';
		$m = new $controllerClass();
		$m->connect();

		/** Catalog Types */
		$sql = 'INSERT INTO ' . $m->model->db->qn('#__catalog_types');
		$sql .= ' VALUES ( NULL, '
			. $m->model->db->q($this->data->title)
			. ', 0, '
			. $m->model->db->q($this->data->title)
			. ', ' . $m->model->db->q('#__content') . ')';

		$m->model->db->setQuery($sql);
		$m->model->db->execute();

		$this->parameters['content_catalog_type_id'] = $m->model->db->insertid();

		/** Create a new Extension Node */
		$data = new \stdClass();
		$data->name = $this->data->title;
		$data->catalog_type_id = $this->data->catalog_type_id;
		$data->model_name = 'Extensions';

		$controller = new CreateController();
		$controller->data = $data;

		$this->data->extension_id = $controller->execute();

		if ($this->data->extension_id  === false) {
			//error
			return false;
		}

		return true;
	}

	/**
	 * onAfterCreate processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterCreate()
	{
		echo 'Catalog ID ' . $this->data->catalog_type_id . '<br />';

		if ($this->data->catalog_type_id >= CATALOG_TYPE_EXTENSION_BEGIN
			AND $this->data->catalog_type_id <= CATALOG_TYPE_EXTENSION_END
		) {
		} else {
			return true;
		}

		echo 'ID ' . $this->data->id . '<br />';
		/** Extension Instance ID */
		$id = $this->data->id;
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

		$results = $controller->execute();
		if ($results === false) {
			//install failed
			return false;
		}
echo 'results are true for site '.'<br />';

		/** Application Extension Instances */
		$controller = new CreateController();

		$data = new \stdClass();
		$data->application_id = APPLICATION_ID;
		$data->extension_instance_id = $id;
		$data->model_name = 'ApplicationExtensionInstances';

		$controller->data = $data;

		$results = $controller->execute();
		if ($results === false) {
			//install failed
			return false;
		}
echo 'results are true for app '.'<br />';
		/** Catalog */
		$controller = new CreateController();

		$data = new \stdClass();
		$data->catalog_type_id = Services::Registry()->get($this->table_registry_name, 'catalog_type_id');
		$data->source_id = $id;
		$data->view_group_id = 1;
		$data->extension_instance_id = $id;
		$data->model_name = 'Catalog';

		$controller->data = $data;

		$this->data->catalog_id = $controller->execute();
		if ($results === false) {
			//install failed
			return false;
		}
echo 'results are true for catalog '.'<br />';
		return true;
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
	 * @since   1.0                                   ve
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
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterUpdate()
	{
		return true;
	}

	/**
	 * onBeforeDelete -
	 *
	 * Returns false and does not delete if there is content for this extension
	 *
	 * Deletes ACL and catalog data for this extension to be deleted
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeDelete()
	{

		/** Only Extension Instances */
		if (isset($this->data->catalog_type_id)
			&& ($this->data->catalog_type_id == 1050)) {
		} else {
			return true;
		}

		/** Do not allow delete if there is content for this component */
		$controllerClass = 'Molajo\\Controller\\ReadController';
		$m = new $controllerClass();

		$m->connect('Table', $this->data->title);

		$primary_prefix = $m->get('primary_prefix', 'a');

		$m->set('get_customfields', '0');
		$m->set('get_item_children', '0');
		$m->set('use_special_joins', '0');
		$m->set('check_view_level_access', '0');

		if (isset($this->parameters['content_catalog_type_id'])) {
			$temp = (int) $this->parameters['content_catalog_type_id'];

			$m->model->query->where($m->model->db->qn($primary_prefix)
				. '.' . $m->model->db->qn('catalog_type_id')
				. ' = ' . $temp);

			$item = $m->getData('item');

			if ($item === false) {
			} else {
				//content exists - cannot delete
				return false;
			}
		}

		/** Delete allowed - get rid of ACL info */
		$controllerClass = 'Molajo\\Controller\\ReadController';
		$m = new $controllerClass();
		$m->connect();

		$sql = 'DELETE FROM ' . $m->model->db->qn('#__application_extension_instances');
		$sql .= ' WHERE ' . $m->model->db->qn('extension_instance_id') . ' = ' . (int) $this->data->id;
		$m->model->db->setQuery($sql);
		$m->model->db->execute();

		$sql = 'DELETE FROM ' . $m->model->db->qn('#__site_extension_instances');
		$sql .= ' WHERE ' . $m->model->db->qn('extension_instance_id') . ' = ' . (int) $this->data->id;
		$m->model->db->setQuery($sql);
		$m->model->db->execute();

		$sql = 'DELETE FROM ' . $m->model->db->qn('#__group_permissions');
		$sql .= ' WHERE ' . $m->model->db->qn('catalog_id') . ' = ' . (int) $this->data->catalog_id;
		$m->model->db->setQuery($sql);
		$m->model->db->execute();

		$sql = 'DELETE FROM ' . $m->model->db->qn('#__view_group_permissions');
		$sql .= ' WHERE ' . $m->model->db->qn('catalog_id') . ' = ' . (int) $this->data->catalog_id;
		$m->model->db->setQuery($sql);
		$m->model->db->execute();

		/** Catalog has triggers for more deletions */
		$controller = new DeleteController();
echo 'Passing this catalog id in to Delete Controller ' . $this->data->catalog_id.'<br />';

		$data = new \stdClass();
		$data->model_name = ucfirst(strtolower('Catalog'));
		$data->id = $this->data->catalog_id;
		$controller->data = $data;
		$controller->set('action', 'delete');

		$id = $controller->execute();

		return true;
	}

	/**
	 * Post-delete processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterDelete()
	{
		$controllerClass = 'Molajo\\Controller\\ReadController';
		$m = new $controllerClass();
		$results = $m->connect('Table', 'ExtensionInstances');
		if ($results == false) {
			return false;
		}

		$m->set('get_customfields', 0);
		$m->set('get_item_children', 0);
		$m->set('use_special_joins', 0);
		$m->set('check_view_level_access', 0);
		$m->set('process_triggers', 0);

		$m->model->query->select('COUNT(*)');
		$m->model->query->from($m->model->db->qn('#__extension_instances'));
		$m->model->query->where($m->model->db->qn('extension_id')
			. ' = ' . (int)$this->data->extension_id);

		$value = $m->getData('result');

		if (empty($value) || (int) $value == 0) {
		} else {
			/** do not delete - more instances remain */
			return true;
		}

		/** Delete orphan node */
		$controller = new DeleteController();

		$data = new \stdClass();
		$data->model_name = ucfirst(strtolower('Extensions'));
		$data->id = $this->data->extension_id;
		$controller->data = $data;
		$controller->set('action', 'delete');

		$controller->execute();

		return true;
	}
}
