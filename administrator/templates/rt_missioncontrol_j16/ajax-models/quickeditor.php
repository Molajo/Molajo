<?php
/**
 * @version Ê 1.6.2 June 9, 2011
 * @author Ê ÊRocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license Ê http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );
jimport('joomla.cache.cache');

global $mctrl;

$db =& JFactory::getDBO();
$user =& JFactory::getUser();

$id = JRequest::getInt('id',0);
$editor = JRequest::getString('editor',null);

$query=$db->getQuery(true)
	->select('params')
	->from('#__users')
	->where('id = '.(int)$id);
	
$db->setQuery($query);
if ($db->query()) {
	$result = $db->loadResult();
	$params = json_decode($result);
	$res = 0;

	if ($editor && $editor != $params->editor) {
		$params->editor = $editor;
		
		$user->setParam('editor', $editor);
		
		$result = json_encode($params);
	
		$query = $db->getQuery(true)
			->update('#__users')
			->set('params = '.$db->Quote($result))
			->where('id = '.(int)$id);

		$db->setQuery($query);
		if ($db->query()) {
			$res = $db->getAffectedRows();
		}
		
	}
	
} else {
	continue;
}



?>