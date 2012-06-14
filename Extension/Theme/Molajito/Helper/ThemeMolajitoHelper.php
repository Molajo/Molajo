<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Theme\Molajito\Helper;

use Molajo\Extension\Helper\MustacheHelper;
use Molajo\Service\Services;

/**
 * Helper
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
Class ThemeMolajitoHelper extends MustacheHelper
{
    /**
     * Parameters
     *
     * @var    object
     * @since  1.0
     */
    public $parameters;

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
    public function loadMedia()
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
}
