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
use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Date Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class DateServicePlugin extends Plugin
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
        $this->service_class->set(
            'locale',
            Services::Language()->get('tag')
        );

        $this->service_class->set(
            'date_translate_array',
            Services::Language()->get('list', 'date_')
        );

        $this->service_class->set(
            'offset',
            Services::User()->get('timezone', '')
        );

        $this->service_class->set(
            'language_utc_offset',
            Services::Application()->get('language_utc_offset', '')
        );

        $this->service_class->set('date_class', 'JPlatform\\date\\JDate');

        return;
    }
}
