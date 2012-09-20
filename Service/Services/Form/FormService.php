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
	 * Build two sets of data:
	 *
	 *  1. Fieldsets: collection of the names of fields to be created (as include statements) in the fieldset
	 *  2. Fields: Field-specific registries which define attributes input to the form field creation view
	 *
	 * @return array
	 */
	public function getFieldset($namespace, $tabLink, $input_fields)
	{
		$fieldset = array();
		$previous_fieldset_label = '';

		foreach ($input_fields as $field) {

			$row = new \stdClass();

			$row->id = $field['name'];
			$row->name = $field['name'];
			$row->label = Services::Language()->translate(strtoupper($field['name'] . '_LABEL'));;

			if (isset($field['tab_title'])) {
				$row->tab_title = $field['tab_title'];
			} else {
				$row->tab_title = '';
			}

			$row->tab_description = $field['tab_description'];

			if (isset($field['tab_fieldset_title'])) {
				$row->fieldset_label = $field['tab_fieldset_title'];
			} else {
				$row->fieldset_label = $row->tab_title;
			}

			if ($previous_fieldset_label == $row->fieldset_label) {
				$row->new_fieldset = 0;
				$row->first_row = 0;
			} else {
				if ($previous_fieldset_label == '') {
					$row->first_row = 1;
				} else {
					$row->first_row = 0;
				}
				$previous_fieldset_label = $row->fieldset_label;
				$row->new_fieldset = 1;
			}
			$row->tab_fieldset_description = $field['tab_fieldset_description'];

			$row->tooltip = Services::Language()->translate(strtoupper($field['name'] . '_TOOLTIP'));
			$row->placeholder = Services::Language()->translate(strtoupper($field['name'] . '_PLACEHOLDER'));

			if (isset($field['locked']) && $field['locked'] == 1) {
				$row->disabled = true;
			} else {
				$row->disabled = false;
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

			if ($row->type == 'text') {
				$row->type = 'textarea';
			}

			if ($row->type == 'char') {
				$row->type = 'text';
			}

			if ($row->type == 'integer') {
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
					$row->view = 'forminput';
					break;
			}

			if (isset($field['datalist'])) {
				$row->view = 'formselect';
				$row->datalist = $field['datalist'];
			} else {
				$row->datalist = '';
			}

			$field['type'] = $row->type;

			switch ($row->view) {
				case 'formradio':
					$row->name = $this->setRadioField($namespace, $tabLink, $field, $row);
					break;

				case 'formselect':
					$row->name = $this->getSelectField($namespace, $tabLink, $field, $row);
					break;

				case 'formtextarea':
					$row->name = $this->getTextareaField($namespace, $tabLink, $field, $row);
					break;

				default:
					$row->name = $this->setInputField($namespace, $tabLink, $field, $row);
					break;
			}

			$fieldset[] = $row;
		}

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

		if (isset($field['null']) && $field['null'] == 1) {
			$iterate['required'] = 'required';
		}

		$iterate['type'] = $field['type'];

		if (isset($field['value'])) {
		} else {
			$field['value'] = NULL;
		}

		$iterate['value'] = $field['value'];

		foreach ($iterate as $key => $value) {
			$row = new \stdClass();
			foreach ($row_start as $rkey=>$rvalue) {
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

		if (isset($field['null']) && $field['null'] == 1) {
			$required = 'required';
		} else {
			$required = '';
		}

		/** Yes */
		$row = new \stdClass();
		foreach ($row_start as $rkey=>$rvalue) {
			$row->$rkey = $rvalue;
		}

		if (isset($field['value'])) {
		} else {
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

		$fieldRecordset[] = $row;

		/** No */
		$row = new \stdClass();
		foreach ($row_start as $rkey=>$rvalue) {
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

		$fieldRecordset[] = $row;

		$registryName = $namespace . strtolower($tabLink) . $row->name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}

	/**
	 * getSelectField field
	 *
	 * @return array
	 */
	protected function getSelectField($namespace, $tabLink, $field, $row_start)
	{
		$fieldRecordset = array();

		$required = '';
		if (isset($field['null']) && $field['null'] == 1) {
			$required = 'required';
		}

		$multiple = '';
		if (isset($field['multiple'])) {
			if ($field['multiple'] == 1) {
				$multiple = ' multiple';
			}
		}

		$size = '';
		if (isset($field['size'])) {
			if ((int) $field['size'] > 1) {
				$size = ' size="' . $field['size']. '"';
			}
		}

		if (isset($field['value'])) {
		} else {
			$field['value'] = NULL;
		}
		$selected = $field['value'];
		$selectedArray = explode(',', $selected);

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
			foreach ($row_start as $rkey=>$rvalue) {
				$row->$rkey = $rvalue;
			}

			$row->datalist = $datalist;
			$row->required = $required;
			$row->multiple = $multiple;
			$row->size = $size;

			$row->id = $item->id;
			$row->value = $item->value;

			if (in_array($row->id, $selectedArray)) {
				$row->selected = ' selected';
				$selectionFound = true;
			} else {
				$row->selected = '';
			}

			$fieldRecordset[] = $row;
		}

		/** Default */
		if ($selectionFound == false) {
			$row = new \stdClass();
			foreach ($row_start as $rkey=>$rvalue) {
				$row->$rkey = $rvalue;
			}

			$row->datalist = $datalist;
			$row->required = $required;
			$row->multiple = $multiple;
			$row->size = $size;

			$row->id = $row->default;
			$row->value = $row->default_message;

			$row->selected = ' selected';
		}

		/** Field Dataset */
		$registryName = $namespace . strtolower($tabLink) . $row->name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}

	/**
	 * getTextareaField field
	 *
	 * @return array
	 */
	public function getTextareaField($namespace, $tabLink, $field, $row_start)
	{
		$fieldRecordset = array();

		$iterate = array();
		$iterate['id'] = $field['name'];
		$iterate['name'] = $field['name'];

		if (isset($field['null']) && $field['null'] == 1) {
			$iterate['required'] = 'required';
		}

		if (isset($field['value'])) {
		} else {
			$field['value'] = NULL;
		}

		$selected = $field['value'];

		foreach ($iterate as $key => $value) {
			$row = new \stdClass();
			foreach ($row_start as $rkey=>$rvalue) {
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
