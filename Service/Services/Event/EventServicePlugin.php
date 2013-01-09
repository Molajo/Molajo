<?php
/**
 * Event Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Event;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('NIAMBIE') or die;

/**
 * Event Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class EventServicePlugin extends ServicesPlugin
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
        $this->service_class_instance->set('frontcontroller_instance', $this->frontcontroller_instance);
    }

    /**
     * On After Service Class "Start" process
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterServiceInitialise()
    {
        $this->service_class_instance->set('on', 1);
    }
}
