<?php
/**
 * Database Adapter
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

// Change MysqliDriver lines 73-74

//$options['port'] = null;
//$options['socket'] = null;

//$options['port'] = (isset($options['[port'])) ? (int) $options['port'] : 3306;
//$options['socket'] = (isset($options['socket'])) ? $options['socket'] : '';

namespace Molajo\Database;

include __DIR__ . '/Bootstrap.php';

$db_type     = "mysqli";
$db_host     = "localhost";
$db_user     = "root";
$db_password = "root";
$db_name     = "molajo_site2";
$db_prefix   = "molajo_";

$options                = array();
$options['db_type']     = $db_type;
$options['db_host']     = $db_host;
$options['db_user']     = $db_user;
$options['db_password'] = $db_password;
$options['db_name']     = $db_name;
$options['db_prefix']   = $db_prefix;
$options['select']      = true;

use Molajo\Database\Adapter\Joomla;

$adapter_adapter = new Joomla($options);
$adapter         = new Driver($adapter_adapter);

echo '<br /> Admin: <br />';
$sql = 'select username from molajo_users where id = 1';
echo $adapter->loadResult($sql);

echo '<br /> Get the Query Object, fill the query Load the results <br />';
$sql     = 'select * from molajo_users';
$results = $adapter->loadObjectList($sql);

echo '<pre>';
var_dump($results);
echo '</pre>';
