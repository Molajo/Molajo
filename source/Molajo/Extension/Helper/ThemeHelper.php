<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Service\Services;
use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * ThemeHelper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class ThemeHelper
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ThemeHelper();
        }

        return self::$instance;
    }

    /**
     * get
     *
     * Get requested theme data
     *
     * @return boolean
     * @since   1.0
     */
    public function get($theme_id = 0)
    {
        if ($theme_id == 0) {
            $theme_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_THEME, 'System');
        }

        Services::Registry()->set('Parameters', 'theme_id', (int) $theme_id);

        $node = Helpers::Extension()->getExtensionNode((int) $theme_id);

        Services::Registry()->set('Parameters', 'theme_path_node', $node);

        Services::Registry()->set('Parameters', 'theme_path', $this->getPath($node));
        Services::Registry()->set('Parameters', 'theme_path_include', $this->getPath($node) . '/index.php');
        Services::Registry()->set('Parameters', 'theme_path_url', $this->getPathURL($node));
        Services::Registry()->set('Parameters', 'theme_favicon', $this->getFavicon($node));

        /** Retrieve the query results */
        $row = Helpers::Extension()->get($theme_id, 'Themes', 'Table');

        /** 500: not found */
        if (count($row) == 0) {

            /** System Default */
            if ($theme_id == Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_THEME, 'System')) {
                // 500 error
                Services::Error()->set(500, 'System Theme not found');

                return false;
            }

            /** System default */
               $theme_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_THEME, 'System');

            Services::Registry()->set('Parameters', 'theme_id', (int) $theme_id);

            $node = Helpers::Extension()->getExtensionNode((int) $theme_id);

            Services::Registry()->set('Parameters', 'theme_path_node', $node);

            Services::Registry()->set('Parameters', 'theme_path', $this->getPath($node));
            Services::Registry()->set('Parameters', 'theme_path_include', $this->getPath($node) . '/index.php');
            Services::Registry()->set('Parameters', 'theme_path_url', $this->getPathURL($node));
            Services::Registry()->set('Parameters', 'theme_favicon', $this->getFavicon($node));

            $row = Helpers::Extension()->get($theme_id, 'Themes', 'Table');

            if (count($row) == 0) {
                Services::Error()->set(500, 'Theme not found');

                return false;
            }
        }

        Services::Registry()->set('Parameters', 'theme_title', $row['title']);
        Services::Registry()->set('Parameters', 'theme_translation_of_id', (int) $row['translation_of_id']);
        Services::Registry()->set('Parameters', 'theme_language', $row['language']);
        Services::Registry()->set('Parameters', 'theme_view_group_id', $row['view_group_id']);
        Services::Registry()->set('Parameters', 'theme_catalog_id', $row['catalog_id']);
        Services::Registry()->set('Parameters', 'theme_catalog_type_id', (int) $row['catalog_type_id']);
        Services::Registry()->set('Parameters', 'theme_catalog_type_title', $row['catalog_types_title']);

        return true;
    }

    /**
     * getPath
     *
     * Return path for selected Theme
     *
     * @param $theme_name
     * @return bool|string
     */
    public function getPath($node)
    {
        if (file_exists(EXTENSIONS_THEMES . '/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
            return EXTENSIONS_THEMES . '/' . ucfirst(strtolower($node));
        }

        return false;
    }

    /**
     * getPathURL
     *
     * Return path for selected Theme
     *
     * @return bool|string
     * @since 1.0
     */
    public function getPathURL($node)
    {
        if (file_exists(EXTENSIONS_THEMES . '/' . ucfirst(strtolower($node)) . '/' . 'index.php')) {
            return EXTENSIONS_THEMES_URL . '/' . ucfirst(strtolower($node));
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
     * @return mixed
     * @since   1.0
     */
    public function getFavicon($node)
    {
        $path = EXTENSIONS_THEMES . '/' . ucfirst(strtolower($node)) . '/images/';
        if (file_exists($path . 'favicon.ico')) {
            return EXTENSIONS_THEMES_URL . '/' . ucfirst(strtolower($node)) . '/images/favicon.ico';
        }

        $path = BASE_FOLDER;
        if (file_exists($path . 'favicon.ico')) {
            return BASE_URL . '/favicon.ico';
        }

        return false;
    }
}
