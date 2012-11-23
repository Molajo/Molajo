<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Catalog;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\MVC\Controller\CreateController;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Catalog
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CatalogPlugin extends Plugin
{
    /**
     * Generates Catalog Datalist
     *
     * This can be moved to onBeforeParse when Plugin ordering is in place
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry('Datasource', 'Catalog');
        $controller->setDataobject();

        $controller->set('get_customfields', 0);
        $controller->set('use_special_joins', 0);
        $controller->set('process_plugins', 0);

        $controller->model->query->select(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('id')
        );
        $controller->model->query->select(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('sef_request')
                . ' AS value '
        );

        $controller->model->query->where(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('redirect_to_id')
                . ' = 0'
        );
        $controller->model->query->where(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('enabled')
                . ' = 1'
        );
        $controller->model->query->where(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('page_type')
                . ' <> '
                . $controller->model->db->q('Link')
        );
        $controller->model->query->where(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('catalog_type_id')
                . ' = ' . CATALOG_TYPE_MENUITEM
                . ' OR ' .
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('catalog_type_id')
                . ' > ' . CATALOG_TYPE_TAG
        );
        $controller->model->query->where(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('application_id')
                . ' = ' . (int) APPLICATION_ID
        );

        $controller->model->query->order(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('sef_request')
        );

        $controller->set('model_offset', 0);
        $controller->set('model_count', 99999);

        $query_results = $controller->getData(QUERY_OBJECT_DISTINCT);

        $catalogArray = array();

        $application_home_catalog_id =
            (int) Services::Registry()->get('configuration', 'application_home_catalog_id');

        if ($application_home_catalog_id === 0) {
        } else {
            if (count($query_results) == 0 || $query_results === false) {
            } else {

                foreach ($query_results as $item) {
                    if ($item->id == $application_home_catalog_id) {
                        $item->value = trim($item->value . ' ' . Services::Language()->translate('Home'));
                        $catalogArray[] = $item;
                    } elseif (trim($item->value) == '' || $item->value === NULL) {
                        unset ($item);
                    } else {
                        $catalogArray[] = $item;
                    }
                }
            }
        }

        Services::Registry()->set('Datalist', 'Catalog', $catalogArray);

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
        $id = $this->data->id;
        if ((int) $id == 0) {
            return false;
        }

        /** Catalog Activity: fields populated by Catalog Activity plugins */
        if (Services::Registry()->get('Configuration', 'log_user_update_activity', 1) == 1) {
            $results = $this->logUserActivity($id, Services::Registry()->get('Actions', ACTION_CREATE));
            if ($results === false) {
                return false;
            }
        }

        if (Services::Registry()->get('Configuration', 'log_catalog_update_activity', 1) == 1) {
            $results = $this->logCatalogActivity($id, Services::Registry()->get('Actions', ACTION_CREATE));
            if ($results === false) {
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
        if (Services::Registry()->get('Configuration', 'log_user_update_activity', 1) == 1) {
            $results = $this->logUserActivity($this->data->id,
                Services::Registry()->get('Actions', ACTION_DELETE));
            if ($results === false) {
                return false;
            }
        }

        if (Services::Registry()->get('Configuration', 'log_catalog_update_activity', 1) == 1) {
            $results = $this->logCatalogActivity($this->data->id,
                Services::Registry()->get('Actions', ACTION_DELETE));
            if ($results === false) {
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
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry();
        $controller->setDataobject();

        $sql = 'DELETE FROM ' . $controller->model->db->qn('#__catalog_categories');
        $sql .= ' WHERE ' . $controller->model->db->qn('catalog_id') . ' = ' . (int) $this->data->id;
        $controller->model->db->setQuery($sql);
        $controller->model->db->execute();

        $sql = 'DELETE FROM ' . $controller->model->db->qn('#__catalog_activity');
        $sql .= ' WHERE ' . $controller->model->db->qn('catalog_id') . ' = ' . (int) $this->data->id;
        $controller->model->db->setQuery($sql);
        $controller->model->db->execute();

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
        if (Services::Registry()->get('Configuration', 'log_user_update_activity', 1) == 1) {
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
        $data->model_table = 'Datasource';
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
        $data->model_table = 'Datasource';
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
