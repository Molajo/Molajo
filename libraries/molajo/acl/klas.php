<?php
/**
 * @version     $id: molajo.php
 * @package     Molajo
 * @subpackage  Joomla ACL
 * @copyright   Copyright (C) 2011 Klas Berlič. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();
jimport('molajo.access.access');
jimport('molajo.user.user');


/**
 * Joomla Molajo ACL
 *
 * @package	Joomla
 * @subpackage	ACL
 * @since	1.0
 */
class MolajoACLKlas extends ACL
{	
	private static $_uid = null;
	
	
	public function __construct($uid = null) {
		if (isset($uid)) {
			self::$_uid = $uid;
		} else {
			self::$_uid = JFactory::getUser()->id;
		}
		
	}
    /**
     *  Molajo ACL - Called by the parent ACL Class
     *
     *  TYPE 1 --> ACL::authoriseTask -> MolajoACL:checkXYZAuthorisation -> direct back into one of three types, below
     *
     *  Manage Tasks
     *  MolajoACL::checkAdminAuthorisation
     *  MolajoACL::checkCheckInAuthorisation
     *  MolajoACL::checkManageAuthorisation -> MolajoACL:checkTaskManage -> JFactory::getUser()->authorise
     *
     *  Display Task
     *  MolajoACL::checkDisplayAuthorisation -> MolajoACL:checkTaskView -> TRUE (for now)
     *
     *  Data Update Tasks
     *  MolajoACL::checkAddAuthorisation
     *  MolajoACL::checkEditAuthorisation
     *  MolajoACL::checkEditownAuthorisation
     *  MolajoACL::checkEditstateAuthorisation
     *  MolajoACL::checkDeleteAuthorisation
     *  MolajoACL::checkTrashAuthorisation
     *  MolajoACL::checkRestoreAuthorisation -> MolajoACL:checkTaskUpdate -> MolajoACL:testAuthorisation -> JFactory::getUser()->authorise
     *
     *  TYPE 2 --> ACL::getQueryInformation -> Build query to filter rows Users are authorised to see and/or relates to the filter selected
     *  TYPE 3 --> ACL::getList -> Retrieves list of ACL-related data
     *  TYPE 4 --> ACL::getValue -> Retrieves specific value of ACL data
     */

    /**
     *  TYPE 1:A Controller -> authoriseTask -> MolajoACL::checkXYZAuthorisation --> MolajoACL::checkTaskManage
     *
     *  Management tasks, such as accessing the Administrator area, updating configuration, and checking in content
     *
     *  Called from Controller to verify user authorisation for a specific Task
     *
     *  ACL authoriseTask Method calls one of below which calls MolajoACL::checkTaskUpdate
     *
     *  $option_value_literal - molajo.admin or molajo.manage or molajo.checkin
     *  $option - component, ex. com_articles or com_comments

     *  Method below calls above, and is called by ACL::authoriseTask
     *
     *  checkAdminAuthorisation - Can User update the configuration data for the Component
     *  checkCheckInAuthorisation - Can User check-in Content for the Component
     *  checkManageAuthorisation - Can User access the Component from within the Administrator
     *
     *  @param string $option_value_literal ex. molajo.admin or molajo.manage.com_articles
     *  @param string $option ex. com_articles
     *
     *  @return boolean
     */
    public function checkTaskManage ($option, $entity, $task, $id, $item)
    {
        /** Molajo_Note: Can handle more than one test per task **/
        $taskTests = MolajoModelConfiguration::getOptionValueLiteral (MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS, $task);
        if (is_array($taskTests)) {
        } else {
            $taskTests = array($taskTests);
        }
        
        foreach ( $taskTests as $item )   {
            
				$authorised = MAccess::isUserAuthorizedTo(self::$_uid, $task, $option);
				       
            if ($authorised) {
                break;
            }
        }
        return $authorised;
    }
    /** members **/
    public function checkAdminAuthorisation ($option, $entity, $task, $id, $item)
    {
        return $this->checkTaskManage ($option, $entity, $task, $id, $item);
    }
    public function checkCheckInAuthorisation ($option, $entity, $task, $id, $item)
    {
        return $this->checkTaskManage ('com_checkin', $entity, $task, $id, $item);
    }
    public function checkManageAuthorisation ($option, $entity, $task, $id, $item)
    {
        return $this->checkTaskManage ($option, $entity, $task, $id, $item);
    }

