<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Mustache
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoAmaziumThemeHelper extends MolajoThemeHelper
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
        return 'Hello ' . Services::User()->get('name') . '!!!';
    }

    /**
     * loadMedia
     *
     * If exists, automatically called in the Theme Rendering process
     * Use this method to load external media, special metadata or links
     *
     * http://weblogs.asp.net/jgalloway/archive/2010/01/21/using-cdn-hosted-jquery-with-a-local-fall-back-copy.aspx
     *
     * @since  1.0
     */
    public function loadMedia()
    {
        /** Favicons */
        Services::Media()->add_link(
            $url = MOLAJO_EXTENSIONS_THEMES_URL
                . '/' . Molajo::Request()->get('theme_name')
                . '/' . 'images/apple-touch-icon.png',
            $relation = 'apple-touch-icon-precomposed',
            $relation_type = 'rel',
            $attributes = array('x,y')
        );
        Services::Media()->add_link(
            $url = MOLAJO_EXTENSIONS_THEMES_URL
                . '/' . Molajo::Request()->get('theme_name')
                . '/' . 'images/apple-touch-icon-72x72.png',
            $relation = 'apple-touch-icon-precomposed',
            $relation_type = 'rel',
            $attributes = array('sizes,72x72')
        );
        Services::Media()->add_link(
            $url = MOLAJO_EXTENSIONS_THEMES_URL
                . '/' . Molajo::Request()->get('theme_name')
                . '/' . 'images/apple-touch-icon-114x114.png',
            $relation = 'apple-touch-icon-precomposed',
            $relation_type = 'rel',
            $attributes = array('sizes,114x114')
        );

        Services::Media()->add_js('http://html5shim.googlecode.com/svn/trunk/html5.js', 1000);
        Services::Media()->add_js('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', 1000);
    }
}
