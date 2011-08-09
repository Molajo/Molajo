<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @copyright   Copyright (C) 2011 Molajo. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$filehelper = new MolajoFileHelper();
$filehelper->requireClassFile(dirname(__FILE__).'/helper.php', 'ModMenuHelper');
$filehelper->requireClassFile(dirname(__FILE__).'/menu.php', 'MolajoAdminCSSMenu');
