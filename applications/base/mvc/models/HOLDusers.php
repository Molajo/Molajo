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
class MolajoUsersModel extends MolajoDisplayModel
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
        $this->table_name = '#__users';
        $this->primary_key = 'id';

        return parent::__construct($id);
    }

    /**
     * _getAdditionalData
     *
     * Method to append additional data elements needed to the standard
     * array of elements provided by the data source
     *
     * @param array $data
     *
     * @return array
     * @since  1.0
     */
    protected function _getLoadAdditionalData($data)
    {
        /** concatenate name */
        $data['name'] = $data['first_name'] . ' ' . $data['last_name'];

        /** retrieve applications for which the user is authorized to login */
        $query = $this->db->getQuery(true);

        $query->select('a.' . $this->db->qn('id'));
        $query->select('a.' . $this->db->qn('name') . ' as title');
        $query->from($this->db->qn('#__applications') . ' as a');
        $query->from($this->db->qn('#__user_applications') . ' as b');
        $query->where('a.' . $this->db->qn('id') .
            ' = b.' . $this->db->qn('application_id'));
        $query->where('b.' . $this->db->qn('user_id') .
            ' = ' . (int)$this->id);

        $this->db->setQuery($query->__toString());

        $data['applications'] = $this->db->loadAssocList('title', 'id');

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** retrieve groups to which the user belongs */
        $query = $this->db->getQuery(true);

        $query->select('a.' . $this->db->qn('id'));
        $query->select('a.' . $this->db->qn('title') . ' as title');
        $query->from($this->db->qn('#__content') . ' as a');
        $query->from($this->db->qn('#__user_groups') . ' as b');
        $query->where('a.' . $this->db->qn('id') .
            ' = b.' . $this->db->qn('group_id'));
        $query->where('b.' . $this->db->qn('user_id') .
            ' = ' . (int)$this->id);

        $this->db->setQuery($query->__toString());

        $data['groups'] = $this->db->loadAssocList('title', 'id');

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** retrieve system groups to which the user belongs */
        $data['public'] = 1;
        $data['guest'] = 0;
        $data['registered'] = 1;
        if (in_array(5, $data['groups'])) {
            $data['administrator'] = 1;
        }

        /** retrieve view access groups to which the user belongs */
        $query = $this->db->getQuery(true);

        $query->select('a.' . $this->db->qn('id'));
        $query->from($this->db->qn('#__view_groups') . ' as a');
        $query->from($this->db->qn('#__user_view_groups') . ' as b');
        $query->where('a.' . $this->db->qn('id') .
            ' = b.' . $this->db->qn('view_group_id'));
        $query->where('b.' . $this->db->qn('user_id') .
            ' = ' . (int)$this->id);
echo $query->__toString();
        $this->db->setQuery($query->__toString());

       $results = $this->db->loadObject();

        foreach ($results as $result) {
            echo $result->id.'<br />';
        }
var_dump($results);
        die;
//$data['view_groups']
        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** return array of primary query and additional data elements */
        return $data;
    }
}
