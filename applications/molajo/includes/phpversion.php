<?php
/**
 * @package     Molajo
 * @subpackage  PHP Version
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** php version check */
if (version_compare(PHP_VERSION, '5.3', '<')) {
    die('Your host needs to use PHP 5.3 or higher to run Molajo.');
}
