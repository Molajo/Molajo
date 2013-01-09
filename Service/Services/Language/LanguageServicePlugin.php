<?php
/**
 * Language Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Language;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('NIAMBIE') or die;

/**
 * Language Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class LanguageServicePlugin extends ServicesPlugin
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
        return;
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
        $this->service_class_instance->set('user_language',
            Services::User()->get('language'));

        $this->service_class_instance->set('default_language',
            Services::Application()->get('language'));

        $this->service_class_instance->set('profile_missing_strings',
            Services::Application()->get('profiler_collect_missing_language_strings'));

        return;
    }

    /**
     * On After Read All Event
     *
     * Follows the getData Method and all special field activities
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterReadAll()
    {
        if ($this->model_registry_name == 'LanguagestringsSystem') {
        } else {
            return;
        }

        $this->service_class_instance->set('parameters', Services::Registry()->getArray('ApplicationDatasourceParameters'));
        $this->service_class_instance->set('metadata', Services::Registry()->getArray('ApplicationDatasourceMetadata'));

        return;
    }
}
