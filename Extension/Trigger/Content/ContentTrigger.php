<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
namespace Molajo\Extension\Trigger\Content;

use Molajo\Extension\Trigger\Trigger\Trigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Item Author
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ContentTrigger extends Trigger
{
	/**
	 * Table Registry Name - can be used to retrieve table parameters
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $table_registry_name;

	/**
	 * Model name
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $model_name;

	/**
	 * Parameters set by the Includer and used in the MVC to generate include output
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $parameters = array();

	/**
	 * Query Object from Model
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $query;

	/**
	 * db object (for escaping fields)
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $db;

	/**
	 * Query Results
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $query_results;

	/**
	 * null_date
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $null_date;

	/**
	 * now
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $now;

	/**
	 * Fields - name and type
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $fields;

	/**
	 * Custom Field Groups for this Data
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $customfieldgroups;

	/**
	 * Used in post-render View triggers, contains output rendered from view
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $rendered_output;

	/**
	 * Get the current value (or default) of the specified Model property
	 *
	 * @param string $key     Property
	 * @param mixed  $default Value
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function get($key, $default = null)
	{
		$value = null;

		if (in_array($key, array('table_registry_name', 'query', 'db', 'parameters',
			'query_results', 'null_date', 'now', 'fields', 'customfieldgroups',
			'data', 'model_name', 'rendered_output'))
		) {
			$value = $this->$key;

		} else {
			if (isset($this->parameters[$key])) {
				$value = $this->parameters[$key];
			}
		}

		if ($value === null) {
			return $default;
		}

		return $value;
	}

	/**
	 * Set the value of a Model property
	 *
	 * @param string $key   Property
	 * @param mixed  $value Value
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function set($key, $value = null)
	{
		if (in_array($key, array('table_registry_name', 'query', 'db', 'parameters',
			'query_results', 'null_date', 'now', 'fields', 'customfieldgroups',
			'data', 'model_name', 'rendered_output'))
		) {

			$this->$key = $value;
		} else {
			$this->parameters[$key] = $value;
		}

		return;
	}

	/**
	 * Unload fields for trigger use
	 *
	 * Note: List of field attributes also defined in ConfigurationService
	 *
	 * @return mixed
	 * @since  1.0
	 */
	public function setFields()
	{
		/** initialise class property */
		$this->fields = array();

		/** process normal fields */
		$fields = Services::Registry()->get($this->table_registry_name, 'fields');
		if (is_array($fields) && count($fields) > 0) {
			$this->processFieldType($type = '', $fields);
		}

		/** "Custom" field groups and fields */
		$this->customfieldgroups = Services::Registry()->get($this->table_registry_name, 'customfieldgroups', array());

		if (is_array($this->customfieldgroups) && count($this->customfieldgroups) > 0) {

			foreach ($this->customfieldgroups as $customFieldName) {

				/** For this Custom Field Group (ex. Parameters, metadata, etc.) */
				$customFieldName = strtolower($customFieldName);

				/** Retrieve Field Definitions from Registry (XML) */
				$fields = Services::Registry()->get($this->table_registry_name, $customFieldName);

				/** Shared processing  */
				$this->processFieldType($customFieldName, $fields);
			}
		}

		/** join fields */
		$joinfields = Services::Registry()->get($this->table_registry_name, 'JoinFields');
		if (is_array($joinfields) && count($joinfields) > 0) {
			$this->processFieldType('JoinFields', $joinfields);
		}

		/** foreign keys */
		$foreignkeys = Services::Registry()->get($this->table_registry_name, 'foreignkeys');
		if (is_array($foreignkeys) && count($foreignkeys) > 0) {
			$this->processFieldType('foreignkeys', $foreignkeys);
		}

		return $this;
	}

	/**
	 * processFieldType processes an array of fields, populating the class property
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function processFieldType($type, $fields)
	{
		foreach ($fields as $key => $value) {

			$row = new \stdClass();

			/** Name */
			if (isset($fields[$key]['name'])) {
				if ($type == 'foreignkeys') {
					$row->name = 'fk_' . $fields[$key]['name'];
				} else {
					$row->name = $fields[$key]['name'];
				}
			} else {
				$row->name = 'Unknown';
			}

			/** As Name */
			if (isset($fields[$key]['as_name'])) {
				$row->as_name = $fields[$key]['as_name'];
			} else {
				$row->as_name = '';
			}

			/** Alias */
			if (isset($fields[$key]['alias'])) {
				$row->alias = $fields[$key]['alias'];
			} else {
				$row->alias = '';
			}

			/** Datatype */
			if (isset($fields[$key]['type'])) {
				$row->type = $fields[$key]['type'];
			} else {
				$row->type = 'char';
			}

			/** Default */
			if (isset($fields[$key]['default'])) {
				$row->default = $fields[$key]['default'];
			} else {
				$row->default = '';
			}

			/** File */
			if (isset($fields[$key]['file'])) {
				$row->file = $fields[$key]['file'];
			} else {
				$row->file = 'file';
			}

			/** Identity */
			if (isset($fields[$key]['identity'])) {
				$row->identity = $fields[$key]['identity'];
			} else {
				$row->identity = 'identity';
			}

			/** Length */
			if (isset($fields[$key]['length'])) {
				$row->length = $fields[$key]['length'];
			} else {
				$row->length = '';
			}

			/** Minimum */
			if (isset($fields[$key]['minimum'])) {
				$row->minimum = $fields[$key]['minimum'];
			} else {
				$row->minimum = '';
			}

			/** Maximum */
			if (isset($fields[$key]['maximum'])) {
				$row->maximum = $fields[$key]['maximum'];
			} else {
				$row->maximum = '';
			}

			/** Null */
			if (isset($fields[$key]['null'])) {
				$row->null = $fields[$key]['null'];
			} else {
				$row->null = false;
			}

			/** Required */
			if (isset($fields[$key]['required'])) {
				$row->required = $fields[$key]['required'];
			} else {
				$row->required = '0';
			}

			/** Shape */
			if (isset($fields[$key]['shape'])) {
				$row->shape = $fields[$key]['shape'];
			} else {
				$row->shape = '0';
			}

			/** Size */
			if (isset($fields[$key]['size'])) {
				$row->size = $fields[$key]['size'];
			} else {
				$row->size = '0';
			}

			/** Table */
			if (isset($fields[$key]['table'])) {
				$row->table = $fields[$key]['table'];
			} else {
				$row->table = '';
			}

			/** Unique */
			if (isset($fields[$key]['unique'])) {
				$row->unique = $fields[$key]['unique'];
			} else {
				$row->unique = '0';
			}

			/** Values */
			if (isset($fields[$key]['values'])) {
				$row->values = $fields[$key]['values'];
			} else {
				$row->values = '';
			}

			if ($type == '') {
				//regular field
				$row->customfield = '';
				$row->foreignkey = 0;

			} elseif ($type == 'foreignkeys') {
				$row->customfield = '';
				$row->foreignkey = 1;
				$row->type = $type;

			} else {
				/** Customfield */
				$row->customfield = $type;
				$row->foreignkey = 0;
			}

			/** Source ID */
			if (isset($fields[$key]['source_id'])) {
				$row->source_id = $fields[$key]['source_id'];
			} else {
				$row->source_id = '0';
			}

			/** Source Model */
			if (isset($fields[$key]['source_model'])) {
				$row->source_model = $fields[$key]['source_model'];
			} else {
				$row->source_model = '';
			}

			$this->fields[] = $row;
		}

		return;
	}

	/**
	 * retrieveFieldsByType processes an array of fields, populating the class property
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function retrieveFieldsByType($type)
	{
		$results = array();

		foreach ($this->fields as $field) {

			if ($field->type == $type) {
				$results[] = $field;
			}
		}

		return $results;
	}

	/**
	 * getField by name
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function getField($name)
	{
		foreach ($this->fields as $field) {

			if ((int)$field->foreignkey = 0) {

			} else {
				if ($field->as_name == '') {
					if ($field->name == $name) {
						return $field;
					}
				} else {
					if ($field->as_name == $name) {
						return $field;
					}
				}
			}
		}

		return false;
	}

	/**
	 * getFieldValue retrieves the actual field value from the 'normal' or special field
	 *
	 * @return mixed
	 * @since  1.0
	 */
	public function getFieldValue($field)
	{
		if (isset($field->as_name)) {
			$name = $field->as_name;

		} else {
			$name = $field->name;
		}

		if (isset($this->data->$name)) {
			return $this->data->$name;

		} elseif ($field->customfield == '') {
			return false;

		} elseif (Services::Registry()->exists($this->get('model_name') . $field->customfield, $name)) {
			return Services::Registry()->get($this->get('model_name') . $field->customfield, $name);

		}

		return false;
	}

	/**
	 * saveField adds a field to the 'normal' or special field group
	 *
	 * @param $field
	 * @param $new_field_name
	 * @param $value
	 *
	 * @return void
	 * @since  1.0
	 */
	public function saveField($field, $new_field_name, $value)
	{
		$name = $field->name;

		/** Update existing field */
		if (isset($this->data->$name)) {
			$this->data->$new_field_name = $value;

			/** Since there is no customfield, this must be a new query field */
		} elseif ($field->customfield == '') {
			$this->data->$new_field_name = $value;

		} elseif (Services::Registry()->exists($this->get('model_name') . $field->customfield, $name)) {
			Services::Registry()->set($this->get('model_name') . $field->customfield, $new_field_name);
		}

		return;
	}

	/**
	 * saveForeignKeyValue
	 *
	 * @param $field
	 * @param $new_field_name
	 * @param $value
	 *
	 * @return void
	 * @since  1.0
	 */
	public function saveForeignKeyValue($new_field_name, $value)
	{
		if (isset($this->data->$new_field_name)) {
			return;
		}

		/** Add new field */
		$this->data->$new_field_name = $value;

		return;
	}

	/**
	 * Pre-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
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
	 * Before the Query results are injected into the View
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeViewRender()
	{
		return true;
	}

	/**
	 * Pre-update processing
	 *
	 * @param   $this->data
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
	 * @param   $this->data
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
	 * @param   $this->data
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
	 * @param   $this->data
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
