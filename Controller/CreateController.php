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
 * Create
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class CreateController extends ModelController
{
	/**
	 * create new row
	 *
	 * @return bool|object
	 * @since  1.0
	 */
	public function create()
	{
		/** tokens */

		if (isset($this->data->model_name)) {
		} else {
			return false;
		}

		$this->connect('Table', $this->data->model_name, 'CreateModel');
		if (isset($this->data->catalog_type_id) && (int)$this->data->catalog_type_id > 0) {
		} else {
			$this->data->catalog_type_id = Services::Registry()->get($this->table_registry_name, 'catalog_type_id');
		}

		$results = $this->checkPermissions();
		if ($results === false) {
			//error
			//return false (not yet)
		}

		$this->getTriggerList('create');

		$valid = $this->onBeforeCreateEvent();
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

			$valid = $this->model->create($data, $this->table_registry_name);
		}

		/** Set Model Values */

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

		return $valid;
	}

	/**
	 * checkPermissions for Create
	 *
	 * @return bool
	 * @since  1.0
	 */
	protected function checkPermissions()
	{

		if (isset($this->data->primary_category_id)) {
			$results = Services::Authorisation()->authoriseTask('Create', $this->data->primary_catagory_id);
			if ($results === true) {
				return true;
			}
		}

		$results = Services::Authorisation()->authoriseTask('Create', $this->data->catalog_type_id);
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

		Services::Debug()->set('CreateController::checkFields Filter::Success: ' . $valid);

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

			} elseif ($type == 'identity') {

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

					echo 'CreateController::checkFields Filter Failed' . ' ' . $e->message;
					die;
				}
			}
		}


		if ($customFieldName == '') {
		} else {
			ksort($fieldArray);
			$this->data->$customFieldName = $fieldArray;
		}

		Services::Debug()->set('CreateController::checkFields Filter::Success: ' . $valid);

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

				$m->set('use_special_joins', 0);
				$m->set($source_id, (int)$this->data->$name);

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
	 * Schedule onBeforeCreateEvent Event - could update model and data objects
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function onBeforeCreateEvent()
	{
		if (count($this->triggers) == 0
			|| (int)$this->get('process_triggers') == 0
		) {
			return true;
		}

		$arguments = array(
			'table_registry_name' => $this->table_registry_name,
			'db' => $this->model->db,
			'query_results' => $this->data,
			'null_date' => $this->model->null_date,
			'now' => $this->model->now,
			'parameters' => $this->parameters,
			'model_name' => $this->get('model_name')
		);

		$arguments = Services::Event()->schedule('onBeforeCreate', $arguments, $this->triggers);
		if ($arguments == false) {
			return false;
		}

		/** Process results */
		$this->parameters = $arguments['parameters'];
		$this->data = $arguments['query_results'];

		return true;
	}

	/**
	 * Schedule onAfterCreateEvent Event
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function onAfterCreateEvent()
	{
		if (count($this->triggers) == 0
			|| (int)$this->get('process_triggers') == 0
		) {
			return true;
		}
		var_dump($this->triggers);
		/** Schedule onAfterCreate Event */
		$arguments = array(
			'table_registry_name' => $this->table_registry_name,
			'db' => $this->model->db,
			'query_results' => $this->data,
			'parameters' => $this->parameters,
			'model_name' => $this->get('model_name')
		);

		$arguments = Services::Event()->schedule('onAfterCreate', $arguments, $this->triggers);
		if ($arguments == false) {
			echo 'yes';
			return false;
		}
		die;
		/** Process results */
		$this->parameters = $arguments['parameters'];
		$this->data = $arguments['query_results'];

		return true;
	}
}
