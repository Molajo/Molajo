<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

defined('MOLAJO') or die;

/**
 * Users
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class UsersModel extends DisplayModel
{
    /**
     * __construct
     *
     * @param   string  $id
     *
     * @return  object
     * @since   1.0
     */
    public function __construct($table = null, $id = null, $path = null)
    {
        $this->name = get_class($this);
        $this->table_name = '#__users';
        $this->primary_key = 'id';

        return parent::__construct($table, $id, $path);
    }

    /**
     * getAdditionalData
     *
     * Method to append additional data elements needed to the standard
     * array of elements provided by the data source
     *
     * @return array
     * @since  1.0
     */
    protected function getLoadAdditionalData()
    {
        parent::getLoadAdditionalData();

        if ((int)$this->id == 0) {
            $this->query_results['name'] = '';
            $this->query_results['applications'] = array();
            $this->query_results['groups'] = array(MOLAJO_SYSTEM_GROUP_PUBLIC, MOLAJO_SYSTEM_GROUP_GUEST);
            $this->query_results['view_groups'] = array(MOLAJO_SYSTEM_GROUP_PUBLIC, MOLAJO_SYSTEM_GROUP_GUEST);
            $this->query_results['public'] = 1;
            $this->query_results['guest'] = 1;
            $this->query_results['registered'] = 0;
            $this->query_results['administrator'] = 0;

        } else {
            /** concatenate name */
            $this->query_results['name'] = $this->query_results['first_name'] . ' ' . $this->query_results['last_name'];

            /** retrieve applications for which the user is authorized to login */
            $m = new UserApplicationsModel ();

            $m->query->select($this->db->qn('application_id'));
            $m->query->where($this->db->qn('user_id') . ' = ' . (int)$this->id);

            $applications = $m->loadObjectList();

            $x = array();
            foreach ($applications as $application) {
                $x[] = $application;
            }
            $this->query_results['applications'] = $x;

            /** retrieve groups to which the user belongs */
            $m = new UserGroupsModel ();

            $m->query->select($this->db->qn('group_id'));
            $m->query->where($this->db->qn('user_id') . ' = ' . (int)$this->id);

            $groups = $m->loadObjectList();

            $x = array();
            foreach ($groups as $group) {
                $x[] = $group->group_id;
            }
            $this->query_results['groups'] = $x;

            /** retrieve system groups to which the user belongs */
            $this->query_results['public'] = 1;
            $this->query_results['guest'] = 0;
            $this->query_results['registered'] = 1;

            if (in_array(MOLAJO_SYSTEM_GROUP_ADMINISTRATOR, $this->query_results['groups'])) {
                $this->query_results['administrator'] = 1;
            }

            /** retrieve view access groups to which the user belongs */
            $m = new UserViewGroupsModel ();

            $m->query->select($this->db->qn('view_group_id'));
            $m->query->where($this->db->qn('user_id') . ' = ' . (int)$this->id);

            $vg = $m->runQuery();

            $x = array();
            foreach ($vg as $g) {
                $x[] = $g->view_group_id;
            }
            $this->query_results['view_groups'] = $x;
        }

        /** return array of primary query and additional data elements */
        return $this->query_results;
    }
}
