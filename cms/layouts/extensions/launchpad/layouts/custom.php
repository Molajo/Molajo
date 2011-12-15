<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if (JRequest::getInt('hidemainmenu', 0) == 1) {
    $enabled = false;
    include dirname(__FILE__) . '/menu_disabled.php';
} else {
    $enabled = true;
    include dirname(__FILE__) . '/menu_enabled.php';
}