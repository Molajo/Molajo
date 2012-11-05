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
     * @return  bool|object
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
     * For Single or Multi-page(or tab) Forms
	 *
	 * @param string $page_array - The request for a single or set of page form fieldsets to be generated
	 *  Examples:
	 * 		Single Page {{Editor,editor}}
	 *  	Multi Pages {{Basic,basic}}{{Access,access}}{{Metadata,metadata}}{{Fields,customfields,Customfields}}{{Editor,editor}}{{Grid,grid}}{{Form,form}}{{Item,item}}{{List,list}}
	 *
	 * @param string $namespace - ex. config, grid, edit, etc.
	 * @param string $model_type - ex. Resources
     * @param string $model_name - ex. Articles
     * @param $extension_instance_id ex. 16000 for Articles
     * @param $form_field_values array of data (Application configuration and Session data)
     *
     * @return array
     * @since  1.0
     */
    public function setPageArray($page_array, $namespace,
								 $model_type, $model_name,
                                 $extension_instance_id,
								 $form_field_values)
    {
        $pages = array();
        $configurationArray = array();
        $temp = explode('}}', $page_array);

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

			$pageTitle = Services::Language()->translate($split[0]);

			$pageTitle = str_replace(
				' ',
				'&nbsp;',
				htmlentities($pageTitle)
			);

			$pageTitleExtended = ucfirst(strtolower($model_name))
				. ' '
				. Services::Language()->translate($split[0])
				. ' '
				. Services::Language()->translate('Configuration');

			$pageTitleExtended = str_replace(
                ' ',
                '&nbsp;',
                htmlentities($pageTitleExtended)
			);
			$translate =  strtoupper(strtoupper($namespace)
				. '_FORM_'
				. strtoupper(str_replace('&nbsp;', '_', $pageTitle))
				. '_DESC');

            $translateTabDesc = Services::Language()->translate($translate);

            $page_link = $split[1];

            if (count($split) == 3) {
                $pageFormFieldsetHandlerView = $split[2];
            } else {
                $pageFormFieldsetHandlerView = 'Formpage';
            }

			$this->createPageFieldsets(
				$namespace,
				$model_type, $model_name,
                $extension_instance_id,
				$form_field_values,

				ucfirst(strtolower($page_link)),
				$pageTitle,
				$pageTitleExtended,
				$translateTabDesc,
				$pageFormFieldsetHandlerView
			);

            $pageArray = 'page_title:' . $pageTitle
				. ',' . 'page_title_extended:' . $pageTitleExtended
                . ',' . 'page_namespace:' . $namespace
                . ',' . 'page_link:' . $namespace . $page_link
                . ',' . 'page_form_fieldset_handler_view:' . $pageFormFieldsetHandlerView
                . ',' . 'page_include_parameter:' . $pageFormFieldsetHandlerView . strtolower($namespace . $page_link);

            $pages[] = '{{' . trim($pageArray) . '}}';
        }

        if ($pages === false) {
            $pageCount = 0;
        } else {
            if (is_array($pages)) {
            } else {
                $pages = array($pages);
            }
            $pageCount = count($pages);
        }

        /** expand into a single row */
        $pagerows = array();

        $row = new \stdClass();

        $row->page_count = $pageCount;
        $row->page_array = '';

        if ($pageCount === 0) {
            $row->page_array = null;
        } else {
            foreach ($pages as $page) {
                $row->page_array .= trim($page);
            }
        }
        $pagerows[] = $row;

        return $pagerows;
    }

    /**
     * createPageFieldsets - Processes a request for one page (could be many fieldsets and fields)
	 * Examples: {{Editor,editor}} or {{Metadata,metadata}} or {{Fields,customfields,Customfields}}
	 * This method is invoked for each page processed by setPageArray.
	 * To build a one-page form, use this method or setPageArray
     *
	 * @param string $namespace - ex. config, grid, edit, etc.
	 * @param string $model_type - ex. Resources
	 * @param string $model_name - ex. Articles
	 * @param $extension_instance_id ex. 16000 for Articles
	 * @param $form_field_values array of data (Application configuration values, resoucre item, Session data, etc.)
	 * @param $page_link - constructed from the page array data to provide a #link value if tabs are used
     * @param $pageTitle - constructed from the page array - used in headings, etc.
	 * @param $pageTitleExtended - constructed from title, type of config and resource
     * @param $translateTabDesc - retrieved from language strings, given page array values
     * @param page_form_fieldset_handler_view - View invoked for Page to render form fieldset and
	 * 	each <include statement needed for each form field
     *
     * @return string
     * @since   1.0
     */
    protected function createPageFieldsets($namespace, $model_type, $model_name,
                $extension_instance_id, $form_field_values, $page_link,
				$pageTitle, $pageTitleExtended, $translateTabDesc,
				$page_form_fieldset_handler_view)

    {
        $configurationArray = array();

        if ($page_link === 'noformfields') {
            $configuration = '{{' . $page_link . ',' . strtolower($page_link) . '}}';
        } else {
            $configuration = Services::Registry()->get('Parameters', $namespace . '_' . strtolower($page_link));
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

		$page_first_row = 1;
		$page_fieldset_column = 1;

        foreach ($configurationArray as $config) {

            $options = explode(',', $config);
            if (count($options) > 1) {
            } else {
                return false;
            }

            $pageFieldsetTitle = str_replace(
                ' ',
                '&nbsp;',
                htmlentities(Services::Language()->translate(
                    $options[0]
                ), ENT_COMPAT, 'UTF-8')
            );

            if ($pageFieldsetTitle == '') {
                $pageFieldsetTitle = $pageTitle;
            }

            $translateFieldsetDesc = Services::Language()->translate(strtoupper(
                strtoupper($namespace) . '_FORM_FIELDSET_'
                    . strtoupper(str_replace('&nbsp;', '_', $pageTitle)) . '_'
                    . strtoupper(str_replace('&nbsp;', '_', $pageFieldsetTitle)) . '_DESC'));

            unset($options[0]);

            $get = 'get' . ucfirst(strtolower($page_link));

			$temp = array();

            if ($namespace === null) {
                /** Only titles and name of view to be included (view will take care of data retrieval) */
                $row = new \stdClass();

                $row->page_title = $pageTitle;
				$row->page_title_extended = $pageTitleExtended;
                $row->page_description = $translateTabDesc;
                $row->page_fieldset_title = $pageFieldsetTitle;
                $row->page_fieldset_description = $translateFieldsetDesc;

                $row->page_link = ucfirst(strtolower(
					$page_form_fieldset_handler_view
						. $namespace
						. $page_link)
				);

                $temp[] = $row;

            } else {
				// todo: retrieve ACL actual values
                if ($namespace == 'Edit') {

					$temp = $this->getActualFields($namespace, $model_type, $model_name,
						$extension_instance_id, $form_field_values, $page_link,
						$pageTitle, $translateTabDesc,
						$page_form_fieldset_handler_view);

                } elseif (method_exists($this, 'get' . $page_link)) {

                    $temp = $this->$get($namespace, $page_link, $options,
                        $pageTitle, $translateTabDesc,
                        $pageFieldsetTitle, $translateFieldsetDesc,
                        $model_type, $model_name, $extension_instance_id);

                } else {

                    $temp = $this->getParameters($namespace, $page_link, $options,
                        $pageTitle, $translateTabDesc,
                        $pageFieldsetTitle, $translateFieldsetDesc,
                        $model_type, $model_name, $extension_instance_id);
                }
            }

			if (count($temp) > 0) {

				$write = array();

				$page_new_fieldset = 1;
				$page_fieldset_count = count($temp);
				$page_fieldset_odd_or_even = 'odd';
				$page_fieldset_row_number = 1;

				foreach ($temp as $form_field_values) {

					$form_field_values->page_title_extended = $pageTitleExtended;
					$form_field_values->page_new_fieldset = $page_new_fieldset;
					$form_field_values->page_first_row = $page_first_row;
					$form_field_values->page_fieldset_count = $page_fieldset_count;
					$form_field_values->page_fieldset_odd_or_even = $page_fieldset_odd_or_even;
					$form_field_values->page_fieldset_row_number = $page_fieldset_row_number;
					$form_field_values->page_fieldset_column = $page_fieldset_column;

					$page_fieldset_row_number++;

					$page_new_fieldset = 0;
					$page_first_row = 0;

					$write[] = $form_field_values;
				}

				if ($page_fieldset_odd_or_even == 'odd') {
					$page_fieldset_odd_or_even = 'even';
				} else {
					$page_fieldset_odd_or_even = 'odd';
				}

				if ($page_fieldset_column == 1) {
					$page_fieldset_column = 2;
				} else {
					$page_fieldset_column = 1;
				}

			}

            $fieldSets = array_merge((array) $fieldSets, (array) $write);
        }

		Services::Registry()->set('Plugindata', $page_form_fieldset_handler_view . $namespace . strtolower($page_link), $fieldSets);

        return true;
    }

    /**
     * Retrieves field definitions and current settings for requested parameters
	 * Examples: {{Editor,editor}} or {{Metadata,metadata}} or {{Fields,customfields,Customfields}}
	 * This method is invoked for each page processed by setPageArray.
	 * To build a one-page form, use this method or setPageArray
	 *
	 * @param string $namespace - ex. config, grid, edit, etc.
	 * @param $page_link - constructed from the page array data to provide a #link value if tabs are used
	 * @param $options - Value used as a prefix to extract parameters which start with that value
	 * @param $pageTitle - constructed from the page array - used in headings, etc.
	 * @param $translateTabDesc - retrieved from language strings, given page array values
	 * @param $pageFieldsetTitle - Fieldset title
	 * @param $translateFieldsetDesc - Fieldset description
	 * @param string $model_type - ex. Resources
	 * @param string $model_name - ex. Articles
	 * @param $extension_instance_id ex. 16000 for Articles
	 *
	 * @return string
	 * @since   1.0
	 */
    protected function getParameters($namespace, $page_link, $options,
                                     $pageTitle, $translateTabDesc,
                                     $pageFieldsetTitle, $translateFieldsetDesc,
                                     $model_type, $model_name, $extension_instance_id)
    {

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

                        $row['page_title'] = $pageTitle;
                        $row['page_description'] = $translateTabDesc;
                        $row['page_fieldset_title'] = $pageFieldsetTitle;
                        $row['page_fieldset_description'] = $translateFieldsetDesc;

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
            $x = Services::Form()->setFieldset($namespace, $page_link, $build_results, $model_type, $model_name);
        } else {
            return array();
        }

		return $x;
    }
	/**
	 * Retrieves Custom Fields created for this Resource
	 * Examples: {{Editor,editor}} or {{Metadata,metadata}} or {{Fields,customfields,Customfields}}
	 * This method is invoked for each page processed by setPageArray.
	 * To build a one-page form, use this method or setPageArray
	 *
	 * @param  string $namespace - ex. config, grid, edit, etc.
	 * @param  string $page_link - constructed from the page array data to provide a #link value if tabs are used
	 * @param  string $options - Value used as a prefix to extract parameters which start with that value
	 * @param  string $pageTitle - constructed from the page array - used in headings, etc.
	 * @param  string $translateTabDesc - retrieved from language strings, given page array values
	 * @param  string $pageFieldsetTitle - Fieldset title
	 * @param  string $translateFieldsetDesc - Fieldset description
	 * @param  string string $model_type - ex. Resources
	 * @param  string $model_name - ex. Articles
	 * @param  string $extension_instance_id ex. 16000 for Articles
	 *
	 * @return  string
	 * @since   1.0
	 */
    protected function getActualFields($namespace, $page_link, $options,
                                       $pageTitle, $translateTabDesc,
                                       $pageFieldsetTitle, $translateFieldsetDesc,
                                       $model_type, $model_name, $extension_instance_id,
                                       $form_field_values)

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

                                $row['page_title'] = $pageTitle;
                                $row['page_description'] = $translateTabDesc;
                                $row['page_fieldset_title'] = $pageFieldsetTitle;
                                $row['page_fieldset_description'] = $translateFieldsetDesc;

                                if (isset($form_field_values->$field_name)) {
                                    $row['value'] = $form_field_values->$field_name;
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
			return Services::Form()->setFieldset($namespace, $page_link, $build_results, $model_type, $model_name);
        } else {
            return array();
        }
    }

	/**
	 * Retrieves Metadata definitions and current configuration values
	 * Examples: {{Metadata,metadata}}
	 * This method is invoked for each page processed by setPageArray.
	 * To build a one-page form, use this method or setPageArray
	 *
	 * @param  string $namespace - ex. config, grid, edit, etc.
	 * @param  string $page_link - constructed from the page array data to provide a #link value if tabs are used
	 * @param  string $options - Value used as a prefix to extract parameters which start with that value
	 * @param  string $pageTitle - constructed from the page array - used in headings, etc.
	 * @param  string $translateTabDesc - retrieved from language strings, given page array values
	 * @param  string $pageFieldsetTitle - Fieldset title
	 * @param  string $translateFieldsetDesc - Fieldset description
	 * @param  string $model_type - ex. Resources
	 * @param  string $model_name - ex. Articles
	 * @param  string $extension_instance_id ex. 16000 for Articles
	 *
	 * @return  string
	 * @since   1.0
	 */
    protected function getMetadata($namespace, $page_link, $options,
                                   $pageTitle, $translateTabDesc,
                                   $pageFieldsetTitle, $translateFieldsetDesc,
                                   $model_type, $model_name, $extension_instance_id)
    {
        $build_results = array();

        foreach (Services::Registry()->get('ResourcesSystem', 'metadata') as $field) {
            $row = $field;
            $row['page_title'] = $pageTitle;
            $row['page_description'] = $translateTabDesc;
            $row['page_fieldset_title'] = $pageFieldsetTitle;
            $row['page_fieldset_description'] = $translateFieldsetDesc;
            $row['value'] = Services::Registry()->get('ResourcesSystemMetadata', $field['name']);
            $row['application_default'] = Services::Registry()->get('Configuration', 'metadata_' . $field['name']);
            $build_results[] = $row;
        }

        if (count($build_results) > 0) {
			return Services::Form()->setFieldset($namespace, $page_link, $build_results, $model_type, $model_name);
        } else {
            return array();
        }
    }

    /**
	 * Retrieves Metadata definitions and current configuration values
	 * Examples: {{Metadata,metadata}}
	 * This method is invoked for each page processed by setPageArray.
	 * To build a one-page form, use this method or setPageArray
	 *
	 * @param  string $namespace - ex. config, grid, edit, etc.
	 * @param  string $page_link - constructed from the page array data to provide a #link value if tabs are used
	 * @param  string $options - Value used as a prefix to extract parameters which start with that value
	 * @param  string $pageTitle - constructed from the page array - used in headings, etc.
	 * @param  string $translateTabDesc - retrieved from language strings, given page array values
	 * @param  string $pageFieldsetTitle - Fieldset title
	 * @param  string $translateFieldsetDesc - Fieldset description
	 * @param  string $model_type - ex. Resources
	 * @param  string $model_name - ex. Articles
	 * @param  string $extension_instance_id ex. 16000 for Articles
	 *
	 * @return  string
	 * @since   1.0
     */
    protected function getGrid($namespace, $page_link, $options,
                               $pageTitle, $translateTabDesc,
                               $pageFieldsetTitle, $translateFieldsetDesc,
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
                        $row['page_title'] = $pageTitle;
                        $row['page_description'] = $translateTabDesc;
                        $row['page_fieldset_title'] = $pageFieldsetTitle;
                        $row['page_fieldset_description'] = $translateFieldsetDesc;
                        $row['value'] = Services::Registry()->get('GridMenuitemParameters', $field['name']);
                        $row['application_default'] = Services::Registry()->get('Configuration', $field['name']);
                        $build_results[] = $row;

                    }
                }
            }
        }

        if (count($build_results) > 0) {
			return Services::Form()->setFieldset($namespace, $page_link, $build_results, $model_type, $model_name);
        } else {
            return array();
        }
    }

    /**
	 * Retrieves Metadata definitions and current configuration values
	 * Examples: {{Metadata,metadata}}
	 * This method is invoked for each page processed by setPageArray.
	 * To build a one-page form, use this method or setPageArray
	 *
	 * @param  string $namespace - ex. config, grid, edit, etc.
	 * @param  string $page_link - constructed from the page array data to provide a #link value if tabs are used
	 * @param  string $options - Value used as a prefix to extract parameters which start with that value
	 * @param  string $pageTitle - constructed from the page array - used in headings, etc.
	 * @param  string $translateTabDesc - retrieved from language strings, given page array values
	 * @param  string $pageFieldsetTitle - Fieldset title
	 * @param  string $translateFieldsetDesc - Fieldset description
	 * @param  string $model_type - ex. Resources
	 * @param  string $model_name - ex. Articles
	 * @param  string $extension_instance_id ex. 16000 for Articles
     *
     * @return array
     * @since   1.0
     */
    protected function getCustomfields($namespace, $page_link, $options,
                                       $pageTitle, $translateTabDesc,
                                       $pageFieldsetTitle, $translateFieldsetDesc,
                                       $model_type, $model_name,
									   $extension_instance_id)
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

                    $pageFieldsetTitle = str_replace(
                        ' ',
                        '&nbsp;',
                        htmlentities(ucfirst(strtolower('Create')), ENT_COMPAT, 'UTF-8')
                    );

                    $translateFieldsetDesc = Services::Language()->translate(strtoupper(
                        strtoupper($namespace) . '_FORM_FIELDSET_'
                            . strtoupper(str_replace('&nbsp;', '_', $pageTitle)) . '_'
                            . strtoupper(str_replace('&nbsp;', '_', $pageFieldsetTitle)) . '_DESC'));

                    $row = $field;
                    $row['page_title'] = $pageTitle;
                    $row['page_description'] = $translateTabDesc;
                    $row['page_fieldset_title'] = $pageFieldsetTitle;
                    $row['page_fieldset_description'] = $translateFieldsetDesc;
                    $row['value'] = null;
                    $row['first_following'] = 0;

                    $row['customfield_type'] = 'Create';

                    $build_results[] = $row;
                }
            }
        }

        $pagele_registry_name = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

        $custom_fields = array();
        $custom_fields[] = 'metadata';
        $custom_fields[] = 'customfields';
        $custom_fields[] = 'parameters';

        $first = 1;

        $temp = Services::Registry()->get($pagele_registry_name, 'customfieldgroups');
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

            $pageFieldsetTitle = str_replace(
                ' ',
                '&nbsp;',
                htmlentities(ucfirst(strtolower($custom_field)), ENT_COMPAT, 'UTF-8')
            );

            $translateFieldsetDesc = Services::Language()->translate(strtoupper(
                strtoupper($namespace) . '_FORM_FIELDSET_'
                    . strtoupper(str_replace('&nbsp;', '_', $pageTitle)) . '_'
                    . strtoupper(str_replace('&nbsp;', '_', $pageFieldsetTitle)) . '_DESC'));

            $fields = Services::Registry()->get($pagele_registry_name, $custom_field);

            if ((int) count($fields) === 0 || $fields === false) {
            } else {
                foreach ($fields as $field) {

                    if ($field['field_inherited'] == 1) {
                    } else {

                        $field['page_title'] = $pageTitle;
                        $field['page_description'] = $translateTabDesc;
                        $field['page_fieldset_title'] = $pageFieldsetTitle;
                        $field['page_fieldset_description'] = $translateFieldsetDesc;
                        $field['first_following'] = $first;

                        $first = 0;

                        $field['customfield_type'] = $custom_field;

                        $build_results[] = $field;
                    }
                }
            }
        }

        if (count($build_results) > 0) {
			return Services::Form()->setFieldset($namespace, $page_link, $build_results, $model_type, $model_name);
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
     * @param $page_link
     * @param $input_fields
	 * @param $model_type
	 * @param $model_name
     *
     * @return array
     * @since  1.0
     */
    public function setFieldset($namespace, $page_link, $input_fields, $model_type, $model_name)
    {
        $fieldset = array();
        $first = true;

        foreach ($input_fields as $field) {

            $row = new \stdClass();

            $row->page_title = $field['page_title'];
            $row->page_description = $field['page_description'];
            $row->page_fieldset_title = $field['page_fieldset_title'];
            $row->page_fieldset_description = $field['page_fieldset_description'];

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

            /** todo: better mapping approach (fields.xml?) for dapagease types to HTML5/form field types */
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
                    $row->name = $this->setRadioField($namespace, $page_link, $field, $row);
                    break;

                case 'formselect':
                    $row->name = $this->setSelectField($namespace, $page_link, $field, $row, $model_type, $model_name);
                    break;

                case 'formtextarea':
                    $row->name = $this->setTextareaField($namespace, $page_link, $field, $row);
                    break;

                default:
                    $row->name = $this->setInputField($namespace, $page_link, $field, $row);
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
     * @param $page_link
     * @param $field
     * @param $row_start
     *
     * @return string
     * @since  1.0
     */
    protected function setInputField($namespace, $page_link, $field, $row_start)
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
//			$iterate['class'] = 'edipagele';
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

        $registryName = $namespace . strtolower($page_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }

    /**
     * setRadioField field
     *
     * @param $namespace
     * @param $page_link
     * @param $field
     * @param $row_start
     *
     * @return string
     * @since  1.0
     */
    protected function setRadioField($namespace, $page_link, $field, $row_start)
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

        $registryName = $namespace . strtolower($page_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }

    /**
     * setSelectField field
     *
     * @param $namespace
     * @param $page_link
     * @param $field
     * @param $row_start
	 * @param $model_type
	 * @param $model_name
     *
     * @return mixed|string
     * @somce  1.0
     */
    protected function setSelectField($namespace, $page_link, $field, $row_start, $model_type, $model_name)
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
//			$iterate['class'] = 'edipagele';
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
			$pagele_registry_name = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
			$list = Services::Registry()->get('Datalist', $pagele_registry_name . 'Fields');

		} elseif (strtolower($datalist) == 'fieldsstandard') {
			$pagele_registry_name = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));
			$list = Services::Registry()->get('Datalist', $pagele_registry_name . 'Fieldsstandard');

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
        $registryName = $namespace . strtolower($page_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }

    /**
     * setTextareaField field
     *
     * @return array
     */
    protected function setTextareaField($namespace, $page_link, $field, $row_start)
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
//			$iterate['class'] = 'edipagele';
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
        $registryName = $namespace . strtolower($page_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }
}
