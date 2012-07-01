<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Controller;

use Molajo\Service\Services;
use Molajo\Controller\ReadController;

defined('MOLAJO') or die;

/**
 * Delete
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class DeleteController extends ReadController
{
	/**
	 * Delete row and trigger other delete actions
	 *
	 * @return bool|object
	 * @since  1.0
	 */
	public function delete()
	{
		/** tokens */

		if (isset($this->data->model_name)) {
		} else {
			return false;
		}

		$results = $this->getDeleteData();
		if ($results === false) {
			return false;
		}

		$results = $this->checkPermissions();
		if ($results === false) {
			//error
			//return false (not yet)
		}

		parent::getTriggerList('delete');

		$valid = $this->onBeforeDeleteEvent();
		if ($valid === false) {
			return false;
			//error
		}

		if ($valid === true) {

			$this->connect('Table', $this->data->model_name, 'DeleteModel');
			$results = $this->model->delete($this->data, $this->table_registry_name);

			if ($results === false) {
			} else {
				$this->data->id = $results;
				$results = $this->onAfterDeleteEvent();
				if ($results === false) {
					return false;
					//error
				}
				$results = $this->data->id;
			}
		}

		/** redirect */
		if ($valid === true) {
			if ($this->get('redirect_on_success', '') == '') {

			} else {
				Services::Redirect()->url
					= Services::Url()->getURL($this->get('redirect_on_success'));
				Services::Redirect()->code == 303;
			}

		} else {
			if ($this->get('redirect_on_failure', '') == '') {

			} else {
				Services::Redirect()->url
					= Services::Url()->getURL($this->get('redirect_on_failure'));
				Services::Redirect()->code == 303;
			}
		}

		return $results;
	}

	/**
	 * Retrieve data to be deleted
	 *
	 * @param string $connect
	 *
	 * @return bool|mixed
	 * @since  1.0
	 */
	public function getDeleteData()
	{
		$hold_model_name = $this->data->model_name;
		$this->connect('Table', $hold_model_name);

		$this->set('use_special_joins', 0);
		$name_key = $this->get('name_key');
		$primary_key = $this->get('primary_key');
		$primary_prefix = $this->get('primary_prefix', 'a');

		if (isset($this->data->$primary_key)) {
			$this->model->query->where($this->model->db->qn($primary_prefix) . '.' . $this->model->db->qn($primary_key)
				. ' = ' . $this->model->db->q($this->data->$primary_key));

		} elseif (isset($this->data->$name_key)) {
			$this->model->query->where($this->model->db->qn($primary_prefix) . '.' . $this->model->db->qn($name_key)
				. ' = ' . $this->model->db->q($this->data->$name_key));

		} else {
			//only deletes single rows
			return false;
		}

		if (isset($this->data->catalog_type_id)) {
			$this->model->query->where($this->model->db->qn($primary_prefix)
				. '.' . $this->model->db->qn('catalog_type_id')
				. ' = ' . $this->model->db->q($this->data->catalog_type_id));
		}

		$item = $this->getData('item');
//		echo '<br /><br /><br />';
//		echo $this->model->query->__toString();
//		echo '<br /><br /><br />';

		if ($item === false) {
			//error
			return false;
		}

		$fields = Services::Registry()->get($this->table_registry_name, 'fields');
		if (count($fields) == 0 || $fields === null) {
			return false;
		}

		$this->data = new \stdClass();
		foreach ($fields as $f) {
			foreach ($f as $key => $value) {
				if ($key == 'name') {
					if (isset($item->$value)) {
						$this->data->$value = $item->$value;
					} else {
						$this->data->$value = null;
					}
				}
			}
		}

		if (isset($item->catalog_id)) {
			$this->data->catalog_id = $item->catalog_id;
		}
		$this->data->model_name = $hold_model_name;

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($this->table_registry_name, 'CustomFieldGroups');

		if (count($customFieldTypes) > 0) {
			foreach ($customFieldTypes as $customFieldName) {
				$customFieldName = ucfirst(strtolower($customFieldName));
				Services::Registry()->merge($this->table_registry_name . $customFieldName, $customFieldName);
				Services::Registry()->deleteRegistry($this->table_registry_name . $customFieldName);
			}
		}
		return true;
	}

	/**
	 * checkPermissions for Delete
	 *
	 * @return bool
	 * @since  1.0
	 */
	protected function checkPermissions()
	{
		//todo - figure out what joining isn't working, get catalog id
		//$results = Services::Authorisation()->authoriseTask('Delete', $this->data->catalog_id);
		//if ($results === false) {
		//error
		//return false (not yet)
		//}

		return true;
	}

	/**
	 * Schedule onBeforeDeleteEvent Event - could update model and data objects
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function onBeforeDeleteEvent()
	{
		if (count($this->triggers) == 0
			|| (int)$this->get('process_triggers') == 0
		) {
			return true;
		}

		$arguments = array(
			'table_registry_name' => $this->table_registry_name,
			'db' => $this->model->db,
			'data' => $this->data,
			'null_date' => $this->model->null_date,
			'now' => $this->model->now,
			'parameters' => $this->parameters,
			'model_name' => $this->get('model_name')
		);

		Services::Debug()->set('DeleteController->onBeforeDeleteEvent Schedules onBeforeDelete', LOG_OUTPUT_TRIGGERS);

		$arguments = Services::Event()->schedule('onBeforeDelete', $arguments, $this->triggers);
		if ($arguments == false) {
			Services::Debug()->set('DeleteController->onBeforeDeleteEvent Schedules onBeforeDelete', LOG_OUTPUT_TRIGGERS);
			return false;
		}

		Services::Debug()->set('DeleteController->onBeforeDeleteEvent Schedules onBeforeDelete', LOG_OUTPUT_TRIGGERS);

		/** Process results */
		$this->parameters = $arguments['parameters'];
		$this->data = $arguments['data'];

		return true;
	}

	/**
	 * Schedule onAfterDeleteEvent Event
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function onAfterDeleteEvent()
	{
		if (count($this->triggers) == 0
			|| (int)$this->get('process_triggers') == 0
		) {
			return true;
		}

		/** Schedule onAfterDelete Event */
		$arguments = array(
			'table_registry_name' => $this->table_registry_name,
			'db' => $this->model->db,
			'data' => $this->data,
			'parameters' => $this->parameters,
			'model_name' => $this->get('model_name')
		);

		Services::Debug()->set('CreateController->onAfterDeleteEvent Schedules onAfterDelete', LOG_OUTPUT_TRIGGERS);

		$arguments = Services::Event()->schedule('onAfterDelete', $arguments, $this->triggers);
		if ($arguments == false) {
			Services::Debug()->set('CreateController->onBeforeDeleteEvent Schedules onBeforeDelete', LOG_OUTPUT_TRIGGERS);
			return false;
		}

		Services::Debug()->set('CreateController->onAfterDeleteEvent Schedules onAfterDelete', LOG_OUTPUT_TRIGGERS);

		/** Process results */
		$this->parameters = $arguments['parameters'];
		$this->data = $arguments['data'];

		return true;
	}
}
