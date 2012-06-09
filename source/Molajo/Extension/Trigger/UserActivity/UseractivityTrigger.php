<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Useractivity;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Useractivity
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class UseractivityTrigger extends ContentTrigger
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new UseractivityTrigger();
        }

        return self::$instance;
    }

    /**
     * onAfterRead
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        if ($this->get('criteria_log_user_activity_read', 0) == 1) {
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

        return;
    }

    /**
     * onAfterUpdate
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        if ($this->get('criteria_log_user_activity_update', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return;
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
            $this->get('action', 'display')
        );

        /** Retrieve User Data  */
        $controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
        $m = new $controllerClass();
		$results = $m->connect('Table', 'UserActivity');
		if ($results == false) {
			return false;
		}

        $m->set('user_id', Services::Registry()->set('User', 'id'));
        $m->set('action_id', $action_id);
        $m->set('catalog_id', $this->query_results->catalog_id);
        $m->set('activity_datetime', null);

        $results = $m->getData('create');

        return true;
    }
}
