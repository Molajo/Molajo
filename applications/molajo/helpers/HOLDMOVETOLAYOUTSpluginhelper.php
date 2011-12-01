<?php
/**
 * @version     $id: mimport
 * @package     Molajo
 * @subpackage  Widget Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

abstract class MolajoPlugin
{
    /**
     * Get the path to a layout for a plugin
     *
     * @static
     * @param    array    $widget    An array containing the type and name of the plugin
     * @param    string    $layout    The name of the module layout. If alternative layout, in the form template:filename.
     * @return    string    The path to the module layout
     * @since    1.5
     */
    public static function getLayoutPath($widget = array(), $layout = 'default')
    {
        /** current template **/
        $template = MolajoFactory::getApplication()->getTemplate();
        $defaultLayout = $layout;

        /** template and base path for the layout **/
        $tPath = MOLAJO_CMS_TEMPLATES . '/' . $template . '/html/' . $widget['type'] . '/' . $widget['name'] . '/' . $layout . '.php';
        $bPath = MOLAJO_WIDGETS . '/' . $widget['type'] . '/' . $widget['name'] . '/layouts/' . $defaultLayout . '.php';

        /** use layout overrides, if available **/
        if (file_exists($tPath)) {
            return $tPath;
        } else {
            return $bPath;
        }
    }

    /**
     * generateLayout
     *
     * @static
     * @param    string    $layout    The name of the plugin layout.
     * @return    string    $renderedLayout rendered output
     * @since    1.5
     */
    function generateLayout($layoutPath)
    {
        ob_start();
        require $layoutPath;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
}