    /**
     *  TYPE 1:B Controller -> authoriseTask -> MolajoACL::getDisplayAuthorisation --> MolajoACL::checkTaskView
     *
     *  checkTaskView
     *
     *  Called from Controller to verify user authorisation for a specific Task
     *
     *  ACL authoriseTask Method calls one of below which calls MolajoACL::checkTaskUpdate
     *
     *  getDisplayAuthorisation - Can User view specific content for the Component
     *
     *  @param string $option - ex. com_articles
     *  @param string $entity - name of item, ex. 'article'
     *  @param string $task - ex save
     *  @param int $id - primary key of item
     *  @param array $item - array of item elements
     *  @param string $option_value_literal - ex molajo.save.article
     *
     *  @return boolean
     */
    public function checkTaskView ($option, $entity, $task, $id, $item)
    {	
    	
    	$assetID = $option.$entity.$id;
        $ma = MAccess::getInstance();
        return $ma->isUserAuthorisedToView (self::$_uid, $assetID);
        
    }
    /** members **/
    public function checkDisplayAuthorisation ($option, $entity, $task, $id, $item)
    { 
    	// WHAT IS THIS?? 
        return $this->checkTaskManage ($option, $entity, $task, $id, $item);
    }

    /**
     *  TYPE 1:C Controller -> authoriseTask -> MolajoACL::getXZYAuthorisation --> MolajoACL::checkTaskUpdate
     *
     *  Called from Controller to verify user authorisation for a specific Task
     *
     *  ACL authoriseTask Method calls one of below which calls MolajoACL::checkTaskUpdate
     *
     *  checkAddAuthorisation - Can user add content
     *  checkEditAuthorisation - Can user edit content
     *  checkEditownAuthorisation - Can user edit own content
     *  checkEditstateAuthorisation - Can user edit state content
     *  checkDeleteAuthorisation - Can user delete content
     *  checkTrashAuthorisation - Can user trash content
     *  checkRestoreAuthorisation - Can user restore content
     *
     *  Access to Content Item and Primary Key, Category, Component and Global
     *  Matching TaskOwn methods checked before advancing to the next level
     *
     *  @param string $option - ex. com_articles
     *  @param string $entity - name of item, ex. 'article'
     *  @param string $task - ex save
     *  @param int $id - primary key of item
     *  @param array $item - array of item elements
     *  @param string $option_value_literal - ex molajo.save.article
     *
     *  @return boolean
     */
    public function checkTaskUpdate ($option, $entity, $task, $id, $item)
    {
        $taskTests = MolajoModelConfiguration::getOptionValueLiteral (MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS, $task);
        if (is_array($taskTests)) {
        } else {
            $taskTests = array($taskTests);
        }
        foreach ( $taskTests as $item )   {
            $authorised = $this->testAuthorisation ($option, $entity, $task, $id, $item, $item->option_value_literal);
            if ($authorised) {
                break;
            }
        }
        return $authorised;
    }
    public function testAuthorisation ($option, $entity, $task, $id, $item, $option_value_literal)
    {
        $authorised = false;

        // WHAT IF $ID = 0 ??
        //if ((int) $id= > 0) {
        if ((int) $id < 0) { 
        	$id = null;
        }
        	
            $authorised = MAccess::isUserAuthorizedTo(self::$_uid, $option_value_literal, $option.'.'.$entity.'.'.$id);      
            
			//  $methodName IS NOT DEFINED, LEAVING THIS OUT
            //if ($authorised === false && method_exists('MolajoACL', $methodName)) {
            if ($authorised === false ) {
            	$selfauthorised = MAccess::isUserAuthorizedTo(self::$_uid, $task, $option_value_literal.'.own', $option.'.'.$entity.'.'.$id);
				
            	// HERE YOU HAD $allowed, WHICH IS NOT DEFINED
                if ($selfauthorised === true) {
                    if ($item->created_by == self::$_uid) {
                        $authorised = true;
                    }
                }
            }
        
        if ($authorised === false && (int) $item->catid > 0) {
            $authorised = MAccess::isUserAuthorizedTo(self::$_uid, $option_value_literal, $option.'.'.'category'.'.'.$item->catid);
        }

        return $authorised;
    }

