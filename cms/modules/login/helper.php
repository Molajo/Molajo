<?php
/**
 * @version		$Id: helper.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	login
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;

class modLoginHelper
{
	static function getReturnURL($parameters, $type)
	{
		$app	= MolajoFactory::getApplication();
		$router = $app->getRouter();
		$url = null;
		if ($itemid =  $parameters->get($type))
		{
			$db		= MolajoFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select($db->nameQuote('link'));
			$query->from($db->nameQuote('#__menu_items'));
			$query->where($db->nameQuote('published').'=1');
			$query->where($db->nameQuote('id').'='.$db->quote($itemid));

			$db->setQuery($query);
			if ($link = $db->loadResult()) {
				if ($router->getMode() == MOLAJO_ROUTER_MODE_SEF) {
					$url = 'index.php?Itemid='.$itemid;
				}
				else {
					$url = $link.'&Itemid='.$itemid;
				}
			}
		}
		if (!$url)
		{
			// stay on the same page
			$uri = MolajoFactory::getURI();
			$vars = $router->parse($uri);
			unset($vars['language']);
			if ($router->getMode() == MOLAJO_ROUTER_MODE_SEF)
			{
				if (isset($vars['Itemid']))
				{
					$itemid = $vars['Itemid'];
					$menu = $app->getMenu();
					$item = $menu->getItem($itemid);
					unset($vars['Itemid']);
					if (isset($item) && $vars == $item->query) {
						$url = 'index.php?Itemid='.$itemid;
					}
					else {
						$url = 'index.php?'.JURI::buildQuery($vars).'&Itemid='.$itemid;
					}
				}
				else
				{
					$url = 'index.php?'.JURI::buildQuery($vars);
				}
			}
			else
			{
				$url = 'index.php?'.JURI::buildQuery($vars);
			}
		}

		return base64_encode($url);
	}
	
	static function getType()
	{
		$user = MolajoFactory::getUser();
		return (!$user->get('guest')) ? 'logout' : 'login';
	}
}
