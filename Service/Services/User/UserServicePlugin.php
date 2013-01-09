<?php
/**
 * User Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\User;

use Molajo\Service\Services;

use Molajo\Service\ServicesPlugin;

defined('NIAMBIE') or die;

/**
 * User Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class UserServicePlugin extends ServicesPlugin
{
    /**
     * on Before Startup Event
     *
     * Follows instantiation of the service class and before the method identified as the "start" method
     *
     * @return  void
     * @since   1.0
     */
    public function onBeforeServiceInitialise()
    {
        if ($this->service_class_instance->get('id', 0) == 0) {
            //$this->service_class_instance->set('id', Services::Session()->get('Userid'));
            $this->service_class_instance->set('id', 1);
        }
    }

    /**
     * On After Startup Event
     *
     * Follows the completion of the start method defined in the configuration
     *
     * @return  void
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function onAfterServiceInitialise()
    {
        $id = $this->service_class_instance->get('id');

        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $model_registry  = $controller->getModelRegistry('Datasource', 'User', 1);

        $this->service_class_instance->set('model_registry', $model_registry);

        $controller->set('primary_key_value', (int)$id, 'model_registry');
        $controller->set('get_customfields', 2, 'model_registry');
        $controller->set('use_special_joins', 1, 'model_registry');
        $controller->set('process_plugins', 1, 'model_registry');

        $data = $controller->getData(QUERY_OBJECT_ITEM);
        if (is_object($data)) {
        } else {
            throw new \RuntimeException ('User Service Plugin: Load User Query Failed');
        }

        $customfields = $model_registry['customfieldgroups'];

        $x = array();
        foreach ($customfields as $type) {
            if ($type == 'customfields') {
                unset($data->customfields);
            } else {
                $x[] = $type;
            }
        }

        $customfields = $x;

        if (count($customfields) > 0) {

            foreach ($customfields as $type) {
                $test_type  = $type . '_';
                $temp_array = array();
                foreach (get_object_vars($data) as $key => $value) {
                    if (substr($key, 0, strlen($test_type)) == strtolower($test_type)) {
                        $key2              = substr($key, strlen($test_type), strlen($key) - strlen($test_type));
                        $temp_array[$key2] = $value;
                        unset($data->$key);
                    }
                }

                $this->service_class_instance->set($type, $temp_array);
                unset($data->$type);
                unset($temp_array);
                unset($type);
            }
        }

        $this->service_class_instance->set('data', $data);

        $this->service_class_instance->getUserData();

        $this->setAuthorisedExtensions();
        //$view_groups = $this->service_class_instance->get('view_groups');

        $extension_class    = $this->frontcontroller_instance->get_class_array('ExtensionHelper');
        $extension_instance = new $extension_class();
        $results            = $extension_instance->get(0, null, null, null, 0);
        return;
    }

    /**
     * Retrieve all Extensions the logged on User is authorised to use. The Extension Helper will use this
     *  registry to avoid a new read when processing requests for Themes, Views, Plugins, Services, etc.
     *
     * @return  bool
     * @since   1.0
     * @throws  \Exception
     */
    protected function setAuthorisedExtensions()
    {
        //$view_groups = $this->service_class_instance->get('view_groups');

        $extension_class    = $this->frontcontroller_instance->get_class_array('ExtensionHelper');
        $extension_instance = new $extension_class();
        $results            = $extension_instance->get(0, null, null, null, 0);

        if ($results === false || count($results) == 0) {
            throw new \Exception('User Service: No authorised Extension Instances.');
        }

        $authorised_extensions       = array();
        $authorised_extension_titles = array();
        foreach ($results as $extension) {

            $authorised_extensions[$extension->id] = $extension;

            if ($extension->catalog_type_id == CATALOG_TYPE_MENUITEM) {
            } else {
                $key                               = trim($extension->title) . $extension->catalog_type_id;
                $authorised_extension_titles[$key] = $extension->title;
            }
        }

        sort($authorised_extensions);
        sort($authorised_extension_titles);

        $this->service_class_instance->set('authorised_extensions', $authorised_extensions);
        $this->service_class_instance->set('authorised_extension_titles', $authorised_extension_titles);

        return;
    }
}
