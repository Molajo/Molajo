<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Theme
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoThemeHelper
{
    /**
     * get
     *
     * Get requested theme data
     *
     * @static
     * @return  array
     * @since   1.0
     */
    public static function get($theme)
    {
        $rows = ExtensionHelper::get(
            MOLAJO_ASSET_TYPE_EXTENSION_THEME,
            $theme
        );

        if (count($rows) == 0) {
            return array();
        }
        $row = null;
        foreach ($rows as $row) {
        }

        return $row;
    }

    /**
     * getPath
     *
     * Return path for selected Theme
     *
     * @static
     * @param $theme_name
     * @return bool|string
     */
    public static function getPath($theme_name)
    {
        if (file_exists(MOLAJO_EXTENSIONS_THEMES . '/' . $theme_name . '/' . 'index.php')) {
            return MOLAJO_EXTENSIONS_THEMES . '/' . $theme_name . '/' . 'index.php';
        }
        return false;
    }

    /**
     * getPath
     *
     * Return path for selected Theme
     *
     * @static
     * @return bool|string
     * @since 1.0
     */
    public static function getPathURL($theme_name)
    {
        if (file_exists(MOLAJO_EXTENSIONS_THEMES . '/' . $theme_name . '/' . 'index.php')) {
            return MOLAJO_EXTENSIONS_THEMES_URL . '/' . $theme_name;
        }
        return false;
    }

    /**
     * getFavicon
     *
     * Retrieve Favicon Path
     *
     * Can be located in:
     *  - Themes/images/ folder (priority 1)
     *  - Root of the website (priority 2)
     *
     * @static
     * @return  mixed
     * @since   1.0
     */
    public static function getFavicon($theme_name)
    {
        $path = MOLAJO_EXTENSIONS_THEMES . '/' . $theme_name . '/images/';
        if (file_exists($path . 'favicon.ico')) {
            return MOLAJO_EXTENSIONS_THEMES_URL . '/' . $theme_name . '/images/favicon.ico';
        }
        $path = MOLAJO_BASE_FOLDER;
        if (file_exists($path . 'favicon.ico')) {
            return MOLAJO_BASE_URL . '/' . $theme_name . '/images/favicon.ico';
        }

        return false;
    }
}
