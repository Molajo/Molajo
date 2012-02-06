<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Extensions
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 * @link
 */
class MolajoExtensionsModel extends MolajoModel
{
    /**
     * Contructor
     *
     * @param database A database connector object
     */
    function __construct($db)
    {
        parent::__construct('#__extensions', 'extension_id', $db);
    }

    /**
     * Overloaded check function
     *
     * @return  boolean  True if the object is ok
     *
     * @see     MolajoModel:bind
     * @since   1.0
     */
    function check()
    {
        // check for valid name
        if (trim($this->name) == '' || trim($this->element) == '') {
            $this->setError(TextHelper::_('MOLAJO_DB_ERROR_MUSTCONTAIN_A_TITLE_EXTENSION'));
            return false;
        }
        return true;
    }

    /**
     * Overloaded bind function
     *
     * @param   array  $hash  Named array
     *
     * @return  null|string  null is operation was satisfactory, otherwise returns an error
     *
     * @see     MolajoModel:bind
     * @since   1.0
     */
    function bind($array, $ignore = '')
    {
        if (isset($array['parameters']) && is_array($array['parameters'])) {
            $registry = new Registry();
            $registry->loadArray($array['parameters']);
            $array['parameters'] = (string)$registry;
        }

        if (isset($array['control']) && is_array($array['control'])) {
            $registry = new Registry();
            $registry->loadArray($array['control']);
            $array['control'] = (string)$registry;
        }

        return parent::bind($array, $ignore);
    }

    function find($options = array())
    {
        $dbo = Molajo::DB();
        $where = Array();
        foreach ($options as $col => $val) {
            $where[] = $col . ' = ' . $dbo->Quote($val);
        }
        $query = 'SELECT extension_id FROM #__extensions WHERE ' . implode(' AND ', $where);
        $dbo->setQuery($query->__toString());
        return $dbo->loadResult();
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    An optional array of primary key values to update.  If not
     *                     set the instance property value is used.
     * @param   integer  The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer  The user id of the user performing the operation.
     *
     * @return  bool  True on success.
     *
     * @since   1.0
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->_tbl_key;

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
                $this->setError(TextHelper::_('MOLAJO_DB_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
            $checkin = '';
        }
        else {
            $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int)$userId . ')';
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
            'UPDATE ' . $this->_db->quoteName($this->_tbl) .
            ' SET ' . $this->_db->quoteName('enabled') . ' = ' . (int)$state .
            ' WHERE (' . $where . ')' .
            $checkin
        );
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin the rows.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the MolajoModel instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->enabled = $state;
        }

        $this->setError('');
        return true;
    }
}
