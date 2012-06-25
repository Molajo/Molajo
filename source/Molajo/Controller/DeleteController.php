<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Controller;

use Molajo\Service\Services;
use Molajo\Controller\ModelController;

defined('MOLAJO') or die;

/**
 * Delete
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class DeleteController extends ModelController
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

		$this->connect('Table', $this->data->model_name, 'DeleteModel');
		if (isset($this->data->id) && (int)$this->data->id > 0) {
		} else {
			$this->data->id = Services::Registry()->get($this->table_registry_name, 'id');
		}

		$results = $this->checkPermissions();
		if ($results === false) {
			//error
			//return false (not yet)
		}

		$this->getTriggerList('create');

		$valid = $this->onBeforeDeleteEvent();
		if ($valid === false) {
			return false;
			//errror
		}

		$valid = $this->checkFields();
		if ($valid === false) {
			return false;
			//errror
		}

		$value = $this->checkForeignKeys();

		if ($valid === true) {

			$fields = Services::Registry()->get($this->table_registry_name, 'fields');

			if (count($fields) == 0 || $fields === null) {
				return false;
			}

			$data = new \stdClass();
			foreach ($fields as $f) {
				foreach ($f as $key => $value) {
					if ($key == 'name') {
						if (isset($this->data->$value)) {
							$data->$value = $this->data->$value;
						} else {
							$data->$value = null;
						}
					}
				}
			}

			$results = $this->model->create($data, $this->table_registry_name);

			if ($results === false) {
			} else {
				$data->id = $results;
				$results = $this->onAfterDeleteEvent($data);
				if ($results === false) {
					return false;
					//errror
				}
				$results = $data->id;
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
	 * checkPermissions for Delete
	 *
	 * @return bool
	 * @since  1.0
	 */
	protected function checkPermissions()
	{

		$results = Services::Authorisation()->authoriseTask('Delete', $this->data->catalog_id);
		if ($results === false) {
			//error
			//return false (not yet)
		}

		return true;
	}

	/**
	 * checkFields
	 *
	 * Runs custom validation methods
	 *
	 * @return object
	 * @since   1.0
	 */
	protected function checkFields()
	{

		$userHTMLFilter = Services::Authorisation()->setHTMLFilter();

		/** Custom Field Groups */
		$customfieldgroups = Services::Registry()->get(
			$this->table_registry_name, 'customfieldgroups', array());

		if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {

			foreach ($customfieldgroups as $customFieldName) {

				/** For this Custom Field Group (ex. Parameters, metadata, etc.) */
				$customFieldName = strtolower($customFieldName);
				if (isset($this->data->$customFieldName)) {
				} else {
					$this->data->$customFieldName = '';
				}

				/** Retrieve Field Definitions from Registry (XML) */
				$fields = Services::Registry()->get($this->table_registry_name, $customFieldName);

				/** Shared processing  */
				$valid = $this->processFieldGroup($fields, $userHTMLFilter, $customFieldName);

				if ($valid === true) {
				} else {
					return false;
				}
			}
		}

		/** Standard Field Group */
		$fields = Services::Registry()->get($this->table_registry_name, 'fields');
		if (count($fields) == 0 || $fields === null) {
			return false;
		}

		$valid = $this->processFieldGroup($fields, $userHTMLFilter, '');
		if ($valid === true) {
		} else {
			return false;
		}

		Services::Debug()->set('DeleteController::checkFields Filter::Success: ' . $valid);

		return $valid;
	}

	/**
	 * processFieldGroup - runs custom filtering, defaults, validation for a field group
	 *
	 * @param $fields
	 * @param $userHTMLFilter
	 *
	 * @return bool
	 * @since  1.0
	 */
	protected function processFieldGroup($fields, $userHTMLFilter, $customFieldName = '')
	{
		$valid = true;

		if ($customFieldName == '') {
		} else {
			$fieldArray = array();
		}

		foreach ($fields as $f) {

			if (isset($f['name'])) {
				$name = $f['name'];
			} else {
				return false;
				//error
			}

			if (isset($f['type'])) {
				$type = $f['type'];
			} else {
				$type = null;
			}

			if (isset($f['null'])) {
				$null = $f['null'];
			} else {
				$null = null;
			}

			if (isset($f['default'])) {
				$default = $f['default'];
			} else {
				$default = null;
			}

			if (isset($f['identity'])) {
				$identity = $f['identity'];
			} else {
				$identity = 0;
			}

			/** Retrieve value from data */
			if ($customFieldName == '') {

				if (isset($this->data->$name)) {
					$value = $this->data->$name;
				} else {
					$value = null;
				}

			} else {
				if (isset($this->data->$customFieldName[$name])) {
					$value = $this->data->$customFieldName[$name];
				} else {
					$value = null;
				}
			}

			if ($type == null || $type == 'customfield') {

			} elseif ($type == 'text' && $userHTMLFilter === false) {

			} elseif ($identity == '1') {

			} else {

				try {
					/** Filters, sets defaults, and validates */
					$value = Services::Filter()->filter($value, $type, $null, $default);

					if ($customFieldName == '') {
						$this->data->$name = trim($value);

					} else {

						$fieldArray[$name] = trim($value);
					}

				} catch (Exception $e) {

					echo 'DeleteController::checkFields Filter Failed' . ' ' . $e->message;
					die;
				}
			}
		}


		if ($customFieldName == '') {
		} else {
			ksort($fieldArray);
			$this->data->$customFieldName = $fieldArray;
		}

		Services::Debug()->set('DeleteController::checkFields Filter::Success: ' . $valid);

		return $valid;
	}

	/**
	 * checkForeignKeys - validates the existence of all foreign keys
	 *
	 * @return object
	 * @since   1.0
	 */
	protected function checkForeignKeys()
	{

		$foreignkeys = Services::Registry()->get($this->table_registry_name, 'foreignkeys');

		if (count($foreignkeys) == 0 || $foreignkeys === null) {
			return false;
		}

		$valid = true;

		foreach ($foreignkeys as $fk) {

			/** Retrieve Model Foreign Key Definitions */
			if (isset($fk['name'])) {
				$name = $fk['name'];
			} else {
				return false;
				//error
			}
			if (isset($fk['source_id'])) {
				$source_id = $fk['source_id'];
			} else {
				return false;
				//error
			}

			if (isset($fk['source_model'])) {
				$source_model = ucfirst(strtolower($fk['source_model']));
			} else {
				return false;
				//error
			}

			if (isset($fk['required'])) {
				$required = $fk['required'];
			} else {
				return false;
				//error
			}

			/** Retrieve Model Foreign Key Definitions */
			if (isset($this->data->$name)) {
			} else {
				if ((int)$required == 0) {
					return true;
				}
				// error
				return false;
			}

			if (isset($this->data->$name)) {

				$controllerClass = 'Molajo\\Controller\\ModelController';
				$m = new $controllerClass();
				$results = $m->connect('Table', $source_model);
				if ($results == false) {
					return false;
				}

				$m->model->query->select('COUNT(*)');
				$m->model->query->from($m->model->db->qn($m->get('table_name')));
				$m->model->query->where($m->model->db->qn($source_id)
					. ' = ' . (int)$this->data->$name);

				$m->set('get_customfields', 0);
				$m->set('get_item_children', 0);
				$m->set('use_special_joins', 0);
				$m->set('check_view_level_access', 0);
				$m->set('process_triggers', 0);

				$value = $m->getData('result');

				if (empty($value)) {
					//error
					return false;
				}

			} else {
				if ($required == 0) {
				} else {
					return false;
				}
			}
		}
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

		$arguments = Services::Event()->schedule('onBeforeDelete', $arguments, $this->triggers);
		if ($arguments == false) {
			return false;
		}

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
	protected function onAfterDeleteEvent($data)
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
			'data' => $data,
			'parameters' => $this->parameters,
			'model_name' => $this->get('model_name')
		);

		$arguments = Services::Event()->schedule('onAfterDelete', $arguments, $this->triggers);
		if ($arguments == false) {
			return false;
		}

		/** Process results */
		$this->parameters = $arguments['parameters'];
		$data = $arguments['data'];

		return $data;
	}
}
