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
     * Render the module.
     *
     * @param   array
     * @return  object
     * @since   1.0
     */
    public function render($attributes)
    {
        echo '<pre>';
        var_dump($this->_request);
        echo '</pre>';
        /** <include:module [name=xyz|position=xyz] attr1=x attr2=y attrN="and-so-on" /> */
        $this->_attributes = $attributes;
        $renderedOutput = '';
        $position = '';
        $holdWrap = '';

        foreach ($this->_attributes as $name => $value) {
            if ($name == 'name' || $name == 'title') {
                $this->_request->set('mvc_extension_instance_name', $value);
                break;

            } else if ($name == 'position') {
                $position = $value;
                break;
            }
        }

        /** Retrieve single Module or all Modules for a Position */
        $modules = $this->_getModules($position);
        if (count($modules) > 0) {
        } else {
            return false;
        }

        foreach ($modules as $module) {

            /** Populate $request */
            $this->_setRequest($module);

            /** Import MVC Classes for Module */
            $this->_import();

            /** Load Language Files */
            $this->_loadLanguage();

            /** For Position, wrap after all Modules are rendered */
            if ($position == '') {
            } else {
                $holdWrap = $this->_request->get('mvc_wrap');
                $this->_request->set('mvc_wrap', 'none');
            }
            $viewHelper = new MolajoViewHelper($this->_request->get('wrap'),
                'wraps',
                $this->_request->get('mvc_option'),
                $this->_request->get('extension_type'),
                ' ',
                $this->_request->get('template_name'));
            $this->_request->set('wrap_path', $viewHelper->view_path);
            $this->_request->set('wrap_path_url', $viewHelper->view_path_url);

            /** Instantiate Controller */
            $this->_request->set('task', 'display');
            $controllerName = $this->_request->get('mvc_controller') . 'ControllerModule';
            $controller = new MolajoControllerDisplay ($this->_request);

            /** Execute Task  */
            $task = $this->_request->get('task');
            $renderedOutput .= $controller->$task();
        }

        /** For Position, wrap after all Modules are rendered */
        if ($position == '') {
            return $renderedOutput;

        } else {
            $this->_request->set('wrap', $holdWrap);
            $viewHelper = new MolajoViewHelper($this->_request->get('wrap'),
                'wraps',
                $this->_request->get('mvc_option'),
                $this->_request->get('extension_type'),
                ' ',
                $this->_request->get('template_name'));
            $this->_request->set('wrap_path', $viewHelper->view_path);
            $this->_request->set('wrap_path_url', $viewHelper->view_path_url);

            $wrapIt = new MolajoControllerDisplay ($this->_request);
            return $wrapIt->wrapView($this->_request->get('wrap'), 'wraps', $renderedOutput);
        }
    }

    /**
     * _getModules
     *
     * @param $position
     * @return bool|mixed
     */
    protected function _getModules($position)
    {
        if ($position == '') {
            return MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_MODULE, $this->_request->get('extension_title'), null);
        } else {
            return MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_POSITION, $position, null);
        }
    }

    /**
     * _setRequest
     *
     * @param $module
     */
    private function _setRequest($module)
    {
        $this->_request->set('extension_id', $module->extension_id);
        $this->_request->set('extension_name', $module->extension_name);
        $this->_request->set('option', $module->extension_name);
        $this->_request->set('extension_title', $module->title);

        $parameters = new JRegistry;
        $parameters->loadString($module->parameters);
        $this->_request->set('extension_parameters', $parameters);
        $this->_request->set('extension_metadata', $module->metadata);

        if (isset($this->_request->set('extension_parameters')->static)
            && $this->_request->set('extension_parameters')->static === true
        ) {
            $this->_request->set('static', true);
        } else {
            $this->_request->set('static', false);
        }
        $this->_request->set('extension_path', MOLAJO_EXTENSIONS_MODULES . '/' . $this->_request->set('extension_name'));
        $this->_request->set('extension_type', 'module');
        $this->_request->set('extension_folder', '');

        $this->_request->set('controller', ucfirst($this->_request->get('extension_name')) . 'ControllerModule');
        $this->_request->set('model', ucfirst($module->extension_name) . 'ModelModule');
        $this->_request->set('task', 'display');

        foreach ($this->_attributes as $name => $value) {

            if ($name == 'wrap') {
                $this->_request->set('wrap', $value);

            } else if ($name == 'view') {
                $this->_request->set('view', $value);

            } else if ($name == 'wrap_id') {
                $this->_request->set('wrap_id', $value);

            } else if ($name == 'wrap_class') {
                $this->_request->set('wrap_class', $value);
            }
            // $this->_request->set('other_parameters', $other_parameters);
        }

        /** View Path */
        $this->_request->set('view_type', 'extensions');

        $viewHelper = new MolajoViewHelper($this->_request->get('view'),
            $this->_request->get('view_type'),
            $this->_request->get('mvc_option'),
            $this->_request->get('extension_type'),
            ' ',
            $this->_request->get('template_name')
        );
        $this->_request->set('view_path', $viewHelper->view_path);
        $this->_request->set('view_path_url', $viewHelper->view_path_url);
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
        if (file_exists($this->_request->get('mvc_extension_path') . '/controller.php')) {
            $fileHelper->requireClassFile($this->_request->get('mvc_extension_path') . '/controller.php', ucfirst($this->_request->get('extension_name')) . 'ControllerModule');
        }
        /** Model */
        if (file_exists($this->_request->get('mvc_extension_path') . '/model.php')) {
            $fileHelper->requireClassFile($this->_request->get('mvc_extension_path') . '/model.php', ucfirst($this->_request->get('extension_name')) . 'ModelModule');
        }
    }

    /**
     * _loadLanguage
     *
     * Loads Language Files
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadLanguage()
    {
        MolajoController::getApplication()->getLanguage()->load(
            $this->_request->get('mvc_extension_path'),
            MolajoController::getApplication()->getLanguage()->getDefault(), false, false);
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
        $this->_loadMediaPlus('/module' . $this->_request->get('mvc_option'),
            MolajoController::getApplication()->get('$media_priority_module', 400));
    }

    /**
     * _loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Template
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadMediaPlus($plus = '', $priority = 500)
    {
        /** Template */
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->_request->get('template_name');
        $urlPath = MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $this->_request->get('template_name');
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Application */
        $filePath = MOLAJO_SITE_MEDIA_FOLDER . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SITE_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $filePath = MOLAJO_SITE_MEDIA_FOLDER . $plus;
        $urlPath = MOLAJO_SITE_MEDIA_URL . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, false);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $filePath = MOLAJO_SITES_MEDIA_FOLDER . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SITES_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $filePath = MOLAJO_SITES_MEDIA_FOLDER . $plus;
        $urlPath = MOLAJO_SITES_MEDIA_URL . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** nothing was loaded */
        return false;
    }
}
