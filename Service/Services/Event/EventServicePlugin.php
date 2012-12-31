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
    public function onBeforeStartup()
    {
        Services::Registry()->createRegistry('Events');

        Services::Registry()->set('Events', 'Events', array());
        Services::Registry()->set('Events', 'Plugins', array());
        Services::Registry()->set('Events', 'EventPlugins', array());


    }

    /**
     * On After Startup Event
     *
     * Follows the completion of the start method defined in the configuration
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterStartup()
    {
        Services::Registry()->set('Events', 'on', true);
    }
}
