<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', 'admin_footer');
$wrap = $params->def('wrap', 'div');

$task = JRequest::getCmd('task');
if ($task == 'edit' || $task == 'editA' || JRequest::getInt('hidemainmenu')) {
    $logoutLink = '';
} else {
    $logoutLink = JRoute::_('index.php?option=com_login&task=logout&'. JUtility::getToken() .'=1');
}
$hideLinks	= JRequest::getBool('hidemainmenu');

$output = array();

// Print the logout link.
$output[] = '<span class="logout">' .($hideLinks ? '' : '<a href="'.$logoutLink.'">').JText::_('JLOGOUT').($hideLinks ? '' : '</a>').'</span>';

// Reverse rendering order for rtl display.
if ($this->direction == "rtl") :
    $output = array_reverse($output);
endif;

// Output the items.
foreach ($output as $item) :
    echo $item;
endforeach;
