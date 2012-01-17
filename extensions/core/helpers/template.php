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
     * getTemplate
     *
     * Get the template data of a specific type if no specific template is specified
     * otherwise only the specific template data is returned.
     *
     * @return  mixed    An array of template data objects, or a template data object.
     * @since   1.0
     */
    public function getTemplate($template_name)
    {
        return MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE, $template_name);
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
}
