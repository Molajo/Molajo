<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$filehelper = new MolajoFileHelper();
$filehelper->requireClassFile(dirname(__FILE__).'/helper.php', 'modMenuHelper');

$rowset	= modMenuHelper::getList($parameters);

//require_once dirname(__FILE__).'/layouts/default.php';
