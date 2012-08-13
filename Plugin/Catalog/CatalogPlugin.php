<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Catalog;

use Molajo\Plugin\Content\ContentPlugin;
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
class CatalogPlugin extends ContentPlugin
{
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
			if ($results == false) {
				return false;
			}
		}

		if (Services::Registry()->get('Configuration', 'log_catalog_update_activity', 1) == 1) {
			$results = $this->logCatalogActivity($id, Services::Registry()->get('Actions', 'create'));
			if ($results == false) {
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
			if ($results == false) {
				return false;
			}
		}

		if (Services::Registry()->get('Configuration', 'log_catalog_update_activity', 1) == 1) {
			$results = $this->logCatalogActivity($this->data->id,
				Services::Registry()->get('Actions', 'delete'));
			if ($results == false) {
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
