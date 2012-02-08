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
     * _setRequest
     *
     * Initialize the request object for MVC values
     *
     * @return mixed
     */
    protected function _setRequest()
    {

//        if ($this->_type == 'request') {
//        } else {
            parent::_setRequest();
            return;
//        }

        $this->task = new Registry();

        /** extension */
        $this->task->set('extension_instance_id',
            (int) Molajo::Request()->get('extension_instance_id'));
        $this->task->set('extension_instance_name',
            Molajo::Request()->get('extension_instance_name'));
        $this->task->set('extension_asset_type_id',
            (int) Molajo::Request()->get('extension_asset_type_id'));
        $this->task->set('extension_asset_id',
            (int) Molajo::Request()->get('extension_asset_id'));
        $this->task->set('extension_view_group_id',
            (int) Molajo::Request()->get('extension_view_group_id'));
        $this->task->set('extension_custom_fields',
            Molajo::Request()->get('extension_custom_fields'));
        $this->task->set('extension_metadata',
            Molajo::Request()->get('extension_metadata'));
        $this->task->set('extension_parameters',
            Molajo::Request()->get('extension_parameters'));
        $this->task->set('extension_path',
            Molajo::Request()->get('extension_path'));
        $this->task->set('extension_type',
            Molajo::Request()->get('extension_type'));
        $this->task->set('extension_folder','');
        $this->task->set('extension_event_type',
            Molajo::Request()->get('extension_event_type'));

        /** view */
        $this->task->set('template_view_id',
            (int) Molajo::Request()->get('template_view_id'));
        $this->task->set('template_view_name',
            Molajo::Request()->get('template_view_name'));
        $this->task->set('template_view_css_id',
            Molajo::Request()->get('template_view_css_id'));
        $this->task->set('template_view_css_class',
            Molajo::Request()->get('template_view_css_class'));
        $this->task->set('template_view_asset_type_id',
            Molajo::Request()->get('template_view_asset_type_id'));
        $this->task->set('template_view_asset_id',
            (int) Molajo::Request()->get('template_view_asset_id'));
        $this->task->set('template_view_path',
            Molajo::Request()->get('template_view_path'));
        $this->task->set('template_view_path_url',
            Molajo::Request()->get('template_view_path_url'));

        /** wrap */
        $this->task->set('wrap_view_id',
            (int) Molajo::Request()->get('wrap_view_id'));
        $this->task->set('wrap_view_name',
            Molajo::Request()->get('wrap_view_name'));
        $this->task->set('wrap_view_css_id',
            Molajo::Request()->get('wrap_view_css_id'));
        $this->task->set('wrap_view_css_class',
            Molajo::Request()->get('wrap_view_css_class'));
        $this->task->set('wrap_view_asset_type_id',
            Molajo::Request()->get('wrap_view_asset_type_id'));
        $this->task->set('wrap_view_asset_id',
            (int) Molajo::Request()->get('wrap_view_asset_id'));
        $this->task->set('wrap_view_path',
            Molajo::Request()->get('wrap_view_path'));
        $this->task->set('wrap_view_path_url',
            Molajo::Request()->get('wrap_view_path_url'));

        /** mvc parameters */
        $this->task->set('controller',
            Molajo::Request()->get('mvc_controller'));
        $this->task->set('task',
            Molajo::Request()->get('mvc_task'));
        $this->task->set('model',
            Molajo::Request()->get('mvc_model'));
        $this->task->set('id',
            (int) Molajo::Request()->get('mvc_id'));
        $this->task->set('category_id',
            (int) Molajo::Request()->get('mvc_category_id'));
        $this->task->set('suppress_no_results',
            (bool)Molajo::Request()->get('mvc_suppress_no_results'));

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
        $this->task->set('extension_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT);

        $results = parent::_getExtension();
        if ($results === false) {
            return false;
        }

        $this->task->set('extension_path',
            ComponentHelper::getPath(
                strtolower($this->task->get('extension_instance_name'))));

        $this->task->set('extension_type', 'component');

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
        $fileHelper = new FileServices();

        $name = ucfirst($this->task->get('extension_instance_name'));
        $name = str_replace (array('-', '_'), '', $name);

        /** Controllers */
        if (file_exists($this->task->get('extension_path') . '/controller.php')) {
            $fileHelper->requireClassFile($this->task->get('extension_path') . '/controller.php', $name . 'Controller');
        }
        $files = JFolder::files($this->task->get('extension_path') . '/controllers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->task->get('extension_path') . '/controllers/' . $file, $name . 'Controller' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Helpers */
        $files = JFolder::files($this->task->get('extension_path') . '/helpers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->task->get('extension_path') . '/helpers/' . $file, $name . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Models */
        $files = JFolder::files($this->task->get('extension_path') . '/models', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->task->get('extension_path') . '/models/' . $file, $name . 'Model' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Tables */
        $files = JFolder::files($this->task->get('extension_path') . '/tables', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->task->get('extension_path') . '/tables/' . $file, $name . 'Table' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Views */
        $folders = JFolder::folders($this->task->get('extension_path') . '/views', false, false);
        if ($folders) {
            foreach ($folders as $folder) {
                $files = JFolder::files($this->task->get('extension_path') . '/views/' . $folder, false, false);
                if ($files) {
                    foreach ($files as $file) {
                        $fileHelper->requireClassFile($this->task->get('extension_path') . '/views/' . $folder . '/' . $file, $name . 'View' . ucfirst($folder));
                    }
                }
            }
        }
    }

    /**
     * _loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  bool
     * @since   1.0
     */
    protected function _loadMedia()
    {
        /**  Primary Category */
        $this->_loadMediaPlus('/category' . $this->task->get('category_id'),
            Molajo::Application()->get('media_priority_primary_category', 700));

        /** Menu Item */
        $this->_loadMediaPlus('/menuitem' . $this->task->get('menu_item_id'),
            Molajo::Application()->get('media_priority_menu_item', 800));

        /** Source */
        $this->_loadMediaPlus('/source' . $this->task->get('id'),
            Molajo::Application()->get('media_priority_source_data', 900));

        /** Component */
        $this->_loadMediaPlus('/component' . $this->task->get('extension_instance_name'),
            Molajo::Application()->get('media_priority_source_data', 900));

        return true;
    }

    /**
     * _loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  bool
     * @since   1.0
     */
    protected function _loadMediaPlus($plus = '', $priority = 500)
    {
        /** Theme */
        $filePath = MOLAJO_EXTENSIONS_THEMES . '/' . $this->task->get('theme_name');
        $urlPath = MOLAJO_EXTENSIONS_THEMES_URL . '/' . $this->task->get('theme_name');
        $css = Molajo::Responder()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::Responder()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $defer = Molajo::Responder()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Application */
        $filePath = MOLAJO_SITE_MEDIA_FOLDER . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SITE_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = Molajo::Responder()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::Responder()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $defer = Molajo::Responder()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $filePath = MOLAJO_SITE_MEDIA_FOLDER . $plus;
        $urlPath = MOLAJO_SITE_MEDIA_URL . $plus;
        $css = Molajo::Responder()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::Responder()->addScriptLinksFolder($filePath, $urlPath, $priority, false);
        $defer = Molajo::Responder()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $filePath = MOLAJO_SITES_MEDIA_FOLDER . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SITES_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = Molajo::Responder()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::Responder()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $defer = Molajo::Responder()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $filePath = MOLAJO_SITES_MEDIA_FOLDER . $plus;
        $urlPath = MOLAJO_SITES_MEDIA_URL . $plus;
        $css = Molajo::Responder()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::Responder()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $defer = Molajo::Responder()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** nothing was loaded */
        return true;
    }
}
