<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Users
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 * @link
 */
class MolajoUsersModel extends MolajoModel
{
    /**
     * __construct
     *
     * @param   string  $id
     *
     * @return  object
     * @since   1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);
        $this->table = '#__users';
        $this->primary_key = 'id';

        return parent::__construct($id);
    }

    /**
     * read
     *
     * Method to read user data
     *
     * @return  bool
     * @since   1.0
     */
    public function read()
    {
        $row = $this->_query();

        if (count($row) > 0) {
            foreach ($row as $item) {
            }
            return $this->bind($item, $ignore = array());
        } else {
            //do an empty row
        }
    }

    /**
     * _query
     *
     * Method to query the database for the data requested
     *
     * @param null $id
     * @param bool $reset
     *
     * @return bool
     * @since  1.0
     */
    protected function _query()
    {
        $row = parent::_query();

        /**
         * append additional data elements needed for user to the
         *   $tableQueryResults object beyond the standard results
         *   provided by the parent query
         */

        /** name */
        $row[0]['name'] = $row[0]['first_name'] . ' ' . $row[0]['last_name'];

        /** applications */
        $query = $this->db->getQuery(true);

        $query->select('a.' . $this->db->nameQuote('id'));
        $query->select('a.' . $this->db->nameQuote('name') . ' as title');
        $query->from($this->db->nameQuote('#__applications') . ' as a');
        $query->from($this->db->nameQuote('#__user_applications') . ' as b');
        $query->where('a.' . $this->db->nameQuote('id') .
            ' = b.' . $this->db->nameQuote('application_id'));
        $query->where('b.' . $this->db->nameQuote('user_id') .
            ' = ' . (int)$this->id);

        $this->db->setQuery($query->__toString());

        $row[0]['applications'] = $this->db->loadAssocList('title', 'id');

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** groups */
        $query = $this->db->getQuery(true);

        $query->select('a.' . $this->db->nameQuote('id'));
        $query->select('a.' . $this->db->nameQuote('title') . ' as title');
        $query->from($this->db->nameQuote('#__content') . ' as a');
        $query->from($this->db->nameQuote('#__user_groups') . ' as b');
        $query->where('a.' . $this->db->nameQuote('id') .
            ' = b.' . $this->db->nameQuote('group_id'));
        $query->where('b.' . $this->db->nameQuote('user_id') .
            ' = ' . (int)$this->id);

        $this->db->setQuery($query->__toString());

        $row[0]['groups'] = $this->db->loadAssocList('title', 'id');

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** roles */
        $row[0]['public'] = 1;
        $row[0]['guest'] = 0;
        $row[0]['registered'] = 1;
        if (in_array(5, $row[0]['groups'])) {
            $row[0]['administrator'] = 1;
        }

        /** view groups */
        $query = $this->db->getQuery(true);

        $query->select('a.' . $this->db->nameQuote('id'));
        $query->from($this->db->nameQuote('#__view_groups') . ' as a');
        $query->from($this->db->nameQuote('#__user_view_groups') . ' as b');
        $query->where('a.' . $this->db->nameQuote('id') . ' = b.' . $this->db->nameQuote('view_group_id'));
        $query->where('b.' . $this->db->nameQuote('user_id') . ' = ' . (int)$this->id);

        $this->db->setQuery($query->__toString());

        $row[0]['view_groups'] = $this->db->loadResultArray();

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        return $row;
    }
}
