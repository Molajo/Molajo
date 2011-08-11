<?php
/**
 * @package     Molajo
 * @subpackage  Submenu
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', 'submenu');
$wrap = $params->def('wrap', 'div');

require_once dirname(__FILE__).'/helper.php';

$list = modSubmenuHelper::getItems();