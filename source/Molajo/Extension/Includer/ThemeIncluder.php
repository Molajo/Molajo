<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Includer;

use Molajo\Extension\Helpers;
use Molajo\Service\Services;
use Molajo\Extension\Includer;

defined('MOLAJO') or die;

/**
 * Theme
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class ThemeIncluder extends Includer
{
    /**
     * __construct
     *
     * Class constructor.
     *
     * @param string $name
     * @param string $type
     *
     * @return null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        Services::Registry()->set('Parameters', 'extension_catalog_type_id', CATALOG_TYPE_EXTENSION_THEME);
        $this->name = $name;
        $this->type = $type;

        return $this;
    }

    /**
     * render
     *
     * Establishes language files and media for theme
     *
     * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
     *
     * @return mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        $this->loadLanguage();

        $this->loadMedia();

        return;
    }

    /**
     * loadLanguage
     *
     * Loads Language Files for extension
     *
     * @return null
     * @since   1.0
     */
    protected function loadLanguage()
    {
        /** Theme */
        Helpers::Extension()->loadLanguage(Services::Registry()->get('Parameters', 'theme_path'));

        /** Page View */
        Helpers::Extension()->loadLanguage(Services::Registry()->get('Parameters', 'page_view_path'));
    }

    /**
     * loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return boolean True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function loadMedia()
    {
        /**  Site */
        $this->loadMediaPlus('',
            Services::Registry()->get('Parameters', 'asset_priority_site', 100));

        /** Application */
        $this->loadMediaPlus('/application' . APPLICATION,
            Services::Registry()->get('Parameters', 'asset_priority_application', 200));

        /** User */
        $this->loadMediaPlus('/user' . Services::Registry()->get('User', 'id'),
            Services::Registry()->get('Parameters', 'asset_priority_user', 300));

        /** Load custom Theme Helper Media, if exists */
        $helperClass = 'Molajo\\Extension\\Theme\\'
            . ucfirst(Services::Registry()->get('Theme', 'title')) . '\\Helper\\'
            . 'Theme' . ucfirst(Services::Registry()->get('Theme', 'title')) . 'Helper';

        if (\class_exists($helperClass)) {
            $load = new $helperClass();
            if (\method_exists($load, 'loadMedia')) {
                $load->loadMedia();
            }
        }

        /** Theme */
        $priority = Services::Registry()->get('Parameters', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('Parameters', 'theme_path');
        $url_path = Services::Registry()->get('Parameters', 'theme_path_url');

        Services::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        /** Page */
        $priority = Services::Registry()->get('Parameters', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('Parameters', 'page_view_path');
        $url_path = Services::Registry()->get('Parameters', 'page_view_path_url');

        $css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        /** Catalog ID specific */
        $this->loadMediaPlus('', Services::Registry()->get('Parameters', 'asset_priority_site', 100));

        return;
    }

    /**
     * loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return boolean True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function loadMediaPlus($plus = '', $priority = 500)
    {
        /** Site Specific: Application */
        $file_path = SITE_MEDIA_FOLDER . '/' . $plus;
        $url_path = SITE_MEDIA_URL . '/' . $plus;

        $css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        /** Site Specific: Site-wide */
        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path = SITE_MEDIA_URL . $plus;

        $css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, false);
        $defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        /** All Sites: Application */
        $file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITES_MEDIA_URL . '/' . APPLICATION . $plus;

        $css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        /** All Sites: Site Wide */
        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path = SITES_MEDIA_URL . $plus;

        $css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        return;
    }
}
