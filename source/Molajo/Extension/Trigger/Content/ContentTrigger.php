<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
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
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Table Registry Name - can be used to retrieve table parameters
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $table_registry_name;

	/**
	 * Parameters set by the Includer and used in the MVC to generate include output
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $parameters;

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
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new ContentTrigger();
		}
		return self::$instance;
	}

	/**
	 * get the class property following the trigger method execution
	 *
	 * @return mixed
	 * @since  1.0
	 */
	public function get($key, $value)
	{
		return $this->$key;
	}

	/**
	 * set the class property before the trigger method executes
	 *
	 * @return mixed
	 * @since  1.0
	 */
	public function set($key, $value)
	{
		$this->$key = $value;

		return $this;
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

		/** Common processing */
		if (is_array($fields) && count($fields) > 0) {
			$this->processFieldType($type = '', $fields);
		}

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
				$row->name = $fields[$key]['name'];
			} else {
				$row->name = 'Unknown';
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

			/** Customfield */
			$row->customfield = $type;

			$this->fields[] = $row;
		}

		return;
	}

	/**
	 * processFieldType processes an array of fields, populating the class property
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
	 * getFieldValue retrieves the actual field value from the 'normal' or special field
	 *
	 * @return mixed
	 * @since  1.0
	 */
	public function getFieldValue($field)
	{
		$name = $field->name;

		if (isset($this->query_results->$name)) {
			return $this->query_results->$name;

		} else if ($field->customfield == '') {
			return true;

		} else if (Services::Registry()->get($this->model_name . $field->customfield, $name, '') > '') {
			return Services::Registry()->get($this->model_name . $field->customfield, $name, '');
		}

		return true;
	}

	/**
	 * addField adds a field to the 'normal' or special field group
	 *
	 * @param $field
	 * @param $new_field_name
	 * @param $value
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function addField($field, $new_field_name, $value)
	{
		$name = $field->name;

		if (isset($this->query_results->$name)) {
			$this->query_results->$new_field_name = $value;
			return true;

		} else if ($field->customfield == '') {
			return true;

		} else if (Services::Registry()->get($this->model_name . $field->customfield, $name, '') > '') {
			Services::Registry()->set($this->model_name . $field->customfield, $new_field_name, $value);
			return true;
		}

		return false;
	}

	/**
	 * Pre-create processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
		return true;
	}

	/**
	 * Post-create processing
	 *
	 * @param $this->query_results, $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterCreate()
	{
		return true;
	}

	/**
	 * Pre-read processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeRead()
	{
		return true;
	}

	/**
	 * Post-read processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		return true;
	}

	/**
	 * Pre-update processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return  boolean
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
	 * @return  boolean
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
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeDelete()
	{
		return true;
	}

	/**
	 * Post-read processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterDelete()
	{
		return true;
	}
}