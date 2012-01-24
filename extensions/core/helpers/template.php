<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Template
 *
 * @package     Molajo
 * @subpackage  Template
 * @since       1.0
 */
class MolajoTemplateHelper
{
    /**
     * get
     *
     * Get the template data of a specific type if no specific template is specified
     * otherwise only the specific template data is returned.
     *
     * @return  mixed    An array of template data objects, or a template data object.
     * @since   1.0
     */
    public function get($template)
    {
        $rows = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE, $template);

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
     * Return path for selected Template
     *
     * @return bool|string
     * @since 1.0
     */
    public function getPath($template_name)
    {
        if (file_exists(MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template_name . '/' . 'index.php')) {
            return MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template_name . '/' . 'index.php';
        }
        return false;
    }

    /**
     * getPath
     *
     * Return path for selected Template
     *
     * @return bool|string
     * @since 1.0
     */
    public function getPathURL($template_name)
    {
        if (file_exists(MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template_name . '/' . 'index.php')) {
            return MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $template_name;
        }
        return false;
    }


    /**
     * loadFavicon
     *
     * Define Favicon Path
     *
     * Can be located in:
     *  - Templates/images/ folder (priority 1)
     *  - Root of the website (priority 2)
     *
     * @return  bool
     * @since   1.0
     */
    static public function loadFavicon($template_name)
    {
        /** template images */
        $path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template_name . '/images/';
        if (file_exists($path . 'favicon.ico')) {
            return MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $template_name . '/images/favicon.ico';
        }

        /** root */
        $path = MOLAJO_BASE_FOLDER;
        if (file_exists($path . 'favicon.ico')) {
            return MOLAJO_BASE_URL . '/' . $template_name . '/images/favicon.ico';
        }

        return false;
    }
}
