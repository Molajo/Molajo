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
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($id = null)
    {
        // class name, table, and primary key set by child classes
        return parent::__construct($id);
    }

    /**
     * getItem
     *
     * Method to retrieve one row of a specific data type and to allow for
     *  appending in additional data elements, if needed
     *
     * @return  object
     * @since   1.0
     */
    public function getItem()
    {
        $this->set('crud', 'r');

        $this->_setQuery();

        $item = $this->_query();

        $this->item = $this->_getAdditionalData($item);

        return $this->item;
    }

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
        $this->query->select(' * ');
        $this->query->from($this->db->quoteName($this->table));
        $this->query->where($this->primary_key
            . ' = '
            . $this->db->quote($this->id));

        $this->db->setQuery($this->query->__toString());
    }

    /**
     * _query
     *
     * Execute query and return an array of data elements
     *
     * @return  object
     * @since   1.0
     */
    protected function _query()
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
        foreach ($item as $i) {
        }

        return $i;
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
        return $item;
    }
}

