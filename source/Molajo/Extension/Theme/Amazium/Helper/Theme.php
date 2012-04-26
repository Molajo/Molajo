<?php
/**
 * @package   Molajo
 * @subpackage  Theme
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Theme;

defined('MOLAJO') or die;

use Molajo\Extension\Helper\MustacheHelper;

/**
 * Helper
 *
 * @package   Molajo
 * @subpackage  Helper
 * @since       1.0
 */
Class AmaziumThemeHelper extends MustacheHelper
{
    /**
     * hello
     *
     * Accesses User Object and returns a text message
     *
     * @results  object
     * @since    1.0
     */
    public function hello()
    {
        return 'Hello ' . Service::User()->get('name') . '!!!';
    }

    /**
     * loadMedia
     *
     * If exists, automatically called in the Theme Rendering process
     * Use this method to load external media, special metadata or links
     *
     * http://weblogs.asp.net/jgalloway/archive/2010/01/21/
     *  using-cdn-hosted-jquery-with-a-local-fall-back-copy.aspx
     *
     * @since  1.0
     */
    public function loadMedia()
    {
        /** Mobile Specific Meta */
        Service::Document()->set_metadata
        ('viewport', 'width=device-width, initial-scale=1, maximum-scale=1');

        /** Favicons */
        Service::Document()->add_link(
            $url = EXTENSIONS_THEMES_URL
                . '/' . Service::Registry()->get('Request', 'theme_name')
                . '/' . 'images/apple-touch-icon.png',
            $relation = 'apple-touch-icon-precomposed',
            $relation_type = 'rel',
            $attributes = array()
        );
        Service::Document()->add_link(
            $url = EXTENSIONS_THEMES_URL
                . '/' . Service::Registry()->get('Request', 'theme_name')
                . '/' . 'images/apple-touch-icon-72x72.png',
            $relation = 'apple-touch-icon-precomposed',
            $relation_type = 'rel',
            $attributes = array('sizes,72x72')
        );
        Service::Document()->add_link(
            $url = EXTENSIONS_THEMES_URL
                . '/' . Service::Registry()->get('Request', 'theme_name')
                . '/' . 'images/apple-touch-icon-114x114.png',
            $relation = 'apple-touch-icon-precomposed',
            $relation_type = 'rel',
            $attributes = array('sizes,114x114')
        );

        /** HTML5 Shim */
        Service::Document()->add_js
        ('http://html5shim.googlecode.com/svn/trunk/html5.js', 1000);

        /** jQuery CDN and fallback */
        Service::Document()->add_js
        ('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', 1000);

        $url = EXTENSIONS_THEMES_URL
            . '/' . Service::Registry()->get('Request', 'theme_name')
            . '/' . 'js/fallback/jquery-1.7.1.min.js';

        $fallback = "
        if (typeof jQuery == 'undefined')
         {
            document.write(unescape(" . '"' . "%3Cscript src='" . $url . "' type='text/javascript'%3E%3C/script%3E" . '"' . "));
         }";
        Service::Document()->add_js_declaration
        ($fallback, 'text/javascript', 1000);
    }
}
