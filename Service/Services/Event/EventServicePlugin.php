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
        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            Services::Profiler()->set(
                'message',
                'Event: registerInstalledPlugins for Extension and Core',
                'Plugins',
                1
            );
        }

        $this->service_class->registerPlugins(PLATFORM_FOLDER, 'Molajo', 1);
        $this->service_class->registerPlugins(PLATFORM_FOLDER . '/' . 'Services', 'Extension', 1);
        $this->service_class->registerPlugins(EXTENSIONS, 'Extension', 1);

        Services::Registry()->set('Events', 'on', true);

        return;
    }

    /**
     * Registers all Plugins in the folder
     *
     * Extensions can override Plugins by including a like-named folder in a Plugin directory within the extension
     *
     * The application will find and register overrides at the point in time the extension is used in rendering.
     *
     * Usage:
     * Services::Event()->registerPlugin('Molajo\\Plugin');
     *
     * @param  string  $folder
     * @param  string  $namespace
     * @param  int     $core
     *
     *
     * @return  array
     * @since   1.0
     */
    public function registerPluginFolder($folder = '', $namespace = '', $core = 0)
    {
        $folders_and_files = scandir($folder);
        if (count($folders_and_files) == 0) {
            return array();
        }

        $plugins = array();
        foreach ($folders_and_files as $key => $value) {
            if ($value == '.') {
            } elseif ($value == '..') {
            } elseif (is_dir($folder . '/' . $value)) {
                $plugins[] = $value;
            }
        }

        if (count($plugins) == 0 || $plugins === false) {
            return array();
        }

        $authorised_plugins = array();
        $authorised         = Services::Registry()->get('AuthorisedExtensionsByInstanceTitle');
        if ($authorised === false) {
            $authorised = array();
        }

        foreach ($plugins as $folder) {

            $plugin_name = ucfirst(strtolower($folder)) . 'Plugin';

            if (substr(strtolower($folder), 0, 4) == 'hold') {

            } elseif (in_array($plugin_name, $authorised) || count($authorised) == 0) {

                $plugin_class = $namespace . $folder . '\\' . $plugin_name;

                /** Overrides - rebuild registry for overridden locations/values */
                if (Services::Registry()->exists($plugin_name) === false
                    || (Services::Registry()->exists($plugin_name) && (int)$core == 0)
                ) {

                    Services::Registry()->deleteRegistry($plugin_name);

                    $controllerClass = CONTROLLER_CLASS_NAMESPACE;
                    $controller      = new $controllerClass();
                    $controller->getModelRegistry('Plugin', ucfirst(strtolower($folder)), 0);
                }

                $temp    = array();
                $temp[0] = $plugin_name;
                $temp[1] = $folder;

                $authorised_plugins[] = $temp;
            }
        }

        return $authorised_plugins;
    }
}
