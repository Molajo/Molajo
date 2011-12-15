<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$fileHelper = new MolajoFileHelper();
$fileHelper->requireClassFile(dirname(__FILE__) . '/helper.php', 'modMenuHelper');

$rowset = modMenuHelper::getList($parameters);

//require_once dirname(__FILE__).'/layouts/default.php';
