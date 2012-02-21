<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoCrudModel
 *
 * Base CRUD Molajo Model
 *
 * @package       Molajo
 * @subpackage    Model
 * @since 1.0
 */
class MolajoCrudModel extends MolajoModel
{
    /**
     * _setLoadQuery
     *
     * Standard query to retrieve all elements of the specific table for a specific item
     *
     * @return  object
     * @since   1.0
     */
    protected function _setLoadQuery()
    {
        $this->query = $this->db->getQuery(true);

        $this->query->select(' * ');
        $this->query->from($this->db->qn($this->table));
        $this->query->where($this->primary_key
            . ' = '
            . $this->db->q($this->id));

        $this->db->setQuery($this->query->__toString());
    }

    /**
     * _runLoadQuery
     *
     * Execute query and return an array of data elements
     *
     * @return  object
     * @since   1.0
     */
    protected function _runLoadQuery()
    {
        $this->data = $this->db->loadAssoc();

        if (empty($this->data)) {
            return false;
        }

        if ($this->db->getErrorNum()) {
            $e = new MolajoException($this->db->getErrorMsg());
            $this->setError($e);
            return false;
        }

        return $this->data;
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
        if (key_exists('custom_fields', $this->data)
            && is_array($this->data['custom_fields'])
        ) {
            $registry = new Registry();
            $registry->loadArray($this->data['custom_fields']);
            $this->data['custom_fields'] = (string)$registry;
        }

        if (key_exists('parameters', $this->data)
            && is_array($this->data['parameters'])
        ) {
            $registry = new Registry();
            $registry->loadArray($this->data['parameters']);
            $this->data['parameters'] = (string)$registry;
        }

        if (key_exists('metadata', $this->data)
            && is_array($this->data['metadata'])
        ) {
            $registry = new Registry();
            $registry->loadArray($this->data['metadata']);
            $this->data['metadata'] = (string)$registry;
        }

        return $this->data;
    }
}

