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
     * Create Configuration Tabs for Forms
     *
     * @param $model_type - ex. Resources
     * @param $model_name - ex. Articles
     * @param $namespace - ex. config, grid, edit
     * @param $tab_array - sent in from request parameters ex. {{Editor,editor}} or full list from config
     * @param $tab_prefix - NULL from grid
     * @param $view_name - ex. Adminconfiguration, Admingrid, Edit, etc.
     * @param $default_tab_view_name ex. Adminconfigurationtab
     * @param $tab_class - typically mobile
     * @param $extension_instance_id ex. 16000 for Articles
     * @param $item array of data (Application configuration and Session data)
     *
     * @return array
     * @since  1.0
     */
    public function setTabArray($model_type, $model_name, $namespace,
                                $tab_array, $tab_prefix,
                                $view_name, $default_tab_view_name, $tab_class,
                                $extension_instance_id, $item)
    {
/*
		echo $model_type. ' ' .  $model_name. ' ' .  $namespace. ' ' .
								$tab_array. ' ' .  $tab_prefix. ' ' .
								$view_name. ' ' .  $default_tab_view_name. ' ' .  $tab_class. ' ' .
								$extension_instance_id;
		echo '<pre>';
		var_dump($item);
		echo '</pre>';
*/
        $tabs = array();
        $configurationArray = array();
        $temp = explode('}}', $tab_array);

        foreach ($temp as $set) {
            $set = str_replace(',', ' ', $set);
            $set = str_replace(':', '=', $set);
            $set = str_replace('{{', '', $set);
            $set = str_replace('http=', 'http:', $set);
            if (trim($set) == '') {
            } else {
                $configurationArray[] = trim($set);
            }
        }

        foreach ($configurationArray as $config) {

            $split = explode(' ', $config);
            if (count($split) > 1) {
            } else {
                return false;
            }

            $tabTitle = str_replace(
                ' ',
                '&nbsp;',
                htmlentities(Services::Language()->translate(
                    $split[0]
                ), ENT_COMPAT, 'UTF-8')
            );

            $translateTabDesc = Services::Language()->translate(strtoupper(
                strtoupper($namespace) . '_FORM_' . strtoupper(str_replace('&nbsp;', '_', $tabTitle)) . '_DESC'));

            $tab_link = $split[1];

            if (count($split) == 3) {
                $tabIncludeName = $split[2];
            } else {
                $tabIncludeName = $default_tab_view_name;
            }

            $this->createTabFieldsets(
                $namespace,
                $tab_prefix,
                ucfirst(strtolower($tab_link)),
                $tabTitle,
                $translateTabDesc,
                $model_type,
                $model_name,
                $view_name,
                $extension_instance_id,
                $item
            );

            $tabArray = 'tab_title:' . $tabTitle
                . ',' . 'tab_namespace:' . $namespace
                . ',' . 'tab_link:' . $namespace . $tab_link
                . ',' . 'tab_include_name:' . $tabIncludeName
                . ',' . 'tab_include_parameter:' . $view_name . strtolower($namespace . $tab_link);

            $tabs[] = '{{' . trim($tabArray) . '}}';
        }

        if ($tabs === false) {
            $tabCount = 0;
        } else {
            if (is_array($tabs)) {
            } else {
                $tabs = array($tabs);
            }
            $tabCount = count($tabs);
        }

        /** expand into a single row */
        $tabrows = array();

        $row = new \stdClass();

        $row->tab_count = $tabCount;
        $row->tab_class = $tab_class;
        $row->tab_array = '';

        if ($tabCount === 0) {
            $row->tab_array = null;
        } else {
            foreach ($tabs as $tab) {
                $row->tab_array .= trim($tab);
            }
        }
        $tabrows[] = $row;

        return $tabrows;
    }

    /**
     * createTabFieldsets - tab parameters determine source data requirements
     *
     * @param $namespace
     * @param $tab_prefix
     * @param $tab_link
     * @param $tabTitle
     * @param $translateTabDesc
     * @param $configuration - contains the requested configuration
     * @param $model_type
     * @param $model_name
     * @param $view_name
     * @param $extension_instance_id
     * @param $item
     *
     * @return string
     * @since   1.0
     */
    protected function createTabFieldsets($namespace, $tab_prefix, $tab_link,
                                          $tabTitle, $translateTabDesc,
                                          $model_type, $model_name, $view_name,
                                          $extension_instance_id, $item)
    {
/**        echo 'Namespace: ' . $namespace. ' Tab Prefix: ' .  $tab_prefix. ' Tab Link: ' .  $tab_link. ' Tab Title: ' .
            $tabTitle. ' Tab Description: ' .  $translateTabDesc. ' Model Type: ' .
            $model_type. ' Model Name: ' .  $model_name. ' View Name: ' .  $view_name. ' Extension Instance ID: ' .
            $extension_instance_id;

        echo '<pre>';
        var_dump($item);
        echo '</pre>';
*/
        $configurationArray = array();

        if ($tab_prefix === null) {
            $configuration = '{{' . $tab_link . ',' . strtolower($tab_link) . '}}';
        } else {
            $configuration = Services::Registry()->get('Parameters', $tab_prefix . strtolower($tab_link));
        }

        $temp = explode('}}', $configuration);
        $fieldSets = array();

        foreach ($temp as $set) {
            $set = str_replace('{{', '', $set);

            if (trim($set) == '') {
            } else {
                $configurationArray[] = trim($set);
            }
        }

		$countFieldsets = 0;
		$tab_first_row = 1;
		$tab_fieldset_column = 1;

        foreach ($configurationArray as $config) {

            $options = explode(',', $config);
            if (count($options) > 1) {
            } else {
                return false;
            }

            $tabFieldsetTitle = str_replace(
                ' ',
                '&nbsp;',
                htmlentities(Services::Language()->translate(
                    $options[0]
                ), ENT_COMPAT, 'UTF-8')
            );

            if ($tabFieldsetTitle == '') {
                $tabFieldsetTitle = $tabTitle;
            }

            $translateFieldsetDesc = Services::Language()->translate(strtoupper(
                strtoupper($namespace) . '_FORM_FIELDSET_'
                    . strtoupper(str_replace('&nbsp;', '_', $tabTitle)) . '_'
                    . strtoupper(str_replace('&nbsp;', '_', $tabFieldsetTitle)) . '_DESC'));

            unset($options[0]);

            $get = 'get' . ucfirst(strtolower($tab_link));

			$temp = array();

            if ($tab_prefix === null) {
                /** Only titles and name of view to be included (view will take care of data retrieval) */
                $row = new \stdClass();

                $row->tab_title = $tabTitle;
                $row->tab_description = $translateTabDesc;
                $row->tab_fieldset_title = $tabFieldsetTitle;
                $row->tab_fieldset_description = $translateFieldsetDesc;

                $row->tab_link = ucfirst(strtolower($view_name . $namespace . $tab_link));

                $temp[] = $row;

            } else {

                if ($namespace == 'Edit') {

                    $temp = $this->getActualFields($namespace, $tab_link, $options,
                        $tabTitle, $translateTabDesc,
                        $tabFieldsetTitle, $translateFieldsetDesc,
                        $model_type, $model_name, $extension_instance_id,
                        $item);

                } elseif (method_exists($this, 'get' . $tab_link)) {

                    $temp = $this->$get($namespace, $tab_link, $options,
                        $tabTitle, $translateTabDesc,
                        $tabFieldsetTitle, $translateFieldsetDesc,
                        $model_type, $model_name, $extension_instance_id);

                } else {

                    $temp = $this->getParameters($namespace, $tab_link, $options,
                        $tabTitle, $translateTabDesc,
                        $tabFieldsetTitle, $translateFieldsetDesc,
                        $model_type, $model_name, $extension_instance_id);
                }
            }

			if (count($temp) > 0) {

				$write = array();

				$tab_new_fieldset = 1;
				$tab_fieldset_count = count($temp);
				$tab_fieldset_odd_or_even = 'odd';
				$tab_fieldset_row_number = 1;

				foreach ($temp as $item) {

					$item->tab_new_fieldset = $tab_new_fieldset;
					$item->tab_first_row = $tab_first_row;
					$item->tab_fieldset_count = $tab_fieldset_count;
					$item->tab_fieldset_odd_or_even = $tab_fieldset_odd_or_even;
					$item->tab_fieldset_row_number = $tab_fieldset_row_number;
					$item->tab_fieldset_column = $tab_fieldset_column;
					$tab_fieldset_row_number++;

					$tab_new_fieldset = 0;
					$tab_first_row = 0;

					$write[] = $item;
				}

				if ($tab_fieldset_odd_or_even == 'odd') {
					$tab_fieldset_odd_or_even = 'even';
				} else {
					$tab_fieldset_odd_or_even = 'odd';
				}

				if ($tab_fieldset_column == 1) {
					$tab_fieldset_column = 2;
				} else {
					$tab_fieldset_column = 1;
				}

			}

            $fieldSets = array_merge((array) $fieldSets, (array) $write);
        }

        Services::Registry()->set('Plugindata', $view_name . $namespace . strtolower($tab_link), $fieldSets);

        return true;
    }

    /**
     * Retrieves field definition, current resource setting, and application setting for requested parameters
     *
     * @param $namespace
     * @param $tab_link
     * @param $options
     * @param $tabTitle
     * @param $translateTabDesc
     * @param $tabFieldsetTitle
     * @param $translateFieldsetDesc
     * @param $model_type
     * @param $model_name,
     * @param $extension_instance_id
     *
     * @return mixed
     * @since   1.0
     */
    protected function getParameters($namespace, $tab_link, $options,
                                     $tabTitle, $translateTabDesc,
                                     $tabFieldsetTitle, $translateFieldsetDesc,
                                     $model_type, $model_name, $extension_instance_id)
    {

//		echo 'Namespace: ' . $namespace. ' Tab Link: ' .  $tab_link . '<br />';

//		echo '<pre>';
//		var_dump($options);
//		echo '</pre>';

//		echo ' Tab Title: ' .
//		$tabTitle. ' Tab Description: ' .  $translateTabDesc.
//			'Tabfieldset Title ' . $tabFieldsetTitle .
//			'Tabfieldset Description ' . $translateFieldsetDesc,
//			' Model Type: ' .
//		$model_type. ' Model Name: ' .  $model_name. ' Extension Instance ID: ' .
//		$extension_instance_id;

        $fieldValues = array();
        $build_results = array();

        foreach ($options as $value) {

            if (substr($value, strlen($value) - 1, 1) == '*') {
                $compare = substr($value, 0, strlen($value) - 1);
            } else {
                $compare = $value;
            }

            if (trim($compare) == '' || strlen($compare) == 0) {
            } else {

                if ($namespace == 'Application') {
                    $data = Services::Registry()->get('ApplicationTable', 'parameters');
                } else {
                    $data = Services::Registry()->get('ResourcesSystem', 'parameters');
                }

                foreach ($data as $field) {
                    $use = false;

                    if ($field['name'] == $compare) {
                        $use = true;
                    }
                    if (substr(strtolower($field['name']), 0, strlen($compare)) == $compare
                        && strlen($compare) > 0
                    ) {
                        $use = true;
                    }
                    if ($use === true) {
                        $row = $field;

                        $row['tab_title'] = $tabTitle;
                        $row['tab_description'] = $translateTabDesc;
                        $row['tab_fieldset_title'] = $tabFieldsetTitle;
                        $row['tab_fieldset_description'] = $translateFieldsetDesc;

                        if ($namespace == 'Application') {
                            $row['value'] = Services::Registry()->get('Configuration', $field['name']);
                        } else {
                            $row['value'] = Services::Registry()->get('ResourcesSystemParameters', $field['name']);
                        }

                        $row['application_default'] = Services::Registry()->get('Configuration', $field['name']);
                        $build_results[] = $row;

                    }
                }
            }
        }

        if (count($build_results) > 0) {
            $x = Services::Form()->setFieldset($namespace, $tab_link, $build_results, $model_type, $model_name);
        } else {
            return array();
        }

		//echo '<br />END OF FIELDSET PROCESSING<pre>';
		//var_dump($x);
		//echo '<pre><br />><br />';
		return $x;
    }

    /**
     * Retrieves field definition, current resource setting, and application setting for requested parameters
     *
     * @param $namespace
     * @param $tab_link
     * @param $options
     * @param $tabTitle
     * @param $translateTabDesc
     * @param $tabFieldsetTitle
     * @param $translateFieldsetDesc
     * @param $model_type
     * @param $model_name,
     * @param $extension_instance_id
     * @param $item
     *
     * @return mixed
     * @since   1.0
     */
    protected function getActualFields($namespace, $tab_link, $options,
                                       $tabTitle, $translateTabDesc,
                                       $tabFieldsetTitle, $translateFieldsetDesc,
                                       $model_type, $model_name, $extension_instance_id,
                                       $item)
    {
        $fieldValues = array();
        $build_results = array();
        $fieldArray = Services::Registry()->get($model_name . $model_type, 'Fields');

        $customfieldgroups = Services::Registry()->get($model_name . $model_type, 'customfieldgroups');

        foreach ($options as $value) {

            if (substr($value, strlen($value) - 1, 1) == '*') {
                $compare = substr($value, 0, strlen($value) - 1);
            } else {
                $compare = $value;
            }

            if (trim($compare) == '' || strlen($compare) == 0) {
            } else {

                $data = Services::Registry()->get('ResourcesSystem', 'parameters');

                foreach ($data as $field) {

                    $use = false;

                    if ($field['name'] == $compare) {
                        $use = true;
                    }
                    if (substr($field['name'], 0, strlen($compare)) == $compare
                        && strlen($compare) > 0
                    ) {
                        $use = true;
                    }
                    if ($use === true) {

                        $field_name = Services::Registry()->get('ResourcesSystemParameters', $field['name']);

                        if ($field_name == '') {
                        } else {

                            $row = '';

                            foreach ($fieldArray as $field) {
                                foreach ($field as $key => $value) {
                                    if ($key == 'name' && $value == $field_name) {
                                        $row = $field;
                                        $row['customfield'] = '';
                                        break;
                                    }
                                }
                            }

                            foreach ($customfieldgroups as $custom) {
                                if ($custom == 'parameters') {
                                } else {
                                    $temp = Services::Registry()->get($model_name . $model_type, $custom);
                                    foreach ($temp as $field) {
                                        foreach ($field as $key => $value) {
                                            if ($key == 'name' && $value == $field_name) {
                                                $row = $field;
                                                $row['customfield'] = $custom;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }

                            if ($row == '') {
                            } else {

                                $row['tab_title'] = $tabTitle;
                                $row['tab_description'] = $translateTabDesc;
                                $row['tab_fieldset_title'] = $tabFieldsetTitle;
                                $row['tab_fieldset_description'] = $translateFieldsetDesc;

                                if (isset($item->$field_name)) {
                                    $row['value'] = $item->$field_name;
                                } else {
                                    $row['value'] = null;
                                }

                                $row['application_default'] = Services::Registry()->get('Configuration', $field['name']);
                                $build_results[] = $row;
                            }
                        }
                    }
                }
            }
        }

        if (count($build_results) > 0) {
			return Services::Form()->setFieldset($namespace, $tab_link, $build_results, $model_type, $model_name);
        } else {
            return array();
        }
    }

    /**
     * Retrieves field definition, current resource setting, and application setting for Metadata
     *
     * @param $namespace
     * @param $tab_link
     * @param $options
     * @param $tabTitle
     * @param $translateTabDesc
     * @param $tabFieldsetTitle
     * @param $translateFieldsetDesc
     * @param $model_type
     * @param $model_name
     * @param $extension_instance_id
     *
     * @return mixed
     * @since  1.0
     */
    protected function getMetadata($namespace, $tab_link, $options,
                                   $tabTitle, $translateTabDesc,
                                   $tabFieldsetTitle, $translateFieldsetDesc,
                                   $model_type, $model_name, $extension_instance_id)
    {
        $build_results = array();

        foreach (Services::Registry()->get('ResourcesSystem', 'metadata') as $field) {
            $row = $field;
            $row['tab_title'] = $tabTitle;
            $row['tab_description'] = $translateTabDesc;
            $row['tab_fieldset_title'] = $tabFieldsetTitle;
            $row['tab_fieldset_description'] = $translateFieldsetDesc;
            $row['value'] = Services::Registry()->get('ResourcesSystemMetadata', $field['name']);
            $row['application_default'] = Services::Registry()->get('Configuration', 'metadata_' . $field['name']);
            $build_results[] = $row;
        }

        if (count($build_results) > 0) {
			return Services::Form()->setFieldset($namespace, $tab_link, $build_results, $model_type, $model_name);
        } else {
            return array();
        }
    }

    /**
     * Retrieves field definition, current resource setting, and application setting for Grid parameters
     *
     * @param $namespace
     * @param $tab_link
     * @param $options
     * @param $tabTitle
     * @param $translateTabDesc
     * @param $tabFieldsetTitle
     * @param $translateFieldsetDesc
     * @param $model_type
     * @param $model_name
     * @param $extension_instance_id
     *
     * @return array
     * @since   1.0
     */
    protected function getGrid($namespace, $tab_link, $options,
                               $tabTitle, $translateTabDesc,
                               $tabFieldsetTitle, $translateFieldsetDesc,
                               $model_type, $model_name, $extension_instance_id)
    {
        if (Services::Registry()->exists('GridMenuitem') === true) {
        } else {

            $item = Helpers::Content()->getResourceMenuitemParameters('Grid', $extension_instance_id);

            if ($item === false || count($item) == 0) {
                return false;
            }
        }

        $fieldValues = array();

        foreach ($options as $value) {
            if (substr($value, strlen($value) - 1, 1) == '*') {
                $compare = substr($value, 0, strlen($value) - 1);
            } else {
                $compare = $value;
            }

            if (trim($compare) == '' || strlen($compare) == 0) {
            } else {

                foreach (Services::Registry()->get('GridMenuitem', 'parameters') as $field) {

                    $use = false;
                    if ($field['name'] == $compare) {
                        $use = true;
                    }
                    if (substr($field['name'], 0, strlen($compare)) == $compare
                        && strlen($compare) > 0
                    ) {
                        $use = true;
                    }
                    if ($use === true) {
                        $row = $field;
                        $row['tab_title'] = $tabTitle;
                        $row['tab_description'] = $translateTabDesc;
                        $row['tab_fieldset_title'] = $tabFieldsetTitle;
                        $row['tab_fieldset_description'] = $translateFieldsetDesc;
                        $row['value'] = Services::Registry()->get('GridMenuitemParameters', $field['name']);
                        $row['application_default'] = Services::Registry()->get('Configuration', $field['name']);
                        $build_results[] = $row;

                    }
                }
            }
        }

        if (count($build_results) > 0) {
			return Services::Form()->setFieldset($namespace, $tab_link, $build_results, $model_type, $model_name);
        } else {
            return array();
        }
    }

    /**
     * Retrieves custom fields defined specifically for this resource (and therefore can be updated)
     *
     * @param $namespace
     * @param $tab_link
     * @param $options
     * @param $tabTitle
     * @param $translateTabDesc
     * @param $tabFieldsetTitle
     * @param $translateFieldsetDesc
     * @param $model_type
     * @param $model_name
     *
     * @return array
     * @since   1.0
     */
    protected function getCustomfields($namespace, $tab_link, $options,
                                       $tabTitle, $translateTabDesc,
                                       $tabFieldsetTitle, $translateFieldsetDesc,
                                       $model_type, $model_name)
    {
        $build_results = array();

        /** Fields needed to define new Custom Fields */
        $entry_fields = Services::Registry()->get('AdminconfigurationTemplate', 'parameters');
        if (count($entry_fields) == 0 || $entry_fields === false) {
        } else {
            $useit = array();
            foreach ($entry_fields as $entry_field) {

                if (substr($entry_field['name'], 0, strlen('define_')) == 'define_') {
                    $useit[] = $entry_field;
                }
            }

            if (count($useit) == 0) {
            } else {
                foreach ($useit as $field) {

                    $tabFieldsetTitle = str_replace(
                        ' ',
                        '&nbsp;',
                        htmlentities(ucfirst(strtolower('Create')), ENT_COMPAT, 'UTF-8')
                    );

                    $translateFieldsetDesc = Services::Language()->translate(strtoupper(
                        strtoupper($namespace) . '_FORM_FIELDSET_'
                            . strtoupper(str_replace('&nbsp;', '_', $tabTitle)) . '_'
                            . strtoupper(str_replace('&nbsp;', '_', $tabFieldsetTitle)) . '_DESC'));

                    $row = $field;
                    $row['tab_title'] = $tabTitle;
                    $row['tab_description'] = $translateTabDesc;
                    $row['tab_fieldset_title'] = $tabFieldsetTitle;
                    $row['tab_fieldset_description'] = $translateFieldsetDesc;
                    $row['value'] = null;
                    $row['first_following'] = 0;

                    $row['customfield_type'] = 'Create';

                    $build_results[] = $row;
                }
            }
        }

        $table_registry_name = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

        $custom_fields = array();
        $custom_fields[] = 'metadata';
        $custom_fields[] = 'customfields';
        $custom_fields[] = 'parameters';

        $first = 1;

        $temp = Services::Registry()->get($table_registry_name, 'customfieldgroups');
        if (count($custom_fields) == 0) {
        } else {
            foreach ($temp as $item) {
                if (in_array($item, $custom_fields)) {
                } else {
                    $custom_fields[] = $item;
                }
            }
        }

        /** Fields defined specifically for this resource */
        foreach ($custom_fields as $custom_field) {

            $tabFieldsetTitle = str_replace(
                ' ',
                '&nbsp;',
                htmlentities(ucfirst(strtolower($custom_field)), ENT_COMPAT, 'UTF-8')
            );

            $translateFieldsetDesc = Services::Language()->translate(strtoupper(
                strtoupper($namespace) . '_FORM_FIELDSET_'
                    . strtoupper(str_replace('&nbsp;', '_', $tabTitle)) . '_'
                    . strtoupper(str_replace('&nbsp;', '_', $tabFieldsetTitle)) . '_DESC'));

            $fields = Services::Registry()->get($table_registry_name, $custom_field);
            if ((int) count($fields) === 0 || $fields === false) {
            } else {
                foreach ($fields as $field) {

                    if ($field['field_inherited'] == 1) {
                    } else {

                        $field['tab_title'] = $tabTitle;
                        $field['tab_description'] = $translateTabDesc;
                        $field['tab_fieldset_title'] = $tabFieldsetTitle;
                        $field['tab_fieldset_description'] = $translateFieldsetDesc;
                        $field['first_following'] = $first;

                        $first = 0;

                        $field['customfield_type'] = $custom_field;

                        $build_results[] = $field;
                    }
                }
            }
        }

        if (count($build_results) > 0) {
			return Services::Form()->setFieldset($namespace, $tab_link, $build_results, $model_type, $model_name);
        } else {
            return array();
        }
    }

    /**
     * setFieldset - builds two sets of data:
     *
     *  1. Fieldsets: collection of the names of fields to be used to create field-specific include statements
     *  2. Fields: Field-specific registries which define attributes input to the template field creation view
     *
     * @param $namespace
     * @param $tab_link
     * @param $input_fields
	 * @param $model_type
	 * @param $model_name
     *
     * @return array
     * @since  1.0
     */
    public function setFieldset($namespace, $tab_link, $input_fields, $model_type, $model_name)
    {
        $fieldset = array();
        $first = true;

        foreach ($input_fields as $field) {

            $row = new \stdClass();

            $row->tab_title = $field['tab_title'];
            $row->tab_description = $field['tab_description'];
            $row->tab_fieldset_title = $field['tab_fieldset_title'];
            $row->tab_fieldset_description = $field['tab_fieldset_description'];

			if (isset($field['name'])) {
			} else {
				echo 'missing name ';
				echo '<pre>';
				var_dump($field);
				echo '</pre>';
				die;
			}

            $row->field_id = $field['name'];
            $row->id = $field['name'];
            $row->name = $field['name'];
            $row->label = Services::Language()->translate(strtoupper($field['name'] . '_LABEL'));
            $row->tooltip = Services::Language()->translate(strtoupper($field['name'] . '_TOOLTIP'));
            $row->placeholder = Services::Language()->translate(strtoupper($field['name'] . '_PLACEHOLDER'));

            if (isset($field['locked']) && $field['locked'] == 1) {
                $row->disabled = 1;
            } else {
                $row->disabled = 0;
            }

            if (isset($field['value'])) {
            } else {
                $field['value'] = NULL;
            }
            $row->value = $field['value'];

            if (isset($field['null'])) {
            } else {
                $field['null'] = 0;
            }
            if ((int) $field['null'] === 1) {
                $row->required = Services::Language()->translate('N');
            } else {
                $row->required = Services::Language()->translate('Y');
            }

            if (isset($field['hidden'])) {
            } else {
                $field['hidden'] = 0;
            }

            if ((int) $field['hidden'] === 1) {
                $row->hidden = Services::Language()->translate('Y');
            } else {
                $row->hidden = Services::Language()->translate('N');
            }

            if (isset($field['application_default'])) {
            } else {
                $field['application_default'] = NULL;
            }

            if (isset($field['default'])) {
            } else {
                $field['default'] = NULL;
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

            if (isset($field['type'])) {
            } else {
                echo $field['name'] . ' unknown type';
                $field['type'] = 'char';
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
                case 'datetime':
                    $row->type = 'date';
                    $row->view = 'forminput';
                    break;

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
                    echo 'WHAT IS THIS TYPE? (changing to text) ' . $row->type . ' Name: ' . $row->name . '<br />';
                    $row->type = 'text';
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
                    $row->name = $this->setRadioField($namespace, $tab_link, $field, $row);
                    break;

                case 'formselect':
                    $row->name = $this->setSelectField($namespace, $tab_link, $field, $row, $model_type, $model_name);
                    break;

                case 'formtextarea':
                    $row->name = $this->setTextareaField($namespace, $tab_link, $field, $row);
                    break;

                default:
                    $row->name = $this->setInputField($namespace, $tab_link, $field, $row);
                    break;
            }

            if (isset($field['first_following'])) {
                $row->first_following = $field['first_following'];
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
     * @param $tab_link
     * @param $field
     * @param $row_start
     *
     * @return string
     * @since  1.0
     */
    protected function setInputField($namespace, $tab_link, $field, $row_start)
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

//		if (isset($field['class'])) {
//			$iterate['class'] = $field['class'];
//		} else {
//			$iterate['class'] = 'editable';
//		}

        $iterate['value'] = $field['value'];

//		foreach ($field as $key => $value) {
//			if (in_array($key, $iterate)) {
//			} else {
//				$iterate[$key] = $value;
//			}
//		}
        foreach ($iterate as $key => $value) {
            $row = new \stdClass();

            foreach ($row_start as $rkey => $rvalue) {
                $row->$rkey = $rvalue;
            }

            $row->key = $key;
            $row->value = $value;

            $fieldRecordset[] = $row;
        }

        $registryName = $namespace . strtolower($tab_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }

    /**
     * setRadioField field
     *
     * @param $namespace
     * @param $tab_link
     * @param $field
     * @param $row_start
     *
     * @return string
     * @since  1.0
     */
    protected function setRadioField($namespace, $tab_link, $field, $row_start)
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
        if ((int) $field['value'] === 1) {
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
        if ((int) $field['value'] === 0) {
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

        $registryName = $namespace . strtolower($tab_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }

    /**
     * setSelectField field
     *
     * @param $namespace
     * @param $tab_link
     * @param $field
     * @param $row_start
	 * @param $model_type
	 * @param $model_name
     *
     * @return mixed|string
     * @somce  1.0
     */
    protected function setSelectField($namespace, $tab_link, $field, $row_start, $model_type, $model_name)
    {
        $fieldRecordset = array();

        $required = '';
        if ($field['null'] == 1) {
            $required = ' required';
        }

        $disabled = '';
        if ($field['disabled'] == 1) {
            $disabled = ' disabled';
        }

        $default = $field['default'];

        $multiple = '';
        if (isset($field['multiple'])) {
            if ($field['multiple'] == 1) {
                $multiple = ' multiple';
            }
        }

        $size = '';
        if (isset($field['size'])) {
            if ((int) $field['size'] > 1) {
                $size = ' size="' . $field['size'] . '"';
            }
        }

//		if (isset($field['class'])) {
//			$iterate['class'] = $field['class'];
//		} else {
//			$iterate['class'] = 'editable';
//		}               [15]=>

        $temp = $field['value'];
        $default_setting = 0;
        if ($temp === NULL) {
            $temp = $field['default'];
            $default_setting = 1;
        }

        if ($field['name'] == 'robots') {
            $selectedArray =array($temp);
        } else {
            $selectedArray = explode(',', $temp);
        }

        $datalist = $field['datalist'];

		$yes = 0;
		if (strtolower($datalist) == 'fields') {
			$table_registry_name = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
			$list = Services::Registry()->get('Datalist', $table_registry_name . 'Fields');

		} elseif (strtolower($datalist) == 'fieldsstandard') {
			$table_registry_name = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
			$list = Services::Registry()->get('Datalist', $table_registry_name . 'Fieldsstandard');

		} else {
			$list = Services::Text()->getList($datalist, array());
		}

        if ($list === false) {
            $items = array();
        } else {
            $items = Services::Text()->buildSelectlist($datalist, $list, 0, 5);
        }

        $selectionFound = false;

        if (count($items) == 0 || $items === false) {
        } else {
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
        }

        if (isset($row)) {
        } else {
            return false;
        }

        /** Field Dataset */
        $registryName = $namespace . strtolower($tab_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }

    /**
     * setTextareaField field
     *
     * @return array
     */
    protected function setTextareaField($namespace, $tab_link, $field, $row_start)
    {
        $fieldRecordset = array();

        $iterate = array();

        if ($field['null'] == 1) {
            $iterate['required'] = ' required';
        }

        if ($field['disabled'] == 1) {
            $iterate['disabled'] = ' disabled';
        }

        if (isset($field['rows'])) {
            $iterate['rows'] = $field['rows'];
        }

        if (isset($field['cols'])) {
            $iterate['cols'] = $field['cols'];
        }

//		if (isset($field['class'])) {
//			$iterate['class'] = $field['class'];
//		} else {
//			$iterate['class'] = 'editable';
//		}
        //foreach ($field as $key => $value) {
        //	if (in_array($key, $iterate)) {
        //	} else {
        //		$iterate[$key] = $value;
        //	}
        //}

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
        $registryName = $namespace . strtolower($tab_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }
}
