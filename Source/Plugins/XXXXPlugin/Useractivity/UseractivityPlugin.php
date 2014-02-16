<?php
/**
 * User Activity Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Useractivity;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * User Activity Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class UseractivityPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * onAfterRead
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->runtime_data->route)) {
        } else {

            return $this;
        }
        if ($this->get('criteria_log_user_view_activity', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return;
    }

    /**
     * onAfterCreate
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterCreate()
    {
        if ($this->get('criteria_log_user_activity_create', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return $this;
    }

    /**
     * onAfterUpdate
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        if ($this->get('criteria_log_user_update_activity', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return $this;
    }

    /**
     * onAfterDelete
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterDelete()
    {
        if ($this->get('criteria_log_user_activity_delete', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return $this;
    }

    /**
     * onAfterRead
     *
     * User Activity
     *
     * @return  $this
     * @since   1.0
     */
    public function setUserActivityLog()
    {
        /** Retrieve Key for Action  */
        $action_id = $this->registry->get(
            'Actions',
            $this->get('action', 'read')
        );

        /** Retrieve User Data  */
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry('datasource', 'UserActivity', 1);

        $controller->set('user_id', $this->registry->set('User', 'id'));
        $controller->set('action_id', $action_id, 'runtime_data');
        $controller->set('catalog_id', $this->row->catalog_id, 'runtime_data');
        $controller->set('activity_datetime', null, 'runtime_data');

        $results = $controller->getData('create');

        return $this;
    }
}
