<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Group Table Class
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 * @link
 */
class MolajoTableGroup extends MolajoTable
{
    /**
     * Constructor
     *
     * @param   object  Database object
     *
     * @return  MolajoTableGroup
     *
     * @since   1.0
     */
    public function __construct(&$db)
    {
        parent::__construct('#__content', 'id', $db);
    }

    /**
     * Method to check the current record to save
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function check()
    {
        // Validate the title.
        if ((trim($this->title)) == '') {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_USERGROUP_TITLE'));
            return false;
        }

        // Check for a duplicate parent_id, title.
        // There is a unique index on the (parend_id, title) field in the table.
        $db = $this->getDbo();
        $query = $db->getQuery(true)
                ->select('COUNT(title)')
                ->from($this->_tbl)
                ->where('title = ' . $db->quote(trim($this->title)))
                ->where('parent_id = ' . (int)$this->parent_id)
                ->where('id <> ' . (int)$this->id);
        $db->setQuery($query);

        if ($db->loadResult() > 0) {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_USERGROUP_TITLE_EXISTS'));
            return false;
        }

        return true;
    }

    /**
     * Method to recursively rebuild the nested set tree.
     *
     * @param   integer  The root of the tree to rebuild.
     * @param   integer  The left id to start with in building the tree.
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function rebuild($parent_id = 0, $left = 0)
    {
        // get the database object
        $db = &$this->_database;

        // get all children of this node
        $db->setQuery(
            'SELECT id FROM ' . $this->_tbl .
            ' WHERE parent_id=' . (int)$parent_id .
            ' ORDER BY parent_id, title'
        );
        $children = $db->loadColumn();

        // the right value of this node is the left value + 1
        $right = $left + 1;

        // execute this function recursively over all children
        for ($i = 0, $n = count($children); $i < $n; $i++)
        {
            // $right is the current right value, which is incremented on recursion return
            $right = $this->rebuild($children[$i], $right);

            // if there is an update failure, return false to break out of the recursion
            if ($right === false) {
                return false;
            }
        }

        // we've got the left value, and now that we've processed
        // the children of this node we also know the right value
        $db->setQuery(
            'UPDATE ' . $this->_tbl .
            ' SET lft=' . (int)$left . ', rgt=' . (int)$right .
            ' WHERE id=' . (int)$parent_id
        );
        // if there is an update failure, return false to break out of the recursion
        if (!$db->query()) {
            return false;
        }

        // return the right value of this node + 1
        return $right + 1;
    }

    /**
     * Inserts a new row if id is zero or updates an existing row in the database table
     *
     * @param   bool  $updateNulls    If false, null object variables are not updated
     *
     * @return  bool  True successful, false otherwise and an internal error message is set
     *
     * @since   1.0
     */
    function store($updateNulls = false)
    {
        if ($result = parent::store($updateNulls)) {
            // Rebuild the nested set tree.
            $this->rebuild();
        }

        return $result;
    }

    /**
     * Delete this object and it's dependancies
     *
     * @param   integer  $id    The primary key of the user group to delete.
     *
     * @return  mixed  Boolean or Exception.
     *
     * @since   1.0
     */
    function delete($id = null)
    {
        // Cannot delete Groups
        //  1: Public
        //  2: Guest
        //  3: Registered
        //  4: Administrator

        if ((int)$id == 0) {
            return new MolajoException(MolajoTextHelper::_('MOLAJO_GROUP_NUMBER_NOT_PROVIDED_FOR_DELETE'));
        }

        if ((int)$id > 0 AND (int)$id < 5) {
            return new MolajoException(MolajoTextHelper::_('MOLAJO_SYSTEM_GROUP_CANNOT_BE_DELETED'));
        }

        if ($id) {
            $this->load($id);
        }

        /**
         * Retrieve Group and Children for the Group
         */
        $db = $this->getDbo();
        $db->setQuery(
            'SELECT c.id' .
            ' FROM ' . $db->quoteName($this->_tbl) . ' AS c' .
            ' WHERE c.lft >= ' . (int)$this->lft . ' AND c.rgt <= ' . $this->rgt .
            '   AND c.id > 4 '
        );

        $groupIds = $db->loadColumn();
        if (empty($groupIds)) {
            return new MolajoException(MolajoTextHelper::_('MOLAJO_GROUP_DOES_NOT_EXIST'));
        }

        /**
         * Retrieve Groupings Impacted for Later Rebuild
         */
        $db->setQuery(
            'SELECT DISTINCT view_group_id ' .
            ' FROM #__group_view_groups ' .
            ' WHERE group_id IN (' . implode(',', $groupIds) . ')'
        );

        $groupingIds = $db->loadColumn();
        if (empty($groupingIds)) {
            $groupingIds = array();
        }

        /**
         * Delete all Group Related Content
         * 1. #__content
         * 2. #__user_groups
         * 3. #__permissions_groups
         * 4. #__group_view_groups
         */
        // Delete Group to Groupings
        $db->setQuery(
            'DELETE FROM #__group_view_groups ' .
            ' WHERE group_id IN (' . implode(',', $groupIds) . ')'
        );
        if ($db->query()) {
        } else {
            $this->setError($db->getErrorMsg());
            return false;
        }

        // Retrieve Groupings that still remain
        $db->setQuery(
            'SELECT DISTINCT view_group_id ' .
            ' FROM #__group_view_groups ' .
            ' WHERE view_group_id IN (' . implode(',', $groupingIds) . ')'
        );

        $groupingIdsRemaining = $db->loadColumn();
        if (empty($groupingIdsRemaining)) {
            $groupingIdsRemaining = array();
        }

        $delete = array();
        foreach ($groupingIds as $singleID) {
            if (in_array($singleID, $groupingIdsRemaining)) {
            } else {
                $delete[] = $singleID;
            }
        }

        // Delete orphans
        $db->setQuery(
            'DELETE FROM `#__`' .
            ' WHERE `group_id` IN (' . implode(',', $groupIds) . ')'
        );
        $db->query();


        // Delete from group to groupings table
        $query = $db->getQuery(true);


        // Delete from groupings table

        //

        // Check for a database error.
        if ($db->getErrorNum()) {
            $this->setError($db->getErrorMsg());
            return false;
        }

        // Delete Group and Children
        $db->setQuery(
            'DELETE FROM ' . $db->quoteName($this->_tbl) .
            ' WHERE id IN (' . implode(',', $groupIds) . ')'
        );
        if ($db->query()) {
        } else {
            $this->setError($db->getErrorMsg());
            return false;
        }

        return true;
    }
}
