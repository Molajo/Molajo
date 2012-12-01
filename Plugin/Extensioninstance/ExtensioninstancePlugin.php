<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Extensioninstance;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\MVC\Controller;
use Molajo\MVC\Controller\CreateController;
use Molajo\MVC\Controller\DeleteController;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Extension Instances
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class ExtensioninstancePlugin extends Plugin
{
    /**
     * onBeforeCreate processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        if ($this->data->catalog_type_id >= CATALOG_TYPE_BEGIN
            AND $this->data->catalog_type_id <= CATALOG_TYPE_END
) {
        } else {
            return true;
        }

        /** Check Permissions */

        /** Check if the Extension Instance already exists */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATA_SOURCE_LITERAL, 'ExtensionInstances');
        $controller->setDataobject();

        $primary_prefix = $controller->get('primary_prefix', 'a');

        $controller->set('get_customfields', '0');
        $controller->set('get_item_children', '0');
        $controller->set('use_special_joins', '0');
        $controller->set('check_view_level_access', '0');

        $controller->model->query->select($controller->model->db->qn($primary_prefix) . '.' . $controller->model->db->qn('id'));
        $controller->model->query->where($controller->model->db->qn($primary_prefix) . '.' . $controller->model->db->qn('title')
            . ' = ' . $controller->model->db->q($this->data->title));
        $controller->model->query->where($controller->model->db->qn($primary_prefix) . '.' . $controller->model->db->qn('catalog_type_id')
            . ' = ' . (int) $this->data->catalog_type_id);

        $id = $controller->getData(QUERY_OBJECT_RESULT);

        if ((int) $id > 0) {
            //name already exists
            return false;
        }

        /** Next, see if the Extension node exists */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATA_SOURCE_LITERAL, 'Extensions');
        $controller->setDataobject();

        $controller->model->query->select($controller->model->db->qn('a.id'));
        $controller->model->query->where($controller->model->db->qn('a.name')
            . ' = ' . $controller->model->db->q($this->data->title));
        $controller->model->query->where($controller->model->db->qn('a.catalog_type_id')
            . ' = ' . (int) $this->data->catalog_type_id);

        $item = $controller->getData(QUERY_OBJECT_ITEM);

        if ($item === false) {
        } else {
            $this->data->extension_id = $item->id;
            $this->data->catalog_type_id = $item->catalog_type_id;

            return;
        }

        /** If Extension Node does not exist */

        //todo decide if another query is warranted for verifying existence of catalog type

        /** Create a new Catalog Type */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry();
        $controller->setDataobject();

        /** Catalog Types */
        $sql = 'INSERT INTO ' . $controller->model->db->qn('#__catalog_types');
        $sql .= ' VALUES ( null, '
            . $controller->model->db->q($this->data->title)
            . ', 0, '
            . $controller->model->db->q($this->data->title)
            . ', ' . $controller->model->db->q('#__content') . ')';

        $controller->model->db->setQuery($sql);
        $controller->model->db->execute();

        $this->parameters['criteria_catalog_type_id'] = $controller->model->db->insertid();

        /** Create a new Extension Node */
        $data = new \stdClass();
        $data->name = $this->data->title;
        $data->catalog_type_id = $this->data->catalog_type_id;
        $data->model_name = 'Extensions';

        $controller = new CreateController();
        $controller->data = $data;

        $this->data->extension_id = $controller->execute();

        if ($this->data->extension_id === false) {
            //error
            return false;
        }

        return true;
    }

    /**
     * onAfterCreate processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterCreate()
    {
        echo 'Catalog ID ' . $this->data->catalog_type_id . '<br />';

        if ($this->data->catalog_type_id >= CATALOG_TYPE_BEGIN
            AND $this->data->catalog_type_id <= CATALOG_TYPE_END
) {
        } else {
            return true;
        }

        echo 'ID ' . $this->data->id . '<br />';
        /** Extension Instance ID */
        $id = $this->data->id;
        if ((int) $id == 0) {
            return false;
        }

        /** Site Extension Instances */
        $controller = new CreateController();

        $data = new \stdClass();
        $data->site_id = SITE_ID;
        $data->extension_instance_id = $id;
        $data->model_name = 'SiteExtensionInstances';

        $controller->data = $data;

        $results = $controller->execute();
        if ($results === false) {
            //install failed
            return false;
        }
        echo 'results are true for site ' . '<br />';

        /** Application Extension Instances */
        $controller = new CreateController();

        $data = new \stdClass();
        $data->application_id = APPLICATION_ID;
        $data->extension_instance_id = $id;
        $data->model_name = 'ApplicationExtensionInstances';

        $controller->data = $data;

        $results = $controller->execute();
        if ($results === false) {
            //install failed
            return false;
        }
        echo 'results are true for app ' . '<br />';
        /** Catalog */
        $controller = new CreateController();

        $data = new \stdClass();
        $data->catalog_type_id = Services::Registry()->get($this->model_registry_name, 'catalog_type_id');
        $data->source_id = $id;
        $data->view_group_id = 1;
        $data->extension_instance_id = $id;
        $data->model_name = 'Catalog';

        $controller->data = $data;

        $this->data->catalog_id = $controller->execute();
        if ($results === false) {
            //install failed
            return false;
        }
        echo 'results are true for catalog ' . '<br />';

        return true;
    }

    /**
     * onBeforeDelete -
     *
     * Returns false and does not delete if there is content for this extension
     *
     * Deletes ACL and catalog data for this extension to be deleted
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeDelete()
    {

        /** Only Extension Instances */
        if (isset($this->data->catalog_type_id)
            && ($this->data->catalog_type_id == CATALOG_TYPE_RESOURCE)
        ) {
        } else {
            return true;
        }

        /** Do not allow delete if there is content for this resource */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $controller->getModelRegistry(DATA_SOURCE_LITERAL, $this->data->title);

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $primary_prefix = $controller->get('primary_prefix', 'a');

        $controller->set('get_customfields', '0');
        $controller->set('get_item_children', '0');
        $controller->set('use_special_joins', '0');
        $controller->set('check_view_level_access', '0');

        if (isset($this->parameters['criteria_catalog_type_id'])) {
            $temp = (int) $this->parameters['criteria_catalog_type_id'];

            $controller->model->query->where($controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('catalog_type_id')
                . ' = ' . $temp);

            $item = $controller->getData(QUERY_OBJECT_ITEM);

            if ($item === false) {
            } else {
                //content exists - cannot delete
                return false;
            }
        }

        /** Delete allowed - get rid of ACL info */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry();

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }
        $sql = 'DELETE FROM ' . $controller->model->db->qn('#__application_extension_instances');
        $sql .= ' WHERE ' . $controller->model->db->qn('extension_instance_id') . ' = ' . (int) $this->data->id;
        $controller->model->db->setQuery($sql);
        $controller->model->db->execute();

        $sql = 'DELETE FROM ' . $controller->model->db->qn('#__site_extension_instances');
        $sql .= ' WHERE ' . $controller->model->db->qn('extension_instance_id') . ' = ' . (int) $this->data->id;
        $controller->model->db->setQuery($sql);
        $controller->model->db->execute();

        $sql = 'DELETE FROM ' . $controller->model->db->qn('#__group_permissions');
        $sql .= ' WHERE ' . $controller->model->db->qn('catalog_id') . ' = ' . (int) $this->data->catalog_id;
        $controller->model->db->setQuery($sql);
        $controller->model->db->execute();

        $sql = 'DELETE FROM ' . $controller->model->db->qn('#__view_group_permissions');
        $sql .= ' WHERE ' . $controller->model->db->qn('catalog_id') . ' = ' . (int) $this->data->catalog_id;
        $controller->model->db->setQuery($sql);
        $controller->model->db->execute();

        /** Catalog has plugins for more deletions */
        $controller = new DeleteController();
        echo 'Passing this catalog id in to Delete Controller ' . $this->data->catalog_id . '<br />';

        $data = new \stdClass();
        $data->model_name = ucfirst(strtolower('Catalog'));
        $data->id = $this->data->catalog_id;
        $controller->data = $data;
        $controller->set('action', 'delete');

        $id = $controller->execute();

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
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $results = $controller->getModelRegistry(DATA_SOURCE_LITERAL, 'ExtensionInstances');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('get_customfields', 0);
        $controller->set('get_item_children', 0);
        $controller->set('use_special_joins', 0);
        $controller->set('check_view_level_access', 0);
        $controller->set('process_plugins', 0);

        $controller->model->query->select('COUNT(*)');
        $controller->model->query->from($controller->model->db->qn('#__extension_instances'));
        $controller->model->query->where($controller->model->db->qn('extension_id')
            . ' = ' . (int) $this->data->extension_id);

        $value = $controller->getData(QUERY_OBJECT_RESULT);

        if (empty($value) || (int) $value == 0) {
        } else {
            /** do not delete - more instances remain */

            return true;
        }

        /** Delete orphan node */
        $controller = new DeleteController();

        $data = new \stdClass();
        $data->model_name = ucfirst(strtolower('Extensions'));
        $data->id = $this->data->extension_id;
        $controller->data = $data;
        $controller->set('action', 'delete');

        $controller->execute();

        return true;
    }
}
