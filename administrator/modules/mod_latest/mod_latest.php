<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', '');
$wrap = $params->def('wrap', 'div');
echo 'in mod_latest';
die();
require_once dirname(__FILE__).'/helper.php';

$items = modLatestHelper::getList($params, $user);