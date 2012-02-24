<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Item
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoItemModel extends MolajoModel
{
    /**
     * store
     *
     * Method to store a row (insert: no PK; update: PK) in the database.
     *
     * @param   boolean True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function store()
    {

        if ((int) $this->id == 0) {
            $stored = $this->db->insertObject(
                    $this->table_name, $this->row, $this->primary_key);
        } else {
            $stored = $this->db->updateObject(
                    $this->table_name, $this->row, $this->primary_key);
        }

        if ($stored) {

        } else {
            $e = new MolajoException(
                Services::Language()->sprintf(
                    'MOLAJO_DB_ERROR_STORE_FAILED', $this->name, $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }
        /**
        if ($this->_locked) {
        $this->_unlock();
        }
         */

        return true;
    }

    /**
     * publish
     *
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param   integer The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer The user id of the user performing the operation.
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->primary_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int)$userId;
        $state = (int)$state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $e = new MolajoException(Services::Language()->_('MOLAJO_DB_ERROR_NO_ROWS_SELECTED'));
                $this->setError($e);

                return false;
            }
        }

        // Update the publishing state for rows with the given primary keys.
        $this->query = $this->db->getQuery(true);
        $this->query->update($this->table_name);
        $this->query->set('published = ' . (int)$state);

        // Determine if there is checkin support for the table.
        if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time')) {
            $this->query->where('(checked_out = 0 OR checked_out = ' . (int)$userId . ')');
            $checkin = true;

        } else {
            $checkin = false;
        }

        // Build the WHERE clause for the primary keys.
        $this->query->where($k . ' = ' . implode(' OR ' . $k . ' = ', $pks));

        $this->db->setQuery($this->query->__toString());

        // Check for a database error.
        if ($this->db->query()) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_PUBLISH_FAILED', $this->name, $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->db->getAffectedRows())) {
            // Checkin the rows.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the MolajoModel instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->published = $state;
        }

        $this->setError('');
        return true;
    }
    /**
     * checkOut
     *
     * @since   1.0
     */
    public function checkOut()
    {
        $userId = Services::User()->get('id');
        if (property_exists($this, 'checked_out')
            && property_exists($this, 'checked_out_time')
        ) {
        } else {
            return true;
        }

        $this->query->update($this->table_name);
        $this->query->set($this->db->qn('checked_out') . ' = ' . (int)$userId);
        $this->query->set($this->db->qn('checked_out_time') . ' = ' . $this->db->q($this->now));
        $this->query->where($this->primary_key . ' = ' . $this->db->q($this->id));

        $this->db->setQuery($this->query->__toString());

        if ($this->db->query()) {
        } else {
            $e = new MolajoException(
                Services::Language()->sprintf(
                    'MOLAJO_DB_ERROR_CHECKOUT_FAILED',
                    $this->name,
                    $this->db->getErrorMsg()
                )
            );
            $this->setError($e);
            return false;
        }

        return true;
    }

    /**
     * checkIn
     *
     * Method to check a row in if the necessary properties/fields exist.  Checking
     * a row in will allow other users the ability to edit the row.
     *
     * @param   mixed    An optional primary key value to check out.  If not set
     *                    the instance property value is used.
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function checkIn()
    {
        // If there is no checked_out or checked_out_time field, just return true.
        if (property_exists($this, 'checked_out')
            && property_exists($this, 'checked_out_time')
        ) {
        } else {
            return true;
        }

        // Initialise variables.
        $k = $this->primary_key;
        $this->id = (is_null($this->id)) ? $this->$k : $this->id;

        // If no primary key is given, return false.
        if ($this->id === null) {
            $e = new MolajoException(Services::Language()->_('MOLAJO_DB_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // Check the row in by primary key.
        $this->query = $this->db->getQuery(true);
        $this->query->update($this->table_name);
        $this->query->set($this->db->qn('checked_out') . ' = 0');
        $this->query->set($this->db->qn('checked_out_time') . ' = ' . $this->db->q($this->db->getNullDate()));
        $this->query->where($this->primary_key . ' = ' . $this->db->q($this->id));
        $this->db->setQuery($this->query->__toString());

        // Check for a database error.
        if ($this->db->query()) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CHECKIN_FAILED', $this->name, $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Set table values in the object.
        $this->checked_out = 0;
        $this->checked_out_time = '';

        return true;
    }

    /**
     * getNextOrder
     *
     * Method to get the next ordering value for a group of rows defined by an SQL WHERE clause.
     * This is useful for placing a new item last in a group of items in the table.
     *
     * @param   string   WHERE clause to use for selecting the MAX(ordering) for the table.
     * @return  mixed    Boolean false an failure or the next ordering value as an integer.
     * @since   1.0
     */
    public function getNextOrder($where = '')
    {
        // If there is no ordering field set an error and return false.
        if (property_exists($this, 'ordering')) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', $this->name));
            $this->setError($e);
            return false;
        }

        // Get the largest ordering value for a given where clause.
        $this->query = $this->db->getQuery(true);
        $this->query->select('MAX(ordering)');
        $this->query->from($this->table_name);

        if ($where) {
            $this->query->where($where);
        }

        $this->db->setQuery($this->query->__toString());
        $max = (int)$this->db->loadResult();

        // Check for a database error.
        if ($this->db->getErrorNum()) {
            $e = new MolajoException(
                Services::Language()->sprintf('MOLAJO_DB_ERROR_GET_NEXT_ORDER_FAILED', $this->name, $this->db->getErrorMsg())
            );
            $this->setError($e);

            return false;
        }

        // Return the largest ordering value + 1.
        return ($max + 1);
    }

    /**
     * reorder
     *
     * Method to compact the ordering values of rows in a group of rows
     * defined by an SQL WHERE clause.
     *
     * @param   string   WHERE clause to use for limiting the selection of rows to
     *                    compact the ordering values.
     * @return  mixed    Boolean true on success.
     * @since   1.0
     */
    public function reorder($where = '')
    {
        // If there is no ordering field set an error and return false.
        if (property_exists($this, 'ordering')) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', $this->name));
            $this->setError($e);
            return false;
        }

        // Initialise variables.
        $k = $this->primary_key;

        // Get the primary keys and ordering values for the selection.
        $this->query = $this->db->getQuery(true);
        $this->query->select($this->primary_key . ', ordering');
        $this->query->from($this->table_name);
        $this->query->where('ordering >= 0');
        $this->query->order('ordering');

        // Setup the extra where and ordering clause data.
        if ($where) {
            $this->query->where($where);
        }

        $this->db->setQuery($this->query->__toString());
        $rows = $this->db->loadObjectList();

        // Check for a database error.
        if ($this->db->getErrorNum()) {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_REORDER_FAILED', $this->name, $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Compact the ordering values.
        foreach ($rows as $i => $row) {
            // Make sure the ordering is a positive integer.
            if ($row->ordering >= 0) {
                // Only update rows that are necessary.
                if ($row->ordering == $i + 1) {
                } else {
                    // Update the row ordering field.
                    $this->query = $this->db->getQuery(true);
                    $this->query->update($this->table_name);
                    $this->query->set('ordering = ' . ($i + 1));
                    $this->query->where($this->primary_key . ' = ' . $this->db->q($row->$k));
                    $this->db->setQuery($this->query->__toString());

                    // Check for a database error.
                    if ($this->db->query()) {
                    } else {
                        $e = new MolajoException(
                            Services::Language()->sprintf(
                                'MOLAJO_DB_ERROR_REORDER_UPDATE_ROW_FAILED', $this->name, $i, $this->db->getErrorMsg()
                            )
                        );
                        $this->setError($e);

                        return false;
                    }
                }
            }
        }

        return true;
    }
}
