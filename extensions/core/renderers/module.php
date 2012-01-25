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
     * _executeMVC
     *
     * Instantiate Controller for the Display View
     *
     * @return mixed
     * @since  1.0
     */
    protected function _executeMVC()
    {
        /** Retrieve single Module or all Modules for a Position */
        $modules = $this->_getModules($this->_position);
        if (count($modules) > 0) {
        } else {
            return false;
        }

        foreach ($modules as $module) {

            /** Populate $request */
            $this->_setRequest($module);

            /** lazy load paths for extension files */
            $this->_setPaths();

            /** import files and classes for extension */
            $this->_import();

            /** load language files for extension */
            $this->_loadLanguage();

            /** load css and js for extension */
            $this->_loadMedia();

            $controllerClass = ucfirst($this->request->get('mvc_controller')) . 'Controller';
            if (ucfirst($this->request->get('mvc_controller')) == 'Display') {
            } else {
                $controllerClass .= $this->request->get('mvc_controller');
            }
            echo $controllerClass;
            die;
            $controller = new $controllerClass ($this->request);

            /** execute task: display, edit, or add  */
            $task = (string)$this->request->get('mvc_task');
            $controller->$task();


            if ($this->_position == '') {
            } else {
                $holdWrap = $this->request->get('mvc_wrap');
                $this->request->set('mvc_wrap', 'none');
                $holdWrapCssId = $this->request->get('mvc_wrap_css_id');
                $holdWrapCssClass = $this->request->get('mvc_wrap_css_class');
            }

            /** For Position, wrap after all Modules are rendered */
            if ($this->_position == '') {
                return $renderedOutput;

            } else {
                $this->request->set('mvc_wrap', $holdWrap);
                $this->request->set('mvc_wrap_css_id', $holdWrapCssId);
                $this->request->set('mvc_wrap_css_class', $holdWrapCssClass);

                $viewHelper = new MolajoViewHelper($this->request->get('wrap'),
                    'wraps',
                    $this->request->get('mvc_option'),
                    $this->request->get('extension_type'),
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
    protected function _getModules($position)
    {
        if ($this->_position == '') {
            return MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_MODULE, $this->request->get('extension_title'), null);
        } else {
            return MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_POSITION, $this->_position, null);
        }
    }

    /**
     * _setRequest
     *
     * @param $module
     */
    private function _setRequest($module)
    {
        $this->request->set('mvc_extension_instance_id', $module->extension_id);
        $this->request->set('mvc_extension_instance_name', $module->extension_name);
        $this->request->set('mvc_option', $module->extension_name);

        $parameters = new JRegistry;
        $parameters->loadString($module->parameters);
        $this->request->set('request_request_extension_parameters', $parameters);
        $this->request->set('request_extension_metadata', $module->metadata);

        $this->request->set('extension_path', MOLAJO_EXTENSIONS_MODULES . '/' . $this->request->set('extension_name'));
        $this->request->set('extension_type', 'module');
        $this->request->set('extension_folder', '');

        $this->request->set('mvc_controller', ucfirst($this->request->get('extension_name')) . 'ControllerModule');
        $this->request->set('mvc_model', ucfirst($module->extension_name) . 'ModelModule');
        $this->request->set('mvc_task', 'display');

        /** View Path */
        $this->request->set('view_type', 'extensions');

        $viewHelper = new MolajoViewHelper($this->request->get('mvc_view'),
            $this->request->get('mvc_view_type'),
            $this->request->get('mvc_option'),
            $this->request->get('mvc_extension_type'),
            ' ',
            $this->request->get('template_name')
        );
        $this->request->set('mvc_view_path', $viewHelper->view_path);
        $this->request->set('mvc_view_path_url', $viewHelper->view_path_url);
    }

    /**
     * import
     *
     * imports module folders and files
     * @since 1.0
     */
    protected function _import()
    {
        $fileHelper = new MolajoFileHelper();

        /** Controller */
        if (file_exists($this->request->get('mvc_extension_path') . '/controller.php')) {
            $fileHelper->requireClassFile($this->request->get('mvc_extension_path') . '/controller.php', ucfirst($this->request->get('extension_name')) . 'ControllerModule');
        }
        /** Model */
        if (file_exists($this->request->get('mvc_extension_path') . '/model.php')) {
            $fileHelper->requireClassFile($this->request->get('mvc_extension_path') . '/model.php', ucfirst($this->request->get('extension_name')) . 'ModelModule');
        }
    }

    /**
     * _loadMedia
     *
     * Loads Media Files for Site, Application, User, and Template
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadMedia()
    {
        /** Module */
        $this->_loadMediaPlus('/module' . $this->request->get('mvc_option'),
            MolajoController::getApplication()->get('$media_priority_module', 400));
    }
}
