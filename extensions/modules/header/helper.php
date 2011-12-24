<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * @package        Molajo
 * @subpackage    header
 * @since        1.0
 */
abstract class MolajoHeaderHelper
{
    /**
     * $data
     *
     * @since    1.0
     */
    protected static $data = array();

    /**
     * Helper method to generate data
     *
     * @param    array    A named array with keys link, image, text, access and imagePath
     *
     * @return    string    HTML for button
     * @since    1.0
     */
    public static function getList($parameters)
    {
        $tmpobj = new JObject();
        $tmpobj->set('site_title', MolajoFactory::getApplication()->get('site_title', 'Molajo'));
        $data[] = $tmpobj;
        return $data;
    }
}