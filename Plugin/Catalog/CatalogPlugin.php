<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Catalog;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\MVC\Controller\CreateController;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Catalog
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CatalogPlugin extends Plugin
{
	/**
	 * Generates list of Datalists for use in defining Custom Fields of Type Selectlist
	 *
	 * This can be moved to onBeforeParse when Plugin ordering is in place
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}
		Services::Registry()->set('Datalist', 'Catalog', array());
		return;
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();

		$results = $connect->connect('Table', 'Catalog');
		if ($results === false) {
			return false;
		}

		$connect->set('get_customfields', 0);
		$connect->set('use_special_joins', 0);
		$connect->set('process_plugins', 0);

		$connect->model->query->select(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('id')
		);
		$connect->model->query->select(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('sef_request')
				. ' AS value '
		);

		$connect->model->query->where(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('redirect_to_id')
				. ' = 0'
		);
		$connect->model->query->where(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('enabled')
				. ' = 1'
		);
		$connect->model->query->where(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('appplication_id')
				. ' = ' . (int) APPLICATION_ID
		);

		$connect->model->query->order(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('sef_request')
		);

		$connect->set('model_offset', 0);
		$connect->set('model_count', 99999);

		$query_results = $connect->getData('list');
		$catalogArray = array();

		$application_home_catalog_id = (int) Services::Registry()->get('configuration', 'application_home_catalog_id');
		if ($application_home_catalog_id === 0) {
		} else {
			if (count($query_results) == 0 || $query_results === false) {
			} else {

				foreach ($query_results as $item) {
					if ($item->id == $application_home_catalog_id) {
						$item->value = trim($item->value . ' ' . Services::Language()->translate('Home'));
						$catalogArray[] = $item;
					} elseif (trim($item->value) == '' || $item->value === NULL) {
						unset ($item);
					} else {
						$catalogArray[] = $item;
					}
				}
			}
		}
		Services::Registry()->set('Datalist', 'Catalog', $catalogArray);

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
		/** Just inserted UD */
		$id = $this->data->id;
		if ((int)$id == 0) {
			return false;
		}

		/** Catalog Activity: fields populated by Catalog Activity plugins */
		if (Services::Registry()->get('Configuration', 'log_user_update_activity', 1) == 1) {
			$results = $this->logUserActivity($id, Services::Registry()->get('Actions', 'create'));
			if ($results === false) {
				return false;
			}
		}

		if (Services::Registry()->get('Configuration', 'log_catalog_update_activity', 1) == 1) {
			$results = $this->logCatalogActivity($id, Services::Registry()->get('Actions', 'create'));
			if ($results === false) {
				return false;
			}
		}

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
		if (Services::Registry()->get('Configuration', 'log_user_update_activity', 1) == 1) {
			$results = $this->logUserActivity($this->data->id,
				Services::Registry()->get('Actions', 'delete'));
			if ($results === false) {
				return false;
			}
		}

		if (Services::Registry()->get('Configuration', 'log_catalog_update_activity', 1) == 1) {
			$results = $this->logCatalogActivity($this->data->id,
				Services::Registry()->get('Actions', 'delete'));
			if ($results === false) {
				return false;
			}
		}

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
		return true; // only redirect id
	}

	/**
	 * Pre-delete processing
	 *
	 * @param   $this->data
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeDelete()
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$m = new $controllerClass();
		$m->connect();

		$sql = 'DELETE FROM ' . $m->model->db->qn('#__catalog_categories');
		$sql .= ' WHERE ' . $m->model->db->qn('catalog_id') . ' = ' . (int)$this->data->id;
		$m->model->db->setQuery($sql);
		$m->model->db->execute();

		$sql = 'DELETE FROM ' . $m->model->db->qn('#__catalog_activity');
		$sql .= ' WHERE ' . $m->model->db->qn('catalog_id') . ' = ' . (int)$this->data->id;
		$m->model->db->setQuery($sql);
		$m->model->db->execute();

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
		//how to get id - referential integrity?
		/**
		if (Services::Registry()->get('Configuration', 'log_user_update_activity', 1) == 1) {
		$this->logUserActivity($id, Services::Registry()->get('Actions', 'delete'));
		}
		if (Services::Registry()->get('Configuration', 'log_catalog_update_activity', 1) == 1) {
		$this->logCatalogActivity($id, Services::Registry()->get('Actions', 'delete'));
		}
		 */

		return true;
	}

	/**
	 * Log user updates
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function logUserActivity($id, $action_id)
	{
		$data = new \stdClass();
		$data->model_name = 'UserActivity';
		$data->model_table = 'Table';
		$data->catalog_id = $id;
		$data->action_id = $action_id;

		$controller = new CreateController();
		$controller->data = $data;
		$user_activity_id = $controller->execute();
		if ($user_activity_id === false) {
			//install failed
			return false;
		}

		return true; // only redirect id
	}

	/**
	 * Pre-update processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function logCatalogActivity($id, $action_id)
	{
		$data = new \stdClass();
		$data->model_name = 'CatalogActivity';
		$data->model_table = 'Table';
		$data->catalog_id = $id;
		$data->action_id = $action_id;

		$controller = new CreateController();
		$controller->data = $data;
		$catalog_activity_id = $controller->execute();
		if ($catalog_activity_id === false) {
			//install failed
			return false;
		}

		return true; // only redirect id
	}
}
