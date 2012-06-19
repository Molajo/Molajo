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
            . ucfirst(Services::Registry()->get('Parameters', 'theme_path_node')) . '\\Helper\\'
            . 'Theme' . ucfirst(Services::Registry()->get('Parameters', 'theme_path_node')) . 'Helper';

        if (class_exists($helperClass)) {
            $load = new $helperClass();
            if (method_exists($load, 'loadMedia')) {
                $load->loadMedia();
            }
        }

		//todo figure out why the theme media is not loading
		$this->loadMedia2();

        /** Theme */
        $priority = Services::Registry()->get('Parameters', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('Parameters', 'theme_path');
        $url_path = Services::Registry()->get('Parameters', 'theme_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        /** Page */
        $priority = Services::Registry()->get('Parameters', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('Parameters', 'page_view_path');
        $url_path = Services::Registry()->get('Parameters', 'page_view_path_url');

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

		/** Site Favicon */
		Services::Asset()->addLink(
			$url = Services::Registry()->get('Parameters', 'theme_favicon'),
			$relation = 'shortcut icon',
			$relation_type = 'image/x-icon',
			$attributes = array()
		);

        /** Catalog ID specific */
        $this->loadMediaPlus('', Services::Registry()->get('Parameters', 'asset_priority_site', 100));

        return;
    }

	/**
	 * loadMedia - automatically loaded in the Theme Rendering process
	 * Method can be used to load external media, special metadata or links
	 *
	 * http://coding.smashingmagazine.com/2012/01/16/resolution-independence-with-svg/
	 *
	 * http://24ways.org/2011/displaying-icons-with-fonts-and-data-attributes
	 *
	 * http://keyamoon.com/icomoon/#toHome
	 * adapt.js
	 * http://responsivepx.com/
	 * http://mattkersley.com/responsive/
	 * http://www.responsinator.com/
	 * http://quirktools.com/screenfly/
	 *
	 * @since  1.0
	 */
	public function loadMedia2()
	{
		/** Theme Folder */
		$theme = Services::Registry()->get('Parameters', 'theme_path_node');

		/** IE */
		Services::Metadata()->set('X-UA-Compatible', 'IE=EmulateIE7; IE=EmulateIE9', 'http-equiv');

		/** Mobile Specific Meta */
		Services::Metadata()->set(
			'viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no', 'name'
		);

		/** Media Queries to load CSS */
		Services::Asset()->addCss(
			$url = EXTENSIONS_THEMES_URL . '/' . $theme . '/' . 'css/grid/base.css',
			$priority=1000,
			$mimetype='test/css',
			$media='all',
			$conditional='',
			$attributes=array());

		Services::Asset()->addCss(
			$url=EXTENSIONS_THEMES_URL. '/' . $theme . '/' . 'css/grid/720_grid.css',
			$priority=1000,
			$mimetype='test/css',
			$media='',
			$conditional='lt IE 9',
			$attributes=array());

		Services::Asset()->addCss(
			$url=EXTENSIONS_THEMES_URL. '/' . $theme . '/' . 'css/grid/720_grid.css',
			$priority=1000,
			$mimetype='test/css',
			$media='screen and (min-width: 720px)',
			$conditional='',
			$attributes=array());

		Services::Asset()->addCss($url = EXTENSIONS_THEMES_URL. '/' . $theme . '/' . 'css/grid/986_grid.css',
			$priority=1000,
			$mimetype='test/css',
			$media='screen and (min-width: 986px)',
			$conditional='',
			$attributes=array());

		Services::Asset()->addCss($url = EXTENSIONS_THEMES_URL. '/' . $theme . '/' . 'css/grid/1236_grid.css',
			$priority=1000,
			$mimetype='test/css',
			$media='screen and (min-width: 1236px)',
			$conditional='',
			$attributes=array());

		/** jQuery CDN and fallback */
		Services::Asset()->addJs('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', 1);

		/** Modernizer */
		Services::Asset()->addJs('http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.5.3/modernizr.min.js', 10000);
		/** HTML5 Shiv */
		Services::Asset()->addJs('http://html5shiv.googlecode.com/svn/trunk/html5.js', 10000);

		$url = EXTENSIONS_THEMES_URL . '/' . $theme  . '/' . 'js/fallback/jquery-1.7.2.min.js';

		$fallback = "
        if (typeof jQuery == 'undefined') {
            document.write(unescape(" . '"' . "%3Cscript src='" . $url . "' type='text/javascript'%3E%3C/script%3E" . '"' . "));
         }";

		Services::Asset()->addJSDeclarations($fallback, 'text/javascript', 10000);

		/** Favicons */
		Services::Asset()->addLink(
			$url = EXTENSIONS_THEMES_URL . '/' . $theme  . '/' . 'images/apple-touch-icon.png',
			$relation = 'apple-touch-icon-precomposed',
			$relation_type = 'rel',
			$attributes = array()
		);
		Services::Asset()->addLink(
			$url = EXTENSIONS_THEMES_URL . '/' . $theme . '/' . 'images/apple-touch-icon-72x72.png',
			$relation = 'apple-touch-icon-precomposed',
			$relation_type = 'rel',
			$attributes = array('sizes,72x72')
		);
		Services::Asset()->addLink(
			$url = EXTENSIONS_THEMES_URL . '/' . $theme . '/' . 'images/apple-touch-icon-114x114.png',
			$relation = 'apple-touch-icon-precomposed',
			$relation_type = 'rel',
			$attributes = array('sizes,114x114')
		);

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

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        /** Site Specific: Site-wide */
        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path = SITE_MEDIA_URL . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, false);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        /** All Sites: Application */
        $file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITES_MEDIA_URL . '/' . APPLICATION . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        /** All Sites: Site Wide */
        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path = SITES_MEDIA_URL . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        return;
    }
}
