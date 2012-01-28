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
class MolajoRendererComponent extends MolajoRenderer
{
    /**
     * _setRequest
     *
     * Initialize the request object for MVC values
     *
     * @return mixed
     */
    protected function _setRequest()
    {
        if ($this->_type == 'request') {
        } else {
            parent::_setRequest();
            return;
        }

        $this->mvc = new JObject();

        /** extension */
        $this->mvc->set('extension_instance_id',
            (int) $this->request->get('extension_instance_id'));
        $this->mvc->set('extension_instance_name',
            $this->request->get('extension_instance_name'));
        $this->mvc->set('extension_asset_type_id',
            (int) $this->request->get('extension_asset_type_id'));
        $this->mvc->set('extension_asset_id',
            (int) $this->request->get('extension_asset_id'));
        $this->mvc->set('extension_view_group_id',
            (int) $this->request->get('extension_view_group_id'));
        $this->mvc->set('extension_custom_fields',
            $this->request->get('extension_custom_fields'));
        $this->mvc->set('extension_metadata',
            $this->request->get('extension_metadata'));
        $this->mvc->set('extension_parameters',
            $this->request->get('extension_parameters'));
        $this->mvc->set('extension_path',
            $this->request->get('extension_path'));
        $this->mvc->set('extension_type',
            $this->request->get('extension_type'));
        $this->mvc->set('extension_folder','');
        $this->mvc->set('extension_event_type',
            $this->request->get('extension_event_type'));

        /** view */
        $this->mvc->set('view_id',
            (int) $this->request->get('view_id'));
        $this->mvc->set('view_name',
            $this->request->get('view_name'));
        $this->mvc->set('view_css_id',
            $this->request->get('view_css_id'));
        $this->mvc->set('view_css_class',
            $this->request->get('view_css_class'));
        $this->mvc->set('view_asset_type_id',
            $this->request->get('view_asset_type_id'));
        $this->mvc->set('view_asset_id',
            (int) $this->request->get('view_asset_id'));
        $this->mvc->set('view_path',
            $this->request->get('view_path'));
        $this->mvc->set('view_path_url',
            $this->request->get('view_path_url'));

        /** wrap */
        $this->mvc->set('wrap_id',
            (int) $this->request->get('wrap_id'));
        $this->mvc->set('wrap_name',
            $this->request->get('wrap_name'));
        $this->mvc->set('wrap_css_id',
            $this->request->get('wrap_css_id'));
        $this->mvc->set('wrap_css_class',
            $this->request->get('wrap_css_class'));
        $this->mvc->set('wrap_asset_type_id',
            $this->request->get('wrap_asset_type_id'));
        $this->mvc->set('wrap_asset_id',
            (int) $this->request->get('wrap_asset_id'));
        $this->mvc->set('wrap_path',
            $this->request->get('wrap_path'));
        $this->mvc->set('wrap_path_url',
            $this->request->get('wrap_path_url'));

        /** mvc parameters */
        $this->mvc->set('mvc_controller',
            $this->request->get('mvc_controller'));
        $this->mvc->set('mvc_task',
            $this->request->get('mvc_task'));
        $this->mvc->set('mvc_model',
            $this->request->get('mvc_model'));
        $this->mvc->set('mvc_id',
            (int) $this->request->get('mvc_id'));
        $this->mvc->set('mvc_category_id',
            (int) $this->request->get('mvc_category_id'));
        $this->mvc->set('mvc_suppress_no_results',
            (bool)$this->request->get('mvc_suppress_no_results'));

        return;
    }

    /**
     * _getExtension
     *
     * Retrieve extension information using either the ID or the name
     *
     * @return  bool
     * @since   1.0
     */
    protected function _getExtension()
    {
        $this->mvc->set('extension_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT);

        $results = parent::_getExtension();
        if ($results === false) {
            return false;
        }

        $this->mvc->set('extension_path',
            MolajoComponentHelper::getPath(
                strtolower($this->mvc->get('extension_instance_name'))));

        $this->mvc->set('extension_type', 'component');

        return true;
    }

    /**
     * import
     *
     * imports component folders and files
     *
     * @return  true
     * @since   1.0
     */
    protected function _importClasses()
    {
        $fileHelper = new MolajoFileHelper();

        $name = ucfirst($this->mvc->get('extension_instance_name'));
        $name = str_replace (array('-', '_'), '', $name);

        /** Controllers */
        if (file_exists($this->mvc->get('extension_path') . '/controller.php')) {
            $fileHelper->requireClassFile($this->mvc->get('extension_path') . '/controller.php', $name . 'Controller');
        }
        $files = JFolder::files($this->mvc->get('extension_path') . '/controllers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->mvc->get('extension_path') . '/controllers/' . $file, $name . 'Controller' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Helpers */
        $files = JFolder::files($this->mvc->get('extension_path') . '/helpers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->mvc->get('extension_path') . '/helpers/' . $file, $name . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Models */
        $files = JFolder::files($this->mvc->get('extension_path') . '/models', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->mvc->get('extension_path') . '/models/' . $file, $name . 'Model' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Tables */
        $files = JFolder::files($this->mvc->get('extension_path') . '/tables', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->mvc->get('extension_path') . '/tables/' . $file, $name . 'Table' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Views */
        $folders = JFolder::folders($this->mvc->get('extension_path') . '/views', false, false);
        if ($folders) {
            foreach ($folders as $folder) {
                $files = JFolder::files($this->mvc->get('extension_path') . '/views/' . $folder, false, false);
                if ($files) {
                    foreach ($files as $file) {
                        $fileHelper->requireClassFile($this->mvc->get('extension_path') . '/views/' . $folder . '/' . $file, $name . 'View' . ucfirst($folder));
                    }
                }
            }
        }
    }

    /**
     * _loadMedia
     *
     * Loads Media Files for Site, Application, User, and Template
     *
     * @return  bool
     * @since   1.0
     */
    protected function _loadMedia()
    {
        /**  Primary Category */
        $this->_loadMediaPlus('/category' . $this->mvc->get('mvc_category_id'),
            MolajoController::getApplication()->get('media_priority_primary_category', 700));

        /** Menu Item */
        $this->_loadMediaPlus('/menuitem' . $this->mvc->get('menu_item_id'),
            MolajoController::getApplication()->get('media_priority_menu_item', 800));

        /** Source */
        $this->_loadMediaPlus('/source' . $this->mvc->get('mvc_id'),
            MolajoController::getApplication()->get('media_priority_source_data', 900));

        /** Component */
        $this->_loadMediaPlus('/component' . $this->mvc->get('extension_instance_name'),
            MolajoController::getApplication()->get('media_priority_source_data', 900));

        return true;
    }

    /**
     * _loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Template
     *
     * @return  bool
     * @since   1.0
     */
    protected function _loadMediaPlus($plus = '', $priority = 500)
    {
        /** Template */
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->mvc->get('template_name');
        $urlPath = MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $this->mvc->get('template_name');
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
        return true;
    }
}
