<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
define('MOLAJO', 'Long Live Molajo!');

ini_set('magic_quotes_runtime', 0);
ini_set('zend.ze1_compatibility_mode', 0);

$f = substr(__DIR__, 0, strlen(__DIR__) - 6);
define('CONFIGURATION_FOLDER', $f . '/Tests/Molajo/Configuration');
define('BASE_FOLDER', $f.'/source');

require_once BASE_FOLDER . '/Autoload.php';

/** Initialize Optional Parameters */
$override_request_url = null;
$override_catalog_id = null;
$override_sequenceXML = null;
$override_finalXML = null;
