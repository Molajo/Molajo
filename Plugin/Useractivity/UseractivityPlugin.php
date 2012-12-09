<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Useractivity;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Useractivity
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class UseractivityPlugin extends Plugin
{

    /**
     * onAfterRead
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (defined('ROUTE')) {
        } else {
            return true;
        }
        if ($this->get('criteria_log_user_view_activity', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return;
    }

    /**
     * onAfterCreate
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterCreate()
    {
        if ($this->get('criteria_log_user_activity_create', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return true;
    }

    /**
     * onAfterUpdate
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        if ($this->get('criteria_log_user_update_activity', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return true;
    }

    /**
     * onAfterDelete
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterDelete()
    {
        if ($this->get('criteria_log_user_activity_delete', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return true;
    }

    /**
     * onAfterRead
     *
     * User Activity
     *
     * @return boolean
     * @since   1.0
     */
    public function setUserActivityLog()
    {
        /** Retrieve Key for Action  */
        $action_id = Services::Registry()->get(
            'Actions',
            $this->get('action', ACTION_READ)
        );

        /** Retrieve User Data  */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATA_SOURCE_LITERAL, 'UserActivity', 1);

        $controller->set('user_id', Services::Registry()->set(USER_LITERAL, 'id'));
        $controller->set('action_id', $action_id, 'parameters');
        $controller->set('catalog_id', $this->row->catalog_id, 'parameters');
        $controller->set('activity_datetime', null, 'parameters');

        $results = $controller->getData('create');

        return true;
    }
}
