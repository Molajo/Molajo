<?php
/**
 * @package     Molajo
 * @subpackage  ACL
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoACL
{
    /**
     *  Type 1 --> authoriseTask
     *
     *  Called from Controllers to verify user authorisation for a specific Task
     *
     *  authoriseTask determines the implemented ACL class and runs appropriate checkTASKAuthorisation method(s)
     *
     * @param      string      $option - component ex. com_articles
     * @param      string      $entity - singular form of component subject ex. article, comment
     * @param      string      $task - ex. display, add, delete
     * @param      int         $catid - category key of the item or the value 0
     * @param      int         $id - primary key of the item or the value 0
     * @param      array       $item - table row related to the id or an empty array
     *
     * @return     boolean
     * @since      1.6
     */
    public function authoriseTask($option, $entity, $task, $catid = 0, $id = 0, $item = array())
    {
        $authoriseTaskMethod = 'check' . ucfirst(strtolower($task)) . 'Authorisation';
        $class = $this->getMethodClass($authoriseTaskMethod, $option);

        if ($class == false) {
            return false;
        }

        $aclClass = new $class();
        if (method_exists($aclClass, $authoriseTaskMethod)) {
            return $aclClass->$authoriseTaskMethod ($option, $entity, $task, $catid, $id, $item);
        } else {
            MolajoError::raiseError(403, MolajoText::_('MOLAJO_ACL_CLASS_METHOD_NOT_FOUND') . ' ' . $aclClass . '::' . $authoriseTaskMethod);
            return false;
        }
    }

    /**
     *  Type 1 --> getUserPermissionSet --> authoriseTask
     *
     * Evaluates User Permissions for a set of tasks and passes back an array with results
     *
     * Note: No additional ACL Implementation method needed as this loops thru authoriseTask
     *
     * @param string $option 'com_articles', etc.
     * @param string $entity 'article', or 'comment', etc.
     * @param string $set maps to a configuration set of tasks
     *
     * @return array
     */
    public function getUserPermissionSet($option, $entity, $set)
    {
        /** component parameters **/
        $params = MolajoComponentHelper::getParams($option);

        /** loop thru config options and add ToolBar buttons **/
        $count = 0;
        $processedOptionArray = array();
        $userPermissions = array();

        for ($i = 1; $i < 99; $i++) {

            $optionValue = $params->def($set . $i, null);

            if ($optionValue == null) {
                break;
            }
            if ($optionValue == '0') {

            } else if (in_array($optionValue, $processedOptionArray)) {

            } else {
                $processedOptionArray[] = $optionValue;
                $aclResults = $this->authoriseTask($option, $entity, $optionValue, 0, 0, array());
                $userPermissions[$optionValue] = $aclResults;
            }
        }
        return $userPermissions;
    }

    /**
     *  Type 1 --> authoriseTask-->getUserItemPermissions
     *
     * Evaluates a set of tasks for the current content item given the current users's authorisation
     *  A column is added to the resultset for each task containing the result of the test
     *
     * Note: No ACL Implementation Method is needed as this method uses authoriseTask
     *
     * @param string $option 'com_articles', etc.
     * @param string $entity 'article', or 'comment', etc.
     * @param string $task 'add', 'delete', 'publish'
     * @param integer $catid - category for content
     * @param integer $id - primary key for content
     * @param object $item - item columns and values
     *
     * @return boolean
     */
    public function getUserItemPermissions($option, $entity, $task, $catid, $id, $item)
    {
        $molajoConfig = new MolajoModelConfiguration ($option);
        $tasks = $molajoConfig->getOptionList(MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS);

        foreach ($tasks as $single) {
            $taskName = strtolower($single->value);
            $aclResults = $this->authoriseTask($option, $entity, $taskName, $catid, $id, $item);
            $itemFieldname = 'can' . ucfirst(strtolower($taskName));
            $item->$itemFieldname = $aclResults;
        }

        return;
    }

    /**
     *  Type 2 --> getQueryInformation
     *
     *  Called from Models and Field Filters to prepare query information
     *
     *  ACL Implementations methods needed for:
     *
     *  getUserQueryInformation - returns filtering access for the View Action for user
     *  getFilterQueryInformation - returns filtering access for the ACL Filter Object Access
     *  getXYZQueryInformation - anything that follows that pattern
     *
     * @param string $option 'com_articles', etc.
     * @param string $query query object
     * @param string $type 'user', 'filter', 'xyz'
     * @param string $filterValue
     *
     * @return     boolean
     * @since      1.0
     */
    public function getQueryInformation($option = '', $query = array(), $type = '', $params = array())
    {
        $method = 'get' . ucfirst(strtolower($type)) . 'QueryInformation';
        $aclClass = $this->getMethodClass($method, $option);
        if ($aclClass == false) {
            return false;
        }
        $acl = new $aclClass;
        $acl->$method ($query, $option, $params);
    }

    /**
     * TYPE 3 --> Retrieve lists for User
     *
     * Provides User and Filter View Action Query Select Information
     *
     * ACL Implementations methods needed for:
     *
     * getActionsList - produces a list of Actions parameter combinations
     * getAssetsList - produces a list of Assets parameter combinations
     * getViewaccessList - produces a View Access List parameter combinations
     * getCategoriesList - produces a list of Categories parameter combinations
     * getGroupsList - produces a list of Groups parameter combinations
     * getUsergroupsList - produces a list of Usergroups for parameter combinations
     * getUsergroupingsList - produces a list of Usergroupings for parameter combinations
     *
     * @param $type
     * @param string $option
     * @param string $task
     * @param array $params
     *
     * @return bool
     */
    public function getList($type, $id = '', $option = '', $task = '', $params = array())
    {
        $method = 'get' . ucfirst(strtolower($type)) . 'List';
        $aclClass = $this->getMethodClass($method, $option);
        if ($aclClass == false) {
            return false;
        }
        $acl = new $aclClass;
        return $acl->$method ($id, $option, $task, $params);
    }

    /**
     * TYPE 4 --> Checks specific authorisation for a user or group
     *
     * ACL Implementations methods needed for:
     *
     * checkUserPermissions - produces a true or false response for specific parameters
     * checkGroupPermissions - produces a true or false response for specific parameters
     *
     * @param $type
     * @param string $key
     * @param string $action
     * @param string $asset
     * @param string $access
     * @return bool
     */
    public function checkPermissions($type, $key = '', $action = '', $asset = '', $access = '')
    {
        $method = 'check' . ucfirst(strtolower($type)) . 'Permissions';
        $aclClass = $this->getMethodClass($method, '');
        if ($aclClass == false) {
            return false;
        }
        $acl = new $aclClass;
        return $acl->$method ($key, $action, $asset, $access);
    }

    /**
     *  Type 5 --> getFormAuthorisations
     *
     *  checkFormAuthorisations
     *
     *  Evaluates set of form fields for current users's authorisation to hide or disable fields from update, if needed
     *
     * @param string $option 'com_articles', etc.
     * @param string $entity 'article', or 'comment', etc.
     * @param string $task 'add', 'delete', 'publish'
     * @param integer $id - primary key for content
     * @param object $form - form object fields
     *
     * @return boolean
     */
    public function getFormAuthorisations($option, $entity, $task, $id, $form, $item)
    {
        $method = 'checkFormAuthorisations';
        $class = $this->getMethodClass($method, $option);
        if ($class == false) {
            return false;
        }

        $aclClass = new $class();
        if (method_exists($aclClass, $method)) {
            $aclClass->$method ($option, $entity, $task, $id, $form, $item);
        } else {
            MolajoError::raiseError(403, MolajoText::_('MOLAJO_ACL_CLASS_METHOD_FORM_AUTH_NOT_FOUND') . ' ' . $aclClass . '::' . $method);
            return false;
        }
    }

    /**
     * getMethodClass
     *
     * Finds first class::method available in component, ACL Option implemented, then default ACL
     *
     * @param string $method
     * @param string $option
     *
     * @return string $class
     */
    public function getMethodClass($method, $option = '')
    {
        /** Component Override */
        if ($option == '') {
        } else {
            if (substr($option, 0, 4) == 'com_') {
                $option = substr($option, 4, strlen($option) - 4);
            }
            $componentClass = 'MolajoACL' . ucfirst(strtolower($option));
            if (class_exists($componentClass)) {
                if (method_exists($componentClass, $method)) {
                    return $componentClass;
                }
            }

            /** Component Option */
            $session = MolajoFactory::getSession();
            if ($session->get('page.option') == $option) {
                $acl_implementation = $session->get('page.acl_implementation');
            } else {
                $molajoConfig = new MolajoModelConfiguration ($option);
                $acl_implementation = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION);
            }

            $implementedClass = 'MolajoACL' . ucfirst($acl_implementation);
            if (class_exists($implementedClass)) {
                if (method_exists($implementedClass, $method)) {
                    return $implementedClass;
                }
            }
        }

        $default_class = 'MolajoACLCore';
        if (class_exists($default_class)) {
            if (method_exists($default_class, $method)) {
                return $default_class;
            } else {
                MolajoError::raiseError(403, MolajoText::_('MOLAJO_ACL_CLASS_METHOD_NOT_FOUND') . ' ' . $default_class . '::' . $method);
                return false;
            }
        } else {
            MolajoError::raiseError(403, MolajoText::_('MOLAJO_ACL_DEFAULT_CLASS_NOT_FOUND') . ' ' . $default_class);
            return false;
        }
    }
}
