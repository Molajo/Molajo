<?php
/**
 * @package     Molajo
 * @subpackage  Load Molajo Framework
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Molajo
 */
if (class_exists('MolajoVersion')) {
} else {
    require_once MOLAJO_APPLICATIONS . '/includes/version.php';
}


/**
 *  Debug ed PSR-0 (for PHP
 */
$load->requireClassFile(PLATFORM_MOLAJO . '/debug/PhpConsole.php', 'PhpConsole');
//PhpConsole::start(true, true, PLATFORM_MOLAJO . '/debug');
