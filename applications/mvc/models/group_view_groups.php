<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Group View Groups
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 * @link
 */
class MolajoModelGroupViewGroups extends MolajoModel
{
    /**
     * Constructor
     *
     * @param   object  Database object
     *
     * @return  MolajoModelGrouping
     *
     * @since   1.0
     */
    public function __construct($db)
    {
        parent::__construct('#__group_view_groups', 'id', $db);
    }

    /**
     * Method to bind the data.
     *
     * @param   array  $array   The data to bind.
     * @param   mixed  $ignore  An array or space separated list of fields to ignore.
     *
     * @return  bool  True on success, false on failure.
     *
     * @since   1.0
     */
    public function bind($array, $ignore = '')
    {
        return parent::bind($array, $ignore);
    }

    /**
     * Method to check the current record to save
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function check()
    {
        // Validate the title.
        if ((trim($this->title)) == '') {
            $this->setError(MolajoTextHelper::_('MOLAJO_DB_ERROR_GROUPING'));
            return false;
        }

        return true;
    }
}
