<?php
/**
 * @package   Molajo
 * @copyright     Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

use Molajo\Application\Services;

defined('MOLAJO') or die;

/**
 * Load
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class LoadModel extends Model
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($id = null)
    {
        return parent::__construct($id);
    }

    /**
     * load
     *
     * Method to load a specific item from a specific model.
     * Creates and runs the database query, allows for additional data,
     * and returns integrated data as the item requested
     *
     * @return  object
     * @since   1.0
     */
    public function load()
    {
        $this->setLoadQuery();

        $this->runLoadQuery();
        if (empty($this->query_results)) {
            return false;
        }

        $this->getLoadAdditionalData();

        return $this->query_results;
    }

    /**
     * setLoadQuery
     *
     * Retrieve all elements of the specific table for a specific item
     *
     * @return  object
     * @since   1.0
     */
    protected function setLoadQuery()
    {
        $this->query = $this->db->getQuery(true);

        $this->query->select(' * ');
        $this->query->from($this->db->qn($this->table_name));
        $this->query->where($this->primary_key
            . ' = '
            . $this->db->q($this->id));

        $this->db->setQuery($this->query->__toString());
    }

    /**
     * runLoadQuery
     *
     * Execute query and returns an associative array of data elements
     *
     * @return  array
     * @since   1.0
     */
    protected function runLoadQuery()
    {
        $this->query_results = $this->db->loadAssoc();

        if (empty($this->query_results)) {

            $this->query_results = array();

            /** User Table Columns */
            $columns = $this->getFieldNames();

            for ($i = 0; $i < count($columns); $i++) {
                $this->query_results[$columns[$i]] = '';
            }
        }

        if (key_exists('custom_fields', $this->query_results)
            && is_array($this->query_results['custom_fields'])
        ) {
            $registry = Services::Registry()->initialise();
            $registry->loadString($this->query_results['custom_fields']);
            $this->query_results['custom_fields'] = (string)$registry;
        }

        if (key_exists('parameters', $this->query_results)
            && is_array($this->query_results['parameters'])
        ) {
            $registry = Services::Registry()->initialise();
            $registry->loadString($this->query_results['parameters']);
            $this->query_results['parameters'] = (string)$registry;
        }

        if (key_exists('metadata', $this->query_results)
            && is_array($this->query_results['metadata'])
        ) {
            $registry = Services::Registry()->initialise();
            $registry->loadString($this->query_results['metadata']);
            $this->query_results['metadata'] = (string)$registry;
        }

        return $this->query_results;
    }

    /**
     * _getAdditionalData
     *
     * Method to append additional data elements needed to the standard
     * array of elements provided by the data source
     *
     * @return array
     * @since  1.0
     */
    protected function getLoadAdditionalData()
    {
        return $this->query_results;
    }
}

