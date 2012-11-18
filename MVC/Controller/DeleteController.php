<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\MVC\Controller;

use Molajo\Service\Services;
use Molajo\MVC\Controller;

defined('MOLAJO') or die;

/**
 * Delete
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class DeleteController extends Controller
{
    /**
     * Delete row and plugin other delete actions
     *
     * @return bool|object
     * @since  1.0
     */
    public function delete()
    {
        /** tokens */

        if (isset($this->data->model_name)) {
        } else {
            return false;
        }

        $results = $this->getDeleteData();
        if ($results === false) {
            return false;
        }

        $results = $this->verifyPermissions();
        if ($results === false) {
            //error
            //return false (not yet)
        }

        parent::getPluginList('delete');

        $valid = $this->onBeforeDeleteEvent();
        if ($valid === false) {
            return false;
            //error
        }

        if ($valid === true) {

            $this->connect('Datasource', $this->data->model_name, 'DeleteModel');
            $results = $this->model->delete($this->data, $this->model_registry);

            if ($results === false) {
            } else {
                $this->data->id = $results;
                $results = $this->onAfterDeleteEvent();
                if ($results === false) {
                    return false;
                    //error
                }
                $results = $this->data->id;
            }
        }

        /** redirect */
        if ($valid === true) {
            if ($this->get('redirect_on_success', '') == '') {

            } else {
                Services::Redirect()->url
                    = Services::Url()->getURL($this->get('redirect_on_success'));
                Services::Redirect()->code == 303;
            }

        } else {
            if ($this->get('redirect_on_failure', '') == '') {

            } else {
                Services::Redirect()->url
                    = Services::Url()->getURL($this->get('redirect_on_failure'));
                Services::Redirect()->code == 303;
            }
        }

        return $results;
    }

    /**
     * Retrieve data to be deleted
     *
     * @param string $connect
     *
     * @return bool|mixed
     * @since  1.0
     */
    public function getDeleteData()
    {
        $hold_model_name = $this->data->model_name;
        $this->connect('Datasource', $hold_model_name);

        $this->set('use_special_joins', 0);
        $name_key = $this->get('name_key');
        $primary_key = $this->get('primary_key');
        $primary_prefix = $this->get('primary_prefix', 'a');

        if (isset($this->data->$primary_key)) {
            $this->model->query->where($this->model->db->qn($primary_prefix) . '.' . $this->model->db->qn($primary_key)
                . ' = ' . $this->model->db->q($this->data->$primary_key));

        } elseif (isset($this->data->$name_key)) {
            $this->model->query->where($this->model->db->qn($primary_prefix) . '.' . $this->model->db->qn($name_key)
                . ' = ' . $this->model->db->q($this->data->$name_key));

        } else {
            //only deletes single rows
            return false;
        }

        if (isset($this->data->catalog_type_id)) {
            $this->model->query->where($this->model->db->qn($primary_prefix)
                . '.' . $this->model->db->qn('catalog_type_id')
                . ' = ' . $this->model->db->q($this->data->catalog_type_id));
        }

        $item = $this->getData(QUERY_OBJECT_ITEM);
//		echo '<br /><br /><br />';
//		echo $this->model->query->__toString();
//		echo '<br /><br /><br />';

        if ($item === false) {
            //error
            return false;
        }

        $fields = Services::Registry()->get($this->model_registry, 'fields');
        if (count($fields) == 0 || $fields === null) {
            return false;
        }

        $this->data = new \stdClass();
        foreach ($fields as $f) {
            foreach ($f as $key => $value) {
                if ($key == 'name') {
                    if (isset($item->$value)) {
                        $this->data->$value = $item->$value;
                    } else {
                        $this->data->$value = null;
                    }
                }
            }
        }

        if (isset($item->catalog_id)) {
            $this->data->catalog_id = $item->catalog_id;
        }
        $this->data->model_name = $hold_model_name;

        /** Process each field namespace  */
        $customFieldTypes = Services::Registry()->get($this->model_registry, 'CustomFieldGroups');

        if (count($customFieldTypes) > 0) {
            foreach ($customFieldTypes as $customFieldName) {
                $customFieldName = ucfirst(strtolower($customFieldName));
                Services::Registry()->merge($this->model_registry . $customFieldName, $customFieldName);
                Services::Registry()->deleteRegistry($this->model_registry . $customFieldName);
            }
        }

        return true;
    }

    /**
     * verifyPermissions for Delete
     *
     * @return bool
     * @since  1.0
     */
    protected function verifyPermissions()
    {
        //todo - figure out what joining isn't working, get catalog id
        //$results = Services::Authorisation()->verifyTask('Delete', $this->data->catalog_id);
        //if ($results === false) {
        //error
        //return false (not yet)
        //}
        return true;
    }

    /**
     * Schedule onBeforeDeleteEvent Event - could update model and data objects
     *
     * @return boolean
     * @since   1.0
     */
    protected function onBeforeDeleteEvent()
    {
        if (count($this->plugins) == 0
            || (int) $this->get('process_plugins') == 0
        ) {
            return true;
        }

        $arguments = array(
            'model_registry' => $this->model_registry,
            'db' => $this->model->db,
            'data' => $this->data,
            'null_date' => $this->model->null_date,
            'now' => $this->model->now,
            'parameters' => $this->parameters,
            'model_type' => $this->get('model_type'),
            'model_name' => $this->get('model_name')
        );

        Services::Profiler()->set('DeleteController->onBeforeDeleteEvent Schedules onBeforeDelete', LOG_OUTPUT_PLUGINS, VERBOSE);

        $arguments = Services::Event()->schedule('onBeforeDelete', $arguments, $this->plugins);
        if ($arguments === false) {
            Services::Profiler()->set('DeleteController->onBeforeDelete failed.', LOG_OUTPUT_PLUGINS, VERBOSE);

            return false;
        }

        Services::Profiler()->set('DeleteController->onBeforeDeleteEvent succeeded.', LOG_OUTPUT_PLUGINS, VERBOSE);

        /** Process results */
        $this->parameters = $arguments['parameters'];
        $this->data = $arguments['data'];

        return true;
    }

    /**
     * Schedule onAfterDeleteEvent Event
     *
     * @return boolean
     * @since   1.0
     */
    protected function onAfterDeleteEvent()
    {
        if (count($this->plugins) == 0
            || (int) $this->get('process_plugins') == 0
        ) {
            return true;
        }

        /** Schedule onAfterDelete Event */
        $arguments = array(
            'model_registry' => $this->model_registry,
            'db' => $this->model->db,
            'data' => $this->data,
            'parameters' => $this->parameters,
            'model_type' => $this->get('model_type'),
            'model_name' => $this->get('model_name')
        );

        Services::Profiler()->set('CreateController->onAfterDeleteEvent Schedules onAfterDelete', LOG_OUTPUT_PLUGINS, VERBOSE);

        $arguments = Services::Event()->schedule('onAfterDelete', $arguments, $this->plugins);
        if ($arguments === false) {
            Services::Profiler()->set('DeleteController->onAfterDelete failed.', LOG_OUTPUT_PLUGINS, VERBOSE);

            return false;
        }

        Services::Profiler()->set('DeleteController->onAfterDelete succeeded.', LOG_OUTPUT_PLUGINS, VERBOSE);

        /** Process results */
        $this->parameters = $arguments['parameters'];
        $this->data = $arguments['data'];

        return true;
    }
}
