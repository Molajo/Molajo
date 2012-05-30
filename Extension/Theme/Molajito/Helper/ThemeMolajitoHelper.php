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
     * loadMedia
     *
     * If exists, automatically called in the Theme Rendering process
     * Use this method to load external media, special metadata or links
     *
     * http://weblogs.asp.net/jgalloway/archive/2010/01/21/
     *  using-cdn-hosted-jquery-with-a-local-fall-back-copy.aspx
     *
     * Uses https://github.com/filamentgroup/Responsive-Images
     * Need image service to <img src="small.jpg?full=large.jpg" >
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
        /** Mobile Specific Meta */
        Services::Registry()->set('Metadata', 'viewport', 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no');

		/** IE */
		Services::Registry()->set('Metadata', 'X-UA-Compatible', 'IE=EmulateIE7; IE=EmulateIE9');

		/** Favicons */
		Services::Document()->add_link(
			$url = EXTENSIONS_THEMES_URL
				. '/' . Services::Registry()->get('Theme', 'title')
				. '/' . 'images/apple-touch-icon.png',
			$relation = 'apple-touch-icon-precomposed',
			$relation_type = 'rel',
			$attributes = array()
		);
		Services::Document()->add_link(
			$url = EXTENSIONS_THEMES_URL
				. '/' . Services::Registry()->get('Theme', 'title')
				. '/' . 'images/apple-touch-icon-72x72.png',
			$relation = 'apple-touch-icon-precomposed',
			$relation_type = 'rel',
			$attributes = array('sizes,72x72')
		);
		Services::Document()->add_link(
			$url = EXTENSIONS_THEMES_URL
				. '/' . Services::Registry()->get('Theme', 'title')
				. '/' . 'images/apple-touch-icon-114x114.png',
			$relation = 'apple-touch-icon-precomposed',
			$relation_type = 'rel',
			$attributes = array('sizes,114x114')
		);

		/** Media Queries to load CSS */
		Services::Document()->add_css(
			$url = EXTENSIONS_THEMES_URL
				. '/' . Services::Registry()->get('Parameters', 'theme_folder_node')
				. '/' . 'css/grid/base.css',
			$media='all',
			$priority=1000);

		Services::Document()->add_css(
			$url = EXTENSIONS_THEMES_URL
				. '/' . Services::Registry()->get('Parameters', 'theme_folder_node')
				. '/' . 'css/grid/720_grid.css',
			$media='screen and (min-width: 720px)',
			$conditional='lt IE 9', // Molajo <!--[if lt IE 9 ]> CSS LINE <![endif]-->
			$priority=1000);

		Services::Document()->add_css(
			$url = EXTENSIONS_THEMES_URL
				. '/' . Services::Registry()->get('Parameters', 'theme_folder_node')
				. '/' . 'css/grid/720_grid.css',
			$media='screen and (min-width: 720px)',
			$priority=1000);

		Services::Document()->add_css(
			$url = EXTENSIONS_THEMES_URL
				. '/' . Services::Registry()->get('Parameters', 'theme_folder_node')
				. '/' . 'css/grid/986_grid.css',
			$media='screen and (min-width: 986px)',
			$priority=1000);

		Services::Document()->add_css(
			$url = EXTENSIONS_THEMES_URL
				. '/' . Services::Registry()->get('Parameters', 'theme_folder_node')
				. '/' . 'css/grid/1236_grid.css',
			$media='screen and (min-width: 1236px)',
			$priority=1000);

        /** jQuery CDN and fallback */
        Services::Document()->add_js('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', 1000);

        /** Modernizer */
        Services::Document()->add_js('http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.5.3/modernizr.min.js', 1000);

        $url = EXTENSIONS_THEMES_URL
            . '/' . Services::Registry()->get('Theme', 'title')
            . '/' . 'js/fallback/jquery-1.7.1.min.js';

        $fallback = "
        if (typeof jQuery == 'undefined') {
            document.write(unescape(" . '"' . "%3Cscript src='" . $url . "' type='text/javascript'%3E%3C/script%3E" . '"' . "));
         }";

        Services::Document()->add_js_declaration
        ($fallback, 'text/javascript', 1000);

        $image_breakpoint = "
        var rwd_images = {
            //set the width breakpoint to 600px instead of 480px
            widthBreakPoint: 600
         }";

        Services::Document()->add_js_declaration
        ('rwd_images', 'text/javascript', 1000);
    }
}
