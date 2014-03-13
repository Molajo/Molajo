<?php
/**
 * Activity Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Activity;

use CommonApi\Event\DisplayInterface;

use Molajo\Plugins\DisplayEventPlugin;
use Molajo\Controller\CreateController;
use CommonApi\Exception\RuntimeException;

/**
 * Activity Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class ActivityPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Log user updates
     *
     * @param   int $id
     * @param   int $action_id
     *
     * @return  $this
     * @since   1.0
     */
    public function logUserActivity($id, $action_id)
    {
        $data              = new \stdClass();
        $data->model_name  = 'UserActivity';
        $data->model_table = 'datasource';
        $data->catalog_id  = $id;
        $data->action_id   = $action_id;

        $controller       = new CreateController();
        $controller->data = $data;
        $user_activity_id = $controller->execute();
        if ($user_activity_id === false) {
            //install failed
            return $this;
        }

        return $this; // only redirect id
    }

    /**
     * Pre-update processing
     *
     * @param   int $id
     * @param   int $action_id
     *
     * @return  $this
     * @since   1.0
     */
    public function logActivityActivity($id, $action_id)
    {
        $data              = new \stdClass();
        $data->model_name  = 'ActivityActivity';
        $data->model_table = 'datasource';
        $data->catalog_id  = $id;
        $data->action_id   = $action_id;

        $controller          = new CreateController();
        $controller->data    = $data;
        $catalog_activity_id = $controller->execute();
        if ($catalog_activity_id === false) {
            //install failed
            return $this;
        }

        return $this; // only redirect id
    }
}