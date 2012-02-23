<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Load
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoLoadModel extends MolajoItemModel
{
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
        $this->_setLoadQuery();

        $this->_runLoadQuery();
        if (empty($this->query_results)) {
            return false;
        }

        $this->_getLoadAdditionalData();

        return $this->query_results;
    }

    /**
     * _setLoadQuery
     *
     * Retrieve all elements of the specific table for a specific item
     *
     * @return  object
     * @since   1.0
     */
    protected function _setLoadQuery()
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
     * _runLoadQuery
     *
     * Execute query and returns an associative array of data elements
     *
     * @return  array
     * @since   1.0
     */
    protected function _runLoadQuery()
    {
        $this->query_results = $this->db->loadAssoc();

        if (empty($this->query_results)) {
            return false;
        }

        if (key_exists('custom_fields', $this->query_results)
            && is_array($this->query_results['custom_fields'])
        ) {
            $registry = new Registry();
            $registry->loadString($this->query_results['custom_fields']);
            $this->query_results['custom_fields'] = (string)$registry;
        }

        if (key_exists('parameters', $this->query_results)
            && is_array($this->query_results['parameters'])
        ) {
            $registry = new Registry();
            $registry->loadString($this->query_results['parameters']);
            $this->query_results['parameters'] = (string)$registry;
        }

        if (key_exists('metadata', $this->query_results)
            && is_array($this->query_results['metadata'])
        ) {
            $registry = new Registry();
            $registry->loadString($this->query_results['metadata']);
            $this->query_results['metadata'] = (string)$registry;
        }

        if ($this->db->getErrorNum()) {
            $e = new MolajoException($this->db->getErrorMsg());
            $this->setError($e);
            return false;
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
    protected function _getLoadAdditionalData()
    {
        return $this->query_results;
    }
}

