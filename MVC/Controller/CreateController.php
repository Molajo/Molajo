<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\MVC\Controller;

use Molajo\Service\Services;
use Molajo\MVC\Controller\Controller;

defined('MOLAJO') or die;

/**
 * Create
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class CreateController extends Controller
{
    /**
     * create new row
     *
     * @return bool|object
     * @since  1.0
     */
    public function execute()
    {
        /** tokens */

        if (isset($this->row->model_type)) {
        } else {
            $this->row->model_type = DATA_SOURCE_LITERAL;
        }
        if (isset($this->row->model_name)) {
        } else {
            return false;
        }

        $this->connect($this->row->model_type, $this->row->model_name, 'CreateModel');
        if (isset($this->row->catalog_type_id) && (int) $this->row->catalog_type_id > 0) {
        } else {
            $this->row->catalog_type_id = Services::Registry()->get($this->model_registry, 'catalog_type_id');
        }

        $results = $this->verifyPermissions();
        if ($results === false) {
            //error
            //return false (not yet)
        }

        parent::getPluginList('create');

        $valid = $this->onBeforeCreateEvent();
        if ($valid === false) {
            return false;
            //error
        }

        $valid = $this->checkFields();
        if ($valid === false) {
            return false;
            //error
        }

        $value = $this->checkForeignKeys();

        if ($valid === true) {

            $fields = Services::Registry()->get($this->model_registry, FIELDS_LITERAL);

            if (count($fields) == 0 || $fields === null) {
                return false;
            }

            $data = new \stdClass();
            foreach ($fields as $f) {
                foreach ($f as $key => $value) {
                    if ($key == 'name') {
                        if (isset($this->row->$value)) {
                            $data->$value = $this->row->$value;
                        } else {
                            $data->$value = null;
                        }
                    }
                }
            }

            $results = $this->model->create($data, $this->model_registry);

            if ($results === false) {
            } else {
                $data->id = $results;
                $results = $this->onAfterCreateEvent($data);
                if ($results === false) {
                    return false;
                    //error
                }
                $results = $data->id;
            }
        }

        /** redirect */
        if ($valid === true) {
            if ($this->get('redirect_on_success', '', 'parameters') == '') {

            } else {
                Services::Redirect()->url
                    = Services::Url()->get(null, null, $this->get('redirect_on_success', '', 'parameters'));
                Services::Redirect()->code == 303;
            }

        } else {
            if ($this->get('redirect_on_failure', '') == '') {

            } else {
                Services::Redirect()->url
                    = Services::Url()->get(null, null, $this->get('redirect_on_failure', '', 'parameters'));
                Services::Redirect()->code == 303;
            }
        }

        return $results;
    }

    /**
     * verifyPermissions for Create
     *
     * @return bool
     * @since  1.0
     */
    protected function verifyPermissions()
    {

        if (isset($this->row->primary_category_id)) {
            $results = Services::Permissions()->verifyTask('Create', $this->row->primary_category_id);
            if ($results === true) {
                return true;
            }
        }

        $results = Services::Permissions()->verifyTask('Create', $this->row->catalog_type_id);
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

        $userHTMLFilter = Services::Permissions()->setHTMLFilter();

        /** Custom Field Groups */
        $customfieldgroups = Services::Registry()->get(
            $this->model_registry, CUSTOMFIELDGROUPS_LITERAL, array());

        if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {

            foreach ($customfieldgroups as $customFieldName) {

                /** For this Custom Field Group (ex. Parameters, metadata, etc.) */
                $customFieldName = strtolower($customFieldName);
                if (isset($this->row->$customFieldName)) {
                } else {
                    $this->row->$customFieldName = '';
                }

                /** Retrieve Field Definitions from Registry (XML) */
                $fields = Services::Registry()->get($this->model_registry, $customFieldName);

                /** Shared processing  */
                $valid = $this->processFieldGroup($fields, $userHTMLFilter, $customFieldName);

                if ($valid === true) {
                } else {
                    return false;
                }
            }
        }

        /** Standard Field Group */
        $fields = Services::Registry()->get($this->model_registry, FIELDS_LITERAL);
        if (count($fields) == 0 || $fields === null) {
            return false;
        }

        $valid = $this->processFieldGroup($fields, $userHTMLFilter, '');
        if ($valid === true) {
        } else {
            return false;
        }

        Services::Profiler()->set('CreateController::checkFields Filter::Success: ' . $valid, PROFILER_ACTIONS);

        return $valid;
    }

    /**
     * processFieldGroup - runs custom filtering, defaults, validation for a field group
     *
     * @param $fields
     * @param $userHTMLFilter
     * @param string $customFieldName
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
            $inputArray = array();
            $inputArray = $this->row->$customFieldName;
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

            if (isset($f['identity'])) {
                $identity = $f['identity'];
            } else {
                $identity = 0;
            }

            /** Retrieve value from data */
            if ($customFieldName == '') {

                if (isset($this->row->$name)) {
                    $value = $this->row->$name;
                } else {
                    $value = null;
                }

            } else {

                if (isset($inputArray[$name])) {
                    $value = $inputArray[$name];
                } else {
                    $value = null;
                }
            }

            if ($type == null || $type == 'customfield' || $type == QUERY_OBJECT_LIST) {

            } elseif ($type == 'text' && $userHTMLFilter === false) {

            } elseif ($identity == '1') {

            } else {

                try {
                    /** Filters, sets defaults, and validates */
                    $value = Services::Filter()->filter($value, $type, $null, $default);

                    if ($customFieldName == '') {
                        $this->row->$name = trim($value);

                    } else {

                        $fieldArray[$name] = trim($value);
                    }

                } catch (\Exception $e) {

                    echo 'CreateController::checkFields Filter Failed ';
                    echo 'Fieldname: ' . $name . ' Value: ' . $value . ' Type: ' . $type . ' null: ' . $null . ' Default: ' . $default . '<br /> ';
                    die;
                }
            }
        }

        if ($customFieldName == '') {
        } else {
            ksort($fieldArray);
            $this->row->$customFieldName = $fieldArray;
        }

        Services::Profiler()->set('CreateController::checkFields Filter::Success: ' . $valid, PROFILER_ACTIONS);

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
        $foreignkeys = Services::Registry()->get($this->model_registry, 'foreignkeys');

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
            if (isset($this->row->$name)) {
            } else {
                if ((int) $required == 0) {
                    return true;
                }
                // error
                return false;
            }

            if (isset($this->row->$name)) {

                $controllerClass = CONTROLLER_CLASS;
                $controller = new $controllerClass();
                $controller->getModelRegistry(DATA_SOURCE_LITERAL, $source_model);

                $controller->model->query->select('COUNT(*)');
                $controller->model->query->from($controller->model->db->qn($controller->get('table_name')));
                $controller->model->query->where($controller->model->db->qn($source_id)
                    . ' = ' . (int) $this->row->$name);

                $controller->set('get_customfields', 0, 'model_registry');
                $controller->set('get_item_children', 0, 'model_registry');
                $controller->set('use_special_joins', 0, 'model_registry');
                $controller->set('check_view_level_access', 0, 'model_registry');
                $controller->set('process_plugins', 0, 'model_registry');

                $value = $controller->getData(QUERY_OBJECT_RESULT);

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
     * Schedule Event onBeforeCreateEvent Event - could update model and data objects
     *
     * @return boolean
     * @since   1.0
     */
    protected function onBeforeCreateEvent()
    {
        if (count($this->plugins) == 0
            || (int) $this->get('process_plugins') == 0
        ) {
            return true;
        }

        $arguments = array(
            'model_registry' => $this->model_registry,
            'db' => $this->model->db,
            'data' => $this->row,
            'null_date' => $this->model->null_date,
            'now' => $this->model->now,
            'parameters' => $this->parameters,
            'model_type' => $this->get('model_type'),
            'model_name' => $this->get('model_name')
        );

        Services::Profiler()->set('CreateController->onBeforeCreateEvent Schedules onBeforeCreate', PROFILER_PLUGINS, VERBOSE);

        $arguments = Services::Event()->scheduleEvent('onBeforeCreate', $arguments, $this->plugins);

        if ($arguments === false) {
            Services::Profiler()->set('CreateController->onBeforeCreateEvent failed.', PROFILER_PLUGINS, VERBOSE);

            return false;
        }

        Services::Profiler()->set('CreateController->onBeforeCreateEvent successful.', PROFILER_PLUGINS, VERBOSE);

        $this->parameters = $arguments['parameters'];
        $this->row = $arguments['row'];

        return true;
    }

    /**
     * Schedule Event onAfterCreateEvent Event
     *
     * @return boolean
     * @since   1.0
     */
    protected function onAfterCreateEvent($data)
    {
        if (count($this->plugins) == 0
            || (int) $this->get('process_plugins') == 0
        ) {
            return true;
        }

        /** Schedule Event onAfterCreate Event */
        $arguments = array(
            'model_registry' => $this->model_registry,
            'db' => $this->model->db,
            'data' => $data,
            'parameters' => $this->parameters,
            'model_type' => $this->get('model_type'),
            'model_name' => $this->get('model_name')
        );

        Services::Profiler()->set('CreateController->onAfterCreateEvent Schedules onAfterCreate', PROFILER_PLUGINS, VERBOSE);

        $arguments = Services::Event()->scheduleEvent('onAfterCreate', $arguments, $this->plugins);

        if ($arguments === false) {
            Services::Profiler()->set('CreateController->onAfterCreateEvent failed.', PROFILER_PLUGINS, VERBOSE);

            return false;
        }

        Services::Profiler()->set('CreateController->onAfterCreateEvent successful.', PROFILER_PLUGINS, VERBOSE);

        $this->parameters = $arguments['parameters'];
        $data = $arguments['row'];

        return $data;
    }
}
