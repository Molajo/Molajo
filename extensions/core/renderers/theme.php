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
class MolajoRendererTheme extends MolajoRenderer
{
    /**
     * __construct
     *
     * Class constructor.
     *
     * @param  null   $name
     * @param  array  $request
     * @param  string $type
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        $this->_name = $name;
        $this->_type = $type;

        $this->parameters = new JRegistry;
        $this->parameters->set('extension_suppress_no_results', 0);
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
    public function render($attributes)
    {
        /** attributes from <include:renderer */
        $this->_attributes = $attributes;

        $this->_loadLanguage();

        /** css and js media files for extension and related entities */
        $this->_loadMedia();

        return;
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
        Molajo::App()->getLanguage()->load
        (Molajo::Request()->get('extension_path'),
            Molajo::App()->getLanguage()->getDefault(),
            false,
            false);
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
            Molajo::App()->get('media_priority_site', 100));

        /** Application */
        $this->_loadMediaPlus('/application' . MOLAJO_APPLICATION,
            Molajo::App()->get('media_priority_application', 200));

        /** User */
        $this->_loadMediaPlus('/user' . Molajo::User()->get('id'),
            Molajo::App()->get('media_priority_user', 300));

        /** Theme */
        $priority = Molajo::App()->get('media_priority_theme', 600);
        $filePath = MOLAJO_EXTENSIONS_THEMES . '/' . Molajo::Request()->get('theme_name');
        $urlPath = MOLAJO_EXTENSIONS_THEMES_URL . '/' . Molajo::Request()->get('theme_name');

        $css = Molajo::App()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::App()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $js = Molajo::App()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);
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
        $filePath = MOLAJO_SITE_MEDIA_FOLDER . '/' . $plus;
        $urlPath = MOLAJO_SITE_MEDIA_URL . '/' . $plus;
        $css = Molajo::App()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::App()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $defer = Molajo::App()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $filePath = MOLAJO_SITE_MEDIA_FOLDER . $plus;
        $urlPath = MOLAJO_SITE_MEDIA_URL . $plus;
        $css = Molajo::App()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::App()->addScriptLinksFolder($filePath, $urlPath, $priority, false);
        $defer = Molajo::App()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $filePath = MOLAJO_SITES_MEDIA_FOLDER . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SITES_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = Molajo::App()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::App()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $defer = Molajo::App()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $filePath = MOLAJO_SITES_MEDIA_FOLDER . $plus;
        $urlPath = MOLAJO_SITES_MEDIA_URL . $plus;
        $css = Molajo::App()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::App()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $defer = Molajo::App()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** nothing was loaded */
        return false;
    }
}
