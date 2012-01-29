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
            $this->_setRequest();

            /** establish values needed for MVC */
            $this->_getAttributes($module);

            /** retrieves MVC defaults for application */
            $this->_getApplicationDefaults();

            /** lazy load paths for view files */
            $this->_setPaths();

            /** import files and classes for extension */
            $this->_importClasses();

            /** load language files for extension */
            $this->_loadLanguage();

            /** load css and js for extension */
            $this->_loadMedia();

            if ($this->_tag == '') {
            } else {
                $holdWrap = $this->mvc->get('mvc_wrap');
                $this->mvc->set('mvc_wrap', 'none');
                $holdWrapCssId = $this->mvc->get('mvc_view_wrap_css_id');
                $holdWrapCssClass = $this->mvc->get('mvc_view_wrap_css_class');
            }

            /** Render Module Output */
            $renderedOutput = $this->_invokeMVC();

            /** For Position, wrap after all Modules are rendered */
            if ($this->_tag == '') {
                return $renderedOutput;

            } else {
                $this->mvc->set('mvc_wrap', $holdWrap);
                $this->mvc->set('mvc_view_wrap_css_id', $holdWrapCssId);
                $this->mvc->set('mvc_view_wrap_css_class', $holdWrapCssClass);

                $viewHelper = new MolajoViewHelper($this->mvc->get('wrap'),
                    'wraps',
                    $this->mvc->get('extension_instance_name'),
                    $this->mvc->get('extension_instance_name'),
                    ' ',
                    $this->mvc->get('theme_name'));
                $this->mvc->set('view_wrap_path', $viewHelper->view_path);
                $this->mvc->set('view_wrap_path_url', $viewHelper->view_path_url);

                $wrapIt = new MolajoControllerDisplay ($this->mvc);
                return $wrapIt->wrapView($this->mvc->get('wrap'), 'wraps', $renderedOutput);
            }
        }
    }

    /**
     * _getModules
     *
     * @param $this->_tag
     * @return bool|mixed
     */
    protected function _getModules()
    {
        if ($this->_tag == '') {
            return MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_MODULE, $this->mvc->get('extension_instance_name'), null);
        } else {
            return MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_POSITION, $this->_tag, null);
        }
    }

    /**
     * _setRequest
     *
     * @param $module
     */
    protected function _getAttributes($module)
    {
        $this->mvc->set('extension_instance_id', $module->extension_id);
        $this->mvc->set('extension_instance_name', strtolower($module->extension_name));
        $this->mvc->set('extension_instance_name', strtolower($module->extension_name));
        $this->mvc->set('extension_path',
            MOLAJO_EXTENSIONS_MODULES . '/' . strtolower($module->extension_name));
        $this->mvc->set('mvc_view_type', 'extension');
        $this->mvc->set('extension_type', 'module');

        $custom_fields = new JRegistry;
        $custom_fields->loadString($module->custom_fields);
        $this->mvc->set('extension_parameters', $custom_fields);

        $metadata = new JRegistry;
        $metadata->loadString($module->metadata);
        $this->mvc->set('extension_metadata', $metadata);

        $parameters = new JRegistry;
        $parameters->loadString($module->parameters);
        $this->mvc->set('extension_parameters', $parameters);

        $this->mvc->set('extension_type', 'module');
        $this->mvc->set('extension_folder', '');

        $this->mvc->set('mvc_controller', 'display');
        $this->mvc->set('mvc_model', 'display');
        $this->mvc->set('mvc_task', 'display');
    }

    /**
     * _importClasses
     *
     * imports module folders and files
     *
     * @return  null
     * @since   1.0
     */
    protected function _importClasses()
    {
        $fileHelper = new MolajoFileHelper();

        /** Controller */
        if (file_exists($this->mvc->get('extension_path') . '/controller.php')) {
            $fileHelper->requireClassFile(
                $this->mvc->get('extension_path') .
                    '/controller.php',
                ucfirst($this->mvc->get('extension_instance_name')) .
                    'ModuleControllerDisplay');
        }
        /** Model */
        if (file_exists($this->mvc->get('extension_path') . '/model.php')) {
            $fileHelper->requireClassFile($this->mvc->get('extension_path')
                    . '/model.php',
                ucfirst($this->mvc->get('extension_instance_name'))
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
        parent::_loadMedia(MOLAJO_EXTENSIONS_MODULES_URL . '/' . $this->mvc->get('extension_instance_name'),
            MOLAJO_SITE_MEDIA_URL . '/' . $this->mvc->get('extension_instance_name'),
            MolajoController::getApplication()->get('$media_priority_module', 400));
    }
}
