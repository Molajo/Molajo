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
 * @subpackage	mod_popular
 * @since		1.6
 */
abstract class modPopularHelper
{
	/**
	 * Get a list of the most popular articles
	 *
	 * @param	JObject		The module parameters.
	 *
	 * @return	array
	 */
	public static function getList($params)
	{
		// Initialise variables
		$user = JFactory::getuser();

/**
 *  Molajo Hack Begins: ALS
 */
		// Get an instance of the generic articles model
		$model = JModel::getInstance('Things', 'ThingsModel', array('ignore_request' => true));

		// Set List SELECT
 		$model->setState('list.select', 'a.id, a.title, a.checked_out, a.checked_out_time, ' .
				' a.access, a.created, a.created_by, a.created_by_alias, a.featured, a.state');
/**
 *  Molajo Hack Ends
 */

		// Set Ordering filter
		$model->setState('list.ordering', 'a.hits');
		$model->setState('list.direction', 'DESC');

		// Set Category Filter
		$categoryId = $params->get('catid');
		if (is_numeric($categoryId)){
			$model->setState('filter.category_id', $categoryId);
		}

		// Set User Filter.
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

		// Set the Start and Limit
		$model->setState('list.start', 0);
		$model->setState('list.limit', $params->get('count', 5));

		$items = $model->getItems();

		if ($error = $model->getError()) {
			JError::raiseError(500, $error);
			return false;
		}

		// Set the links
		foreach ($items as &$item) {
			if ($user->authorise('edit','com_articles.article.'.$item->id)){
				$item->link = JRoute::_('index.php?option=com_articles&task=article.edit&id='.$item->id);
			} else {
				$item->link = '';
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
		return JText::plural('MOD_POPULAR_TITLE'.($catid ? "_CATEGORY" : '').($who!='0' ? "_$who" : ''), (int)$params->get('count'), $title);
	}
}