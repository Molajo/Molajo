<?php
/**
 * @version     $id: router.php
 * @package     Molajo
 * @subpackage  Router
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajosamplesBuildRoute
 *
 * Build the route for the com_molajosamples component
 *
 * @param  $query
 * @return array
 */
function MolajosamplesBuildRoute(&$query)
{
    $router = new MolajoRouter ();
    $router->
    return buildRoute(&$query, 'com_molajosamples', 'molajosample', 'molajosamples', 'Molajosample', '#__molajosamples');
}

/**
 * MolajosamplesParseRoute
 *
 * Parse the segments of a URL.
 *
 * called out of JRouterSite::_parseSefRoute()
 *
 * @param  $query
 * @return array
 */
function MolajosamplesParseRoute ($segments)
{
    return MolajoParseRoute($segments, 'com_molajosamples', 'molajosample', 'molajosamples', 'Molajosample', '#__molajosamples');
}