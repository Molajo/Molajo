<?php
/**
 * @version     $id: mimport
 * @package     Molajo
 * @subpackage  Widget Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

abstract class MolajoPluginHelper
{
    /**
     * Get the path to a view for a plugin
     *
     * @static
     * @param    array    $widget    An array containing the type and name of the plugin
     * @param    string    $view    The name of the module view. If alternative view, in the form template:filename.
     * @return    string    The path to the module view
     * @since    1.5
     */
    public static function getViewPath($widget = array(), $view = 'default')
    {
        /** current template **/
        $template = MolajoFactory::getApplication()->getTemplate();
        $defaultView = $view;

        /** template and base path for the view **/
        $tPath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template . '/html/' . $widget['type'] . '/' . $widget['name'] . '/' . $view . '.php';
        $bPath = MOLAJO_WIDGETS . '/' . $widget['type'] . '/' . $widget['name'] . '/views/' . $defaultView . '.php';

        /** use view overrides, if available **/
        if (file_exists($tPath)) {
            return $tPath;
        } else {
            return $bPath;
        }
    }

    /**
     * generateView
     *
     * @static
     * @param    string    $view    The name of the plugin view.
     * @return    string    $renderedView rendered output
     * @since    1.5
     */
    function generateView($viewPath)
    {
        ob_start();
        require $viewPath;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
}