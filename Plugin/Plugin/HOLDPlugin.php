<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Plugin;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Plugin
 *
 * Base class for Plugins
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class Plugin
{
    /**
     * Called Class
     *
     * @var    string
     * @since  1.0
     */
    protected $class;

    /**
     * Event Processing
     *
     * @var    string
     * @since  1.0
     */
    protected $event;

    /**
     * Instance of Model Properties from Controller/Model
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry;

    /**
     * Registry Name - can be used to retrieve table parameters
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry_name;

    /**
     * Model type
     *
     * @var    object
     * @since  1.0
     */
    protected $model_type;

    /**
     * Model name
     *
     * @var    object
     * @since  1.0
     */
    protected $model_name;

    /**
     * Parameters set by the Includer and used in the MVC to render output or process data
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
    protected $data;

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
     * Used in post-render View plugins, contains output rendered from view
     *
     * @var    object
     * @since  1.0
     */
    protected $rendered_output;

    /**
     * Get the current value (or default) of the specified Model property
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        $value = null;

        if (in_array($key,
            array('class',
                 'event',
                'model_registry',
                'model',
                'model_type',
                'model_name',
                'parameters',
                'query',
                'db',
                'data',
                'null_date',
                'now',
                'fields',
                'customfieldgroups',
                'rendered_output'))
            && (isset($this->$key))
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
     * Set the value of a property
     *
     * Initially, the setter is used by the Event processPluginClass method
     *  to establish initial property values sent in by the scheduling method
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        if (in_array($key,
            array('class',
                'event',
                'model',
                'model_registry',
            'model_type',
            'model_name',
            'parameters',
            'query',
            'db',
            'data',
            'null_date',
            'now',
            'fields',
            'customfieldgroup',
            'rendered_output'))
        ) {

            if ($key == 'parameters') {

                foreach ($value as $k => $v) {

                    if (is_string($k)) {
                        $this->parameters[$k] = $v;

                    } else {
                        echo '<pre>';
                        var_dump($k);
                        echo '</pre>';
                        die;
                        throw new \RuntimeException('Invalid Key for Parameter Array');
                    }
                }

            } else {
                $this->$key = $value;
            }

        } else {
            $this->parameters[$key] = $value;
        }

        return;
    }

    /**
     * Unload fields for plugin use
     *
     * @return  mixed
     * @since   1.0
     */
    public function setFields()
    {
        $this->fields = array();

        if ($this->model_name == null || $this->model_type == null) {
        }  else {
            $this->model_name = ucfirst(strtolower($this->model_name));
            $this->model_type = ucfirst(strtolower($this->model_type));
        }

        if ($this->model_registry_name === null) {
            if ($this->model_name == null || $this->model_type == null) {
                return $this;
            } else {
                $this->model_registry_name = $this->model_name  . $this->model_type;
            }
        }

        $fields = Services::Registry()->get($this->model_registry_name, FIELDS_LITERAL);

        if (count($fields) == 0) {
            return true;
        }
        $this->processFieldType($type = '', $fields);

        $this->customfieldgroups = Services::Registry()->get($this->model_registry_name, CUSTOMFIELDGROUPS_LITERAL, array());

        if (is_array($this->customfieldgroups) && count($this->customfieldgroups) > 0) {
            foreach ($this->customfieldgroups as $customFieldName) {
                $customFieldName = strtolower($customFieldName);
                $fields = Services::Registry()->get($this->model_registry_name, $customFieldName);
                $this->processFieldType($customFieldName, $fields);
            }
        }

        /** join fields */
        $joinfields = Services::Registry()->get($this->model_registry_name, 'JoinFields');
        if (is_array($joinfields) && count($joinfields) > 0) {
            $this->processFieldType('JoinFields', $joinfields);
        }

        /** foreign keys */
        $foreignkeys = Services::Registry()->get($this->model_registry_name, 'foreignkeys');
        if (is_array($foreignkeys) && count($foreignkeys) > 0) {
            $this->processFieldType('foreignkeys', $foreignkeys);
        }

        /** children */
        $childkeys = Services::Registry()->get($this->model_registry_name, 'children');
        if (is_array($childkeys) && count($childkeys) > 0) {
            $this->processFieldType('children', $childkeys);
        }

         echo $this->event . ' ' . $this->model_registry_name . ' '
             . $this->class . ' ' . count($this->fields) . ' '
             . 'Process Plugins ' . Services::Registry()->get($this->model_registry_name, 'process_plugins')
             . '<br /> ';

        return $this;
    }

    /**
     * retrieveFieldsByType processes an array of fields, populating the class property
     *
     * @return  boolean
     * @since   1.0
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
     * @return  boolean
     * @since   1.0
     */
    public function getField($name)
    {
        foreach ($this->fields as $field) {

            if ((int) $field->foreignkey = 0) {

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
     * @param   $field
     *
     * @return  bool
     * @since   1.0
     */
    public function getFieldValue($field)
    {

        if (is_object($field)) {
        } else {
            return false;
        }

        if (isset($field->as_name)) {
            if ($field->as_name == '') {
                $name = $field->name;
            } else {
                $name = $field->as_name;
            }
        } else {
            $name = $field->name;
        }

        if (isset($this->data->$name)) {
            return $this->data->$name;

        } elseif (isset($field->customfield)) {
            if (Services::Registry()->exists($this->get('model_name', '', 'parameters') . $field->customfield, $name)) {
                return Services::Registry()->get($this->get('model_name', '', 'parameters') . $field->customfield, $name);
            }
        }

        return false;
    }

    /**
     * saveField adds a field to the 'normal' or special field group
     *
     * @param   $field
     * @param   $new_field_name
     * @param   $value
     *
     * @return  void
     * @since   1.0
     */
    public function saveField($field, $new_field_name, $value)
    {
        if (is_object($field)) {
            $name = $field->name;
        } else {
            $name = $new_field_name;
        }

        if (isset($this->data->$name)) {
            $this->data->$name = $value;

        } elseif (isset($this->parameters[$name])) {
            $this->parameters[$name] = $value;

        } else {
            if (is_object($this->data)) {
            } else {
                $this->data = new \stdClass();
            }
            $this->data->$new_field_name = $value;
        }

        return;
    }

    /**
     * saveForeignKeyValue
     *
     * @param   $new_field_name
     * @param   $value
     *
     * @return  void
     * @since   1.0
     */
    public function saveForeignKeyValue($new_field_name, $value)
    {
        if (isset($this->data->$new_field_name)) {
            return;
        }

        $this->data->$new_field_name = $value;

        return;
    }

    /**
     * processFieldType processes an array of fields, populating the class property
     *
     * @return  boolean
     * @since   1.0
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

            /** null */
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
            if (isset($fields[$key]['table_name'])) {
                $row->table = $fields[$key]['table_name'];
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
                $row->customfield = '';
                $row->foreignkey = 0;

            } elseif ($type == 'foreignkeys') {
                $row->customfield = '';
                $row->foreignkey = 1;
                $row->type = $type;

            } else {
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
     * Runs before Route and after Services and Helpers have been instantiated
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterInitialise()
    {
        return true;
    }

    /**
     * Scheduled after Route has been determined. Parameters contain all instruction to produce primary request.
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        return true;
    }

    /**
     * Scheduled after core Authorise to augment, change authorisation process or override a failed test
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterAuthorise()
    {
        return true;
    }

    /**
     * After Route and Permissions,  the Theme/Page are parsed
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        return true;
    }

    /**
     * Pre-read processing
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeRead()
    {
        return true;
    }

    /**
     * Post-read processing - one row at a time
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        return true;
    }

    /**
     * Post-read processing - all rows at one time from query_results
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterReadall()
    {
        return true;
    }

    /**
     * After the Read Query has executed but Before Query results are injected into the View
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeViewRender()
    {
        return true;
    }


    /**
     * After the View has been rendered but before the output has been passed back to the Includer
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterViewRender()
    {
        return true;
    }

    /**
     * Document parsing and rendering for document body complete, document head has not started
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterParseBody()
    {
        return true;
    }

    /**
     * After list of Head Include tags has been loaded and before the parsing begins
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeParseHead()
    {
        return true;
    }

    /**
     * After all document parsing, body and head, has been accomplished and include tags replaced with rendered output
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterParse()
    {
        return true;
    }

    /**
     * Event fires after execute for both display and non-display task
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterExecute()
    {
        return true;
    }

    /**
     * Plugin that fires after all views are rendered
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterResponse()
    {
        return true;
    }

    /**
     * Pre-create processing
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
     * @return  boolean
     * @since   1.0
     */
    public function onAfterCreate()
    {
        return true;
    }

    /**
     * Before update processing
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        return true;
    }

    /**
     * After update processing
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
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeDelete()
    {
        return true;
    }

    /**
     * Post-delete processing
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterDelete()
    {
        return true;
    }
}
