<?php
/**
 * Date Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Date;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('NIAMBIE') or die;

/**
 * Date Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class DateServicePlugin extends ServicesPlugin
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
    }

    /**
     * On After Startup Event
     *
     * Follows the completion of the start method defined in the configuration
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterServiceInitialise()
    {
        $this->service_class_instance->set(
            'locale',
            Services::Language()->get('tag')
        );

        $this->service_class_instance->set(
            'date_translate_array',
            Services::Language()->get('list', 'date_')
        );

        $this->service_class_instance->set(
            'offset_user',
            Services::User()->get('timezone', '')
        );

        $this->service_class_instance->set(
            'offset_server',
            Services::Application()->get('language_utc_offset', 'UTC')
        );

        return;
    }
}
