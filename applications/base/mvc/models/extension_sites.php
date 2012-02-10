<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Extension Sites
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 * @link
 */
class MolajoExtensionSitesModel extends MolajoModel
{
    /**
     * Contructor
     *
     * @param database A database connector object
     */
    function __construct($db)
    {
        parent::__construct('#__extension_sites', 'id', $db);
    }


    /**
     * Overloaded check function
     *
     * @return  boolean  True if the object is ok
     *
     * @see     MolajoModel:bind
     */
    public function check()
    {
        // check for valid name
        if (trim($this->name) == '' || trim($this->element) == '') {
            $this->setError(TextServices::_('MOLAJO_DB_ERROR_MUSTCONTAIN_A_TITLE_EXTENSION'));
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
     * @see     MolajoModel:bind
     * @since   1.0
     */
    public function bind($array, $ignore = '')
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

    function find($options = Array())
    {
        $db = Molajo::Services()->connect('jdb');
        $where = Array();
        foreach ($options as $col => $val) {
            $where[] = $col . ' = ' . $db->quote($val);
        }
        $query = 'SELECT update_id FROM #__extension_sites WHERE ' . implode(' AND ', $where);
        $db->setQuery($query->__toString());
        return $db->loadResult();
    }
}
