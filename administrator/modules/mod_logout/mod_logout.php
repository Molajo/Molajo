<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', 'admin_footer');
$wrap = $params->def('wrap', 'div');
$rowset[0]->content = '';

/** not logged on */
if ($user-> id == 0) {
//    return;
}

/** logout link */
$task = $request['task'];
if ($task == 'edit' || $task == 'editA' || JRequest::getInt('hidemainmenu')) {
    $logoutLink = '';
} else {
    $logoutLink = JRoute::_('index.php?option=com_login&task=logout&'. JUtility::getToken() .'=1');
}
$hideLinks	= JRequest::getBool('hidemainmenu');

$output = array();
$output[] = '<span class="logout">' .($hideLinks ? '' : '<a href="'.$logoutLink.'">').JText::_('JLOGOUT').($hideLinks ? '' : '</a>').'</span>';

/** rtl support */
if ($document->direction == "rtl") :
    $output = array_reverse($output);
endif;

/** output into content array */
foreach ($output as $item) :
    $rowset[0]->content .= $item;
endforeach;
