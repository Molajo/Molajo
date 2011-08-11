<?php
/**
 * @version		$Id: mod_status.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	mod_status
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
return;

// Initialise variables.
$config	= MolajoFactory::getConfig();
$user	= MolajoFactory::getUser();
$db		= MolajoFactory::getDbo();
$lang	= MolajoFactory::getLanguage();

// Get the number of unread messages in your inbox.
$query	= $db->getQuery(true);
$query->select('COUNT(*)');
$query->from('#__messages');
$query->where('state = 0 AND user_id_to = '.(int) $user->get('id'));

$db->setQuery($query);
$unread = (int) $db->loadResult();

// Get the number of back-end logged in users.
$query->clear();
$query->select('COUNT(session_id)');
$query->from('#__session');
$query->where('guest = 0 AND application_id = 1');

$db->setQuery($query);
$count = (int) $db->loadResult();

// Set the inbox link.
if (JRequest::getInt('hidemainmenu')) {
	$inboxLink = '';
} else {
	$inboxLink = JRoute::_('index.php?option=com_messages');
}

// Set the inbox class.
if ($unread) {
	$inboxClass = 'unread-messages';
} else {
	$inboxClass = 'no-unread-messages';
}

// Get the number of frontend logged in users.
$query->clear();
$query->select('COUNT(session_id)');
$query->from('#__session');
$query->where('guest = 0 AND application_id = 0');

$db->setQuery($query);
$online_num = (int) $db->loadResult();

require MolajoModuleHelper::getLayoutPath('mod_status', $params->get('layout', 'default'));