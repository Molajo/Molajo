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
class MolajoThemeRenderer extends MolajoRendererController
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
    public function render($attributes=array())
    {
        $this->_loadLanguage();
        $this->_loadMedia();

        return;
    }

    /**
     * _loadLanguage
     *
     * Loads Language Files for Theme
     *
     * @return  null
     * @since   1.0
     */
    protected function _loadLanguage()
    {
        return Molajo::Application()->getLanguage()->load
        (Molajo::Request()->get('extension_path'),
            Molajo::Application()->getLanguage()->getDefault(),
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
            Molajo::Application()->get('media_priority_site', 100));

        /** Application */
        $this->_loadMediaPlus('/application' . MOLAJO_APPLICATION,
            Molajo::Application()->get('media_priority_application', 200));

        /** User */
        $this->_loadMediaPlus('/user' . Molajo::User()->get('id'),
            Molajo::Application()->get('media_priority_user', 300));

        /** Theme */
        $priority = Molajo::Application()->get('media_priority_theme', 600);
        $filePath = MOLAJO_EXTENSIONS_THEMES . '/' . Molajo::Request()->get('theme_name');
        $urlPath = MOLAJO_EXTENSIONS_THEMES_URL . '/' . Molajo::Request()->get('theme_name');

        $css = Molajo::Application()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::Application()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $js = Molajo::Application()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);

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
        $filePath = MOLAJO_SITE_MEDIA_FOLDER . '/' . $plus;
        $urlPath = MOLAJO_SITE_MEDIA_URL . '/' . $plus;
        $css = Molajo::Application()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::Application()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $defer = Molajo::Application()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);

        /** Site Specific: Site-wide */
        $filePath = MOLAJO_SITE_MEDIA_FOLDER . $plus;
        $urlPath = MOLAJO_SITE_MEDIA_URL . $plus;
        $css = Molajo::Application()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::Application()->addScriptLinksFolder($filePath, $urlPath, $priority, false);
        $defer = Molajo::Application()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);

        /** All Sites: Application */
        $filePath = MOLAJO_SITES_MEDIA_FOLDER . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SITES_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = Molajo::Application()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::Application()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $defer = Molajo::Application()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);

        /** All Sites: Site Wide */
        $filePath = MOLAJO_SITES_MEDIA_FOLDER . $plus;
        $urlPath = MOLAJO_SITES_MEDIA_URL . $plus;
        $css = Molajo::Application()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = Molajo::Application()->addScriptLinksFolder($filePath, $urlPath, $priority, 0);
        $defer = Molajo::Application()->addScriptLinksFolder($filePath, $urlPath, $priority, 1);

        return;
    }
}
