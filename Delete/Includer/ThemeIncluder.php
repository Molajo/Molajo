<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Includer;

defined('MOLAJO') or die;

use Molajo\Service;
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

        $this->parameters = Service::Registry()->initialise();
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
     * Loads Metadata values into Service::Document Metadata array
     *
     * @return  null
     * @since   1.0
     */
    protected function _loadMetadata()
    {
        if (Service::Registry()->get('Request', 'status_error') == true) {

            Service::Document()->set_metadata('title',
                Service::Language()->translate('ERROR_FOUND'));

            Service::Document()->set_metadata('description', '');
            Service::Document()->set_metadata('keywords', '');
            Service::Document()->set_metadata('robots', '');
            Service::Document()->set_metadata('author', '');
            Service::Document()->set_metadata('content_rights', '');

        } else {

            Service::Document()->set_metadata('title',
                Service::Registry()->get('Request', 'metadata_title'));
            Service::Document()->set_metadata('description',
                Service::Registry()->get('Request', 'metadata_description'));
            Service::Document()->set_metadata('keywords',
                Service::Registry()->get('Request', 'metadata_keywords'));
            Service::Document()->set_metadata('robots',
                Service::Registry()->get('Request', 'metadata_robots'));
            Service::Document()->set_metadata('author',
                Service::Registry()->get('Request', 'metadata_author'));
            Service::Document()->set_metadata('content_rights',
                Service::Registry()->get('Request', 'metadata_content_rights'));

            Service::Document()->set_last_modified(
                Service::Registry()->get('Request', 'source_last_modified'));
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
                . Service::Registry()->get('Request', 'theme_name')
        );
        /** Page view */
        ExtensionHelper::loadLanguage(
            Service::Registry()->get('Request', 'page_view_path')
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
            Service::Registry()->get('Configuration', 'media_priority_site', 100));

        /** Application */
        $this->_loadMediaPlus('/application' . APPLICATION,
            Service::Registry()->get('Configuration', 'media_priority_application', 200));

        /** User */
        $this->_loadMediaPlus('/user' .
                Service::User()
                    ->get('id'),
            Service::Registry()->get('Configuration', 'media_priority_user', 300));

        /** Theme Helper Load Media */
        $helperClass = 'Molajo' .
            ucfirst(Service::Registry()->get('Request', 'theme_name'))
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
            Service::Registry()->get('Configuration', 'media_priority_site', 100));

        $priority = Service::Registry()->get('Configuration', 'media_priority_theme', 600);
        $file_path = EXTENSIONS_THEMES . '/' .
            Service::Registry()->get('Request', 'theme_name');
        $url_path = EXTENSIONS_THEMES_URL . '/' .
            Service::Registry()->get('Request', 'theme_name');
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        /** Page */
        $priority = Service::Registry()->get('Configuration', 'media_priority_theme', 600);
        $file_path = Service::Registry()->get('Request', 'page_view_path');
        $url_path = Service::Registry()->get('Request', 'page_view_path_url');
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);

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
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        /** Site Specific: Site-wide */
        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path = SITE_MEDIA_URL . $plus;
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, false);
        $defer = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        /** All Sites: Application */
        $file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITES_MEDIA_URL . '/' . APPLICATION . $plus;
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        /** All Sites: Site Wide */
        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path = SITES_MEDIA_URL . $plus;
        $css = Service::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Service::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Service::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        return;
    }
}
