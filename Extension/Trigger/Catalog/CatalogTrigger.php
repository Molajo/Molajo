<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Catalog;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Controller\CreateController;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Catalog
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CatalogTrigger extends ContentTrigger
{
    /**
     * Post-create processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterCreate()
    {
        /** Just inserted UD */
        $id = $this->data->id;
        if ((int) $id == 0) {
            return false;
        }

        /** Catalog Activity: fields populated by Catalog Activity triggers */
        if (Services::Registry()->get('Configuration', 'log_user_activity_update', 1) == 1) {
            $results = $this->logUserActivity($id, Services::Registry()->get('Actions', 'create'));
            if ($results == false) {
                return false;
            }
        }

        if (Services::Registry()->get('Configuration', 'log_catalog_update_activity', 1) == 1) {
            $results = $this->logCatalogActivity($id, Services::Registry()->get('Actions', 'create'));
            if ($results == false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Post-update processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        if (Services::Registry()->get('Configuration', 'log_user_activity_update', 1) == 1) {
            $results = $this->logUserActivity($this->data->id,
                Services::Registry()->get('Actions', 'delete'));
            if ($results == false) {
                return false;
            }
        }

        if (Services::Registry()->get('Configuration', 'log_catalog_update_activity', 1) == 1) {
            $results = $this->logCatalogActivity($this->data->id,
                Services::Registry()->get('Actions', 'delete'));
            if ($results == false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Pre-update processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        return true; // only redirect id
    }

    /**
     * Pre-delete processing
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeDelete()
    {
        $controllerClass = 'Molajo\\Controller\\DisplayController';
        $m = new $controllerClass();
        $m->connect();

        $sql = 'DELETE FROM ' . $m->model->db->qn('#__catalog_categories');
        $sql .= ' WHERE ' . $m->model->db->qn('catalog_id') . ' = ' . (int) $this->data->id;
        $m->model->db->setQuery($sql);
        $m->model->db->execute();

        $sql = 'DELETE FROM ' . $m->model->db->qn('#__catalog_activity');
        $sql .= ' WHERE ' . $m->model->db->qn('catalog_id') . ' = ' . (int) $this->data->id;
        $m->model->db->setQuery($sql);
        $m->model->db->execute();

        return true;
    }

    /**
     * Post-delete processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterDelete()
    {
        //how to get id - referential integrity?
        /**
        if (Services::Registry()->get('Configuration', 'log_user_activity_update', 1) == 1) {
        $this->logUserActivity($id, Services::Registry()->get('Actions', 'delete'));
        }
        if (Services::Registry()->get('Configuration', 'log_catalog_update_activity', 1) == 1) {
        $this->logCatalogActivity($id, Services::Registry()->get('Actions', 'delete'));
        }
         */

        return true;
    }

    /**
     * Log user updates
     *
     * @return boolean
     * @since   1.0
     */
    public function logUserActivity($id, $action_id)
    {
        $data = new \stdClass();
        $data->model_name = 'UserActivity';
        $data->model_table = 'Table';
        $data->catalog_id = $id;
        $data->action_id = $action_id;

        $controller = new CreateController();
        $controller->data = $data;
        $user_activity_id = $controller->execute();
        if ($user_activity_id === false) {
            //install failed
            return false;
        }

        return true; // only redirect id
    }

    /**
     * Pre-update processing
     *
     * @return boolean
     * @since   1.0
     */
    public function logCatalogActivity($id, $action_id)
    {
        $data = new \stdClass();
        $data->model_name = 'CatalogActivity';
        $data->model_table = 'Table';
        $data->catalog_id = $id;
        $data->action_id = $action_id;

        $controller = new CreateController();
        $controller->data = $data;
        $catalog_activity_id = $controller->execute();
        if ($catalog_activity_id === false) {
            //install failed
            return false;
        }

        return true; // only redirect id
    }
}
