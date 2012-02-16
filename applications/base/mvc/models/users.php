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
class MolajoUsersModel extends MolajoCrudModel
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
     * _getAdditionalData
     *
     * Method to append additional data elements needed to the standard
     * array of elements provided by the data source
     *
     * @param array $item
     *
     * @return array
     * @since  1.0
     */
    protected function _getAdditionalData($item)
    {
        /** concatenate name */
        $item['name'] = $item['first_name'] . ' ' . $item['last_name'];

        /** retrieve applications for which the user is authorized to login */
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

        $item['applications'] = $this->db->loadAssocList('title', 'id');

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** retrieve groups to which the user belongs */
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

        $item['groups'] = $this->db->loadAssocList('title', 'id');

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** retrieve system groups to which the user belongs */
        $item['public'] = 1;
        $item['guest'] = 0;
        $item['registered'] = 1;
        if (in_array(5, $item['groups'])) {
            $item['administrator'] = 1;
        }

        /** retrieve view access groups to which the user belongs */
        $query = $this->db->getQuery(true);

        $query->select('a.' . $this->db->nameQuote('id'));
        $query->from($this->db->nameQuote('#__view_groups') . ' as a');
        $query->from($this->db->nameQuote('#__user_view_groups') . ' as b');
        $query->where('a.' . $this->db->nameQuote('id') .
            ' = b.' . $this->db->nameQuote('view_group_id'));
        $query->where('b.' . $this->db->nameQuote('user_id') .
            ' = ' . (int)$this->id);

        $this->db->setQuery($query->__toString());

        $item['view_groups'] = $this->db->loadResultArray();

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** return array of primary query and additional data elements */
        return $item;
    }
}
