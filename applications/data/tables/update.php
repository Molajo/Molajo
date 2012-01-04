<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Update Table Class
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 * @link
 */
class MolajoTableUpdate extends MolajoTable
{
    /**
     * Contructor
     *
     * @param database A database connector object
     */
    function __construct(&$db)
    {
        parent::__construct('#__updates', 'update_id', $db);
    }

    /**
     * Overloaded check function
     *
     * @return  boolean  True if the object is ok
     *
     * @see     MolajoTable:bind
     */
    public function check()
    {
        // check for valid name
        if (trim($this->name) == '' || trim($this->element) == '') {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_EXTENSION'));
            return false;
        }
        return true;
    }

    /**
     * Overloaded bind function
     *
     * @param   array  $hash named array
     *
     * @return  null|string  null is operation was satisfactory, otherwise returns an error
     *
     * @see     MolajoTable:bind
     * @since   1.0
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['parameters']) && is_array($array['parameters'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['parameters']);
            $array['parameters'] = (string)$registry;
        }

        if (isset($array['control']) && is_array($array['control'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['control']);
            $array['control'] = (string)$registry;
        }

        return parent::bind($array, $ignore);
    }

    function find($options = Array())
    {
        $dbo = MolajoController::getDbo();
        $where = Array();
        foreach ($options as $col => $val) {
            $where[] = $col . ' = ' . $dbo->Quote($val);
        }
        $query = 'SELECT update_id FROM #__updates WHERE ' . implode(' AND ', $where);
        $dbo->setQuery($query);
        return $dbo->loadResult();
    }
}
