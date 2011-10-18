<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', 'default');
$wrap = $params->def('wrap', 'none');
$rowset[0]->content = '';

// Initialise variables.
$config	= MolajoFactory::getConfig();
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
	$inboxLink = MolajoRoute::_('index.php?option=com_messages');
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


$hideLinks	= JRequest::getBool('hidemainmenu');
$output = array();

// Print the logged in users.
if ($params->get('show_loggedin_users', 1)) :
	$output[] = '<span class="loggedin-users">'.MolajoText::plural('MOD_STATUS_USERS', $online_num).'</span>';
endif;

// Print the back-end logged in users.
if ($params->get('show_loggedin_users_admin', 1)) :
	$output[] = '<span class="backloggedin-users">'.MolajoText::plural('MOD_STATUS_BACKEND_USERS', $count).'</span>';
endif;

//  Print the inbox message.
if ($params->get('show_messages', 1)) :
	$output[] = '<span class="'.$inboxClass.'">'.
			($hideLinks ? '' : '<a href="'.$inboxLink.'">').
			MolajoText::plural('MOD_STATUS_MESSAGES', $unread).
			($hideLinks ? '' : '</a>').
			'</span>';
endif;

// Print the Preview link to Main site.
$output[] = '<span class="viewsite"><a href="'.JURI::root().'" target="_blank">'.MolajoText::_('MOD_STATUS_VIEW_SITE').'</a></span>';

// Reverse rendering order for rtl display.
if ($lang->isRTL()) :
	$output = array_reverse($output);
endif;

/** output into content array */
foreach ($output as $item) :
    $rowset[0]->text .= $item;
endforeach;