<?php
/**
 * @package     Molajo
 * @subpackage  Mustache
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *	Mustache
 */
$fileHelper = new MolajoFileHelper();
$fileHelper->requireClassFile(PLATFORM_MUSTACHE . '/Mustache.php', 'Mustache');
