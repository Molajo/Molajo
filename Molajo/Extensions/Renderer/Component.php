<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Renderer;
namespace Molajo\Application;

defined('MOLAJO') or die;

use Molajo\Renderer;

/**
 * Component
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class ComponentRenderer extends Renderer
{
    /**
     * _setRenderCriteria
     *
     * Initialize the request object for MVC values
     *
     * @return mixed
     */
    protected function _setRenderCriteria()
    {
        if ($this->type == 'request') {
        } else {
            parent::_setRenderCriteria();
            return;
        }

        $this->parameters = Molajo::Request()->parameters;

        $this->task_request = new Registry();

        /** extension */
        $this->set('extension_instance_id',
            (int)Molajo::Request()->get('extension_instance_id'));
        $this->set('extension_instance_name',
            Molajo::Request()->get('extension_instance_name'));
        $this->set('extension_asset_type_id',
            (int)Molajo::Request()->get('extension_asset_type_id'));
        $this->set('extension_asset_id',
            (int)Molajo::Request()->get('extension_asset_id'));
        $this->set('extension_view_group_id',
            (int)Molajo::Request()->get('extension_view_group_id'));
        $this->set('extension_custom_fields',
            Molajo::Request()->get('extension_custom_fields'));
        $this->set('extension_metadata',
            Molajo::Request()->get('extension_metadata'));
        $this->set('extension_parameters',
            Molajo::Request()->get('extension_parameters'));
        $this->set('extension_path',
            Molajo::Request()->get('extension_path'));
        $this->set('extension_type',
            Molajo::Request()->get('extension_type'));
        $this->set('source_asset_type_id',
            Molajo::Request()->get('source_asset_type_id'));

        $this->set('extension_primary', true);

        $this->set('extension_event_type',
            Molajo::Request()->get('extension_event_type'));

        /** view */
        $this->set('template_view_id',
            (int)Molajo::Request()->get('template_view_id'));
        $this->set('template_view_name',
            Molajo::Request()->get('template_view_name'));
        $this->set('template_view_css_id',
            Molajo::Request()->get('template_view_css_id'));
        $this->set('template_view_css_class',
            Molajo::Request()->get('template_view_css_class'));
        $this->set('template_view_asset_type_id',
            Molajo::Request()->get('template_view_asset_type_id'));
        $this->set('template_view_asset_id',
            (int)Molajo::Request()->get('template_view_asset_id'));
        $this->set('template_view_path',
            Molajo::Request()->get('template_view_path'));
        $this->set('template_view_path_url',
            Molajo::Request()->get('template_view_path_url'));

        /** wrap */
        $this->set('wrap_view_id',
            (int)Molajo::Request()->get('wrap_view_id'));
        $this->set('wrap_view_name',
            Molajo::Request()->get('wrap_view_name'));
        $this->set('wrap_view_css_id',
            Molajo::Request()->get('wrap_view_css_id'));
        $this->set('wrap_view_css_class',
            Molajo::Request()->get('wrap_view_css_class'));
        $this->set('wrap_view_asset_type_id',
            Molajo::Request()->get('wrap_view_asset_type_id'));
        $this->set('wrap_view_asset_id',
            (int)Molajo::Request()->get('wrap_view_asset_id'));
        $this->set('wrap_view_path',
            Molajo::Request()->get('wrap_view_path'));
        $this->set('wrap_view_path_url',
            Molajo::Request()->get('wrap_view_path_url'));

        /** mvc parameters */
        $this->set('controller',
            Molajo::Request()->get('mvc_controller'));
        $this->set('task',
            Molajo::Request()->get('mvc_task'));
        $this->set('model',
            Molajo::Request()->get('mvc_model'));
        $this->set('table',
            Molajo::Request()->get('source_table'));
        $this->set('id',
            (int)Molajo::Request()->get('mvc_id'));
        $this->set('category_id',
            (int)Molajo::Request()->get('mvc_category_id'));
        $this->set('suppress_no_results',
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
        $this->set(
            'extension_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT
        );

        $results = parent::_getExtension();
        if ($results === false) {
            return false;
        }

        $this->set(
            'extension_path',
            ComponentHelper::getPath(
                strtolower($this->get('extension_instance_name')))
        );

        $this->set('extension_type', 'component');

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
        $load = new LoadHelper();

        $name = ucfirst($this->get('extension_instance_name'));
        $name = str_replace(array('-', '_'), '', $name);
        $name = 'Molajo' . $name;

        /** Controllers */
        if (file_exists($this->get('extension_path') . '/controller.php')) {
            $load->requireClassFile(
                $this->get('extension_path') . '/controller.php',
                $name . 'Controller'
            );
        }
        $files = Service::Folder()->files($this->get('extension_path') . '/controllers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $load->requireClassFile(
                    $this->get('extension_path') . '/controllers/' . $file,
                    $name . 'Controller' . ucfirst(substr($file, 0, strpos($file, '.')))
                );
            }
        }

        /** Helpers */
        $files = Service::Folder()->files($this->get('extension_path') . '/helpers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $load->requireClassFile($this->get('extension_path') . '/helpers/' . $file,
                    $name . ucfirst(substr($file, 0, strpos($file, '.')))
                );
            }
        }

        /** Models */
        $files = Service::Folder()->files($this->get('extension_path') . '/models', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $load->requireClassFile($this->get('extension_path') . '/models/' . $file,
                    $name . 'Model' . ucfirst(substr($file, 0, strpos($file, '.')))
                );
            }
        }

        /** Tables */
        $files = Service::Folder()->files($this->get('extension_path') . '/tables', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $load->requireClassFile($this->get('extension_path') . '/tables/' . $file,
                    $name . 'Table' . ucfirst(substr($file, 0, strpos($file, '.')))
                );
            }
        }

        /** Views */
        $folders = Service::Folder()->folders($this->get('extension_path') . '/views', false, false);
        if ($folders) {
            foreach ($folders as $folder) {
                $files = Service::Folder()->files($this->get('extension_path') . '/views/' . $folder, false, false);
                if ($files) {
                    foreach ($files as $file) {
                        $load->requireClassFile($this->get('extension_path') . '/views/' . $folder . '/' . $file,
                            $name . 'View' . ucfirst($folder));
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
        $this->_loadMediaPlus('/category' . $this->get('category_id'),
            Service::Configuration()->get('media_priority_primary_category', 700));

        /** Menu Item */
        $this->_loadMediaPlus('/menuitem' . $this->get('menu_item_id'),
            Service::Configuration()->get('media_priority_menu_item', 800));

        /** Source */
        $this->_loadMediaPlus('/source' . $this->get('id'),
            Service::Configuration()->get('media_priority_source_data', 900));

        /** Component */
        $this->_loadMediaPlus('/component' . $this->get('extension_instance_name'),
            Service::Configuration()->get('media_priority_source_data', 900));

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
        $file_path = MOLAJO_EXTENSIONS_THEMES . '/' . $this->get('theme_name');
        $url_path = MOLAJO_EXTENSIONS_THEMES_URL . '/' . $this->get('theme_name');
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Application */
        $file_path = SITE_MEDIA_FOLDER . '/' . MOLAJO_APPLICATION . $plus;
        $url_path = SITE_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path = SITE_MEDIA_URL . $plus;
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, false);
        $defer = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $file_path = SITES_MEDIA_FOLDER . '/' . MOLAJO_APPLICATION . $plus;
        $url_path = SITES_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path = SITES_MEDIA_URL . $plus;
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** nothing was loaded */
        return true;
    }
}
