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
 * Molajo Router
 *
 * @static
 * @package		Joomla
 * @subpackage	Router
 * @since 1.5
 */
class MolajoRouter extends JObject
{
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
    protected function getKey ($ccyy, $mm, $dd, $alias, $tableParam)
    {
        $db = JFactory::getDBO();
    
        $query = $db->getQuery(true);
        $query->select('MIN(id)');
        $query->from($tableParam);
        $query->where('publish_up like "'.$ccyy.'-'.$mm.'-'.$dd.'%"');
        $query->where('alias = '.$db->Quote($alias));
        $query->where('state > -10');
    
        $db->setQuery($query);
    
        return $db->loadResult();
    }    
    
    /**
     * getItemRoute
     *
     * @param  int      $id
     * @param  int      $catid
     * @param  int      $data
     * @param  string   $singleItemParam
     * @param  string   $typeParam
     * @param  string   $componentOptionParam
     * 
     * @return string
     */
	public function getItemRoute ($id, $catid, $data, $singleItemParam, $typeParam, $componentOptionParam)
    {
        $data->slug			= $data->alias ? ($data->id.':'.$data->alias) : $data->id;
        $data->catslug		= $data->category_alias ? ($data->catid.':'.$data->category_alias) : $data->catid;
        $data->parent_slug	= $data->category_alias ? ($data->parent_id.':'.$data->parent_alias) : $data->parent_id;
        
		$needles = array(
			$singleItemParam  => array((int) $id)
		);

/** amy add more documentation and options for the WP URLs here */
        $timestamp = JHTML::_('date', $data->publish_up, 'U');

		$link = 'index.php?option='.$componentOptionParam.
                '&view='.$singleItemParam.
                '&id='. $id.
                '&alias='.$data->alias.
                '&layout=item'.
                '&ts='.$timestamp;

		if ((int)$catid > 1) {
			$categories = JCategories::getInstance($typeParam);
			$category = $categories->get((int)$catid);
			if ($category) {
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($itemID = self::_findItem($needles, $componentOptionParam)) {
			$link .= '&Itemid='.$itemID;
		}
		elseif ($itemID = self::_findItem(null, $componentOptionParam)) {
			$link .= '&Itemid='.$itemID;
		}

		return $link;
	}

    /**
     * getCategoryRoute
     *
     * Builds the URL for a Category Page
     *
     * @static
     * @param   array   $data
     * @param   int     $catid
     * @param   string  $singleItemParam
     * @param   string  $typeParam
     * @param   string  $componentOptionParam
     * @return  string
     */
	public static function getCategoryRoute ($data, $catid = 0, $singleItemParam, $typeParam, $componentOptionParam)
	{
		if ($catid instanceof JCategoryNode) {
			$id = $catid->id;
			$category = $catid;
		} else {
			$id = (int) $catid;
			$category = JCategories::getInstance($typeParam)->get($id);
		}

		if($id < 1) {
			$link = '';
		} else {
			$needles = array(
				'category' => array($id)
			);

			if ($data = self::_findItem($needles, $componentOptionParam)) {
				$link = 'index.php?Itemid='.$data;
			} else {

				$link = 'index.php?option='.$componentOptionParam.'&view=category&id='.$id;
				if($category) {
					$catids = array_reverse($category->getPath());
					$needles = array(
						'category' => $catids,
						'categories' => $catids
					);
					if ($data = self::_findItem($needles, $componentOptionParam)) {
						$link .= '&Itemid='.$data;
					} elseif ($data = self::_findItem(null, $componentOptionParam)) {
						$link .= '&Itemid='.$data;
					}
				}
			}
		}

		return $link;
	}

    /**
     * getFormRoute
     *
     * @static
     * @param int $id
     * @param  $singleItemParam
     * @param  $typeParam
     * @param  $componentOptionParam
     * @return string
     */
	public function getFormRoute ($data, $id=0, $singleItemParam, $typeParam, $componentOptionParam)
	{
		if ($id) {
			$link = 'index.php?option='.$componentOptionParam.'&task='.$singleItemParam.'.edit&id='. $id;
		} else {
			$link = 'index.php?option='.$componentOptionParam.'&task='.$singleItemParam.'.edit&id=0';
		}
		return $link;
	}

    /**
     * _findItem
     *
     * @static
     * @param null $needles
     * @param  $componentOptionParam
     * @return null
     */
	protected static function _findItem ($needles = null, $componentOptionParam)
	{
		$menus		= JFactory::getApplication()->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null) {
			self::$lookup = array();

			$component	= MolajoComponentHelper::getComponent($componentOptionParam);
			$items		= $menus->getItems('component_id', $component->id);

			foreach ($items as $data) {
				if (isset($data->query) && isset($data->query['view'])) {
					$view = $data->query['view'];
					if (!isset(self::$lookup[$view])) {
						self::$lookup[$view] = array();
					}
					if (isset($data->query['id'])) {
						self::$lookup[$view][$data->query['id']] = $data->id;
					}
				}
			}
		}

		if ($needles) {
			foreach ($needles as $view => $ids) {
				if (isset(self::$lookup[$view])) {
					foreach($ids as $id) {
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		} else {
			$active = $menus->getActive();
			if ($active && $active->component == $componentOptionParam) {
				return $active->id;
			}
		}

		return null;
	}
}