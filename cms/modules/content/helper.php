<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * @package		Molajo
 * @subpackage	content
 * @since       1.0
 */
class modContentHelper
{
	/**
	 * Get a list of items for a specific type(s) of content
	 *
	 * @param	object  $parameters Module Parameters
	 * @param	object  $user   User object
	 *
	 * @return	mixed	An array of items for specified content or false
	 */
	public static function getList($parameters, $user)
	{
//todo: change to use MolajoModelDisplay

        $db	= MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $date = MolajoFactory::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();

        $query->select('a.id, a.title, a.checked_out, a.checked_out_time');
        $query->select('a.access, a.created, a.created_by, a.created_by_alias, a.featured, a.state');
        $query->select('a.catid, b.title as category_title');

        $query->from('#'.$parameters->get('component_table', '_articles').' AS a');
        $query->join('LEFT','#__categories AS b ON b.id = a.catid');

        $query->where('a.published = 1');
        $query->where('(a.start_publishing_datetime = '.$db->Quote($nullDate).' OR a.start_publishing_datetime <= '.$db->Quote($now).')');
        $query->where('(a.stop_publishing_datetime = '.$db->Quote($nullDate).' OR a.stop_publishing_datetime >= '.$db->Quote($now).')');

        $query->where('b.published = 1');
        $query->where('(b.start_publishing_datetime = '.$db->Quote($nullDate).' OR b.start_publishing_datetime <= '.$db->Quote($now).')');
        $query->where('(b.stop_publishing_datetime = '.$db->Quote($nullDate).' OR b.stop_publishing_datetime >= '.$db->Quote($now).')');

        $acl = new MolajoACL ();
        $acl->getQueryInformation ('', &$query, 'viewaccess', array('table_prefix'=>'a'));

        $lang = MolajoFactory::getLanguage()->getTag();
        $query->where('a.language IN ('.$db->Quote($lang).','.$db->Quote('*').')');

        /** category filter */
        $categoryId = $parameters->def('catid', 0);
        if ((int) $categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

//        $categoryIds = $parameters->get('catid', array());
        /** Online User */
        if ((int) $parameters->def('limit_to_online_user', 0) > 0) {
            $query->where('a.created_by = '.(int) $user->get('id'));
        }

       /** olimit and rdering */
		$query->ordering('start', 0);
		$query->ordering('limit', $parameters->get('count', 5));
		$query->ordering('order', $parameters->get('ordering', 'desc'));

        $db->setQuery($query->__toString());

        $items = $db->loadObjectList();

        if($db->getErrorNum()){
            JError::raiseWarning(500, MolajoText::sprintf('MOLAJO_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
            return false;
        }

		/** Add information to query results */
        $i = 1;
        $acl = new MolajoACL ();

        if (count($items) == 0) {
            $items[0]->columncount = '4';
            $items[0]->columnheading1 = MolajoText::_('LATEST_LATEST_ITEMS');
            $items[0]->columnheading2 = MolajoText::_('JSTATUS');
            $items[0]->columnheading3 = MolajoText::_('LATEST_CREATED');
            $items[0]->columnheading4 = MolajoText::_('LATEST_CREATED_BY');

        } else {

            foreach ($items as $item) {

                /** Headings */
                $item->columncount = '4';
                $item->columnheading.$i = MolajoText::_('LATEST_LATEST_ITEMS');
                $item->columnheading.$i = MolajoText::_('JSTATUS');
                $item->columnheading.$i = MolajoText::_('LATEST_CREATED');
                $item->columnheading.$i = MolajoText::_('LATEST_CREATED_BY');

                /** ACL */
                if ($acl->authoriseTask ('articles', 'display', 'view', $item->id, $item->catid, $item)) {
                    $item->link = MolajoRouteHelper::_('index.php?option=articles&task=edit&id='.$item->id);
                } else {
                    $item->link = '';
                }

                /** Rowcount */
                $item->rowcount = $i++;
            }
        }

		return $items;
	}

	/**
	 * Get the alternate title for the module
	 *
	 * @param	JObject	The module parameters.
	 * @return	string	The alternate title for the module.
	 */
	public static function getTitle($parameters)
	{
		$who = $parameters->get('user_id');
		$catid = (int)$parameters->get('catid');
		$type = $parameters->get('ordering') == 'c_dsc' ? '_CREATED' : '_MODIFIED';
		if ($catid)
		{
			$category = JCategories::getInstance('Content')->get($catid);
			if ($category) {
				$title = $category->title;
			}
			else {
				$title = MolajoText::_('POPULAR_UNEXISTING');
			}
		}
		else
		{
			$title = '';
		}
		return MolajoText::plural('LATEST_TITLE'.$type.($catid ? "_CATEGORY" : '').($who!='0' ? "_$who" : ''), (int)$parameters->get('count'), $title);
	}
}
