<?php
/**
 * Metadata Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Metadata;

use Molajo\Service\Services;
use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Metadata Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class MetadataServicePlugin extends Plugin
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
        $this->service_class->set('language', $this->frontcontroller_class->get('language_current'));
        $this->service_class->set('direction', $this->frontcontroller_class->get('language_direction'));
        $this->service_class->set('html5', $this->frontcontroller_class->get('application_html5'));
        $this->service_class->set('line_end', $this->frontcontroller_class->get('application_line_end'));
        $this->service_class->set('mimetype', $this->frontcontroller_class->get('request_mimetype'));
        $this->service_class->set('request_date', $this->frontcontroller_class->get('request_date'));

        return;
    }
}
