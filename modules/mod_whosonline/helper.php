<?php
/**
 * @version		$Id: helper.php 21084 2011-04-05 00:49:22Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_whosonline
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modWhosonlineHelper
{
	// show online count
	static function getOnlineCount() {
		$db		= MolajoFactory::getDbo();
		// calculate number of guests and users
		$result	= array();
		$user_array  = 0;
		$guest_array = 0;
		$query	= $db->getQuery(true);
		$query->select('guest, usertype, application_id');
		$query->from('#__session');
		$query->where('application_id = 0');
		$db->setQuery($query);
		$sessions = (array) $db->loadObjectList();

		if (count($sessions)) {
			foreach ($sessions as $session) {
				// if guest increase guest count by 1
				if ($session->guest == 1 && !$session->usertype) {
					$guest_array ++;
				}
				// if member increase member count by 1
				if ($session->guest == 0) {
					$user_array ++;
				}
			}
		}

		$result['user']  = $user_array;
		$result['guest'] = $guest_array;

		return $result;
	}

	// show online member names
	static function getOnlineUserNames() {
		$db		= MolajoFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.username, a.time, a.userid, a.usertype, a.application_id');
		$query->from('#__session AS a');
		$query->where('a.userid != 0');
		$query->where('a.application_id = 0');
		$query->group('a.userid');
		$db->setQuery($query);
		return (array) $db->loadObjectList();
	}
}
