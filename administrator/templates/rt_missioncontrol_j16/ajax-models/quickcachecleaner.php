<?php
/**
 * @version � 1.6.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );
jimport('joomla.cache.cache');

global $mctrl;
require_once(dirname(__FILE__).DS.'..'.DS.'lib'.DS.'rtcachecleaner.class.php');

$action = JRequest::getString('action',null);
if ($action == 'clean') {
	RTCacheClean::clean();
} 

//return count
echo (RTCacheClean::getCount());


?>

