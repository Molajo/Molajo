<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\MVC\Model;

use Molajo\Application;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * ReadModel
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class ReadModel extends Model
{
    /**
     * Constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * setBaseQuery
     *
     * Retrieve all elements of the specific table for a specific item
     *
     * @param  $columns
     * @param  $table_name
     * @param  $primary_prefix
     * @param  $primary_key
     * @param  $id
     * @param  $name_key
     * @param  $name_key_value
     *
     * @return ReadModel
     * @since  1.0
     */
    public function setBaseQuery($columns, $table_name, $primary_prefix, $primary_key, $id, $name_key, $name_key_value)
    {
        if ($this->query->select == null) {
            foreach ($columns as $column) {
                $this->query->select($this->db->qn($primary_prefix . '.' . $column['name']));
            }
        }

        if ($this->query->from == null) {
            $this->query->from($this->db->qn($table_name) . ' as ' . $this->db->qn($primary_prefix));
        }

        if ($this->query->where == null) {
            if ((int) $id > 0) {
                $this->query->where($this->db->qn($primary_prefix . '.' . $primary_key)
                    . ' = ' . $this->db->q($id));

            } elseif (trim($name_key_value) == '') {

            } else {
                $this->query->where($this->db->qn($primary_prefix . '.' . $name_key)
                    . ' = ' . $this->db->q($name_key_value));
            }
        }

        return $this;
    }

    /**
     * addACLCheck
     *
     * Add ACL checking to the Query
     *
     * @return object
     * @since   1.0
     */
    public function addACLCheck($primary_prefix, $primary_key)
    {
        Services::Authorisation()->setQueryViewAccess(
            $this->query,
            $this->db,
            array('join_to_prefix' => $primary_prefix,
                'join_to_primary_key' => $primary_key,
                'catalog_prefix' => 'acl_check_catalog',
                'select' => true
            )
        );

        return $this;
    }

    /**
     * useSpecialJoins
     *
     * Use joins defined in table xml to extend model
     *
     * @return array
     * @since   1.0
     */
    public function useSpecialJoins($joins, $primary_prefix)
    {
        foreach ($joins as $join) {

            $join_table = $join['table'];
            $alias = $join['alias'];
            $select = $join['select'];
            $joinTo = $join['jointo'];
            $joinWith = $join['joinwith'];

//echo  'JOIN: ' . $join_table . ' ' . $alias . ' ' . $select . ' ' . $joinTo . ' ' . $joinWith . '<br />';

            /* Join to table */
            if (trim($alias) == '') {
                $alias = substr($join_table, 3, strlen($join_table));
            }

            $this->query->from($this->db->qn($join_table) . ' as ' . $this->db->qn($alias));

            /* Select fields */
            if (trim($select) == '') {
                $selectArray = array();
            } else {
                $selectArray = explode(',', $select);
            }

            if (count($selectArray) > 0) {

                foreach ($selectArray as $selectItem) {

                    $this->query->select(
                        $this->db->qn(trim($alias) . '.' . trim($selectItem))
                            . ' as ' .
                            $this->db->qn(trim($alias) . '_' . trim($selectItem))
                    );
                }
            }

            /* joinTo and joinWith Fields */
            $joinToArray = explode(',', $joinTo);
            $joinWithArray = explode(',', $joinWith);

            if (count($joinToArray) > 0) {

                $i = 0;
                foreach ($joinToArray as $joinToItem) {

                    /** join THIS to that */
                    $to = $joinToItem;

                    if ($to == 'APPLICATION_ID') {
                        $whereLeft = APPLICATION_ID;

                    } elseif ($to == 'SITE_ID') {
                        $whereLeft = SITE_ID;

                    } elseif (is_numeric($to)) {
                        $whereLeft = (int) $to;

                    } else {

                        $hasAlias = explode('.', $to);

                        if (count($hasAlias) > 1) {
                            $toJoin = trim($hasAlias[0]) . '.' . trim($hasAlias[1]);
                        } else {
                            $toJoin = trim($alias) . '.' . trim($to);
                        }

                        $whereLeft = $this->db->qn($toJoin);
                    }

                    /** join this to THAT */
                    $with = $joinWithArray[$i];

                    if ($with == 'APPLICATION_ID') {
                        $whereRight = APPLICATION_ID;

                    } elseif ($with == 'SITE_ID') {
                        $whereRight = SITE_ID;

                    } elseif (is_numeric($with)) {
                        $whereRight = (int) $with;

                    } else {

                        $hasAlias = explode('.', $with);

                        if (count($hasAlias) > 1) {
                            $withJoin = trim($hasAlias[0]) . '.' . trim($hasAlias[1]);
                        } else {
                            $withJoin = trim($primary_prefix) . '.' . trim($with);
                        }

                        $whereRight = $this->db->qn($withJoin);
                    }

                    /** put the where together */
                    $this->query->where($whereLeft . ' = ' . $whereRight);

                    $i++;
                }
            }
        }

        return $this;
    }

    /**
     * getQueryResults
     *
     * Execute query and returns an associative array of data elements
     *
     * @return array
     * @since   1.0
     */
    public function getQueryResults($columns)
    {
        $this->db->setQuery($this->query->__toString());

        $this->query_results = $this->db->loadObjectList();

        if (empty($this->query_results)) {

            $this->query_results = array();

            foreach ($columns as $column) {
                $this->query_results[$column['name']] = '';
            }
        }

        return $this;
    }

    /**
     * addCustomFields
     *
     * Populate the custom fields defined by the Table xml with query results
     *
     * @return object
     * @since   1.0
     */
    public function addCustomFields($model_name, $customFieldName, $fields, $retrieval_method, $query_results)
    {
        /** Prepare Registry Name */
        $customFieldName = strtolower($customFieldName);
        $useRegistryName = $model_name . ucfirst($customFieldName);

        /** See if there are query results for this Custom Field Group */
        if (is_object($query_results) && isset($query_results->$customFieldName)) {

            $jsonData = $query_results->$customFieldName;

            $data = json_decode($jsonData);

            /** test for application-specific values */
            if (count($data) > 0 && (defined('APPLICATION_ID')) ) {

                foreach ($data as $key => $value) {

                    if ($key == APPLICATION_ID) {
                        $data = $value;
                        break;
                    }
                }
            }

            /** Inject data for custom field group into named pairs array */
            $lookup = array();

            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    $lookup[$key] = $value;
                }
            }

            if (is_object($query_results) && isset($query_results->$customFieldName)) {
                unset($query_results->$customFieldName);
            }

        /** No data in query results for this specific custom field */

        } else {

            $data = array();
            $lookup = array();
        }

        /** Process each of the Custom Field Group Definitions for Query Results or defaults */
        foreach ($fields as $f) {

            $name = $f['name'];
            $name = strtolower($name);

            $default = null;
            if (isset($f['default'])) {
                $default = $f['default'];
            }

            if ($default == '') {
                $default = null;
            }

            /** Use value, if exists, or defined default */
            if (isset($lookup[$name])) {
                $setValue = $lookup[$name];
            } else {
                $setValue = $default;
            }

            /** Filter Input and Save the Registry */
            //$set = $this->filterInput($name, $set, $dataType, $null, $default);

            /** Option 2: Make each custom field a "regular" field in query results */
            if ($retrieval_method == 2 && strtolower($customFieldName) == 'customfields') {
                $query_results->$name = $setValue;
            } else {
                /** Option 1: all custom field pairs are saved in Registry */
                Services::Registry()->set($useRegistryName, $name, $setValue);
            }
        }

        return $query_results;
    }

    /**
     * addItemChildren
     *
     * Method to append additional data elements needed to the standard
     * array of elements provided by the data source
     *
     * @return array
     * @since  1.0
     */
    public function addItemChildren($children, $id, $query_results)
    {
        foreach ($children as $child) {

            $name = (string) $child['name'];
            $name = ucfirst(strtolower($name));

            $controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
            $m = new $controllerClass();
            $m->connect($name);

            $join = (string) $child['join'];
            $m->model->query->where($m->model->db->qn($join) . ' = ' . (int) $id);

            $results = $m->getData('list');

            $query_results->$name = $results;
        }

        /** return array containing primary query and additional data elements */

        return $query_results;
    }
}
