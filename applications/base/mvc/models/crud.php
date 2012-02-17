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
     * _setQuery
     *
     * Standard query to retrieve all elements of the specific table for a specific item
     *
     * @return  object
     * @since   1.0
     */
    protected function _setQuery()
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
     * _runQuery
     *
     * Execute query and return an array of data elements
     *
     * @return  object
     * @since   1.0
     */
    protected function _runQuery()
    {
        $item = $this->db->loadAssocList();

        if ($this->db->getErrorNum()) {
            $e = new MolajoException($this->db->getErrorMsg());
            $this->setError($e);
            return false;
        }

        if (empty($item)) {
            $e = new MolajoException(Services::Language()->_('MOLAJO_DB_ERROR_EMPTY_ROW_RETURNED'));
            $this->setError($e);
            return false;
        }

        if (key_exists('custom_fields', $item)
            && is_array($item['custom_fields'])
        ) {
            $registry = new Registry();
            $registry->loadArray($item['custom_fields']);
            $item['custom_fields'] = (string)$registry;
        }

        if (key_exists('parameters', $item)
            && is_array($item['parameters'])
        ) {
            $registry = new Registry();
            $registry->loadArray($item['parameters']);
            $item['parameters'] = (string)$registry;
        }

        if (key_exists('metadata', $item)
            && is_array($item['metadata'])
        ) {
            $registry = new Registry();
            $registry->loadArray($item['metadata']);
            $item['metadata'] = (string)$registry;
        }

        /** return as an array of data elements */
        foreach ($item as $data) {
        }

        return $data;
    }

}

