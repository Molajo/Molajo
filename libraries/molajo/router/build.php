<?php
/**
 * @version     $id: router.php
 * @package     Molajo
 * @subpackage  Router
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Molajo Router
 *
 * @static
 * @package		Joomla
 * @subpackage	Router
 * @since 1.5
 */
class MolajoRouterBuild extends MolajoRouter
{
    /**
     * buildRoute
     *
     * called out of JRouterSite::_buildSefRoute()
     *
     * @param array     $query An array of URL arguments
     * @param string    $componentParam ex com_articles
     * @param string    $singleParam ex. article
     * @param string    $multipleParam ex. articles
     * @param string    $typeParam ex. Article
     * @param string    $tableParam ex. #__articles
     *
     * @return array    The URL arguments to use to assemble the subsequent URL.
     */
    function buildRoute(&$query, $componentParam, $singleParam, $multipleParam, $typeParam, $tableParam)
    {
        $menu		= JFactory::getApplication()->getMenu();
        $params		= MolajoComponentHelper::getParams($componentParam);
        $advanced	= $params->get('sef_advanced_link', 0);
        $segments	= array();
        $temp       = array();

    /** component parameters (Amy Stephen - temporary) */
        $parameter_default_item_layout = 'item';
        $parameter_default_items_layout = 'list';
        $parameter_url_type = 1;
        $parameter_url_base = $singleParam;     // add as a parm

        /** query parameters */
        foreach($query as $name=>$value) {
            $temp[$name] = $value;
            unset($query[$name]);
        }

        /** default layout */
        if (isset($temp['layout'])) {
        } else if ((isset($temp['id']) && (int) $temp['id'] > 0)) {
            $temp['layout'] = $parameter_default_item_layout;
        } else {
            $temp['layout'] = $parameter_default_items_layout;
        }

    /** retrieve menu item */

        /** menu item */
        if (isset($temp['Itemid'])) {
            $menuItem = $menu->getItem($temp['Itemid']);
            if ((isset($temp['id']) && (int) $temp['id'] > 0)) {
            } else if ($menuItem instanceof stdClass) {
                return $segments;
            }
        }

        /** base/category-alias/NN */
        if ($parameter_url_type == 1) {

        }

    // if i don't do this then the /component/ thing goes on
    // but this URL includes the menu item which is bad. (dups)
    //    unset($query['option']);
    //    unset($query['Itemid']);

    //    $path = strftime('/%Y/%m/%d/', $timestamp).$alias;
    //    $segments = explode('/', $path);

        return $segments;
    }
}