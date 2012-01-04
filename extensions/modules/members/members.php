<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$view = $parameters->def('view', 'default');
$wrap = $parameters->def('wrap', 'none');

require_once dirname(__FILE__) . '/helper.php';

$count = modOnlineHelper::getOnlineCount();

$rowset[0]->text = $count . '<img src="images/users.png" alt="' . MolajoTextHelper::_('ONLINE_USERS_ONLINE') . '/>';