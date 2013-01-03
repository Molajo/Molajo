<?php
/**
 * Delete Controller
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\MVC\Controller;

use Molajo\Service\Services;
use Molajo\MVC\Controller;

defined('NIAMBIE') or die;

/**
 * The delete controller uses model registry data and HTTP post variables to verifying foreign key restraints,
 * and permissions, etc, archive version history, and delete data. The delete controller also schedules the
 * before and after delete event.
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
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

        if (isset($this->row->model_name)) {
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

            $this->connect('datasource', $this->row->model_name, 'DeleteModel');
            $results = $this->model->delete($this->row, $this->model_registry);

            if ($results === false) {
            } else {
                $this->row->id = $results;
                $results       = $this->onAfterDeleteEvent();
                if ($results === false) {
                    return false;
                    //error
                }
                $results = $this->row->id;
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
            if ($this->get('redirect_on_failure', '', 'parameters') == '') {

            } else {
                Services::Redirect()->url
                    = Services::Url()->get(null, null, $this->get('redirect_on_failure', '', 'parameters'));

                Services::Redirect()->code == 303;
            }
        }

        return $results;
    }

    /**
     * Retrieve data to be deleted
     *
     * @return bool|mixed
     * @since  1.0
     */
    public function getDeleteData()
    {
        $hold_model_name = $this->row->model_name;
        $this->connect('datasource', $hold_model_name);

        $this->set('use_special_joins', 0);
        $name_key       = $this->get('name_key');
        $primary_key    = $this->get('primary_key');
        $primary_prefix = $this->get('primary_prefix', 'a');

        if (isset($this->row->$primary_key)) {
            $this->model->query->where(
                $this->model->db->qn($primary_prefix) . '.' . $this->model->db->qn($primary_key)
                    . ' = ' . $this->model->db->q($this->row->$primary_key)
            );

        } elseif (isset($this->row->$name_key)) {
            $this->model->query->where(
                $this->model->db->qn($primary_prefix) . '.' . $this->model->db->qn($name_key)
                    . ' = ' . $this->model->db->q($this->row->$name_key)
            );

        } else {
            //only deletes single rows
            return false;
        }

        if (isset($this->row->catalog_type_id)) {
            $this->model->query->where(
                $this->model->db->qn($primary_prefix)
                    . '.' . $this->model->db->qn('catalog_type_id')
                    . ' = ' . $this->model->db->q($this->row->catalog_type_id)
            );
        }

        $item = $this->getData(QUERY_OBJECT_ITEM);
//		echo '<br /><br /><br />';
//		echo $this->model->query->__toString();
//		echo '<br /><br /><br />';

        if ($item === false) {
            //error
            return false;
        }

        $fields = Services::Registry()->get($this->model_registry, 'Fields');
        if (count($fields) == 0 || $fields === null) {
            return false;
        }

        $this->row = new \stdClass();
        foreach ($fields as $f) {
            foreach ($f as $key => $value) {
                if ($key == 'name') {
                    if (isset($item->$value)) {
                        $this->row->$value = $item->$value;
                    } else {
                        $this->row->$value = null;
                    }
                }
            }
        }

        if (isset($item->catalog_id)) {
            $this->row->catalog_id = $item->catalog_id;
        }
        $this->row->model_name = $hold_model_name;

        /** Process each field namespace  */
        $customFieldTypes = Services::Registry()->get($this->model_registry, 'customfieldgroups');

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
        //@todo - figure out what joining isn't working, get catalog id
        //$results = Services::Permissions()->verifyTask('Delete', $this->row->catalog_id);
        //if ($results === false) {
        //error
        //return false (not yet)
        //}
        return true;
    }

    /**
     * Schedule Event onBeforeDeleteEvent Event - could update model and data objects
     *
     * @return boolean
     * @since   1.0
     */
    protected function onBeforeDeleteEvent()
    {
        if (count($this->plugins) == 0
            || (int)$this->get('process_plugins') == 0
        ) {
            return true;
        }

        $arguments = array(
            'model_registry' => $this->model_registry,
            'db'             => $this->model->db,
            'data'           => $this->row,
            'null_date'      => $this->model->null_date,
            'now'            => $this->model->now,
            'parameters'     => $this->parameters,
            'model_type'     => $this->get('model_type'),
            'model_name'     => $this->get('model_name')
        );

        Services::Profiler()->set('message',
            'DeleteController->onBeforeDeleteEvent Schedules onBeforeDelete',
            'Plugins',
            1
        );

        $arguments = Services::Event()->scheduleEvent('onBeforeDelete', $arguments, $this->plugins);
        if ($arguments === false) {
            Services::Profiler()->set('message', 'DeleteController->onBeforeDelete failed.', 'Plugins', 1);

            return false;
        }

        Services::Profiler()->set('message', 'DeleteController->onBeforeDeleteEvent succeeded.', 'Plugins', 1);

        /** Process results */
        $this->parameters = $arguments['parameters'];
        $this->row        = $arguments['row'];

        return true;
    }

    /**
     * Schedule Event onAfterDeleteEvent Event
     *
     * @return boolean
     * @since   1.0
     */
    protected function onAfterDeleteEvent()
    {
        if (count($this->plugins) == 0
            || (int)$this->get('process_plugins') == 0
        ) {
            return true;
        }

        /** Schedule Event onAfterDelete Event */
        $arguments = array(
            'model_registry' => $this->model_registry,
            'db'             => $this->model->db,
            'data'           => $this->row,
            'parameters'     => $this->parameters,
            'model_type'     => $this->get('model_type'),
            'model_name'     => $this->get('model_name')
        );

        Services::Profiler()->set('message',
            'CreateController->onAfterDeleteEvent Schedules onAfterDelete',
            'Plugins',
            1
        );

        $arguments = Services::Event()->scheduleEvent('onAfterDelete', $arguments, $this->plugins);
        if ($arguments === false) {
            Services::Profiler()->set('message', 'DeleteController->onAfterDelete failed.', 'Plugins', 1);

            return false;
        }

        Services::Profiler()->set('message', 'DeleteController->onAfterDelete succeeded.', 'Plugins', 1);

        /** Process results */
        $this->parameters = $arguments['parameters'];
        $this->row        = $arguments['row'];

        return true;
    }
}
