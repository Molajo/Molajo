<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Includer;

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
     * @param   string  $name
     * @param   string  $type
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_id', CATALOG_TYPE_THEME);
        $this->name = $name;
        $this->type = $type;

        return $this;
    }

    /**
     * Establishes language files and media for theme
     *
     * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
     *
     * @return mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        $this->loadMedia();

        return;
    }

    /**
     * loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  boolean true, if the file has successfully loaded.
     * @since   1.0
     */
    protected function loadMedia()
    {
        $this->loadMediaPlus('',
            Services::Registry()->get('parameters', 'asset_priority_site', 100));

        $this->loadMediaPlus('/application' . APPLICATION,
            Services::Registry()->get('parameters', 'asset_priority_application', 200));

        $this->loadMediaPlus('/user' . Services::Registry()->get(USER_LITERAL, 'id'),
            Services::Registry()->get('parameters', 'asset_priority_user', 300));

        $priority = Services::Registry()->get('parameters', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('parameters', 'theme_path');
        $url_path = Services::Registry()->get('parameters', 'theme_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $priority = Services::Registry()->get('parameters', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('parameters', 'page_view_path');
        $url_path = Services::Registry()->get('parameters', 'page_view_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        Services::Asset()->addLink(
            $url = Services::Registry()->get('parameters', 'theme_favicon'),
            $relation = 'shortcut icon',
            $relation_type = 'image/x-icon',
            $attributes = array()
        );

        $this->loadMediaPlus('', Services::Registry()->get('parameters', 'asset_priority_site', 100));

        return;
    }

    /**
     * loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  boolean  true, if the file has successfully loaded.
     * @since   1.0
     */
    protected function loadMediaPlus($plus = '', $priority = 500)
    {
        $file_path = SITE_MEDIA_FOLDER . '/' . $plus;
        $url_path = SITE_MEDIA_URL . '/' . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path = SITE_MEDIA_URL . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, false);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITES_MEDIA_URL . '/' . APPLICATION . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path = SITES_MEDIA_URL . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        return;
    }

    /**
     * Load Plugins Overrides from the Theme and/or Page View folders
     *
     * @return  void
     * @since   1.0
     */
    protected function loadPlugins()
    {
        Services::Event()->registerPlugins(
            Helpers::Extension()->getPath(CATALOG_TYPE_THEME, 'theme_path'),
            Helpers::Extension()->getNamespace(CATALOG_TYPE_THEME, 'theme_path')
        );

        Services::Event()->registerPlugins(
            Helpers::Extension()->getPath(CATALOG_TYPE_PAGE_VIEW, 'page_view_path_node'),
            Helpers::Extension()->getNamespace(CATALOG_TYPE_PAGE_VIEW, 'page_view_path_node')
        );

        return;
    }
}
