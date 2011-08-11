<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', 'plain');
$wrap = $params->def('wrap', 'none');

require_once dirname(__FILE__).'/helper.php';

$count = modOnlineHelper::getOnlineCount();

$rowset[0]->content = $count.'<img src="images/users.png" alt="'.JText::_('MOD_ONLINE_USERS_ONLINE').'/>';