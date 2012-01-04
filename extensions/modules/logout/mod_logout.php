<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$view = $parameters->def('view', 'default');
$wrap = $parameters->def('wrap', 'none');
$rowset[0]->content = '';

/** logout link */
$task = $request['task'];
if ($task == 'edit' || $task == 'editA' || JRequest::getInt('hidemainmenu')) {
    $logoutLink = '';
} else {
    $logoutLink = MolajoRouteHelper::_('index.php?option=login&task=logout&' . JUtility::getToken() . '=1');
}
$hideLinks = JRequest::getBool('hidemainmenu');

$output = array();
$output[] = '<span class="logout">' . ($hideLinks ? ''
        : '<a href="' . $logoutLink . '">') . MolajoTextHelper::_('JLOGOUT') . ($hideLinks ? '' : '</a>') . '</span>';

/** rtl support */
if (MolajoController::getApplication()->direction == "rtl") :
    $output = array_reverse($output);
endif;

/** output into content array */
foreach ($output as $item) :
    $rowset[0]->text .= $item;
endforeach;