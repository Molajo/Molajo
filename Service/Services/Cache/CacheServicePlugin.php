<?php
/**
 * Cache Serviced Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Cache;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('NIAMBIE') or die;

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
    public function onBeforeStartup()
    {
    }

    /**
     * On After Service Class "Start" process
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterStartup()
    {
        $this->set('cache_service', false);
        $this->set('cache_keys', array());

        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_handler', 'file') == 'file') {
            $this->system_cache_folder = SITE_BASE_PATH . '/'
                . Services::Registry()->get(CONFIGURATION_LITERAL, 'system_cache_folder');
        } else {
            return false;
        }

        $this->service_class->set(
            'Parameter',
            'cache_handler',
            Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_handler', 'file')
        );

        $this->service_class->set(
            'Parameter',
            'system_cache_folder',
            $this->system_cache_folder = SITE_BASE_PATH . '/'
                . Services::Registry()->get(CONFIGURATION_LITERAL, 'system_cache_folder')
        );

        $time = Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_time', 900);
        if ($time == 0) {
            $time = 900;
        }
        $this->service_class->set('Parameter', 'cache_service_time', $time);

        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_service') == 0) {
            $cache = false;
        } else {
            $cache = true;
        }
        $this->service_class->set('Parameter', 'cache_service', $cache);

        $this->service_class->set(
            'Parameter',
            'cache_type_model',
            (int)Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_type_model', 0)
        );

        $this->service_class->set(
            'Parameter',
            'cache_type_page',
            (int)Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_type_page', 0)
        );

        $this->service_class->set(
            'Parameter',
            'cache_type_query',
            (int)Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_type_query', 0)
        );

        $this->service_class->set(
            'Parameter',
            'cache_type_template',
            (int)Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_type_template', 0)
        );

        $valid_types = array();
        $valid_types[] = 'model';
        $valid_types[] = 'page';
        $valid_types[] = 'query';
        $valid_types[] = 'template';

        $this->service_class->set('Parameter', 'valid_types', $valid_types);

        $this->initialise();

        Services::Registry()->set('cache_service', 'on', $this->service_class->get('cache_service'));

        return;
    }
}
