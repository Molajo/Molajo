<?php
/**
 * @package     Molajo
 * @subpackage  Mustache
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *    Mustache
 */
$load = new LoadHelper();
$load->requireClassFile(PLATFORMS . '/Mustache' . '/Mustache.php', 'Mustache');
