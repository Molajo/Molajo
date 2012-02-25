<?php
/**
 * @package     Molajo
 * @subpackage  Symfony Event Dispatcher Load
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 * The Symfony Event Dispatcher is part of the symfony framework and released under the MIT license.
 * https://github.com/fabpot/event-dispatcher
 */
defined('MOLAJO') or die;

/**
 *  File Helper
 */
$load = new MolajoLoadHelper();
require_once(PLATFORMS . '/sfEvent' . '/sfEventDispatcher.php');