    /** members **/
    public function checkAddAuthorisation ($option, $entity, $task, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $id, $item);
    }
    public function checkEditAuthorisation ($option, $entity, $task, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $id, $item);
    }
    public function checkEditownAuthorisation ($option, $entity, $task, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $id, $item);
    }
    public function checkEditstateAuthorisation ($option, $entity, $task, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $id, $item);
    }
    public function checkDeleteAuthorisation ($option, $entity, $task, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $id, $item);
    }
    public function checkTrashAuthorisation ($option, $entity, $task, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $id, $item);
    }
    public function checkRestoreAuthorisation ($option, $entity, $task, $id, $item)
    {
        return $this->checkTaskUpdate ($option, $entity, $task, $id, $item);
    }
    
    /**
     *  TYPE 2 --> ACL::getQueryInformation -> getXYZQueryInformation
     * 
     *  Called by ACL::getQueryInformation - which is called from Models and Field Filters to prepare query information
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
     *  TYPE 2 --> ACL::getQueryInformation -> getUserQueryInformation
     */
    public function getUserQueryInformation ($query, $type, $params)
    {
        $query->select('a.asset_id'); 
        
        $db = JFactory::getDbo();     
        MAccess::insertFilterQuery($db, $query, 'a.asset_id' , 'view');

        return;
    }

    /**
     *  TYPE 2 --> ACL::getQueryInformation -> getFilterQueryInformation
     */
    public function getFilterQueryInformation ($query, $type, $params)
    {
        if (is_array($params)) {
            $filterValue = (int) $params['filterValue'];
        }
        if (is_numeric($filterValue) && $filterValue > 0) {
        } else {
            return;
        }
        
        $query->select('a.asset_id'); 
        
        $db = JFactory::getDbo();     
        MAccess::insertFilterQuery($db, $query, 'a.asset_id' , 'view', null, $filterValue );

    }

    /**
     *  TYPE 3 --> ACL::getList -> Retrieves list of ACL-related data
     *
     *  Called by ACL::getList
     *
     *  getUserviewgroupList - produces a list of View Access Level Groups for User
     *  getUsergroupList - produces an array of the authorised access levels for the user
     *  getUsercategoriesList - produces a list of all categories that a user has permission for a given action
     *  getXYZList - any such pattern
     *
     *  @param string $type directs to method needed
     *  @param array $params set of parameters needed to produce the list
     *
     *  @return object list requested
     *  @return boolean
     */

    /**
     *  TYPE 3 --> ACL::getList -> getUsergroupings
     */
    public function getUserviewgroupList ($option, $task, $params=array())
    {
		//THERE IS NO SUCH THING
    }

    /**
     *  TYPE 3 --> ACL::getList -> getUsergroupList
     */
    public function getUsergroupList ($option, $task, $params=array())
    {
        /*
        IN CORE JOOMLA THIS WILL GIVE YOU USERS GROUPS, NOT VIEW LEVELS!
        IM GIVING YOU THE SAME THING
        $groups	= JFactory::getUser()->getAuthorisedGroups();*/
    	
    	$groups = MUser::getGroupsByUser(self::$_uid, true);
        return implode(',', array_unique($groups)); 
    	
    }
    
    /**
     *  TYPE 3 --> ACL::getList -> getUsercategoriesList
     */
    public function getUsercategoriesList ($option, $task, $params=array())
    {
        $taskTests = MolajoModelConfiguration::getOptionValueLiteral (MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS, $task);
        if (is_array($taskTests)) {
        } else {
            $taskTests = array($taskTests);
        }
        $categories = array();
        $ma = MAccess::getInstance();
        
        foreach ( $taskTests as $action )   {
            
        	$authorised =  $ma->CategoriesIsUserAuthorizedTo($component, $action->option_value_literal);
            
            if (count($authorised) > 0 && is_array($authorised)) {
            	// ?????
                // $categories = array_merge($authorised, $categories);
                $categories = $authorised;
            }
        }
        return implode(',', array_unique($categories));
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
    	$ma = MAccess::getInstance();
        return $ma->CategoriesIsUserAuthorizedTo($component, $option_value_literal);
    }
}