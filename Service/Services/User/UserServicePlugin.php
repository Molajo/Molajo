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
use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * User Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class UserServicePlugin extends Plugin
{
    /**
     * on Before Startup Event
     *
     * Follows instantiation of the service class and before the method identified as the "start" method
     *
     * @return  void
     * @since   1.0
     */
    public function onBeforeServiceStartup()
    {
        if ($this->service_class->get('id', 0) == 0) {
            $this->service_class->set('id', Services::Registry()->get('User'));
        }
    }

    /**
     * On After Startup Event
     *
     * Follows the completion of the start method defined in the configuration
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterServiceStartup()
    {
        return;
    }

    /**
     * On After Read All Event
     *
     * Follows the getData Method and all special field activities
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterReadAll()
    {
        if ($this->model_registry_name == 'UserDatasource') {
        } else {
            return;
        }

        $this->service_class->set(
            'parameters',
            Services::Registry()->getArray('UserDatasourceParameters')
        );

        $this->service_class->set(
            'metadata',
            Services::Registry()->getArray('UserDatasourceMetadata')
        );

        $this->service_class->set(
            'customfields',
            Services::Registry()->getArray('UserDatasourceCustomfields')
        );

        return;
    }
}
