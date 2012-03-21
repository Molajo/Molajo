<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
define('MOLAJO', 'Long Live Molajo!');

ini_set('magic_quotes_runtime', 0);
ini_set('zend.ze1_compatibility_mode', 0);

define('MOLAJO_BASE_FOLDER', __DIR__);

require_once MOLAJO_BASE_FOLDER . '/Autoload.php';

Molajo\Application\Molajo::Application()
	->initialise()
	->request()
	->process()
	->response();

