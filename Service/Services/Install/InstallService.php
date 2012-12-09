<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Install;
use Molajo\MVC\Controller\Controller;
use Molajo\MVC\Controller\CreateController;
use Molajo\MVC\Controller\DeleteController;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Install
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class InstallService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new InstallService();
        }

        return self::$instance;
    }

    /**
     * Create Content
     *
     *  Services::Text()->addImage(200, 300, 'cat');
     *
     * @return bool
     * @since  1.0
     */
    public function content()
    {
        $copy_model_name = 'Content';
        $copy_model_type = DATA_SOURCE_LITERAL;
        $copy_extension_instance_id = 2;
        $copy_catalog_type_id = 10000;

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry($copy_model_type, $copy_model_name, 1);

        $model_registry = $copy_model_name . $copy_model_type;

        $controller->set('get_item_children', 0, 'model_registry');
        $controller->set('use_special_joins', 0, 'model_registry');
        $controller->set('check_view_level_access', 1, 'model_registry');
        $controller->set('process_plugins', 0, 'model_registry');

        $name_key = $controller->get('name_key');
        $primary_key = $controller->get('primary_key', 'id', 'model_registry');
        $primary_prefix = $controller->get('primary_prefix', 'a', 'model_registry');

        $controller->model->query->where($controller->model->db->qn($primary_prefix)
            . '.' . $controller->model->db->qn('extension_instance_id')
            . ' = ' . (int) $copy_extension_instance_id);

        $controller->model->query->where($controller->model->db->qn($primary_prefix)
            . '.' . $controller->model->db->qn('catalog_type_id')
            . ' = ' . (int) $copy_catalog_type_id);

        $item = $controller->getData(QUERY_OBJECT_ITEM);

        /** target data */
        $extension_instance_id = 5;
        $criteria_extension_instance_id = $extension_instance_id;
        $catalog_type_id = 30000;
        $criteria_catalog_type_id = $catalog_type_id;
        $item_parent_menu_id = 100;
        $parent_menuitem = 124;
        $item_model_name = "Comments";

        $visitor_name = array();
        $email_address = array();
        $website = array();

        $visitor_name[] = 'Bob Marley';
        $email_address[] = 'BabMarley@example.com';
        $website[] = 'http://example.com/';

        $visitor_name[] = 'Ding Dong';
        $email_address[] = 'DingDong@example.com';
        $website[] = 'http://example.com/';

        $visitor_name[] = 'Mary Kline';
        $email_address[] = 'MaryKline@example.com';
        $website[] = 'http://example.com/';

        $fields = Services::Registry()->get($model_registry, FIELDS_LITERAL);
        if (count($fields) == 0 || $fields === null) {
            return false;
        }

        $data = new \stdClass();
        /** Clone */
        foreach ($fields as $f) {
            foreach ($f as $key => $value) {
                if ($key == 'name') {
                    if (isset($item->$value)) {
                        $data->$value = $item->$value;
                    } else {
                        $data->$value = null;
                    }
                }
            }
        }

        if (isset($item->catalog_id)) {
            $data->catalog_id = $item->catalog_id;
        }

        /** Overlay for this extension */
        $data->id = null;
        $data->title = trim(Services::Text()->getPlaceHolderText(1, 5, 'plain', 1));
        $data->content_text = trim(Services::Text()->getPlaceHolderText(1, 20, 'html', 1));

//$data->content_text = Services::Text()->getPlaceHolderText(1, 20, 'html', 1)
//			. '$pattern = '#{readmore}#''
//			. Services::Text()->getPlaceHolderText(2, 20, 'html', 1);

        $data->alias = Services::Filter()->filter($data->title, 'alias', 0, $data->title);

        $data->extension_instance_id = $extension_instance_id;
        $data->catalog_type_id = $catalog_type_id;

        $data->start_publishing_datetime = null;
        $data->stop_publishing_datetime = null;
        $data->created_datetime = null;
        $data->created_by = 0;
        $data->modified_datetime = null;
        $data->modified_by = 0;
        $data->checked_out_datetime = null;
        $data->checked_out_by = 0;
        $data->catalog_id = 0;
        $data->catalog_sef_request = null;
        $data->version = 1;
        $data->version_of_id = 0;
        $data->status_prior_to_version = 0;
        $data->protected = 0;
        $data->model_name = $item_model_name;
        $data->model_type = DATA_SOURCE_LITERAL;

//Services::Text()->addImage(200, 300, 'cat');

        $data->parameters = array();
        Services::Registry()->sort($model_registry . PARAMETERS_LITERAL);
        $parameters = Services::Registry()->getArray($model_registry . PARAMETERS_LITERAL);
        if (count($parameters) > 0) {
            foreach ($parameters as $key => $value) {

                if ($key == 'criteria_extension_instance_id') {
                    $data->parameters[$key] = $criteria_extension_instance_id;

                } elseif ($key == 'criteria_catalog_type_id') {
                    $data->parameters[$key] = $criteria_catalog_type_id;

                } elseif ($key == 'parent_menuid') {
                    $data->parameters[$key] = $item_parent_menu_id;

                } elseif ($key == 'item_model_name') {
                    $data->parameters[$key] = $item_model_name;

                } else {
                    $data->parameters[$key] = $value;
                }
            }
        }

        $data->metadata = array();
        Services::Registry()->sort($model_registry . METADATA_LITERAL);
        $parameters = Services::Registry()->getArray($model_registry . METADATA_LITERAL);

        if (count($parameters) > 0) {
            foreach ($parameters as $key => $value) {
                if ($key == 'title') {
                    $data->metadata[$key] = $data->title;
                } else {
                    $data->metadata[$key] = '';
                }
            }
        }

        for ($i = 0; $i < 3; $i++) {

            $data->customfields = array();
            Services::Registry()->sort($model_registry . CUSTOMFIELDS_LITERAL);
            $customfields = Services::Registry()->getArray($model_registry . CUSTOMFIELDS_LITERAL);

            if (count($customfields) > 0) {
                foreach ($customfields as $key => $value) {

                    if ($key == 'visitor_name') {
                        $data->parameters[$key] = $visitor_name[$i];

                    } elseif ($key == 'email_address') {
                        $data->parameters[$key] = $email_address[$i];

                    } elseif ($key == 'website') {
                        $data->parameters[$key] = $website[$i];

                    } else {
                        $data->customfields[$key] = '';
                    }
                }
            }

            /** Create Catalog for Item (it will plugin more) */
            $controller = new CreateController();
            $controller->data = $data;
            $id = $controller->execute();

            /** Create Catalog for Item (it will plugin more) */
            $controller2 = new CreateController();

            $data2 = new \stdClass();
            $data2->catalog_type_id = $data->catalog_type_id;
            $data2->source_id = $id;
            $data2->view_group_id = 1;
            $data2->extension_instance_id = $id;
            $data2->model_name = 'Catalog';
            $data2->sef_request
                = Services::Filter()->filter($item_model_name, 'alias', 0, $item_model_name)
                . '/'
                . $data->alias;

            $data2->page_type = '';
            $data2->routable = 1;

            $controller2->data = $data2;

            $controller2->execute();
        }

        return true;
    }

    /**
     * Catalog
     *
     * @return bool
     * @since  1.0
     */
    public function catalog()
    {

        /** Create Catalog for Menu Item (it will plugin more) */
        $controller = new CreateController();

        $data2 = new \stdClass();
        $data2->catalog_type_id = 60037;
        $data2->source_id = 157;
        $data2->view_group_id = 1;
        $data2->extension_instance_id = 281;
        $data2->model_name = 'Catalog';
        $data2->sef_request = 'data-dictionary';
        $data2->page_type = 'grid';
        $data2->routable = 1;

        $controller->data = $data2;

        $controller->execute();

    }

    public function testCreateExtension($extension_name, $model_name, $source_path = null, $destination_path = null)
    {
        $controller = new CreateController();
        $model_registry = ucfirst(strtolower($model_name)) . DATA_SOURCE_LITERAL;

        $data = new \stdClass();
        $data->title = $extension_name;
        $data->model_name = $model_name;

        $controller->data = $data;

        $id = $controller->execute();
        if ($id === false) {
            //install failed
            return false;
        }

        return true;
    }

    public function testDeleteExtension($extension_name, $model_name, $source_path = null)
    {
        /** Get Catalog Type ID */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry('Catalogtypes', DATA_SOURCE_LITERAL, 1);
        $controller->setDataobject();

        $primary_prefix = $controller->get('primary_prefix', 'a', 'model_registry');

        $controller->model->query->select($controller->model->db->qn($primary_prefix) . '.' . $controller->model->db->qn('id'));
        $controller->model->query->where($controller->model->db->qn($primary_prefix) . '.' . $controller->model->db->qn('model_name')
            . ' = ' . $controller->model->db->q($model_name));

        $catalog_type_id = $controller->getData(QUERY_OBJECT_RESULT);

        // With the Catalog ID now available, contact the Delete Controller
        $controller = new DeleteController();

        $data = new \stdClass();

        $data->model_name = ucfirst(strtolower('ExtensionInstances'));
        $data->catalog_type_id = $catalog_type_id;
        $data->title = $extension_name;

        $controller->data = $data;

        $controller->set('action', 'delete');

        $id = $controller->execute();

        if ($id === false) {
            //install failed
            return false;
        }

        return true;
    }

    /**
     * testPlugin
     *
     * @return bool
     */
    public function testPlugin()
    {

        $controller = new CreateController();
        $model_registry = 'ArticlesResource';

        $plugins = array();
        $plugins[] = 'Create';

        $query_results = array();
        $data = new \stdClass();
        $data->id = 333;
        $data->title = 'Test';
        $data->catalog_type_id = CATALOG_TYPE_RESOURCE;
        $query_results[] = $data;

        $parameters = array('create_extension' => 1,
            'create_sample_data' => 1);

        /** Schedule Event onAfterCreate Event */
        $arguments = array(
            'model_registry' => $model_registry,
            'db' => '',
            'data' => $query_results,
            'parameters' => $parameters,
            'model_type' => $this->get('model_type'),
            'model_name' => $this->get('model_name')
        );

        $arguments = Services::Event()->scheduleEvent('onAfterCreate', $arguments, $plugins);
        if ($arguments['success'] === true) {
            $arguments = $plugin['arguments'];
        } else {

            var_dump($arguments);

            return false;
        }
    }

}
