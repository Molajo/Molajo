<?php
/**
 * @package     Molajo
 * @subpackage  ACL
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Core ACL
 *
 * @package	Molajo
 * @subpackage	ACL
 * @since	1.0
 */
class MolajoACLCore extends MolajoACL
{
    /**
     *  Core ACL - Called by the ACL Class
     *
     *  TYPE 0 --> No check needed (Cancel, Close, Help, Separator, etc.)
     *  TYPE 1 --> MolajoACL::authoriseTask -> MolajoACLCore:checkXYZAuthorisation -> direct back into one of three sub-types, below
     *  TYPE 2 --> MolajoACL::getQueryInformation -> Build query to filter rows Users are authorised to see and/or relates to the filter selected
     *  TYPE 3 --> MolajoACL::getList -> Retrieves list of ACL-related data
     *  TYPE 4 --> MolajoACL::checkPermissions -> Verifies access for a specific user or group
     *  TYPE 5 --> MolajoACL::getFormAuthorisations -> MolajoACLCore::checkFormAuthorisations
     */

    /**
     *  TYPE 0 No check needed
     *
     *  @return boolean true
     */
    public function checkCancelAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return true;
    }
    public function checkCopyAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return true;
    }
    public function checkCloseAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return true;
    }
    public function checkMoveAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return true;
    }
    public function checkOpenAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return true;
    }
    public function checkHelpAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return true;
    }
    public function checkSeparatorAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return true;
    }

    /**
     *  TYPE 1:A Controller -> authoriseTask -> MolajoACLCore::checkXYZAuthorisation --> MolajoACLCore::checkTaskManage
     *
     *  Management tasks, such as accessing the Administrator area, updating configuration, and checking in content
     *
     *  Method below calls checkTask Manage and is called by MolajoACL::authoriseTask which is called by a controller
     *
     *  checkAdminAuthorisation - Can User update the configuration data for the Component
     *  checkCheckInAuthorisation - Can User check-in Content for the Component
     *  checkManageAuthorisation - Can User access the Component from within the Administrator
     *
     *  @param string $option_value_literal ex. admin or manage.com_articles
     *  @param string $option ex. com_articles
     *
     *  @return boolean
     */
    public function checkTaskManage ($option, $entity, $task, $catid, $id, $item)
    {
        $molajoConfig = new MolajoModelConfiguration ($option);
        $taskTests = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_ACL_TASK_TO_METHODS, $task);

        if (is_array($taskTests)) {
        } else {
            $taskTests = array($taskTests);
        }

        $authorised = false;
        foreach ( $taskTests as $item )   {
//amy            $authorised = $this->authorise($item, $option);
            $authorised = true;
            if ($authorised) {
                break;
            }
        }
        return $authorised;
    }

    /** members **/
    public function checkAdminAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskManage ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkCheckinAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskManage ($option='com_checkin', $entity, $task, $catid, $id, $item);
    }
    public function checkManageAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskManage ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkOptionsAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskManage ($option, $entity, 'admin', $catid, $id, $item);
    }

    /**
     *  TYPE 1:B Controller -> authoriseTask -> MolajoACLCore::getDisplayAuthorisation --> MolajoACLCore::checkTaskView
     *
     *  Display Task or View Access
     *
     *  Method below calls checkTaskView and is called by MolajoACL::authoriseTask which is called by a controller
     *
     *  getDisplayAuthorisation - Can User view specific content for the Component
     *
     *  @param string $option - ex. com_articles
     *  @param string $entity - name of item, ex. 'article'
     *  @param string $task - ex save
     *  @param int $id - primary key of item
     *  @param array $item - array of item elements
     *  @param string $option_value_literal - ex save.article
     *
     *  @return boolean
     */
    public function checkTaskView ($option, $entity, $task, $catid, $id, $item)
    {
        $viewGroups = $this->getUsergroupsView();
        if (in_array($item->access, $viewGroups)) {
            return true;
        }
    }

    /** members **/
    public function checkDisplayAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskManage ($option, $entity, $task, $catid, $id, $item);
    }

    /**
     *  TYPE 1:C Controller -> authoriseTask -> MolajoACLCore::getXZYAuthorisation --> MolajoACLCore::checkTaskUpdate
     *
     *  Called from Controller to verify user authorisation for a specific Task
     *
     *  ACL authoriseTask Method calls one of below which calls MolajoACLCore::checkTaskUpdate
     *
     *  checkAddAuthorisation - test before presenting the editor for a create
     *  checkApplyAuthorisation - create or save (check id)
     *  checkCreateAuthorisation - create
     *  checkDeleteAuthorisation - delete
     *  checkEditAuthorisation - test before presenting the editor for an update
     *  checkEditownAuthorisation - test before presenting the editor for an update
     *  checkPublishAuthorisation - can update fields identified as 002 MOLAJO_CONFIG_OPTION_ID_PUBLISH_FIELDS
     *  checkSaveAuthorisation - save
     *  checkSave2copyAuthorisation - view existing item and create new
     *  checkSave2newAuthorisation - view existing item and create new
     *  checkTrashAuthorisation - trash
     *  checkRestoreAuthorisation - restore
     *
     *  Access to Content Item and Primary Key, Category, Component and Global
     *  Matching TaskOwn methods checked before advancing to the next level
     *
     *  @param string $option - ex. com_articles
     *  @param string $entity - name of item, ex. 'article'
     *  @param string $task - ex save
     *  @param int $id - primary key of item
     *  @param array $item - array of item elements
     *  @param string $option_value_literal - ex save.article
     *
     *  @return boolean
     */
    public function checkTaskUpdate ($option, $entity, $task, $catid, $id, $item)
    {
        $molajoConfig = new MolajoModelConfiguration ($option);
        $taskTests = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_ACL_TASK_TO_METHODS, $task);
        if (is_array($taskTests)) {
        } else {
            $taskTests = array($taskTests);
        }
        if (count($taskTests) == 0) {
            MolajoError::raiseError(500, MolajoText::_('MOLAJO_ACL_NOT_IDENTIFIED_TASK_ACL_METHOD'). ' '.$task);
            return false;
        }
        
        /** loop thru tests **/
        $authorised = false;
        foreach ( $taskTests as $option_value_literal )   {
            if ($option_value_literal == '') {
                $authorised = true;
            } else {
                $authorised = $this->testAuthorisation ($option, $entity, $task, $catid, $id, $item, $option_value_literal);
            }
        }

        return $authorised;
    }

    /**
     * testAuthorisation
     *
     * called from checkTaskUpdate to look through (possibly) multiple ACL checks for a single task
     * 
     * @param $option
     * @param $entity
     * @param $task
     * @param $catid
     * @param $id
     * @param $item
     * @param $option_value_literal
     * @return bool
     */
    public function testAuthorisation ($option, $entity, $task, $catid, $id, $item, $option_value_literal)
    {
    
        return true;

        $authorised = false;

        if ((int) $id > 0) {
            $authorised = $this->authorise($option_value_literal, $option.'.'.$entity.'.'.$id);

            if ($authorised === false) {
                $authorised = $this->authorise($option_value_literal.'.own', $option.'.'.$entity.'.'.$id);

                if ($authorised === true) {
                    if ($item->created_by == MolajoFactory::getUser()->get('id')) {
                        $authorised = true;
                    }
                }
            }
        }
        if ($authorised === false && (int) $catid > 0) {
            $authorised = $this->authorise($option_value_literal, $option.'.'.'category'.'.'.$catid);
        } else if ($authorised === false) {
            $authorised = $this->authorise($option_value_literal, $option);
        }

        return $authorised;
    }

    /** members **/
    public function checkAddAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkApplyAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkArchiveAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkCreateAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkDeleteAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkEditAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkEditownAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkPublishAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkFeatureAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkNewAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkOrderupAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkOrderdownAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkEditstateAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkReorderAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkRestoreAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkSaveAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkSave2copyAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkSave2newAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkSaveorderAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkSearchAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkSpamAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkStateAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkStickyAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkTrashAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkUnfeatureAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkUnpublishAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkUnstickyAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }
    public function checkLoginAuthorisation ($option, $entity, $task, $catid, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $catid, $id, $item);
    }

    /**
     *  TYPE 2 --> MolajoACL::getQueryInformation -> getXYZQueryInformation
     * 
     *  Called by MolajoACL::getQueryInformation - which is called from Models and Field Filters to prepare query information
     *
     *  getUserQueryInformation - returns query parts filtering access for the View Action for user
     *  getQueryInformationFilter - returns query parts filtering access for the ACL Filter Object Access
     *  getXYZQueryInformation - anything that follows that pattern
     *
     *  @param object $query - query updated by method
     *  @param string $type - specifies the type of method
     *  @param array $params set of parameters needed by method to build query
     *  @return boolean
     */

    /**
     *  TYPE 2 --> MolajoACL::getQueryInformation -> getUserQueryInformation
     */
    public function getUserQueryInformation ($query, $option, $params)
    {
        $query->select('a.access');
//        $query->select('ag.title AS access_level');

        $aclClass = MolajoACL;
        $acl = new $aclClass();
        $groupList = $acl->getList('viewaccess');

        $query->where('a.access in ('.$groupList.')');
//$query->join('LEFT', '#__viewlevels AS ag ON a.access = ag.id');

        /** Molajo_note: come back and deal with edit and edit state capabilities
         * the way this is currently coded in core, users would not be able to update their articles
         * from a blog or list view unless they have core edit or edit state for all of a component
         *
         * Find a better way to restrict published information that only restricts what is needed

        if ($this->authorise('edit.state', 'com_articles')
            || $this->authorise('edit', 'com_articles')) {
        } else {
            $query->where('a.state in ('.MOLAJO_STATE_ARCHIVED.','.MOLAJO_STATE_PUBLISHED.')');

            $nullDate = $this->getDbo()->Quote($this->getDbo()->getNullDate());
            $nowDate = $this->getDbo()->Quote(MolajoFactory::getDate()->toMySQL());

            $query->where('(a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.')');
            $query->where('(a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.')');
        }
        */
        return $query;
    }

    /**
     *  TYPE 2 --> MolajoACL::getQueryInformation -> getFilterQueryInformation
     *
     * @param $query
     * @param $option
     * @param $filterValue
     * @return void
     */
    public function getFilterQueryInformation ($query, $option, $filterValue)
    {
        if ((int) $filterValue == 0) {
        } else {
            $query->where('a.access = '. (int) $filterValue);
        }
    }

    /**
     *  TYPE 2 --> MolajoACL::getQueryInformation -> getViewaccessQueryInformation
     *
     *  Usage:
     *  $acl = new MolajoACL ();
     *  $acl->getQueryInformation ('', &$query, 'viewaccess', array('table_prefix'=>'m.'));
     *
     * @param $query
     * @param $option
     * @param $params
     * @return
     */
    public function getViewaccessQueryInformation ($query, $option, $params)
    {
        $prefix = $params['table_prefix'];
        if (trim($prefix == '')) {
        } else {
            $prefix = $prefix.'.';
        }

        $acl	= new MolajoACL();
        $list = implode(',', $acl->getList('viewaccess'));

        $query->join('INNER', '#__assets AS assets ON assets.id = '.$prefix.'asset_id');
        $query->where('assets.access IN ('.$list.')');

        return;
    }

    /**
     *  TYPE 3 --> MolajoACL::getList -> Retrieves list of ACL-related data
     *
     *  Called by MolajoACL::getList
     *
     *  getActionsList
     *  getAssetsList
     *  getCategoriesList
     *  getGroupsList
     *  getGroupingsList 
     *  getUsergroupsList
     *  getUsergroupingsList
     *
     * MolajoFactory::getUser()->
     *
     *  @param string $id
     *  @param string $option
     *  @param string $task
     *  @param array  $params optional
     *
     *  @return object list requested
     *  @return boolean
     */

    /**
     *  TYPE 3 --> MolajoACL::getList -> getActionsList
     */
    public function getActionsList($id, $option, $task, $params=array())
    {
		$actions = array();

        $component = $params[0];
        $section = $params[1];

		if (defined('MOLAJO_BASE_FOLDER')
            && is_file(MOLAJO_BASE_FOLDER.'/components/'.$component.'/access.xml')) {
			$xml = simplexml_load_file(MOLAJO_BASE_FOLDER.'/components/'.$component.'/access.xml');

			foreach ($xml->children() as $child)
			{
				if ($section == (string) $child['name']) {
					foreach ($child->children() as $action) {
						$actions[] = (object) array('name' => (string) $action['name'],
                                                    'title' => (string) $action['title'],
                                                    'description' => (string) $action['description']);
					}
					break;
				}
			}
		}
		return $actions;
    }

    /**
     *  TYPE 3 --> MolajoACL::getList -> getAssetsList
     */
    public function getAssetsList($id, $option, $task, $params=array())
    {

    }

    /**
     *  TYPE 3 --> MolajoACL::getList -> getViewaccessList
     */
    public function getViewaccessList ($userid='', $option='', $action='', $params=array())
    {
        return $this->getUsergroupsList($userid, $option, MOLAJO_ACL_ACTION_VIEW, $params);
    }
    
    /**
     *  TYPE 3 --> MolajoACL::getList -> getCategoriesList
     */
    public function getCategoriesList($id, $option, $task, $params=array())
    {
        echo 'Finish getCategoriesList';
        die();

		$db = MolajoFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('DISTINCT a.id, a.title');

        $query->from('#__categories a');
        $query->from('#__assets b');
        $query->from('#__groupings c');
        $query->from('#__permissions_groupings d');

        $query->where('a.asset_id = b.id');
        $query->where('b.access = c.id');
        $query->where('d.grouping_id = c.id');
        
        $query->join('LEFT', '#__group_to_groupings AS b ON b.grouping_id = a.id');
        $query->join('LEFT', '#__groupings AS d ON d.access = a.id');

        if ($option == '') {
        } else {
            $query->where('a.extension = '.$db->escape($option));
        }

        $query->join('ORDER BY c.id ASC');

		$results = $db->loadObjectList();

        $categories = array();
        foreach ( $results as $category )   {
            $categories[] = $category->id;
        }
        return implode(',', array_unique($categories));
    }

    /**
     *  TYPE 3 --> MolajoACL::getList -> getGroupsList
     */
    public function getGroupsList($id, $option, $task, $params=array())
    {
		$db = MolajoFactory::getDBO();
		$db->setQuery(
			'SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level' .
			' FROM #__groups AS a' .
			' LEFT JOIN `#__groups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC'
		);
		$options = $db->loadObjectList();
		return $options;
    }

    /**
     *  TYPE 3 --> MolajoACL::getList -> getUsergroupsList
     */
    public function getUsergroupsList($userid, $option, $action, $params=array())
    {
        $acl = new MolajoACL();
        $cache 	= MolajoFactory::getCache('coreacl.getUsergroupsList', '');

        /** $key */
        if ((int) $userid == 0) {
            $userid = MolajoFactory::getUser()->get('id');
        }

        /** query  */
		$db = MolajoFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('DISTINCT c.id');

        if ($action == MOLAJO_ACL_ACTION_VIEW) {
            $query->from('#__groupings a');
            $query->join('LEFT', '#__group_to_groupings AS b ON b.grouping_id = a.id');
            $query->join('LEFT', '#__groups AS c ON c.id = b.group_id');
        } else {
            $query->from('#__groups c');
        }

        /** Guest: 3 - Public: 4 */
        if ((int) $userid == 0) {
            $query->where('c.id IN ('.MOLAJO_ACL_GROUP_PUBLIC.','.MOLAJO_ACL_GROUP_GUEST.')');
        } else {
            $query->join('LEFT', '#__user_groups AS d ON d.group_id = b.group_id');
            $query->where('d.user_id = '.(int) $userid);
        }

        /** $asset */
        if ($option == '') {
        } else if ($action == MOLAJO_ACL_ACTION_VIEW) {
            /* find the access level for com_content in the extensions table */
            $query->from('#__extensions AS e');
            $query->where('e.element = '.$db->_quoted($option));
            $query->where('e.access = a.id');
        }

        /** run query **/
        $hash = hash('md5', $query->__toString(), false);
        $authorised = $cache->get($hash);

        if ($authorised) {
        } else {

            $db->setQuery($query->__toString());
            $options = $db->loadObjectList();

            /** error handling */
            if ($db->getErrorNum()) {
               //amy $this->setError($db->getErrorMsg());
                return false;

            } else if (count($options) == 0) {
                return false;
            }

            /** load into an array as expected by 1.6 */
            $authorised = array();
            foreach ($options as $option) {
                $authorised[] = $option->id;
            }

            $authorised[] = MOLAJO_ACL_GROUP_PUBLIC;
            if ((int) $userid == 0) {
                $authorised[] = MOLAJO_ACL_GROUP_GUEST;
            } else {
                $authorised[] = MOLAJO_ACL_GROUP_REGISTERED;
            }

            $cache->store($authorised, $hash);
        }

		return $authorised;
    }

    /**
     *  TYPE 3 --> MolajoACL::getList -> getUsergroupingsList
     *
     * @param $id
     * @param $option
     * @param $task
     * @param array $params
     * @return void
     */
    public function getUsergroupingsList($id, $option, $task, $params=array())
    {

    }

    /**
     * getAllUserGroups
     *
     * Get all defined groups
     *
     * @param string $option
     * @param string $task
     * @param array $params
     * @return void
     */
    public function getAllusergroupsList ($option='', $task='', $params=array())
    {
echo 'bang';
        die();
	}

   /**
     *  TYPE 4 --> MolajoACL::checkPermissions -> MolajoACLCore::checkUserPermissions
     *
     *  Checks specific authorisation for a user or group, returning a true or false test result.
     *
     *  @param string $userid
     *  @param string $action 1-login; 2-create; 3-view; 4-edit; 5-delete; 6-admin
     *  @param integer $asset key
     *  $param integer $access key
     *
     *  @return object list requested
     *  @return boolean
     */
    public function checkUserPermissions ($userid, $action, $asset='', $access='')
    {
        if ((int) $userid == 0) {
            $userid = MolajoFactory::getUser()->id;
        }

        if ($action == 'login') {
            if ((int) $userid == 0) {
                return false;
            }
            return $this->checkUserPermissionsLogin($userid);
        }

        /** user groups */
        $acl = new MolajoACL ();
        $userGroups = $acl->getList('Usergroups', $userid, '', $action);

        /** query  */
		$db = MolajoFactory::getDBO();
        $query = $db->getQuery(true);

        /** view access */
        if ($action == MOLAJO_ACL_ACTION_VIEW) {

            if ((int) $access == 0) {
                $query->select('content_table');
                $query->from('#__assets a');
                $query->where('asset_id = '.(int) $asset);

                $db->setQuery($query);
                $tableName = $db->loadResult();

                if ($db->getErrorNum()) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }

                $query = $db->getQuery(true);
                $query->select('access');
                $query->from($db->_nameQuote($tableName));
                $query->where('asset_id = '.(int) $asset);
                $db->setQuery($query);
                $access = $db->loadResult();
                if ($db->getErrorNum()) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }
            }
            return in_array($access, $userGroups);
        }

        /** Not View Access */
        $query->select('a.group');
        $query->from('#__permissions_groups AS a');
        $query->join('LEFT', '#__assets AS b ON b.id = a.asset_id');
        $query->join('LEFT', '#__actions AS c ON c.id = a.action_id');
        $query->where('c.title = '.$db->_quoted($action));
        $query->where('a.group IN ('.implode(',', $userGroups).')');

        $db->setQuery($query);
        $accessResult = $db->loadObjectList();
        if ($db->getErrorNum()) {
            return new MolajoException($db->getErrorMsg());
        }

        if (count($accessResult) == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * checkUserPermissionsLogin
     *
     * @param $key
     * @param $action
     * @param null $asset
     * @return void
     */
    public function checkUserPermissionsLogin ($userid)
    {
		$db = MolajoFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('count(*) as count');
        $query->from('#__user_applications a');
        $query->where('application_id = '.(int) MOLAJO_APPLICATION_ID);
        $query->where('user_id = '.(int) $userid);

        $db->setQuery($query->__toString());
        $result = $db->loadResult();

        if ($db->getErrorNum()) {
            $this->setError($db->getErrorMsg());
            return false;
        }

        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * checkGroupPermissions
     *
     * @param $key
     * @param $action
     * @param null $asset
     * @return void
     */
    public function checkGroupPermissions ($key, $action, $asset=null)
    {

    }

    /**
     *  TYPE 5 --> MolajoACL::getFormAuthorisations -> MolajoACLCore::checkFormAuthorisations
     *
     *  Disables form fields that required editstate for those without such authorisation
     *
     *  @param string $option 'com_articles', etc.
     *  @param string $entity 'article', or 'comment', etc.
     *  @param string $task 'add', 'delete', 'publish'
     *  @param integer $id - primary key for content
     *  @param object $form - form object fields
     *  @param object $item - item data
     *
     *  @return object list requested
     *  @return boolean
     */
    public function checkFormAuthorisations ($option, $entity, $task, $id, $form, $item)
    {
        if ($item->canPublish === true) {
            return;
        }

        $molajoConfig = new MolajoModelConfiguration ($option);
        $fieldsEditState = $molajoConfig->getOptionList (MOLAJO_CONFIG_OPTION_ID_PUBLISH_FIELDS);
        foreach ($fieldsEditState as $count => $editstateItem ) {
            $form->setFieldAttribute($editstateItem->value, 'disabled', 'true');
            $form->setFieldAttribute($editstateItem->value, 'filter', 'unset');
        }
    }
    
    /**
     * checkComponentTaskAuthorisation
     *
     * Method to return a list of all categories that a user has permission for a given ACL Action
     *
     *  @param      string      $component
     *  @param      string      $action
     *  @return     boolean
     */
    public function checkComponentTaskAuthorisation ($option, $option_value_literal)
    {
        return MolajoFactory::getUser()->getAuthorisedCategories($option, $option_value_literal);
    }
}