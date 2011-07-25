<?php
/**
 * @version     $id: parse.php
 * @package     Molajo
 * @subpackage  Router
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Router
 *
 * @static
 * @package		Joomla
 * @subpackage	Router
 * @since 1.5
 */
class MolajoRouterParse extends MolajoRouter
{
    /**
     * parseRoute
     *
     * called out of ArticlesParseRoute (ex), which is activated by JRouterSite::_parseSefRoute()
     *
     * @param array     $segments An array of URL arguments
     * @param string    $componentParam ex com_articles
     * @param string    $singleParam ex. article
     * @param string    $multipleParam ex. articles
     * @param string    $typeParam ex. Article
     * @param string    $tableParam ex. #__articles
     *
     * @return array    The URL arguments to use to assemble the subsequent URL.
     */
    function parseRoute($segments, $componentParam, $singleParam, $multipleParam, $typeParam, $tableParam)
    {
        $vars = array();

        //Get the active menu item.
        $app	= MolajoFactory::getApplication();
        $menu	= $app->getMenu();
        $item	= $menu->getActive();
        $params = MolajoComponentHelper::getParams($componentParam);
        $advanced = $params->get('sef_advanced_link', 0);
        $db = MolajoFactory::getDBO();

        /** Count route segments */
        $count = count($segments);

        /** 1. pull off the 'reserved' parameters to the right of the url */

        /** tag/value1,value2 or tag=value1,value2 */
        /** date/ccyymmdd or date/ccyymm or date/ccyy */
        /** author/id */

        /** 2. Determine type of urls selected for configuration */
        $urlType = 1;

        /** 3. Process for Extension*/
        if ($urlType == 1) {
            $results = $this->parseDateURLs($segments, $componentParam, $singleParam, $typeParam, $tableParam);
        }

        /** 4. Return if no match */
        if ($results === false) {
            return $segments;
        }

        /** 5. For match, set vars */
        $vars['id'] = $results;
        $vars['option'] = $componentParam;
        $vars['view'] = $multipleParam;
        $vars['layout'] = 'item';       /** amy - make this a component parameter */

        return $vars;
    }

    /**
     * parseDateURLs
     *
     * @param  $segments
     * @param  $componentParam
     * @param  $singleParam
     * @param  $typeParam
     * @param  $tableParam
     * @return array
     */
    protected function parseDateURLs ($segments, $componentParam, $singleParam, $typeParam, $tableParam)
    {
        /** provide for hackable URLs (default for component) */

        /** ccyy/mm/dd, ccyy/mm, ccyy */

        /** ccyy */
        if((int) ($segments[0]) > 1980 && $segments[0] < 2060) {
            $ccyy = $segments[0];
        } else {
            return false;
        }

        /** mm */
        if((int) ($segments[1]) > 0 && $segments[1] < 32) {
            $mm = (int) $segments[1];
            if ($mm < 10) {
                $mm = '0'.$mm;
            }
        } else {
            return false;
        }

        /** dd */
        if((int) ($segments[2]) > 0 && $segments[2] < 32) {
            $dd = (int) $segments[2];
            if ($dd < 10) {
                $dd = '0'.$dd;
            }
        } else {
            return false;
        }

        /** alias */
        $alias = trim(substr($segments[3], 0, 2).'-'.substr($segments[3], 3, strlen($segments[3])-3));

        /** run query */
        if (count($segments) > 3) {
            $id = parent::getKey($ccyy, $mm, $dd, $alias, $tableParam);
        }

        if ($id === false) {
            return false;
        } else {
            return $id;
        }

    }
}