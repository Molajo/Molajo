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

			$view = 'input';
			$datalist = '';

			if (isset($field['locked']) && $field['locked'] == 1) {
			} else {

				switch ($field['type']) {
					case 'boolean':
						$view = 'radio';
						break;

					case 'audio':
					case 'image':
					case 'color':
					case 'date':
					case 'datetime':
					case 'email':
					case 'file':
					case 'month':
					case 'password':
					case 'range':
					case 'search':
					case 'tel':
					case 'time':
					case 'url':
					case 'integer':
						$view = 'input';
						break;

					case 'text':
						$view = 'textarea';
						break;

					default:
						$view = 'input';
						break;
				}

				if (isset($field['hidden']) && $field['hidden'] == 1) {
					$view = 'input';
				}

				if (isset($field['datalist'])) {
					$view = 'select';
					$datalist = $field['datalist'];
				}

				if (isset($field['type'])) {
					if ($field['type'] == 'char') {
						$field['type'] = 'text';
					}
					if ($field['type'] == 'integer') {
						$field['type'] = 'number';
					}
				}

				if (isset($field['tab_title'])) {
					$tab_title = $field['tab_title'];
				} else {
					$tab_title = '';
				}

				if (isset($field['tab_fieldset_title'])) {
					$fieldset_label = $field['tab_fieldset_title'];
				} else {
					$fieldset_label = '';
				}

				switch ($view) {
					case 'radio':
						$registryName = $this->getRadioField($namespace, $tabLink, $field);
						break;

					case 'select':
						$registryName = $this->getSelectField($namespace, $tabLink, $field);
						break;

					case 'textarea':
						$registryName = $this->getTextareaField($namespace, $tabLink, $field);
						break;

					default:
						$registryName = $this->getInputField($namespace, $tabLink, $field);
						break;
				}

				$row = new \stdClass();

				$row->name = $registryName;
				$row->view = $view;
				$row->datalist = $datalist;
				if ($previous_fieldset_label == $fieldset_label) {
					$row->fieldset_change = 0;
				} else {
					$previous_fieldset_label = $fieldset_label;
					$row->fieldset_change = 1;
				}
				$row->tab_title = $tab_title;
				$row->fieldset_label = $fieldset_label;


				$fieldset[] = $row;
			}
		}

		return $fieldset;
	}

	/**
	 * getSelectField field
	 *
	 * @return array
	 */
	protected function getInputField($namespace, $tabLink, $field)
	{
		$fieldRecordset = array();

		$name = $field['name'];

		$label = Services::Language()->translate(strtoupper($field['name'] . '_LABEL'));
		$tooltip = Services::Language()->translate(strtoupper($field['name'] . '_TOOLTIP'));
		$placeholder = Services::Language()->translate(strtoupper($field['name'] . '_PLACEHOLDER'));

		if (isset($field['application_default'])) {
		} else {
			$field['application_default'] = NULL;
		}

		if (($field['application_default'] === NULL || $field['application_default'] == ' ')
			&& ($field['default'] === NULL || $field['default'] == ' ')
		) {
			$default_message = Services::Language()->translate('No default value defined.');

		} elseif ($field['application_default'] === NULL || $field['application_default'] == ' ') {

			$default_message = Services::Language()->translate('Field-level default: ')
				. $field['default'];

		} else {
			$default_message = Services::Language()->translate('Application configured default setting: ')
				. $field['application_default'];
		}

		$iterate = array();
		$iterate['id'] = $field['name'];
		$iterate['name'] = $field['name'];

		if (isset($field['null']) && $field['null'] == 1) {
			$iterate['required'] = 'required';
		}

		if ($field['type'] == 'boolean') {
			$iterate['type'] = 'radio';
		} else {
			$iterate['type'] = $field['type'];
		}

		if (isset($field['hidden']) && $field['hidden'] == 1) {
			$iterate['type'] = 'hidden';
		}

		if (isset($field['value'])) {
		} else {
			$field['value'] = NULL;
		}

		$iterate['value'] = $field['value'];

		foreach ($iterate as $key => $value) {
			$row = new \stdClass();

			$row->view = 'forminput';
			$row->name = $name;
			$row->label = $label;
			$row->placeholder = $placeholder;
			$row->tooltip = $tooltip;
			$row->default_message = $default_message;

			$row->key = $key;
			$row->value = $value;

			$fieldRecordset[] = $row;
		}

		/** Field Dataset */
		$registryName = $namespace . strtolower($tabLink) . $name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}

	/**
	 * getRadioField field
	 *
	 * @return array
	 */
	protected function getRadioField($namespace, $tabLink, $field)
	{
		$fieldRecordset = array();

		$name = $field['name'];

		$label = Services::Language()->translate(strtoupper($field['name'] . '_LABEL'));
		$tooltip = Services::Language()->translate(strtoupper($field['name'] . '_TOOLTIP'));
		$placeholder = Services::Language()->translate(strtoupper($field['name'] . '_PLACEHOLDER'));

		if (isset($field['application_default'])) {
		} else {
			$field['application_default'] = NULL;
		}

		if (($field['application_default'] === NULL || $field['application_default'] == ' ')
			&& ($field['default'] === NULL || $field['default'] == ' ')
		) {
			$default_message = Services::Language()->translate('No default value defined.');

		} elseif ($field['application_default'] === NULL || $field['application_default'] == ' ') {

			$default_message = Services::Language()->translate('Field-level default: ')
				. $field['default'];

		} else {
			$default_message = Services::Language()->translate('Application configured default setting: ')
				. $field['application_default'];
		}

		$iterate = array();

		$name = $field['name'];

		if (isset($field['null']) && $field['null'] == 1) {
			$required = 'required';
		} else {
			$required = '';
		}

		if (isset($field['value'])) {
		} else {
			$field['value'] = NULL;
		}

		if ((int)$field['value'] === 1) {
			$value = 1;
		} else {
			$value = 0;
		}

		/** Yes */
		$row = new \stdClass();

		$row->view = 'formradio';
		$row->name = $name;
		$row->label = $label;
		$row->placeholder = $placeholder;
		$row->tooltip = $tooltip;
		$row->default_message = $default_message;
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

		$row->view = 'formradio';
		$row->name = $name;
		$row->label = $label;
		$row->placeholder = $placeholder;
		$row->tooltip = $tooltip;
		$row->default_message = $default_message;
		$row->required = $required;
		$row->id = 0;
		$row->id_label = Services::Language()->translate('No');
		if ((int)$field['value'] === 1) {
			$row->checked = '';
		} else {
			$row->checked = ' checked';
		}

		$fieldRecordset[] = $row;

		/** Field Dataset */
		$registryName = $namespace . strtolower($tabLink) . $name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}

	/**
	 * getSelectField field
	 *
	 * @return array
	 */
	protected function getSelectField($namespace, $tabLink, $field)
	{
		$fieldRecordset = array();

		$name = $field['name'];
		$id = $field['name'];

		$label = Services::Language()->translate(strtoupper($field['name'] . '_LABEL'));
		$tooltip = Services::Language()->translate(strtoupper($field['name'] . '_TOOLTIP'));
		$placeholder = Services::Language()->translate(strtoupper($field['name'] . '_PLACEHOLDER'));

		if (isset($field['application_default'])) {
		} else {
			$field['application_default'] = NULL;
		}

		if (($field['application_default'] === NULL || $field['application_default'] == ' ')
			&& ($field['default'] === NULL || $field['default'] == ' ')
		) {

			$default_message = Services::Language()->translate('No default value defined.');

		} elseif ($field['application_default'] === NULL || $field['application_default'] == ' ') {

			$default_message = Services::Language()->translate('Field-level default: ')
				. $field['default'];

		} else {
			$default_message = Services::Language()->translate('Application configured default setting: ')
				. $field['application_default'];
		}

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

		$size = 1;
		if (isset($field['size'])) {
			if ($field['size'] > 1) {
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

		foreach ($items as $item) {
			$row = new \stdClass();

			$row->view = 'formselect';

			$row->name = $name;
			$row->label = $label;
			$row->datalist = $datalist;
			$row->placeholder = $placeholder;
			$row->tooltip = $tooltip;
			$row->default_message = $default_message;
			$row->selected = $selected;
			$row->required = $required;
			$row->multiple = $multiple;
			$row->size = $size;

			$row->id = $item->id;
			$row->value = $item->value;

			if (in_array($row->id, $selectedArray)) {
				$row->selected = ' selected';
			} else {
				$row->selected = '';
			}

			$fieldRecordset[] = $row;
		}

		/** Field Dataset */
		$registryName = $namespace . strtolower($tabLink) . $name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}

	/**
	 * getTextareaField field
	 *
	 * @return array
	 */
	public function getTextareaField($namespace, $tabLink, $field)
	{
		$fieldRecordset = array();

		$name = $field['name'];

		$label = Services::Language()->translate(strtoupper($field['name'] . '_LABEL'));
		$tooltip = Services::Language()->translate(strtoupper($field['name'] . '_TOOLTIP'));
		$placeholder = Services::Language()->translate(strtoupper($field['name'] . '_PLACEHOLDER'));

		if (isset($field['application_default'])) {
		} else {
			$field['application_default'] = NULL;
		}

		if (($field['application_default'] === NULL || $field['application_default'] == ' ')
			&& ($field['default'] === NULL || $field['default'] == ' ')
		) {

			$default_message = Services::Language()->translate('No default value defined.');

		} elseif ($field['application_default'] === NULL || $field['application_default'] == ' ') {

			$default_message = Services::Language()->translate('Field-level default: ')
				. $field['default'];

		} else {
			$default_message = Services::Language()->translate('Application configured default setting: ')
				. $field['application_default'];
		}

		$iterate = array();
		$iterate['id'] = $field['name'];
		$iterate['name'] = $field['name'];

		if (isset($field['null']) && $field['null'] == 1) {
			$iterate['required'] = 'required';
		}

		if (isset($field['hidden']) && $field['hidden'] == 1) {
			$iterate['type'] = 'hidden';
		}

		if (isset($field['value'])) {
		} else {
			$field['value'] = NULL;
		}

		$selected = $field['value'];


		foreach ($iterate as $key => $value) {
			$row = new \stdClass();

			$row->view = 'formtextarea';
			$row->name = $name;
			$row->label = $label;
			$row->placeholder = $placeholder;
			$row->tooltip = $tooltip;
			$row->default_message = $default_message;
			$row->selected = $selected;
			$row->key = $key;
			$row->value = $value;

			$fieldRecordset[] = $row;
		}

		/** Field Dataset */
		$registryName = $namespace . strtolower($tabLink) . $name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}
}
