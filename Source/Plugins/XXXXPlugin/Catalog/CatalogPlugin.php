<?php
/**
 * Catalog Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Catalog;

use CommonApi\Event\DisplayInterface;

use Molajo\Plugins\AbstractPlugin;
use Molajo\Plugins\DisplayEventPlugin;
use Molajo\Controller\CreateController;
use CommonApi\Exception\RuntimeException;

/**
 * Catalog Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class CatalogPlugin extends AbstractPlugin
{
    /**
     * Post-create processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterCreate()
    {
        $id = $this->row->id;
        if ((int)$id == 0) {
            return $this;
        }

        /** Catalog Activity: fields populated by Catalog Activity plugins */
        if ($this->application->get('log_user_update_activity', 1) == 1) {
            $results = $this->logUserActivity($id, $this->registry->get('Actions', 'create'));
            if ($results === false) {
                return $this;
            }
        }

        if ($this->application->get('log_catalog_update_activity', 1) == 1) {
            $results = $this->logCatalogActivity($id, $this->registry->get('Actions', 'create'));
            if ($results === false) {
                return $this;
            }
        }

        return $this;
    }

    /**
     * Post-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        if ($this->application->get('log_user_update_activity', 1) == 1) {
            $results = $this->logUserActivity(
                $this->row->id,
                $this->registry->get('Actions', 'delete')
            );
            if ($results === false) {
                return $this;
            }
        }

        if ($this->application->get('log_catalog_update_activity', 1) == 1) {
            $results = $this->logCatalogActivity(
                $this->row->id,
                $this->registry->get('Actions', 'delete')
            );
            if ($results === false) {
                return $this;
            }
        }

        return $this;
    }

    /**
     * Pre-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        return $this; // only redirect id
    }

    /**
     * Pre-delete processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeDelete()
    {
        /** @todo - fix empty setModelRegistry */
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->model->getModelRegistryModelRegistry('x', 'y', 1);

        $sql = 'DELETE FROM ' . $controller->model->database->qn('#__catalog_categories');
        $sql .= ' WHERE ' . $controller->model->database->qn('catalog_id') . ' = ' . (int)$this->row->id;
        $controller->model->database->setQueryPermissions($sql);
        $controller->model->database->execute();

        $sql = 'DELETE FROM ' . $controller->model->database->qn('#__catalog_activity');
        $sql .= ' WHERE ' . $controller->model->database->qn('catalog_id') . ' = ' . (int)$this->row->id;
        $controller->model->database->setQueryPermissions($sql);
        $controller->model->database->execute();

        return $this;
    }

    /**
     * Post-delete processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterDelete()
    {
        //how to get id - referential integrity?
        /**
         * if ($this->application->get('log_user_update_activity', 1) == 1) {
         * $this->logUserActivity($id, $this->registry->get('Actions', 'delete'));
         * }
         * if ($this->application->get('log_catalog_update_activity', 1) == 1) {
         * $this->logCatalogActivity($id, $this->registry->get('Actions', 'delete'));
         * }
         */

        return $this;
    }

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
    public function logCatalogActivity($id, $action_id)
    {
        $data              = new \stdClass();
        $data->model_name  = 'CatalogActivity';
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
