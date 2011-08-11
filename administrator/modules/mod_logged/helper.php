<?php
/**
 * @version		$Id: helper.php 21320 2011-05-11 01:01:37Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	mod_logged
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	mod_logged
 */
abstract class modLoggedHelper
{
	/**
	 * Get a list of logged users.
	 *
	 * @param	JObject	The module parameters.
	 * @return	mixed	An array of articles, or false on error.
	 */
	public static function getList($params)
	{
		// Initialise variables
		$db = MolajoFactory::getDbo();
		$user = MolajoFactory::getUser();
		$query = $db->getQuery(true);

		$query->select('s.time, s.application_id, u.id, u.name, u.username');
		$query->from('#__session AS s');
		$query->leftJoin('#__users AS u ON s.userid = u.id');
		$query->where('s.guest = 0');

		$db->setQuery($query, 0, $params->get('count', 5));

        $results = $db->loadObjectList();

		// Check for database errors
		if ($error = $db->getErrorMsg()) {
			JError::raiseError(500, $error);
			return false;
		};

        /** Add information to query results */
        $i = 1;
        $acl = new MolajoACL ();
        
        if (count($results) == 0) {
            $results[0]->columncount = '5';
            if ($params->get('name', 1)) {
                $results[0]->columnheading1 = JText::_('MOD_LOGGED_NAME');
            } else {
                $results[0]->columnheading1 = JText::_('JGLOBAL_USERNAME');
            }
            $results[0]->columnheading2 = JText::_('JCLIENT');
            $results[0]->columnheading3 = JText::_('JGRID_HEADING_ID');
            $results[0]->columnheading4 = JText::_('MOD_LOGGED_LAST_ACTIVITY');
            $results[0]->columnheading5 = JText::_('MOD_LOGGED_LOGOUT');
        
        } else {

            $permission = $acl->authoriseTask ('administer', 'com_users', '', '', '');

            foreach($results as $k => $result) {
                $results[$k]->logoutLink = '';
                
                $results[$k]->columncount = '5';
                if($params->get('name', 1)) {
                    $results[$k]->columnheading1 = JText::_('MOD_LOGGED_NAME');
                } else {
                    $results[$k]->columnheading1 = JText::_('JGLOBAL_USERNAME');
                }
                $results[$k]->columnheading2 = JText::_('JCLIENT');
                $results[$k]->columnheading3 = JText::_('JGRID_HEADING_ID');
                $results[$k]->columnheading4 = JText::_('MOD_LOGGED_LAST_ACTIVITY');
                $results[$k]->columnheading5 = JText::_('MOD_LOGGED_LOGOUT');     
                $permission = true;
                if($permission) {
                    $results[$k]->editLink = JRoute::_('index.php?option=com_users&task=user.edit&id='.$result->id);
                    $results[$k]->logoutLink = JRoute::_('index.php?option=com_login&task=logout&uid='.$result->id .'&'. JUtility::getToken() .'=1');
                }
                if($params->get('name', 1) == 0) {
                    $results[$k]->name = $results[$k]->username;
                }
            }
        }
        
		return $results;
	}

	/**
	 * Get the alternate title for the module
	 *
	 * @param	JObject	The module parameters.
	 * @return	string	The alternate title for the module.
	 */
	public static function getTitle($params)
	{
		return JText::plural('MOD_LOGGED_TITLE',$params->get('count'));
	}
}

