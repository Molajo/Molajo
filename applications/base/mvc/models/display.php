<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display
 *
 * Abstracted class used as the parent class for common display views
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoDisplayModel extends MolajoModel
{
    /**
     * $select
     *
     * Array of data elements for display
     *
     * @var    array
     * @since  1.0
     */
    protected $select;

    /**
     * $from
     *
     * Array of tables
     *
     * @var    array
     * @since  1.0
     */
    protected $from;

    /**
     * $where
     *
     * Array of query criteria
     *
     * @var    array
     * @since  1.0
     */
    protected $where;

    /**
     * $ordering
     *
     * Value to limit the number of results
     *
     * @var    array
     * @since  1.0
     */
    protected $ordering;

    /**
     * $limitResults
     *
     * Value to limit the number of results
     *
     * @var    array
     * @since  1.0
     */
    protected $limitResults;

    /**
     * _setCriteria
     *
     * Method to set the criteria needed for a query
     *
     * @return  object
     * @since   1.0
     */
    protected function _setCriteria()
    {
        /** Model Helper */
        $extensionName = ucfirst($this->get('extension_instance_name', ''));
        $extensionName = str_replace(array('-', '_'), '', $extensionName);

        $helperClass = 'Molajo' . $extensionName . 'ModelHelper';

        if (class_exists($helperClass)) {
            $h = new $helperClass();
        } else {
            $h = new MolajoModelHelper();
        }

        /** select fields can define joins in helper files */
        $this->select = array();
        $fields = $this->getFieldnames();
        if (count($fields) > 0) {
            foreach ($fields as $field) {
                $this->select[] = 'a.' . ',' . $field . ',' . $field;
            }
        }

        /** from tables; true == acl check */
        $this->from = array();
        $this->from[] = $this->table . ',' . 'a' . ',' . true;

        /** status - use published - create status groups */
        $this->where = array();

        /** use task request criteria */
        $taskRequestArray = $this->task_request->toArray();

        while (list($name, $value) = each($taskRequestArray)) {
            if (substr($name, 0, strlen('criteria')) == 'criteria') {
                // helperfunction - pass in fieldname and query object

            } else if ($name == 'id' && (int)$value > 0) {
                $this->where[] = 'a.id = ' . (int)$value;

            } else if ($name == 'source_asset_type_id' && (int)$value > 0) {
                $this->where[] = 'a.asset_type_id = ' . (int)$value;
            }
        }

        /** predefined criteria from menu items and other configurations */
        $xmlfile = MOLAJO_EXTENSIONS_COMPONENTS . '/articles/options/grid.xml';
        if (file_exists($xmlfile)) {
            $configuration = simplexml_load_file($xmlfile);
        } else {
            $configuration = array();
        }

        if (count($configuration) > 111111110) {

            foreach ($configuration->filters->children() as $child) {
                $field = (string)$child['name'];

                $key = 'where.' . $this->table
                    . '.' . $field
                    . '.' . Molajo::Request()->get('request_asset_id');

                //echo $key;
                $value = Services::User()->get($key, null, 'state');
                //echo ' '.$value. '<br />';
                $this->set($key, $value);
            }
        }

        /**
         *  Ordering
         */
        if ((int)$this->limitResults == 0) {
            $this->limitResults = 10;
        }

        /**
         *  Limit
         */
        if ((int)$this->limitResults == 0) {
            $this->limitResults = 10;
        }

        return;
        /**
        echo '<pre>';
        var_dump($this->select);
        echo '</pre>';
         */

    }

    /**
     * _setQuery
     *
     * Method to create a query object in preparation of running a query
     *
     * @return  object
     * @since   1.0
     */
    protected function _setQuery()
    {
        $this->query = $this->db->getQuery(true);

        /** select */
        if (count($this->select) > 0) {
            foreach ($this->select as $select) {
                $x = explode(',', $select);
                $this->query->select($x[0] . $this->db->nq($x[1]) . ' as ' . $x[2]);
            }
        }

        /** from */
        if (count($this->from) > 0) {
            foreach ($this->from as $from) {
                $x = explode(',', $from);

                $this->query->from($this->db->nq($x[0]) . ' as ' . $x[1]);

                if ($x[2] == true) {
                    MolajoAccessService::setQueryViewAccess(
                        $this->query,
                        array('join_to_prefix' => $x[1],
                            'join_to_primary_key' => 'id',
                            'asset_prefix' => $x[1] . '_assets',
                            'select' => true
                        )
                    );
                }
            }
        }

        /** where */
        if (count($this->where) > 0) {
            foreach ($this->where as $where) {
                $this->query->where($where);
            }
        }

        /** order by */

        /** set the query */
        echo $this->query->__toString();
        echo '<br />';
        $this->db->setQuery($this->query->__toString());

        return;
    }

    protected function _hold()
    {
        /** Status and Dates */
        $this->query->where('a.' . $this->db->nq('status') .
            ' = ' . MOLAJO_STATUS_PUBLISHED);

        $this->query->where('(a.start_publishing_datetime = ' .
                $this->db->q($this->nullDate) .
                ' OR a.start_publishing_datetime <= ' .
                $this->db->q($this->now) . ')'
        );
        $this->query->where('(a.stop_publishing_datetime = ' .
                $this->db->q($this->nullDate) .
                ' OR a.stop_publishing_datetime >= ' .
                $this->db->q($this->now) . ')'
        );


        /** ordering */
        $this->query->order('a.start_publishing_datetime DESC');

    }

    /**
     * _runQuery
     *
     * Method to execute a prepared and set query statement,
     * returning the results
     *
     * @return  object
     * @since   1.0
     */
    protected function _runQuery()
    {
        $data = $this->db->loadObjectList();
//var_dump($data);
        if ($this->db->getErrorNum() == 0) {

        } else {
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_DATABASE_QUERY') . ' ' .
                    $this->db->getErrorNum() . ' ' .
                    $this->db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = $this->name . ':' . 'getData',
                $debug_object = $this->db
            );
            return $this->request->set('status_found', false);
        }

        if (count($data) == 0) {
            return array();
        }

        return $data;
    }
}
