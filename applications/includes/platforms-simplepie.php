<?php
/**
 * @package     Molajo
 * @subpackage  Simple Pie
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 *  File Helper
 */
$fileHelper = new FileHelper();
$fileHelper->requireClassFile(PLATFORMS . '/jplatform/simplepie/simplepie.php', 'SimplePie');

