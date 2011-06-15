<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Access
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Deprecated - Use MolajoACL
 *
 * Joomla 1.6 Class that handles all access authorisation routines.
 *
 * @package     Joomla.Platform
 * @subpackage  Access
 * @since       11.1
 */
class JAccess extends MolajoACL
{
	/**
     * Deprecated: Use MolajoACL::checkPermissions
     *
	 * Method to check if a user is authorised to perform an action, optionally on an asset.
	 *
	 * @param   integer  $userId  Id of the user for which to check authorisation.
	 * @param   string   $action  The name of the action to authorise.
	 * @param   mixed    $asset   Integer asset id or the name of the asset as a string.  Defaults to the global asset node.
	 *
	 * @return  boolean  True if authorised.
	 *
	 * @since   11.1
	 */
	public function check($userId, $action, $asset = null)
    {
        $acl = new MolajoACL();
        return $acl->checkPermissions ('user', $userId, $action, $asset, '');
    }

	/**
     * Deprecated: Use MolajoACL::checkPermissions
     *
	 * Method to check if a group is authorised to perform an action, optionally on an asset.
	 *
	 * @param   integer  $groupId  The path to the group for which to check authorisation.
	 * @param   string   $action   The name of the action to authorise.
	 * @param   mixed    $asset    Integer asset id or the name of the asset as a string.  Defaults to the global asset node.
	 *
	 * @return  boolean  True if authorised.
	 *
	 * @since   11.1
	 */
	public function checkGroup($groupId, $action, $asset = null)
	{
        $acl = new MolajoACL();
        return $acl->checkPermissions ('group', $groupId, $action, $asset, '');
	}

	/**
     * Note: No longer needed because checkGroup not used
     *
	 * Gets the parent groups that a leaf group belongs to in its branch back to the root of the tree
	 * (including the leaf group id).
	 *
	 * @param   mixed  $groupId  An integer or array of integers representing the identities to check.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   11.1
	 */
	protected function getGroupPath($groupId) {}

	/**
     * Deprecated: Use MolajoACL::getList
     *
	 * Method to return the JRules object for an asset.  The returned object can optionally hold
	 * only the rules explicitly set for the asset or the summation of all inherited rules from
	 * parent assets and explicit rules.
	 *
	 * @param   mixed    $asset      Integer asset id or the name of the asset as a string.
	 * @param   boolean  $recursive  True to return the rules object with inherited rules.
	 *
	 * @return  JRules   JRules object for the asset.
	 *
	 * @since   11.1
	 */
	public function getAssetRules($asset, $recursive = false)
    {
        $acl = new MolajoACL();
        return $acl->getList ('rules', $asset, $option='', $task='', $params=array());
    }

	/**
     * Deprecated: Use MolajoACL::getList
     *
	 * Method to return a list of user groups mapped to a user. The returned list can optionally hold
	 * only the groups explicitly mapped to the user or all groups both explicitly mapped and inherited
	 * by the user.
	 *
	 * @param   integer  $userId     Id of the user for which to get the list of groups.
	 * @param   boolean  $recursive  True to include inherited user groups.
	 *
	 * @return  array    List of user group ids to which the user is mapped.
	 *
	 * @since   11.1
     *
     * __usergroups
	 */
	public function getGroupsByUser($userId, $recursive = true)
	{
        $acl = new MolajoACL();
        return $acl->getList ('groups', $userId, $option='', $task='', $params=array());
	}

	/**
     * Deprecated: Use MolajoACL::getList

	 * Method to return a list of user Ids contained in a Group
	 *
	 * @param   integer   $groupId    The group Id
	 * @param   boolean   $recursive  Recursively include all child groups (optional)
	 *
	 * @return  array
	 *
	 * @since   11.1
	 *
	 * @todo      This method should move somewhere else?
	 */
	public function getUsersByGroup($groupId, $recursive = false)
	{
        $acl = new MolajoACL();
        return $acl->getList ('Groupusers', $option='', $task='', $params=array($groupId, $recursive));
	}

	/**
     * Deprecated: Use MolajoACL::getList
     *
	 * Method to return a list of view levels for which the user is authorised.
	 *
	 * @param   integer  $userId  Id of the user for which to get the list of authorised view levels.
	 *
	 * @return  array  List of view levels for which the user is authorised.
	 *
     * @deprecated 1.1	Use the MolajoACL::getList('Usergroupings', $userId) method instead.
     */
	public static function getAuthorisedViewLevels($userId)
	{
        $acl = new MolajoACL();
        return $acl->getList ('Usergroupings', $userId, $option='', $task='', $params=array());
	}

	/**
     * Deprecated: Use MolajoACL::getList
     *
	 * Method to return a list of actions for which permissions can be set given a component and section.
	 *
	 * @param   string   $component  The component from which to retrieve the actions.
	 * @param   string   $section    The name of the section within the component from which to retrieve the actions.
	 *
	 * @return  array    List of actions available for the given component and section.
	 *
	 * @since   11.1
	 * @todo    Need to decouple this method from the CMS. Maybe check if $component is a valid file (or create a getActionsFromFile method).
	 */
	public function getActions($component, $section='component')
	{
        $acl = new MolajoACL();
        return $acl->getList ('Actions', $option='', $task='', $params=array($component, $section));
	}
}