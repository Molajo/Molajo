<?php
/**
 * Profiler Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Profiler;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('NIAMBIE') or die;

/**
 * Profiler Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class ProfilerServicePlugin extends ServicesPlugin
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
        if ((int)Services::Application()->get('profiler_service') == 1) {
            $this->service_class->set('on', 1);
        } else {
            $this->service_class->set('on', 0);
        }

        $phase_array_list = $this->service_class->get('phase_array_list', array());

        $this->service_class->set(
            'profiler_start_with',
            Services::Application()->get('profiler_start_with', 'Initialise')
        );

        if (in_array($this->service_class->get('profiler_start_with'), $phase_array_list)) {
        } else {
            $this->service_class->set('profiler_start_with', 'Initialise');
        }

        $this->service_class->set('profiler_end_with',
            Services::Application()->get('profiler_end_with', 'Response')
        );

        if (in_array($this->service_class->get('profiler_end_with'), $phase_array_list)) {
        } else {
            $this->service_class->set('profiler_end_with', 'Response');
        }

        $outputOptions = array(
            'Actions',
            'Application',
            'Authorisation',
            'Queries',
            'Registry',
            'Rendering',
            'Routing',
            'Services',
            'Plugins'
        );

        $temp = Services::Application()->get('profiler_output');

        if ($temp == '' || $temp == null || count($temp) == 0) {
            $temp = $outputOptions;
        }

        $profiler_output_options = array();
        foreach ($temp as $item) {
            if (in_array($item, $outputOptions)) {
                $profiler_output_options[] = $item;
            }
        }
        $this->service_class->set('profiler_output_options', $profiler_output_options);

        $this->service_class->set('verbose', (int)Services::Application()->get('profiler_verbose', 0));
        if ($this->service_class->get('verbose', 1)) {
        } else {
            $this->service_class->set('verbose', 0);
        }

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
        if ($this->service_class->get('on') == 0) {
            define('PROFILER_ON', false);
            Services::Registry()->set('Profiler', 'on', false);
        } else {
            define('PROFILER_ON', true);
            Services::Registry()->set('Profiler', 'on', true);
        }

        return;
    }
}
