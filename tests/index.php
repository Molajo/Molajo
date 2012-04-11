<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
define('MOLAJO', 'Long Live Molajo!');
echo 'hello';

ini_set('magic_quotes_runtime', 0);
ini_set('zend.ze1_compatibility_mode', 0);

$f = substr(__DIR__, 0, strlen(__DIR__) - 6);
define('MOLAJO_CONFIGURATION_FOLDER', $f . '/Tests/Molajo/Application/Configuration');
define('MOLAJO_BASE_FOLDER', $f.'/source');

require_once MOLAJO_BASE_FOLDER . '/Autoload.php';

Molajo\Application\Molajo::Application()
    ->initialise()
    ->request()
    ->process()
    ->response();
