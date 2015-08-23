<?php
/**
 * Activity Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Activity;

use CommonApi\Event\SystemEventInterface;
use Molajo\Plugins\SystemEvent;

/**
 * Activity Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class ActivityPlugin extends SystemEvent implements SystemEventInterface
{
    /**
     * After Logging in event
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterAuthenticate()
    {
        return $this;
    }

    /**
     * After Dispatcher Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterDispatcher()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->insertActivityRecord(3, 'Dispatcher');

        return $this;
    }

    /**
     * After delete processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterDelete()
    {
        return $this;
    }

    /**
     * After update processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterUpdate()
    {
        return $this;
    }

    /**
     * Post-create processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterCreate()
    {
        return $this;
    }

    /**
     * Post-read processing - all rows at one time from query_results
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        return $this;
    }

    /**
     * After Logging out event
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterLogout()
    {
        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        return false;
    }

    /**
     * Insert Activity Record
     *
     * @param   integer $action_id
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function insertActivityRecord($action_id, $type)
    {
        $this->connect('create');
        $now    = $this->query->getDate();
        $row    = $this->query->initialiseRow();
        $method = 'setInsertValues' . $type;
        $row    = $this->$method($row, $now, $action_id);

        $this->query->setInsertStatement($row);
        $this->query->setSql();
        $this->query->insertData();

        return $this;
    }

    /**
     * Set Activity Row Values
     *
     * @param   object  $row
     * @param   string  $now
     * @param   integer $action_id
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setInsertValuesDispatcher($row, $now, $action_id)
    {
        $row->id                = null;
        $row->user_id           = $this->runtime_data->user->id;
        $row->action_id         = $action_id;
        $row->catalog_id        = $this->runtime_data->resource->data->catalog_id;
        $row->session_id        = $this->runtime_data->user->session_id;
        $row->activity_datetime = $now;
        $row->ip_address        = $this->runtime_data->request->client->remote_address;

        return $row;
    }

    /**
     * Get Query Connection
     *
     * @param   string $crud_type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function connect()
    {
        $this->setQueryController('Molajo//Model//Datasource//Useractivity.xml', 'Create');

        $this->setQueryControllerDefaults(
            $process_events = 0,
            $query_object = 'item',
            $get_customfields = 0,
            $use_special_joins = 0,
            $use_pagination = 0,
            $check_view_level_access = 0,
            $get_item_children = 0
        );

        return $this;
    }
}
