<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
define('MOLAJO', 'Long Live Molajo!');

ini_set('magic_quotes_runtime', 0);
ini_set('zend.ze1_compatibility_mode', 0);

define('BASE_FOLDER', __DIR__);

require_once BASE_FOLDER . '/Autoload.php';

$app = Molajo\Application\Molajo::Application()->execute();

