<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Component
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoComponentRenderer extends MolajoRenderer
{

    /**
     * render
     *
     * Render the component.
     *
     * @param $attributes
     * @return mixed
     */
    public function render($attributes)
    {
        /** @var $attributes  */
        $this->_attributes = $attributes;

        $this->_setParameters();

        /** import files and classes */
        $this->_import();

        /** Load Language Files */
        $this->_loadLanguage();

        /** Instantiate Controller */
        $controllerClass = ucfirst($this->_request->get('option')) . 'Controller';
        if (ucfirst($this->_request->get('controller')) == 'Display') {
        } else {
            $controllerClass .= $this->_request->get('controller');
        }
        $controller = new $controllerClass ($this->_request);

        /** Execute Task  */
        $task = (string)$this->_request->get('task');
        return $controller->$task();
    }

    /**
     * _setRequest
     *
     *  Retrieve request information needed to execute component
     *  Note: this is only used for <include:component attr=1 attr=2 etc />
     *      Not for the <include:request /> Primary Component defined in MolajoRequest
     */
    protected function _setRequest()
    {
        foreach ($this->_attributes as $name => $value) {
            if ($name == 'name' || $name == 'title' || $name == 'option') {
                $this->_request->set('extension_title', $value);
                $this->_request->set('option', $value);

            } else if ($name == 'wrap') {
                $this->_request->set('wrap', $value);

            } else if ($name == 'view') {
                $this->_request->set('view', $value);

            } else if ($name == 'id' || $name == 'wrap_id') {
                $this->_request->set('wrap_id', $value);

            } else if ($name == 'class' || $name == 'wrap_class') {
                $this->_request->set('wrap_class', $value);
            }
            // $this->_request->set('other_parameters') = $other_parameters;
        }

        $this->_request = MolajoExtensionHelper::getExtensionOptions($this->_request);
        if ($this->_request->get('results') === false) {
            echo 'failed getExtensionOptions';
        }

        /** View Path */
        $this->_request->set('view_type', 'extensions');
        $viewHelper = new MolajoViewHelper($this->_request->get('view'),
            $this->_request->get('view_type'),
            $this->_request->get('option'),
            $this->_request->get('extension_type'),
            ' ',
            $this->_request->get('template_name')
        );
        $this->_request->set('view_path', $viewHelper->view_path);
        $this->_request->set('view_path_url', $viewHelper->view_path_url);

        /** Wrap Path */
        $wrapHelper = new MolajoViewHelper($this->_request->get('wrap'),
            'wraps',
            $this->_request->get('option'),
            $this->_request->get('extension_type'),
            ' ',
            $this->_request->get('template_name')
        );
        $this->_request->set('wrap_path', $wrapHelper->view_path);
        $this->_request->set('wrap_path_url', $wrapHelper->view_path_url);
    }

    /**
     * import
     *
     * imports component folders and files
     * @since 1.0
     */
    protected function _import()
    {
        $fileHelper = new MolajoFileHelper();

        /** Controllers */
        if (file_exists($this->_request->get('extension_path') . '/controller.php')) {
            $fileHelper->requireClassFile($this->_request->get('extension_path') . '/controller.php', ucfirst($this->_request->get('option')) . 'Controller');
        }
        $files = JFolder::files($this->_request->get('extension_path') . '/controllers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->_request->get('extension_path') . '/controllers/' . $file, ucfirst($this->_request->get('option')) . 'Controller' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }
        /** Helpers */
        $files = JFolder::files($this->_request->get('extension_path') . '/helpers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->_request->get('extension_path') . '/helpers/' . $file, ucfirst($this->_request->get('option')) . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Models */
        $files = JFolder::files($this->_request->get('extension_path') . '/models', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->_request->get('extension_path') . '/models/' . $file, ucfirst($this->_request->get('option')) . 'Model' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Tables */
        $files = JFolder::files($this->_request->get('extension_path') . '/tables', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->_request->get('extension_path') . '/tables/' . $file, ucfirst($this->_request->get('option')) . 'Table' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Views */
        $folders = JFolder::folders($this->_request->get('extension_path') . '/views', false, false);
        if ($folders) {
            foreach ($folders as $folder) {
                $files = JFolder::files($this->_request->get('extension_path') . '/views/' . $folder, false, false);
                if ($files) {
                    foreach ($files as $file) {
                        $fileHelper->requireClassFile($this->_request->get('extension_path') . '/views/' . $folder . '/' . $file, ucfirst($this->_request->get('option')) . 'View' . ucfirst($folder));
                    }
                }
            }
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
        MolajoController::getApplication()->getLanguage()->load
        ($this->_request->get('extension_path'),
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
        /**  Primary Category */
        $this->_loadMediaPlus('/category' . $this->_request->get('primary_category'),
            MolajoController::getApplication()->get('media_priority_primary_category', 700));

        /** Menu Item */
        $this->_loadMediaPlus('/menuitem' . $this->_request->get('menu_item'),
            MolajoController::getApplication()->get('media_priority_menu_item', 800));

        /** Source */
        $this->_loadMediaPlus('/source' . $this->_request->get('source_id'),
            MolajoController::getApplication()->get('media_priority_source_data', 900));

        /** Component */
        $this->_loadMediaPlus('/component' . $this->_request->get('option'),
            MolajoController::getApplication()->get('media_priority_source_data', 900));
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
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SITE_FOLDER_PATH_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . $plus;
        $urlPath = MOLAJO_SITE_FOLDER_PATH_MEDIA_URL . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, false);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $filePath = MOLAJO_SHARED_MEDIA . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SHARED_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $filePath = MOLAJO_SHARED_MEDIA . $plus;
        $urlPath = MOLAJO_SHARED_MEDIA_URL . $plus;
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
