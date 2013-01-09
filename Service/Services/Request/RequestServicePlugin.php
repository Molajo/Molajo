<?php
/**
 * Request Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Request;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('NIAMBIE') or die;

/**
 * Request Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class RequestServicePlugin extends ServicesPlugin
{
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
        $this->frontcontroller_instance
            ->set('request_method', $this->service_class_instance->get('method', 'GET'));
        $this->frontcontroller_instance
            ->set('request_mimetype', $this->service_class_instance->get('mimetype', 'text/html'));
        $this->frontcontroller_instance
            ->set('request_post_variables', $this->service_class_instance->get('post_variables', array()));
        $this->frontcontroller_instance
            ->set('request_using_ssl', $this->service_class_instance->get('is_secure'));
        $this->frontcontroller_instance
            ->set('request_base_url', $this->service_class_instance->get('base_url'));

        return;
    }
}
