<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Theme
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoThemeRenderer extends MolajoRenderer
{
    /**
     * __construct
     *
     * Class constructor.
     *
     * @param  string $name
     * @param  string $type
     * @param  array  $items (used for event processing renderers, only)
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null, $items = null)
    {
        $this->name = $name;
        $this->type = $type;

        $this->parameters = new Registry;
        $this->parameters->set('suppress_no_results', 0);
    }

    /**
     * render
     *
     * Establishes language files and media for theme
     *
     * @param   $attributes <include:renderer attr1=x attr2=y attr3=z ... />
     *
     * @return  mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        $this->_loadMetadata();
        $this->_loadLanguage();
        $this->_loadMedia();

        return;
    }

    /**
     * _loadMetadata
     *
     * Loads Metadata values into Molajo::Response array
     *
     * @return  null
     * @since   1.0
     */
    protected function _loadMetadata()
    {
        if (Molajo::Request()->get('status_error') == true) {

            Services::Media()->set_metadata('title',
                Services::Language()->translate('ERROR_FOUND'));

            Services::Media()->set_metadata('description', '');
            Services::Media()->set_metadata('keywords', '');
            Services::Media()->set_metadata('robots', '');
            Services::Media()->set_metadata('author', '');
            Services::Media()->set_metadata('content_rights', '');

        } else {

            Services::Media()->set_metadata('title',
                Molajo::Request()->get('metadata_title'));
            Services::Media()->set_metadata('description',
                Molajo::Request()->get('metadata_description'));
            Services::Media()->set_metadata('keywords',
                Molajo::Request()->get('metadata_keywords'));
            Services::Media()->set_metadata('robots',
                Molajo::Request()->get('metadata_robots'));
            Services::Media()->set_metadata('author',
                Molajo::Request()->get('metadata_author'));
            Services::Media()->set_metadata('content_rights',
                Molajo::Request()->get('metadata_content_rights'));

            Services::Media()->set_last_modified(
                Molajo::Request()->get('source_last_modified'));
        }
    }

    /**
     * _loadLanguage
     *
     * Loads Language Files for extension
     *
     * @return  null
     * @since   1.0
     */
    protected function _loadLanguage()
    {
        /** theme */
        ExtensionHelper::loadLanguage(
            MOLAJO_EXTENSIONS_THEMES . '/'
                . Molajo::Request()->get('theme_name')
        );
        /** pages view */
        ExtensionHelper::loadLanguage(
            Molajo::Request()->get('page_view_path')
        );
    }

    /**
     * _loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadMedia()
    {
        /**  Site */
        $this->_loadMediaPlus('',
            Services::Configuration()->get('media_priority_site', 100));

        /** Application */
        $this->_loadMediaPlus('/application' . MOLAJO_APPLICATION,
            Services::Configuration()->get('media_priority_application', 200));

        /** User */
        $this->_loadMediaPlus('/user' .
                Services::User()
                    ->get('id'),
            Services::Configuration()->get('media_priority_user', 300));

        /** Theme Helper Load Media */
        $helperClass = 'Molajo' .
            ucfirst(Molajo::Request()->get('theme_name'))
            . 'ThemeHelper';

        if (class_exists($helperClass)) {
            $h = new $helperClass();
        } else {
            $helperClass = 'MolajoThemeHelper';
        }
        $h = new $helperClass();

        if (method_exists($helperClass, 'loadMedia')) {
            $h->loadMedia();
        }

        /** Theme */
        $this->_loadMediaPlus('',
            Services::Configuration()->get('media_priority_site', 100));

        $priority = Services::Configuration()->get('media_priority_theme', 600);
        $file_path = MOLAJO_EXTENSIONS_THEMES . '/' .
            Molajo::Request()->get('theme_name');
        $url_path = MOLAJO_EXTENSIONS_THEMES_URL . '/' .
            Molajo::Request()->get('theme_name');
        $css = Services::Media()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Media()->add_js_folder($file_path, $url_path, $priority, 0);
        $js = Services::Media()->add_js_folder($file_path, $url_path, $priority, 1);

        /** Page */
        $priority = Services::Configuration()->get('media_priority_theme', 600);
        $file_path = Molajo::Request()->get('page_view_path');
        $url_path = Molajo::Request()->get('page_view_path_url');
        $css = Services::Media()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Media()->add_js_folder($file_path, $url_path, $priority, 0);
        $js = Services::Media()->add_js_folder($file_path, $url_path, $priority, 1);

        return;
    }

    /**
     * _loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadMediaPlus($plus = '', $priority = 500)
    {
        /** Site Specific: Application */
        $file_path = SITE_MEDIA_FOLDER . '/' . $plus;
        $url_path = SITE_MEDIA_URL . '/' . $plus;
        $css = Services::Media()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Media()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Services::Media()->add_js_folder($file_path, $url_path, $priority, 1);

        /** Site Specific: Site-wide */
        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path = SITE_MEDIA_URL . $plus;
        $css = Services::Media()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Media()->add_js_folder($file_path, $url_path, $priority, false);
        $defer = Services::Media()->add_js_folder($file_path, $url_path, $priority, 1);

        /** All Sites: Application */
        $file_path = SITES_MEDIA_FOLDER . '/' . MOLAJO_APPLICATION . $plus;
        $url_path = SITES_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = Services::Media()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Media()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Services::Media()->add_js_folder($file_path, $url_path, $priority, 1);

        /** All Sites: Site Wide */
        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path = SITES_MEDIA_URL . $plus;
        $css = Services::Media()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Media()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Services::Media()->add_js_folder($file_path, $url_path, $priority, 1);

        return;
    }
}
