<?php
/**
 * Cache Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Cache;

use Molajo\Service\Services;
use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Cache Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class CacheServicePlugin extends Plugin
{
    /**
     * On Before Service Class "Start" process
     *
     * @return  void
     * @since   1.0
     */
    public function onBeforeServiceStartup()
    {
        $this->service_class->set('Parameter', 'cache_service', false);
        $this->service_class->set('Parameter', 'cache_keys', array());

        $this->service_class->set(
            'Parameter',
            'cache_handler',
            Services::Registry()->get('configuration', 'cache_handler', 'file')
        );

        $time = Services::Registry()->get('configuration', 'cache_time', 900);
        if ($time == 0) {
            $time = 900;
        }
        $this->service_class->set('Parameter', 'cache_service_time', $time);

        if (Services::Registry()->get('configuration', 'cache_service') == 0) {
            $cache = false;
        } else {
            $cache = true;
        }
        $this->service_class->set('Parameter', 'cache_service', $cache);

        $this->service_class->set(
            'Parameter',
            'cache_model',
            (int)Services::Registry()->get('configuration', 'cache_model', 0)
        );

        $this->service_class->set(
            'Parameter',
            'cache_page',
            (int)Services::Registry()->get('configuration', 'cache_page', 0)
        );

        $this->service_class->set(
            'Parameter',
            'cache_query',
            (int)Services::Registry()->get('configuration', 'cache_query', 0)
        );

        $this->service_class->set(
            'Parameter',
            'cache_template',
            (int)Services::Registry()->get('configuration', 'cache_template', 0)
        );

        if (Services::Registry()->get('configuration', 'cache_handler', 'file') == 'file') {
            $this->system_cache_folder = SITE_BASE_PATH . '/'
                . Services::Registry()->get('configuration', 'system_cache_folder');
        }

        $this->service_class->set(
            'Parameter',
            'system_cache_folder',
            $this->system_cache_folder = SITE_BASE_PATH . '/'
                . Services::Registry()->get('configuration', 'system_cache_folder')
        );


        $valid_types   = array();
        $valid_types[] = 'model';
        $valid_types[] = 'page';
        $valid_types[] = 'query';
        $valid_types[] = 'template';

        $this->service_class->set('Parameter', 'valid_types', $valid_types);

        return;

    }

    /**
     * On After Service Class "Start" process
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterServiceStartup()
    {
        Services::Registry()->set('cache_service', 'on',
            $this->service_class->get('Parameter', 'cache_service')
        );
    }
}
