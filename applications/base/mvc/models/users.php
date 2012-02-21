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
     * @param array $this->data
     *
     * @return array
     * @since  1.0
     */
    protected function _getLoadAdditionalData()
    {
        parent::_getLoadAdditionalData();

        /** concatenate name */
        $this->data['name'] = $this->data['first_name'] . ' ' . $this->data['last_name'];

        /** retrieve applications for which the user is authorized to login */
        $m = new MolajoUserApplicationsModel ();
        $m->query->select($this->db->qn('application_id'));
        $m->query->where($this->db->qn('user_id') . ' = ' . (int)$this->id);

        $applications = $m->loadObjectList();

        $x = array();
        foreach ($applications as $application) {
            $x[] = $application;
        }
        $this->data['applications'] = $x;

        /** retrieve groups to which the user belongs */
        $m = new MolajoUserGroupsModel ();
        $m->query->select($this->db->qn('group_id'));
        $m->query->where($this->db->qn('user_id') . ' = ' . (int)$this->id);
        $groups = $m->loadObjectList();

        $x = array();
        foreach ($groups as $group) {
            $x[] = $group->group_id;
        }
        $this->data['groups'] = $x;

        /** retrieve system groups to which the user belongs */
        $this->data['public'] = 1;
        $this->data['guest'] = 0;
        $this->data['registered'] = 1;
        if (in_array(5, $this->data['groups'])) {
            $this->data['administrator'] = 1;
        }

        /** retrieve view access groups to which the user belongs */
        $m = new MolajoUserViewGroupsModel ();
        $m->query->select($this->db->qn('view_group_id'));
        $m->query->where($this->db->qn('user_id') . ' = ' . (int)$this->id);
        $vgroups = $m->runQuery();

        $x = array();
        foreach ($vgroups as $vgroup) {
            $x[] = $vgroup->view_group_id;
        }
        $this->data['view_groups'] = $x;

        /** return array of primary query and additional data elements */
        return $this->data;
    }
}
