<?php
/**
 * Cache Service Plugin
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Cache;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('MOLAJO') or die;

/**
 * Cache Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class CacheServicePlugin extends ServicesPlugin
{
    /**
     * On Before Service Class "Start" process
     *
     * @return  void
     * @since   1.0
     */
    public function onBeforeServiceInitialise()
    {
        $this->service_class_instance->set('Parameter', 'cache_service', false);
        $this->service_class_instance->set('Parameter', 'cache_keys', array());

        $this->service_class_instance->set(
            'Parameter',
            'cache_handler',
            Services::Application()->get('cache_handler', 'file')
        );

        $time = Services::Application()->get('cache_time', 900);
        if ($time == 0) {
            $time = 900;
        }
        $this->service_class_instance->set('Parameter', 'cache_service_time', $time);

        if (Services::Application()->get('cache_service') == 0) {
            $cache = false;
        } else {
            $cache = true;
        }
        $this->service_class_instance->set('Parameter', 'cache_service', $cache);

        $this->service_class_instance->set(
            'Parameter',
            'cache_model',
            (int)Services::Application()->get('cache_model', 0)
        );

        $this->service_class_instance->set(
            'Parameter',
            'cache_page',
            (int)Services::Application()->get('cache_page', 0)
        );

        $this->service_class_instance->set(
            'Parameter',
            'cache_query',
            (int)Services::Application()->get('cache_query', 0)
        );

        $this->service_class_instance->set(
            'Parameter',
            'cache_template',
            (int)Services::Application()->get('cache_template', 0)
        );

        if (Services::Application()->get('cache_handler', 'file') == 'file') {
            $this->service_class_instance->set(
                'Parameter',
                'system_cache_folder',
                SITE_BASE_PATH . '/' . Services::Application()->get('system_cache_folder', 'cache')
            );
        }

        $valid_types   = array();
        $valid_types[] = 'model';
        $valid_types[] = 'page';
        $valid_types[] = 'query';
        $valid_types[] = 'template';

        $this->service_class_instance->set('Parameter', 'valid_types', $valid_types);

        return;

    }

    /**
     * On After Service Class "Start" process
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterServiceInitialise()
    {
        Services::Registry()->set(
            'cache_service',
            'on',
            $this->service_class_instance->get('Parameter', 'cache_service')
        );
    }
}
