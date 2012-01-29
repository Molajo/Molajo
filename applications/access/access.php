<?php
/**
 * @package     Molajo
 * @subpackage  Access
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoAccess
{
    /**
     *  authoriseTask
     *
     * @param  string  $task
     * @param  string  $model
     * @param  string  $asset_id
     *
     * @return  boolean
     * @since   1.0
     */
    static public function authoriseTask($task='login', $asset_id=0)
    {
        return true;
    }

    /**
     *  authoriseTaskList
     *
     * @param  string  $task
     * @param  string  $asset_id
     * @param  string  $row
     *
     * @return  boolean
     * @since   1.0
     */
    static public function authoriseTaskList ($tasklist=array(), $asset_id=0, $row=array())
    {
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
     * @param string $option 'articles', etc.
     * @param string $query query object
     * @param string $type 'user', 'filter', 'xyz'
     * @param string $filterValue
     *
     * @return     boolean
     * @since      1.0
     */
    static public function getQueryInformation($option = '', $query = array(), $type = '', $parameters = array())
    {

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
     * @param array $parameters
     *
     * @return bool
     */
    public function getList($type, $id = '', $option = '', $task = '', $parameters = array())
    {

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

    }

    /**
     *  Type 5 --> getFormAuthorisations
     *
     *  checkFormAuthorisations
     *
     *  Evaluates set of form fields for current users's authorisation to hide or disable fields from update, if needed
     *
     * @param string $option 'articles', etc.
     * @param string $entity 'article', or 'comment', etc.
     * @param string $task 'add', 'delete', 'publish'
     * @param integer $id - primary key for content
     * @param object $form - form object fields
     *
     * @return boolean
     */
    public function getFormAuthorisations($option, $entity, $task, $id, $form, $item)
    {

    }
}
