<?php
/**
 * @package     Molajo
 * @subpackage  Molajo URLs
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Molajo Event: MolajoOnAfterInitialise
 *
 * @return	string
 */
class plgMolajoURLs extends JPlugin	{

    /**
     * MolajoOnAfterInitialise
     *
     * @return	string
     */
    function MolajoOnAfterInitialise () {

        $app =& JFactory::getApplication('JSite');
        $router =& $app->getRouter();

        if ($router->getMode() == JROUTER_MODE_SEF) {
                $router->attachBuildRule(array(&$this, 'MolajoBuildRoute'));
                $router->attachParseRule(array(&$this, 'MolajoParseURL'));
        }
    }

    /**
     * MolajoBuildRoute
     *
     * @return	string
     */
    function MolajoBuildRoute(&$router, &$uri)	{

        $query = $uri->getQuery(true);

        if(isset($query['task'])) {
            return;
        }
        $option = '';
        if(isset($query['option'])) {
            $option = $query['option'];
        }
        if ($option == '') {
            return;
        }

        $component = preg_replace('/[^A-Z0-9_\.-]/i', '', $option);
        $file = JPATH_PLUGINS.'/molajo/urls/router/'.substr($component, 4).'build.php';
        if (file_exists($file)) {
                require_once($file);
                $function = substr($component, 4) . 'MolajoBuildRoute';
                $function ($router, $uri);
        }

        return;
    }

    /**
     * MolajoParseURL
     *
     * @return	string
     */
    function MolajoParseURL (&$router, &$uri)	{

        $app =& JFactory::getApplication();
        if ($app->isAdmin()) {
                return $vars;
        }

        $vars = $uri->getQuery(true);

        return $vars;

    }
}