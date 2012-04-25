<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Includer;

defined('MOLAJO') or die;

use Molajo\Application\Services;
use Molajo\Application\Request;
use Molajo\Extension\Helper\ExtensionHelper;
use Molajo\Extension\Helper\ThemeHelper;

/**
 * Theme
 *
 * @package   Molajo
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

        $this->parameters = Services::Registry()->initialise();
        $this->parameters->set('suppress_no_results', 0);
    }

    /**
     * render
     *
     * Establishes language files and media for theme
     *
     * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
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
     * Loads Metadata values into Services::Document Metadata array
     *
     * @return  null
     * @since   1.0
     */
    protected function _loadMetadata()
    {
        if (Services::Registry()->get('Request', 'status_error') == true) {

            Services::Document()->set_metadata('title',
                Services::Language()->translate('ERROR_FOUND'));

            Services::Document()->set_metadata('description', '');
            Services::Document()->set_metadata('keywords', '');
            Services::Document()->set_metadata('robots', '');
            Services::Document()->set_metadata('author', '');
            Services::Document()->set_metadata('content_rights', '');

        } else {

            Services::Document()->set_metadata('title',
                Services::Registry()->get('Request', 'metadata_title'));
            Services::Document()->set_metadata('description',
                Services::Registry()->get('Request', 'metadata_description'));
            Services::Document()->set_metadata('keywords',
                Services::Registry()->get('Request', 'metadata_keywords'));
            Services::Document()->set_metadata('robots',
                Services::Registry()->get('Request', 'metadata_robots'));
            Services::Document()->set_metadata('author',
                Services::Registry()->get('Request', 'metadata_author'));
            Services::Document()->set_metadata('content_rights',
                Services::Registry()->get('Request', 'metadata_content_rights'));

            Services::Document()->set_last_modified(
                Services::Registry()->get('Request', 'source_last_modified'));
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
            EXTENSIONS_THEMES . '/'
                . Services::Registry()->get('Request', 'theme_name')
        );
        /** Page view */
        ExtensionHelper::loadLanguage(
            Services::Registry()->get('Request', 'page_view_path')
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
            Services::Registry()->get('Configuration', 'media_priority_site', 100));

        /** Application */
        $this->_loadMediaPlus('/application' . APPLICATION,
            Services::Registry()->get('Configuration', 'media_priority_application', 200));

        /** User */
        $this->_loadMediaPlus('/user' .
                Services::User()
                    ->get('id'),
            Services::Registry()->get('Configuration', 'media_priority_user', 300));

        /** Theme Helper Load Media */
        $helperClass = 'Molajo' .
            ucfirst(Services::Registry()->get('Request', 'theme_name'))
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
            Services::Registry()->get('Configuration', 'media_priority_site', 100));

        $priority = Services::Registry()->get('Configuration', 'media_priority_theme', 600);
        $file_path = EXTENSIONS_THEMES . '/' .
            Services::Registry()->get('Request', 'theme_name');
        $url_path = EXTENSIONS_THEMES_URL . '/' .
            Services::Registry()->get('Request', 'theme_name');
        $css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        /** Page */
        $priority = Services::Registry()->get('Configuration', 'media_priority_theme', 600);
        $file_path = Services::Registry()->get('Request', 'page_view_path');
        $url_path = Services::Registry()->get('Request', 'page_view_path_url');
        $css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

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
