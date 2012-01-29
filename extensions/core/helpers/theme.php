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
 * @subpackage  Theme
 * @since       1.0
 */
class MolajoThemeHelper
{
    /**
     * get
     *
     * Get requested theme data
     *
     * @return  array
     * @since   1.0
     */
    public function get($theme)
    {
        $rows = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_THEME, $theme);
        if (count($rows) == 0) {
            return array();
        }
        foreach ($rows as $row) {
        }

        return $row;
    }

    /**
     * getPath
     *
     * Return path for selected Theme
     *
     * @return bool|string
     * @since 1.0
     */
    public function getPath($theme_name)
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
     * @return bool|string
     * @since 1.0
     */
    public function getPathURL($theme_name)
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
     * @return  mixed
     * @since   1.0
     */
    static public function getFavicon($theme_name)
    {
        /** theme images */
        $path = MOLAJO_EXTENSIONS_THEMES . '/' . $theme_name . '/images/';
        if (file_exists($path . 'favicon.ico')) {
            return MOLAJO_EXTENSIONS_THEMES_URL . '/' . $theme_name . '/images/favicon.ico';
        }

        /** root */
        $path = MOLAJO_BASE_FOLDER;
        if (file_exists($path . 'favicon.ico')) {
            return MOLAJO_BASE_URL . '/' . $theme_name . '/images/favicon.ico';
        }

        return false;
    }
}
