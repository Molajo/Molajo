<?php
/**
 * @version        $Id: helper.php 21020 2011-03-27 06:52:01Z infograf768 $
 * @package        Joomla.Site
 * @subpackage    search
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package        Joomla.Site
 * @subpackage    search
 * @since        1.5
 */
class modSearchHelper
{
    /**
     * Display the search button as an image.
     *
     * @param    string    $button_text    The alt text for the button.
     *
     * @return    string    The HTML for the image.
     * @since    1.5
     */
    public static function getSearchImage($button_text)
    {
        $img = MolajoHTML::_('image', 'searchButton.gif', $button_text, NULL, true, true);
        return $img;
    }
}