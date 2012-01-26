<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Module
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoRendererModule extends MolajoRenderer
{
    /**
     * render
     *
     * Render the component.
     *
     * @param   $attributes <include:renderer attr1=x attr2=y attr3=z ... />
     *
     * @return  mixed
     * @since   1.0
     */
    public function render($attributes)
    {
        /** attributes come from <include:renderer statement */
        $this->_attributes = $attributes;

        /** specific module or all modules for a specific tag */
        $modules = $this->_getModules();

        if (count($modules) > 0) {
        } else {
            return false;
        }

        foreach ($modules as $module) {

            /** reset MVC variables for extension */
            $this->_initialize = true;
            $this->_initializeMVC();

            /** establish values needed for MVC */
            $this->_setParameters($module);

            /** lazy load paths for view files */
            $this->_setPaths();

            /** import files and classes for extension */
            $this->_import();

            /** load language files for extension */
            $this->_loadLanguage();

            /** load css and js for extension */
            $this->_loadMedia();

            if ($this->_position == '') {
            } else {
                $holdWrap = $this->request->get('mvc_wrap');
                $this->request->set('mvc_wrap', 'none');
                $holdWrapCssId = $this->request->get('mvc_wrap_css_id');
                $holdWrapCssClass = $this->request->get('mvc_wrap_css_class');
            }

            /** Render Module Output */
            $renderedOutput = $this->_invokeMVC();

            /** For Position, wrap after all Modules are rendered */
            if ($this->_position == '') {
                return $renderedOutput;

            } else {
                $this->request->set('mvc_wrap', $holdWrap);
                $this->request->set('mvc_wrap_css_id', $holdWrapCssId);
                $this->request->set('mvc_wrap_css_class', $holdWrapCssClass);

                $viewHelper = new MolajoViewHelper($this->request->get('wrap'),
                    'wraps',
                    $this->request->get('extension_instance_name'),
                    $this->request->get('extension_instance_name'),
                    ' ',
                    $this->request->get('template_name'));
                $this->request->set('wrap_path', $viewHelper->view_path);
                $this->request->set('wrap_path_url', $viewHelper->view_path_url);

                $wrapIt = new MolajoControllerDisplay ($this->request);
                return $wrapIt->wrapView($this->request->get('wrap'), 'wraps', $renderedOutput);
            }
        }
    }

    /**
     * _getModules
     *
     * @param $this->_position
     * @return bool|mixed
     */
    protected function _getModules()
    {
        if ($this->_position == '') {
            return MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_MODULE, $this->request->get('extension_instance_name'), null);
        } else {
            return MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_POSITION, $this->_position, null);
        }
    }

    /**
     * _setRequest
     *
     * @param $module
     */
    protected function _setParameters($module)
    {
        $this->request->set('extension_instance_id', $module->extension_id);
        $this->request->set('extension_instance_name', strtolower($module->extension_name));
        $this->request->set('extension_instance_name', strtolower($module->extension_name));
        $this->request->set('extension_path',
            MOLAJO_EXTENSIONS_MODULES . '/' . strtolower($module->extension_name));
        $this->request->set('mvc_view_type', 'extension');
        $this->request->set('extension_type', 'module');

        $custom_fields = new JRegistry;
        $custom_fields->loadString($module->custom_fields);
        $this->request->set('extension_parameters', $custom_fields);

        $metadata = new JRegistry;
        $metadata->loadString($module->metadata);
        $this->request->set('extension_metadata', $metadata);

        $parameters = new JRegistry;
        $parameters->loadString($module->parameters);
        $this->request->set('extension_parameters', $parameters);

        $this->request->set('extension_type', 'module');
        $this->request->set('extension_folder', '');

        $this->request->set('mvc_controller', 'display');
        $this->request->set('mvc_model', 'display');
        $this->request->set('mvc_task', 'display');
    }

    /**
     * _import
     *
     * imports module folders and files
     *
     * @return  null
     * @since   1.0
     */
    protected function _import()
    {
        $fileHelper = new MolajoFileHelper();

        /** Controller */
        if (file_exists($this->request->get('extension_path') . '/controller.php')) {
            $fileHelper->requireClassFile(
                $this->request->get('extension_path') .
                    '/controller.php',
                ucfirst($this->request->get('extension_instance_name')) .
                    'ModuleControllerDisplay');
        }
        /** Model */
        if (file_exists($this->request->get('extension_path') . '/model.php')) {
            $fileHelper->requireClassFile($this->request->get('extension_path')
                    . '/model.php',
                ucfirst($this->request->get('extension_instance_name'))
                    . 'ModuleModelDisplay');
        }
    }

    /**
     *  _loadMedia
     *
     * Load CSS, JS, and Deferred JS for Extension
     *
     * @return  null
     * @since  1.0
     */
    protected function _loadMedia()
    {
        parent::_loadMedia(MOLAJO_EXTENSIONS_MODULES_URL . '/' . $this->request->get('extension_instance_name'),
            MOLAJO_SITE_MEDIA_URL . '/' . $this->request->get('extension_instance_name'),
            MolajoController::getApplication()->get('$media_priority_module', 400));
    }
}
