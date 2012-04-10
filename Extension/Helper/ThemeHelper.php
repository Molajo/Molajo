<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Extension\Helper;

defined('MOLAJO') or die;

/**
 * Theme
 *
 * @package   Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class ThemeHelper
{
    /**
     * $data
     *
     * Allows collection of any set of data for a single $item
     *
     * @var    array
     * @since  1.0
     */
    public $data = array();

    /**
     * $rows
     *
     * Retains pointer to current row contained within the $data array
     *
     * @var    int
     * @since  1.0
     */
    protected $rows = 0;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
    }

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
        return Molajo::Helper()
            ->get('Extension',
            MOLAJO_ASSET_TYPE_EXTENSION_THEME,
            $theme
        );
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
            return MOLAJO_EXTENSIONS_THEMES . '/' . $theme_name;
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
            return MOLAJO_BASE_URL . $theme_name . '/images/favicon.ico';
        }

        return false;
    }
}
