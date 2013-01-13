<?php
/**
 * Metadata Service Plugin
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Metadata;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('MOLAJO') or die;

/**
 * Metadata Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class MetadataServicePlugin extends ServicesPlugin
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
        $this->service_class_instance->set('language', $this->frontcontroller_instance->get('language_tag'));
        $this->service_class_instance->set('direction', $this->frontcontroller_instance->get('language_direction'));
        $this->service_class_instance->set('html5', $this->frontcontroller_instance->get('application_html5'));
        $this->service_class_instance->set('line_end', $this->frontcontroller_instance->get('application_line_end'));
        $this->service_class_instance->set('mimetype', $this->frontcontroller_instance->get('request_mimetype'));
        $this->service_class_instance->set('request_date', $this->frontcontroller_instance->get('request_date'));

        return;
    }
}
