<?php
/**
 * @version		$Id: helper.php 20541 2011-02-03 21:12:06Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_articles/models', 'ContentModel');

jimport('joomla.application.categories');

/**
 * @package		Joomla.Administrator
 * @subpackage	mod_latest
 */
abstract class modLatestHelper
{
	/**
	 * Get a list of articles.
	 *
	 * @param	JObject		The module parameters.
	 *
	 * @return	mixed		An array of articles, or false on error.
	 */
	public static function getList($params, $user)
	{

//todo: change to use MolajoModelDisplay

        $db	= MolajoFactory::getDbo();
        $query = $db->getQuery(true);
        $lang = MolajoFactory::getLanguage()->getTag();

        $date = MolajoFactory::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();

        $query->select('a.id, a.title, a.checked_out, a.checked_out_time');
        $query->select('a.access, a.created, a.created_by, a.created_by_alias, a.featured, a.state');
        $query->select('a.catid, b.title as category_title');

        $query->from('#'.$params->get('component_table', '_articles').' AS a');
        $query->join('LEFT','#__categories AS b ON b.id = a.catid');

        $query->where('a.published = 1');
        $query->where('(a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).')');
        $query->where('(a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')');

        $query->where('b.published = 1');
        $query->where('(b.publish_up = '.$db->Quote($nullDate).' OR b.publish_up <= '.$db->Quote($now).')');
        $query->where('(b.publish_down = '.$db->Quote($nullDate).' OR b.publish_down >= '.$db->Quote($now).')');

        $acl = new MolajoACL ();
        $acl->getQueryInformation ('', &$query, 'viewaccess', array('table_prefix'=>'a'));

        if (MolajoFactory::getApplication()->isSite()
            && MolajoFactory::getApplication()->getLanguageFilter()) {
            $query->where('a.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
        }

        /** category filter */
        $categoryId = $params->def('catid', 0);
        if ((int) $categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

//        $categoryIds = $params->get('catid', array());

        /** user id filter */
        $userId = $user->get('id');
        switch ($params->get('user_id')) {
            case 'by_me':
                $model->setState('filter.author_id', $userId);
                break;

            case 'not_me':
                $model->setState('filter.author_id', $userId);
                $model->setState('filter.author_id.include', false);
                break;
        }

       /** ordering */
        switch ($params->get('ordering')) {
            case 'm_dsc':
                $query->ordering('list.ordering', 'modified DESC, created');
                $query->ordering('list.direction', 'DESC');
                break;

            case 'c_dsc':
            default:
                $query->ordering('list.ordering', 'created');
                $query->ordering('list.direction', 'DESC');
                break;
        }

		/** limit */
		$model->setState('list.start', 0);
		$model->setState('list.limit', $params->get('count', 5));

        $db->setQuery($query->__toString());

        $items = $db->loadObjectList();

        if($db->getErrorNum()){
            JError::raiseWarning(500, JText::sprintf('MOLAJO_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
            return $clean;
        }


		/** Add information to query results */
        $i = 1;
        $acl = new MolajoACL ();

        if (count($items) == 0) {
            $items[0]->columncount = '4';
            $items[0]->columnheading1 = JText::_('MOD_LATEST_LATEST_ITEMS');
            $items[0]->columnheading2 = JText::_('JSTATUS');
            $items[0]->columnheading3 = JText::_('MOD_LATEST_CREATED');
            $items[0]->columnheading4 = JText::_('MOD_LATEST_CREATED_BY');

        } else {

            foreach ($items as $item) {

                /** Headings */
                $item->columncount = '4';
                $item->columnheading.$i = JText::_('MOD_LATEST_LATEST_ITEMS');
                $item->columnheading.$i = JText::_('JSTATUS');
                $item->columnheading.$i = JText::_('MOD_LATEST_CREATED');
                $item->columnheading.$i = JText::_('MOD_LATEST_CREATED_BY');

                /** ACL */
                if ($acl->authoriseTask ('com_articles', 'display', 'view', $item->id, $item->catid, $item)){
                    $item->link = JRoute::_('index.php?option=com_articles&task=edit&id='.$item->id);
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
	public static function getTitle($params)
	{
		$who = $params->get('user_id');
		$catid = (int)$params->get('catid');
		$type = $params->get('ordering') == 'c_dsc' ? '_CREATED' : '_MODIFIED';
		if ($catid)
		{
			$category = JCategories::getInstance('Content')->get($catid);
			if ($category) {
				$title = $category->title;
			}
			else {
				$title = JText::_('MOD_POPULAR_UNEXISTING');
			}
		}
		else
		{
			$title = '';
		}
		return JText::plural('MOD_LATEST_TITLE'.$type.($catid ? "_CATEGORY" : '').($who!='0' ? "_$who" : ''), (int)$params->get('count'), $title);
	}
}
