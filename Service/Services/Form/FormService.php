<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Form;

use Molajo\Helpers;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Form
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class FormService
{
	/**
	 * @static
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * @static
	 * @return bool|object
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new FormService();
		}

		return self::$instance;
	}

	/**
	 * getFieldlist retrieves the extensive fieldlist for a resource
	 *
	 * @param $model_type
	 * @param $model_name
	 *
	 * @return array
	 * @since  1.0
	 */
	public function getFieldlist ($model_type, $model_name)
	{
		$table_registry_name = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

		if (Services::Registry()->exists($table_registry_name) === true)  {
		} else {
			Helpers::Content()->getResourceContentParameters($model_type, $model_name);
		}

		$primary_prefix = Services::Registry()->get($table_registry_name, 'primary_prefix');

		$fieldArray = array();

		$normalFields = Services::Registry()->get($table_registry_name, 'fields');

		if (count($normalFields) > 0) {
			foreach ($normalFields as $field) {
				$row = new \stdClass();
				$row->value = $field['name'];
				$row->id = $field['name'];

				$fieldArray[] = $row;
			}
		}

		$joins = Services::Registry()->get($table_registry_name, 'joins');

		if (count($joins) > 0) {
			foreach ($joins as $field) {
				$temp = explode(',', $field['select']);
				if (count($temp) > 0) {
					foreach ($temp as $f) {
						if (trim($f) == '') {
						} else {
							$row = new \stdClass();
							$row->value = $f . ' (' . $field['alias'] . ')';
							$row->id = $field['alias'] . '.' . $f;

							$fieldArray[] = $row;
						}
					}
				}
			}
		}

		$customfields =  Services::Registry()->get($table_registry_name, 'Customfields');
		if (count($customfields) > 0) {
			foreach ($customfields as $field) {
				$row = new \stdClass();
				$row->value = $field['name'] . ' (customfield)';
				$row->id = 'customfields' . '.' . $field['name'];

				$fieldArray[] = $row;
			}
		}

		$metadata =  Services::Registry()->get($table_registry_name, 'Metadata');
		if (count($metadata) > 0) {
			foreach ($metadata as $field) {
				$row = new \stdClass();
				$row->value = $field['name'] . ' (metadata)';
				$row->id = 'metadata' . '.' . $field['name'];

				$fieldArray[] = $row;
			}
		}

		sort($fieldArray);

		return $fieldArray;
	}

	/**
	 * setFieldset - builds two sets of data:
	 *
	 *  1. Fieldsets: collection of the names of fields to be used to create field-specific include statements
	 *  2. Fields: Field-specific registries which define attributes input to the template field creation view
	 *
	 * @param $namespace
	 * @param $tabLink
	 * @param $input_fields
	 *
	 * @return array
	 * @since  1.0
	 */
	public function setFieldset($namespace, $tabLink, $input_fields)
	{
		$fieldset = array();
		$previous_tab_fieldset_title = '';

		foreach ($input_fields as $field) {

			$row = new \stdClass();

			$row->tab_title = $field['tab_title'];
			$row->tab_description = $field['tab_description'];
			$row->tab_fieldset_title = $field['tab_fieldset_title'];
			$row->tab_fieldset_description = $field['tab_fieldset_description'];

			if ($previous_tab_fieldset_title == $row->tab_fieldset_title) {
				$row->new_fieldset = 0;
				$row->first_row = 0;
			} else {
				if ($previous_tab_fieldset_title == '') {
					$row->first_row = 1;
				} else {
					$row->first_row = 0;
				}
				$previous_tab_fieldset_title = $row->tab_fieldset_title;
				$row->new_fieldset = 1;
			}

			$row->field_id = $field['name'];
			$row->id = $field['name'];
			$row->name = $field['name'];
			$row->label = Services::Language()->translate(strtoupper($field['name'] . '_LABEL'));
			;
			$row->tooltip = Services::Language()->translate(strtoupper($field['name'] . '_TOOLTIP'));
			$row->placeholder = Services::Language()->translate(strtoupper($field['name'] . '_PLACEHOLDER'));

			if (isset($field['locked']) && $field['locked'] == 1) {
				$row->disabled = 1;
			} else {
				$row->disabled = 0;
			}

			if (isset($field['application_default'])) {
			} else {
				$field['application_default'] = NULL;
			}

			if (($field['application_default'] === NULL || $field['application_default'] == ' ')
				&& ($field['default'] === NULL || $field['default'] == ' ')
			) {
				$row->default_message = Services::Language()->translate('No default value defined.');
				$row->default = NULL;

			} elseif ($field['application_default'] === NULL || $field['application_default'] == ' ') {

				$row->default_message = Services::Language()->translate('Field-level default: ')
					. $field['default'];
				$row->default = $field['default'];

			} else {
				$row->default_message = Services::Language()->translate('Application configured default: ')
					. $field['application_default'];
				$row->default = $field['application_default'];
			}

			$row->type = $field['type'];

			/** todo: better mapping approach (fields.xml?) for database types to HTML5/form field types */
			if ($row->type == 'text') {
				$row->type = 'textarea';
			}

			if ($row->type == 'char') {
				$row->type = 'text';
			}

			if ($row->type == 'integer') {
				$row->type = 'number';
			}

			if ($row->type == 'catalog_id') {
				$row->type = 'number';
			}

			if ($row->type == 'ip_address') {
				$row->type = 'text';
			}

			if ($row->type == 'userid') {
				$row->type = 'number';
			}

			if (isset($field['hidden']) && $field['hidden'] == 1) {
				$row->type = 'hidden';
				$row->hidden = 1;
			} else {
				$row->hidden = 0;
			}

			switch ($row->type) {
				case 'boolean':
					$row->view = 'formradio';
					break;

				case 'audio':
				case 'color':
				case 'date':
				case 'datetime':
				case 'email':
				case 'file':
				case 'hidden':
				case 'image':
				case 'month':
				case 'number':
				case 'password':
				case 'range':
				case 'search':
				case 'tel':
				case 'text':
				case 'time':
				case 'url':
					$row->view = 'forminput';
					break;

				case 'textarea':
					$row->view = 'formtextarea';
					break;

				default:
					echo 'WHAT IS THIS TYPE? ' . $row->type . '<br />';
					$row->view = 'forminput';
					break;
			}

			if (isset($field['datalist'])) {
				$row->view = 'formselect';
				$row->datalist = $field['datalist'];
			} else {
				$row->datalist = '';
			}

			/** Changes to field needed in following methods */
			$field['type'] = $row->type;

			if (isset($field['value'])) {
			} else {
				$field['value'] = NULL;
			}

			if (isset($field['null'])) {
			} else {
				$field['null'] = 0;
			}

			$field['disabled'] = $row->disabled;
			$field['default'] = $row->default;
			$field['hidden'] = $row->hidden;

			/** Branch to form-field type logic where Registry will be created for this formfield */
			switch ($row->view) {
				case 'formradio':
					$row->name = $this->setRadioField($namespace, $tabLink, $field, $row);
					break;

				case 'formselect':
					$row->name = $this->setSelectField($namespace, $tabLink, $field, $row);
					break;

				case 'formtextarea':
					$row->name = $this->setTextareaField($namespace, $tabLink, $field, $row);
					break;

				default:
					$row->name = $this->setInputField($namespace, $tabLink, $field, $row);
					break;
			}

			$fieldset[] = $row;
		}

		/** Fieldset returned to be used to create template includes for field registries created below */
		return $fieldset;
	}

	/**
	 * setInputField field
	 *
	 * @param $namespace
	 * @param $tabLink
	 * @param $field
	 * @param $row_start
	 *
	 * @return string
	 * @since  1.0
	 */
	protected function setInputField($namespace, $tabLink, $field, $row_start)
	{
		$fieldRecordset = array();

		$iterate = array();

		$iterate['id'] = $field['name'];
		$iterate['name'] = $field['name'];
		$iterate['type'] = $field['type'];

		if ($field['null'] == 1) {
			$iterate['required'] = ' required';
		}

		if ($field['disabled'] == 1) {
			$iterate['disabled'] = ' disabled';
		}

		$iterate['value'] = $field['value'];

		foreach ($iterate as $key => $value) {
			$row = new \stdClass();

			foreach ($row_start as $rkey => $rvalue) {
				$row->$rkey = $rvalue;
			}

			$row->key = $key;
			$row->value = $value;

			$fieldRecordset[] = $row;
		}

		$registryName = $namespace . strtolower($tabLink) . $row->name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}

	/**
	 * setRadioField field
	 *
	 * @param $namespace
	 * @param $tabLink
	 * @param $field
	 * @param $row_start
	 *
	 * @return string
	 * @since  1.0
	 */
	protected function setRadioField($namespace, $tabLink, $field, $row_start)
	{
		$fieldRecordset = array();

		if ($field['null'] == 1) {
			//not all browsers ready for required on it's own
			$required = ' required';
		} else {
			$required = '';
		}

		/** Yes */
		$row = new \stdClass();

		foreach ($row_start as $rkey => $rvalue) {
			$row->$rkey = $rvalue;
		}

		if ($field['value'] == NULL) {
			$field['value'] = $row->default;
		}

		$row->required = $required;
		$row->id = 1;
		$row->id_label = Services::Language()->translate('Yes');
		if ((int)$field['value'] === 1) {
			$row->checked = ' checked';
		} else {
			$row->checked = '';
		}

		if ($field['disabled'] == 1) {
			$row->disabled = ' disabled';
		} else {
			$row->disabled = '';
		}


		$fieldRecordset[] = $row;

		/** No */
		$row = new \stdClass();

		foreach ($row_start as $rkey => $rvalue) {
			$row->$rkey = $rvalue;
		}

		$row->required = $required;
		$row->id = 0;
		$row->id_label = Services::Language()->translate('No');
		if ((int)$field['value'] === 0) {
			$row->checked = ' checked';
		} else {
			$row->checked = '';
		}

		if ($field['disabled'] == 1) {
			$row->disabled = ' disabled';
		} else {
			$row->disabled = '';
		}

		$fieldRecordset[] = $row;

		$registryName = $namespace . strtolower($tabLink) . $row->name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}

	/**
	 * setSelectField field
	 *
	 * @param $namespace
	 * @param $tabLink
	 * @param $field
	 * @param $row_start
	 *
	 * @return mixed|string
	 * @somce  1.0
	 */
	protected function setSelectField($namespace, $tabLink, $field, $row_start)
	{
		$fieldRecordset = array();

		$required = '';
		if ($field['null'] == 1) {
			//not all browsers ready for required on it's own
			$required = ' required';
		}

		$disabled = '';
		if ($field['disabled'] == 1) {
			//not all browsers ready for required on it's own
			$disabled = ' disabled';
		}

		$default = $field['default'];

		$multiple = '';
		if (isset($field['multiple'])) {
			if ($field['multiple'] == 1) {
				//not all browsers can handle this
				$multiple = ' multiple';
			}
		}

		$size = '';
		if (isset($field['size'])) {
			if ((int)$field['size'] > 1) {
				$size = ' size="' . $field['size'] . '"';
			}
		}

		$temp = $field['value'];
		$default_setting = 0;
		if ($temp == NULL) {
			$temp = $field['default'];
			$default_setting = 1;
		}
		$selectedArray = explode(',', $temp);

		$datalist = $field['datalist'];
		$list = Services::Text()->getList($datalist, array());

		if ($list == false) {
			$items = array();
		} else {
			$items = Services::Text()->buildSelectlist($datalist, $list, 0, 5);
		}

		$selectionFound = false;
		foreach ($items as $item) {
			$row = new \stdClass();

			foreach ($row_start as $rkey => $rvalue) {
				$row->$rkey = $rvalue;
			}

			$row->datalist = $datalist;
			$row->required = $required;
			$row->disabled = $disabled;
			$row->multiple = $multiple;
			$row->size = $size;

			$row->id = $item->id;
			$row->value = $item->value;

			if (in_array($row->id, $selectedArray)) {
				$row->selected = ' selected';
				if ($default_setting == 0) {
				} else {
					$item->value .= ' (' . Services::Language()->translate('Default') . ')';
				}
			} else {
				$row->selected = '';
			}

			$fieldRecordset[] = $row;
		}

		/** Field Dataset */
		$registryName = $namespace . strtolower($tabLink) . $row->name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}

	/**
	 * setTextareaField field
	 *
	 * @return array
	 */
	protected function setTextareaField($namespace, $tabLink, $field, $row_start)
	{
		$fieldRecordset = array();

		$iterate = array();
		$iterate['id'] = $field['name'];
		$iterate['name'] = $field['name'];

		if ($field['null'] == 1) {
			$iterate['required'] = ' required';
		}

		if ($field['disabled'] == 1) {
			$iterate['disabled'] = ' disabled';
		}

		$selected = $field['value'];

		foreach ($iterate as $key => $value) {
			$row = new \stdClass();

			foreach ($row_start as $rkey => $rvalue) {
				$row->$rkey = $rvalue;
			}

			$row->selected = $selected;
			$row->key = $key;
			$row->value = $value;

			$fieldRecordset[] = $row;
		}

		/** Field Dataset */
		$registryName = $namespace . strtolower($tabLink) . $row->name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}
}
