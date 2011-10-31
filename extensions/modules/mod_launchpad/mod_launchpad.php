<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', 'admin_launchpad');
$wrap = $params->def('wrap', 'none');

$filehelper = new MolajoFileHelper();
$filehelper->requireClassFile(dirname(__FILE__).'/helper.php', 'MolajoLaunchpadHelper');