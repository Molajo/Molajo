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
     * Namespace - input
     *
     * ex. configuration, grid, edit, etc
     *
     * @var    string
     * @since  1.0
     */
    protected $namespace;

    /**
     * Model Type - input
     *
     * ex. Resources
     *
     * @var    string
     * @since  1.0
     */
    protected $model_type;

    /**
     * Model Name - input
     *
     * ex. Articles
     *
     * @var    string
     * @since  1.0
     */
    protected $model_name;

    /**
     * Model Registry Name - input
     *
     * ex. ArticlesResource
     *
     * @var    string
     * @since  1.0
     */
    protected $model_registry_name;

    /**
     * Extension Instance ID - input
     *
     * ex. 16000 for Articles
     *
     * @var    string
     * @since  1.0
     */
    protected $extension_instance_id;

    /**
     * Data - input
     *
     * Different types of data, ex. application configuration, content, session data, etc.
     *
     * @var    object
     * @since  1.0
     */
    protected $data;

    /**
     * Parameters - input
     *
     * Depending on the Form, different sources of parameters, ex. configuration or resources, etc.
     *
     * @var    object
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * Parameter Field Definitions - input
     *
     * Used to create form fields
     *
     * @var    object
     * @since  1.0
     */
    protected $parameter_fields = array();

    /**
     * Metadata - input
     *
     * @var    object
     * @since  1.0
     */
    protected $metadata = array();

    /**
     * Parameter Field Definitions - input
     *
     * @var    object
     * @since  1.0
     */
    protected $metadata_fields = array();

    /**
     * Custom fields - input
     *
     * @var    object
     * @since  1.0
     */
    protected $customfields = array();

    /**
     * Customfield Field Definitions - input
     *
     * @var    object
     * @since  1.0
     */
    protected $customfields_fields = array();

    /**
     * Page Title - output
     *
     * Used when rendering the form page
     *
     * @var    object
     * @since  1.0
     */
    protected $page_title;

    /**
     * Page Title Extended - output
     *
     * @var    object
     * @since  1.0
     */
    protected $page_title_extended;

    /**
     * Page Link - output
     *
     * Can contain a URL (for multi-part form pages) or a hash tag (for tabs)
     *
     * @var    object
     * @since  1.0
     */
    protected $page_link;

    /**
     * Page Description
     *
     * @var    object
     * @since  1.0
     */
    protected $page_description;

    /**
     * Page Subtitle - output
     *
     * @var    string
     * @since  1.0
     */
    protected $page_subtitle;

    /**
     * Page Subtitle Description
     *
     * @var    object
     * @since  1.0
     */
    protected $page_subtitle_description;

    /**
     * Fieldset Title
     *
     * @var    object
     * @since  1.0
     */
    protected $fieldset_title;

    /**
     * Fieldset Descriptions
     *
     * @var    object
     * @since  1.0
     */
    protected $fieldset_description;

    /**
     * Fieldset Template Views
     *
     * @var    object
     * @since  1.0
     */
    protected $fieldset_template_view;

    /**
     * For Single or Multi-page(or tab) Forms
     *
     * @param string $pages_array - The request for a single or set of page form fieldsets to be generated
     *  Examples:
     *      Single Page {{Editor,editor}}
     *      Multi Pages {{Basic,basic}}{{Access,access}}{{Metadata,metadata}}{{Fields,customfields,Customfields}} ...
     *
     * @return array
     * @since  1.0
     */
    public function set($element, $value)
    {
        $this->$element = $value;

        return $this;
    }

    /**
     * Set the value of a specified property
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     * @since   1.0
     */
    public function setArray($type, $key, $value = null)
    {
        $this->$type[$key] = $value;

        return $this;
    }

    /**
     * Create Single or Multi-page(or tab) Form
     *
     * @param string $pages_array - The request for a single or set of page form fieldsets to be generated
     *  Examples:
     *      Single Page {{Editor,editor}}
     *      Multi Pages {{Basic,basic}}{{Access,access}}{{Metadata,metadata}}{{Fields,customfields,Customfields}} ...
     *
     * @return array
     * @since  1.0
     */
    public function execute($pages_array)
    {
        $temp = explode('}}', $pages_array);
        $configurationArray = array();

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

        $pages = array();
        foreach ($configurationArray as $config) {

            $split = explode(' ', $config);
            if (count($split) > 1) {
            } else {
                return false;
            }

            $this->page_title = Services::Language()->translate($split[0]);

            $this->page_title = str_replace(' ', '&nbsp;', htmlentities($this->page_title));

            $this->page_title_extended = ucfirst(strtolower($this->model_name))
                . ' '
                . Services::Language()->translate($split[0])
                . ' '
                . Services::Language()->translate($this->namespace);

            $this->page_title_extended = str_replace(' ', '&nbsp;', htmlentities($this->page_title_extended));

            $translate = strtoupper(
                strtoupper($this->namespace)
                    . '_FORM_'
                    . strtoupper(str_replace('&nbsp;', '_', $this->page_title))
                    . '_DESC'
            );

            $this->page_description = Services::Language()->translate($translate);

            $this->page_link = $split[1];

            if (count($split) == 3) {
                $this->fieldset_template_view = $split[2];
            } else {
                $this->fieldset_template_view = 'Formpage';
            }

            $this->page_subtitle = '';
            $this->page_subtitle_description = '';

            $this->setFieldset();

            $pageArray = 'page_title:' . $this->page_title
                . ',' . 'page_title_extended:' . $this->page_title_extended
                . ',' . 'page_namespace:' . $this->namespace
                . ',' . 'page_link:' . $this->namespace . $this->page_link
                . ',' . 'page_subtitle:' . $this->page_subtitle
                . ',' . 'page_subtitle_description:' . $this->page_subtitle_description
                . ',' . 'fieldset_template_view:' . $this->fieldset_template_view
                . ',' . 'fieldset_template_view_parameter:'
                . $this->fieldset_template_view . strtolower($this->namespace . $this->page_link);

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
        $pagesExpanded = array();

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
        $pagesExpanded[] = $row;

        return $pagesExpanded;
    }

    /**
     * Get Form Page Fieldsets
     *
     * @param $pages
     * @return array
     */
    public function getPages($pages, $page_count)
    {
        $page_array = array();
        $temp_array = array();
        $temp = explode('}}', $pages);

        foreach ($temp as $set) {
            $set = str_replace(',', ' ', $set);
            $set = str_replace(':', '=', $set);
            $set = str_replace('{{', '', $set);
            $set = str_replace('http=', 'http:', $set);
            if (trim($set) == '') {
            } else {
                $temp_array[] = trim($set);
            }
        }

        $current_page_number = count($temp_array);
        $current_page_number_word =
            Services::Text()->convertNumberToText($current_page_number, 0, 1);
        foreach ($temp_array as $set) {
            $fields = explode(' ', $set);
            foreach ($fields as $field) {
                $temp = explode('=', $field);
                $pairs[$temp[0]] = $temp[1];
            }

            $row = new \stdClass();
            foreach ($pairs as $key => $value) {
                $row->$key = $value;
                $row->current_page_number = $current_page_number;
                $row->current_page_number_word = $current_page_number_word;
                $row->total_page_count = $page_count;
            }
            $page_array[] = $row;
        }

        return $page_array;
    }

    /**
     * Get the current value (or default) of the specified property
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     * @since   1.0
     */
    protected function get($type, $key = null, $default = null)
    {
        if ($type == 'parameters') {
            if (isset($this->parameters[$key])) {
                return $this->parameters[$key];
            }

        } elseif ($type == 'metadata') {
            if (isset($this->metadata[$key])) {
                return $this->metadata[$key];
            }

        } elseif ($type == 'customfields') {
            if (isset($this->customfields[$key])) {
                return $this->customfields[$key];
            }

        } elseif ($key === null) {
            return $this->$type;
        }

        return $default;
    }

    /**
     * setFieldset - Processes a request for a fieldset
     *
     *  Uses namespace and page link to retrieve configuration file, ex. 'Configuration' + - + "views"
     *  Retrieves XML file Configuration_views.xml,
     *  Within for default value processes
     *  {{Page,form_parent*,form_theme*,form_page*}}{{Template,form_template*}}{{Wrap,form_wrap*}}{{Model,form_model*}}{{Page,item_parent* ...
     *
     * @return  string
     * @since   1.0
     */
    protected function setFieldset()
    {
        $configurationArray = array();

        if ($this->page_link == 'noformfields') {
            $configuration = '{{' . $this->page_link . ',' . strtolower($this->page_link) . '}}';

        } else {
             echo $this->namespace . '_' . strtolower($this->page_link);
            echo '<br>';
            $configuration = $this->get('parameters', $this->namespace . '_' . strtolower($this->page_link), '');
            if ($configuration == '') {
                return false;
            }
        }

        $temp = explode('}}', $configuration);
        if ($temp == null || count($temp) == 0) {
            $temp = array();
        }
        $fieldSets = array();

        if (count($temp) > 0) {
            foreach ($temp as $set) {
                $set = str_replace('{{', '', $set);

                if (trim($set) == '') {
                } else {
                    $configurationArray[] = trim($set);
                }
            }
        }
        echo '<pre>';
        var_dump($configurationArray);
        echo '</pre>';
        /**
        array(5) {
        [0]=>
        string(25) "Main,edit_main_*,Editmain"
        [1]=>
        string(48) "Categorization,categorization,Editcategorization"
        [2]=>
        string(30) "Metadata,metadata,Editmetadata"
        [3]=>
        string(36) "Publishing,publishing,Editpublishing"
        [4]=>
        string(30) "Versions,versions,Editversions"
        }
         */
        $page_first_row = 1;
        $page_subtitle_first_row = 1;

        if (count($configurationArray) > 0) {

            foreach ($configurationArray as $config) {

                echo '<pre>';
                var_dump($config);
                echo '</pre>';

                $i = 0;
                $options = explode(',', $config);
                if (count($options) > 1) {
                } else {
                    return false;
                }

                if (substr($options[$i], 0, strlen('subtitle:')) == 'subtitle:') {

                    $this->page_subtitle = str_replace(
                        ' ',
                        '&nbsp;',
                        htmlentities(
                            Services::Language()->translate(
                                substr($options[$i], strlen('subtitle:'), 9999)
                            ),
                            ENT_COMPAT,
                            'UTF-8'
                        )
                    );

                    $this->page_subtitle_description = Services::Language()->translate(
                        strtoupper(
                            strtoupper($this->namespace) . '_FORM_FIELDSET_'
                                . strtoupper(str_replace('&nbsp;', '_', $this->page_subtitle))
                                . '_DESC'
                        )
                    );

                    $page_subtitle_first_row = 1;

                    unset($options[$i]);
                    $i++;
                }

                $this->fieldset_title = str_replace(
                    ' ',
                    '&nbsp;',
                    htmlentities(
                        Services::Language()->translate(
                            $options[$i]
                        ),
                        ENT_COMPAT,
                        'UTF-8'
                    )
                );

                if ($this->fieldset_title == '') {
                    $this->fieldset_title = $this->page_title;
                }

                $this->fieldset_description = Services::Language()->translate(
                    strtoupper(
                        strtoupper($this->namespace) . '_FORM_FIELDSET_'
                            . strtoupper(str_replace('&nbsp;', '_', $this->page_title)) . '_'
                            . strtoupper(str_replace('&nbsp;', '_', $this->fieldset_title)) . '_DESC'
                    )
                );

                unset($options[$i]);

                $get = 'get' . ucfirst(strtolower($this->page_link));

                $temp = array();

                if ($this->namespace == 'noformfields') {

                    /** Only titles and name of view to be included (view will take care of data retrieval) */
                    $row = new \stdClass();

                    $row->page_title = $this->page_title;
                    $row->page_title_extended = $this->page_title_extended;
                    $row->page_description = $this->page_description;
                    $row->page_link =
                        ucfirst(strtolower($this->fieldset_template_view . $this->namespace . $this->page_link));

                    $row->page_subtitle = $this->page_subtitle;
                    $row->page_subtitle_description = $this->page_subtitle_description;
                    if ($this->page_subtitle == '') {
                        $row->page_subtitle_first_row = 0;
                    } else {
                        $row->page_subtitle_first_row = $page_subtitle_first_row;
                    }

                    $row->fieldset_title = $this->fieldset_title;
                    $row->fieldset_description = $this->fieldset_description;

                    $temp[] = $row;

                } else {

                    if (method_exists($this, 'get' . $this->page_link)) {
                        $temp = $this->$get($options);

                    } else {
                        $temp = $this->getParameters($options);
                    }
                }
echo '<pre>';
var_dump($temp);
echo '</pre>';

                if ($namespace == 'edit') { //but find better way
                }
                die;
                $write = array();

                if (count($temp) > 0) {

                    $new_fieldset = 1;

                    $fieldset_count = count($temp);
                    $fieldset_odd_or_even = 'odd';
                    $fieldset_row_number = 1;

                    foreach ($temp as $item) {

                        $item->page_title = $this->page_title;
                        $item->page_title_extended = $this->page_title_extended;
                        $item->page_description = $this->page_description;
                        $item->page_first_row = $page_first_row;

                        $item->page_subtitle = $this->page_subtitle;
                        $item->page_subtitle_description = $this->page_subtitle_description;
                        if ($this->page_subtitle == '') {
                            $item->page_subtitle_first_row = 0;
                        } else {
                            $item->page_subtitle_first_row = $page_subtitle_first_row;
                        }

                        $item->new_fieldset = $new_fieldset;

                        $item->fieldset_count = $fieldset_count;
                        $item->fieldset_odd_or_even = $fieldset_odd_or_even;
                        $item->fieldset_row_number = $fieldset_row_number;

                        $fieldset_row_number++;

                        $new_fieldset = 0;
                        $page_first_row = 0;
                        $page_subtitle_first_row = 0;

                        $write[] = $item;
                    }

                    if ($fieldset_odd_or_even == 'odd') {
                        $fieldset_odd_or_even = 'even';
                    } else {
                        $fieldset_odd_or_even = 'odd';
                    }
                }
                $fieldSets = array_merge((array)$fieldSets, (array)$write);
            }
        }

        Services::Registry()->set(
            'Plugindata',
            $this->fieldset_template_view . $this->namespace . strtolower($this->page_link),
            $fieldSets
        );

        return;
    }

    /**
     * Retrieves field definitions and current settings for requested parameters
     *
     * @return string
     * @since   1.0
     */
    protected function getParameters($options)
    {
        $fieldValues = array();
        $input_fields = array();

        foreach ($options as $value) {

            if (substr($value, strlen($value) - 1, 1) == '*') {
                $compare = substr($value, 0, strlen($value) - 1);
            } else {
                $compare = $value;
            }

            if (trim($compare) == '' || strlen($compare) == 0) {
            } else {

                foreach ($this->parameters as $key => $value) {

                    $use = false;

                    if ($key == $compare) {
                        $use = true;
                    }

                    if (substr(strtolower($key), 0, strlen($compare)) == $compare && strlen($compare) > 0) {
                        $use = true;
                    }

                    if ($use === true) {

                        $row = array();
                        foreach ($this->parameter_fields as $field) {

                            if ($field['name'] == $key) {
                                $row = $field;
                                break;
                            }
                        }

                        $row['name'] = $key;
                        $row['value'] = $value;

                        $row['page_title'] = $this->page_title;
                        $row['page_description'] = $this->page_description;

                        $row['page_subtitle'] = $this->page_subtitle;
                        $row['page_subtitle_description'] = $this->page_subtitle_description;
                        $row['page_subtitle_first_row'] = 0;

                        $row['fieldset_title'] = $this->fieldset_title;
                        $row['fieldset_description'] = $this->fieldset_description;

                        //todo defaults
                        $row['application_default'] = Services::Registry()->get('Configuration', $key);

                        $input_fields[] = $row;
                    }
                }
            }
        }

        if (count($input_fields) > 0) {
            $x = $this->setFields($input_fields);
        } else {
            return array();
        }

        return $x;
    }

    /**
     * Retrieves Custom Fields created for this Resource
     *
     * @param  string $options - Value used as a prefix to extract parameters which start with that value
     *
     * @return  string
     * @since   1.0
     */
    protected function getActualFields($options)
    {
        $fieldValues = array();
        $input_fields = array();

        $fieldArray = Services::Registry()->get($this->model_name . $this->model_type, 'Fields');
        $customfieldgroups = Services::Registry()->get($this->model_name . $this->model_type, 'customfieldgroups');

        foreach ($options as $value) {

            if (substr($value, strlen($value) - 1, 1) == '*') {
                $compare = substr($value, 0, strlen($value) - 1);
            } else {
                $compare = $value;
            }

            if (trim($compare) == '' || strlen($compare) == 0) {
            } else {

                foreach ($this->parameter_fields as $field) {

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

                        $field_name = $field['name'];

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
                                    $temp = Services::Registry()->get($this->model_name . $this->model_type, $custom);
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

                                $row['page_title'] = $this->page_title;
                                $row['page_description'] = $this->page_description;
                                $row['fieldset_title'] = $this->fieldset_title;
                                $row['fieldset_description'] = $this->fieldset_description;

                                if (isset($this->data->$field_name)) {
                                    $row['value'] = $this->data->$field_name;
                                } else {
                                    $row['value'] = null;
                                }

                                $row['application_default'] = Services::Registry()->get(
                                    'Configuration',
                                    $field['name']
                                );
                                $input_fields[] = $row;
                            }
                        }
                    }
                }
            }
        }

        if (count($input_fields) > 0) {
            return $this->setFields($input_fields);
        } else {
            return array();
        }
    }

    /**
     * Retrieves Metadata definitions and current configuration values
     *
     * @param  string $options - Value used as a prefix to extract parameters which start with that value
     *
     * @return  string
     * @since   1.0
     */
    protected function getMetadata($options)
    {
        $input_fields = array();

        foreach ($this->metadata_fields as $field) {

            $row = $field;

            $row['page_title'] = $this->page_title;
            $row['page_description'] = $this->page_description;
            $row['fieldset_title'] = $this->fieldset_title;
            $row['fieldset_description'] = $this->fieldset_description;

            $row['value'] = $this->metadata_fields($field['name']);

            $row['application_default'] = Services::Registry()->get('Configuration', 'metadata_' . $field['name']);

            $input_fields[] = $row;
        }

        if (count($input_fields) > 0) {
            return $this->setFields($input_fields);
        } else {
            return array();
        }
    }

    /**
     * Retrieves Metadata definitions and current configuration values
     *
     * @param  string $options - Value used as a prefix to extract parameters which start with that value
     *
     * @return  string
     * @since   1.0
     */
    protected function getGrid($options)
    {
        if (Services::Registry()->exists('GridMenuitem') === true) {
        } else {

            $item = Helpers::Content()->getResourceMenuitemParameters('Grid', $this->extension_instance_id);

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

                        $row['page_title'] = $this->page_title;
                        $row['page_description'] = $this->page_description;
                        $row['fieldset_title'] = $this->fieldset_title;
                        $row['fieldset_description'] = $this->fieldset_description;

                        $row['value'] = Services::Registry()->get('GridMenuitemParameters', $field['name']);

                        $row['application_default'] = Services::Registry()->get('Configuration', $field['name']);
                        $input_fields[] = $row;
                    }
                }
            }
        }

        if (count($input_fields) > 0) {
            return $this->setFields($input_fields);
        } else {
            return array();
        }
    }

    /**
     * Retrieves Metadata definitions and current configuration values
     *
     * @param  string $options - Value used as a prefix to extract parameters which start with that value
     *
     * @return array
     * @since   1.0
     */
    protected function getCustomfields($options)
    {
        $input_fields = array();

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

                    $this->fieldset_title = str_replace(
                        ' ',
                        '&nbsp;',
                        htmlentities(ucfirst(strtolower('Create')), ENT_COMPAT, 'UTF-8')
                    );

                    $this->fieldset_description = Services::Language()->translate(
                        strtoupper(
                            strtoupper($this->namespace) . '_FORM_FIELDSET_'
                                . strtoupper(str_replace('&nbsp;', '_', $this->page_title)) . '_'
                                . strtoupper(str_replace('&nbsp;', '_', $this->fieldset_title)) . '_DESC'
                        )
                    );

                    $row = $field;
                    $row['page_title'] = $this->page_title;
                    $row['page_description'] = $this->page_description;
                    $row['fieldset_title'] = $this->fieldset_title;
                    $row['fieldset_description'] = $this->fieldset_description;
                    $row['value'] = null;
                    $row['first_following'] = 0;
                    $row['customfield_type'] = 'Create';

                    $input_fields[] = $row;
                }
            }
        }

        $this->model_registry_name = ucfirst(strtolower($this->model_name)) . ucfirst(strtolower($this->model_type));

        $custom_fields = array();
        $custom_fields[] = 'metadata';
        $custom_fields[] = 'customfields';
        $custom_fields[] = 'parameters';

        $first = 1;

        $temp = Services::Registry()->get($this->model_registry_name, 'customfieldgroups');
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

            $this->fieldset_title = str_replace(
                ' ',
                '&nbsp;',
                htmlentities(ucfirst(strtolower($custom_field)), ENT_COMPAT, 'UTF-8')
            );

            $this->fieldset_description = Services::Language()->translate(
                strtoupper(
                    strtoupper($this->namespace) . '_FORM_FIELDSET_'
                        . strtoupper(str_replace('&nbsp;', '_', $this->page_title)) . '_'
                        . strtoupper(str_replace('&nbsp;', '_', $this->fieldset_title)) . '_DESC'
                )
            );

            $fields = Services::Registry()->get($this->model_registry_name, $custom_field);

            if ((int)count($fields) === 0 || $fields === false) {
            } else {
                foreach ($fields as $field) {

                    if ($field['field_inherited'] == 1) {
                    } else {

                        $field['page_title'] = $this->page_title;
                        $field['page_description'] = $this->page_description;
                        $field['fieldset_title'] = $this->fieldset_title;
                        $field['fieldset_description'] = $this->fieldset_description;
                        $field['first_following'] = $first;
                        $first = 0;
                        $field['customfield_type'] = $custom_field;

                        $input_fields[] = $field;
                    }
                }
            }
        }

        if (count($input_fields) > 0) {
            return $this->setFields($input_fields);
        } else {
            return array();
        }
    }

    /**
     * setFields - builds two sets of data:
     *
     *  1. Fieldsets: collection of the names of fields to be used to create field-specific include statements
     *  2. Fields: Field-specific registries which define attributes input to the template field creation view
     *
     * @param $input_fields
     *
     * @return array
     * @since  1.0
     */
    protected function setFields($input_fields)
    {
        $first = true;
        $fieldset = array();

        foreach ($input_fields as $field) {

            $row = new \stdClass();

            $row->page_title = $field['page_title'];
            $row->page_description = $field['page_description'];
            $row->fieldset_title = $field['fieldset_title'];
            $row->fieldset_description = $field['fieldset_description'];

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
                $field['value'] = null;
            }
            $row->value = $field['value'];

            if (isset($field['null'])) {
            } else {
                $field['null'] = 0;
            }
            if ((int)$field['null'] === 1) {
                $row->required = Services::Language()->translate('N');
            } else {
                $row->required = Services::Language()->translate('Y');
            }

            if (isset($field['hidden'])) {
            } else {
                $field['hidden'] = 0;
            }

            if ((int)$field['hidden'] === 1) {
                $row->hidden = Services::Language()->translate('Y');
            } else {
                $row->hidden = Services::Language()->translate('N');
            }

            if (isset($field['application_default'])) {
            } else {
                $field['application_default'] = null;
            }

            if (isset($field['default'])) {
            } else {
                $field['default'] = null;
            }

            if (($field['application_default'] === null || $field['application_default'] == ' ')
                && ($field['default'] === null || $field['default'] == ' ')
            ) {
                $row->default_message = Services::Language()->translate('No default value defined.');
                $row->default = null;

            } elseif ($field['application_default'] === null || $field['application_default'] == ' ') {

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
                echo $field['name'] . ' unknown type' . '<br />';
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
                    echo 'FORMSERVICE: WHAT IS THIS TYPE? (changing to text) ' . $row->type . ' Name: ' . $row->name . '<br />';
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
                    $row->name = $this->setRadioField($field, $row);
                    break;

                case 'formselect':
                    $row->name = $this->setSelectField($field, $row);
                    break;

                case 'formtextarea':
                    $row->name = $this->setTextareaField($field, $row);
                    break;

                default:
                    $row->name = $this->setInputField($field, $row);
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
     * @param $field
     * @param $row_start
     *
     * @return string
     * @since  1.0
     */
    protected function setInputField($field, $row_start)
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

        $registryName = $this->namespace . strtolower($this->page_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }

    /**
     * setRadioField field
     *
     * @param $field
     * @param $row_start
     *
     * @return string
     * @since  1.0
     */
    protected function setRadioField($field, $row_start)
    {
        $fieldRecordset = array();

        if ($field['null'] == 1) {
            $required = ' required';
        } else {
            $required = '';
        }

        /** Yes */
        $row = new \stdClass();

        foreach ($row_start as $rkey => $rvalue) {
            $row->$rkey = $rvalue;
        }

        if ($field['value'] == null) {
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

        $registryName = $this->namespace . strtolower($this->page_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }

    /**
     * setSelectField field
     *
     * @param $field
     * @param $row_start
     *
     * @return mixed|string
     * @somce  1.0
     */
    protected function setSelectField($field, $row_start)
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
            if ((int)$field['size'] > 1) {
                $size = ' size="' . $field['size'] . '"';
            }
        }

        $temp = $field['value'];
        $default_setting = 0;
        if ($temp === null) {
            $temp = $field['default'];
            $default_setting = 1;
        }

        if ($field['name'] == 'robots') {
            $selectedArray = array($temp);
        } else {
            $selectedArray = explode(',', $temp);
        }

        $datalist = $field['datalist'];

        $yes = 0;
        if (strtolower($datalist) == 'fields') {
            $list = Services::Registry()->get('Datalist', $this->model_registry_name . 'Fields');

        } elseif (strtolower($datalist) == 'fieldsstandard') {
            $this->model_registry_name
                = ucfirst(strtolower($this->model_name)) . ucfirst(strtolower($this->model_type));
            $list = Services::Registry()->get('Datalist', $this->model_registry_name . 'Fieldsstandard');

        } else {
            $results = Services::Text()->getList($datalist, array());
            $list = $results[0]->listitems;
            $multiple = $results[0]->multiple;
            $size = $results[0]->size;
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
        $registryName = $this->namespace . strtolower($this->page_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }

    /**
     * setTextareaField field
     *
     * @return array
     */
    protected function setTextareaField($field, $row_start)
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
        $registryName = $this->namespace . strtolower($this->page_link) . $row->name;
        $registryName = str_replace('_', '', $registryName);

        Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

        return $registryName;
    }
}
