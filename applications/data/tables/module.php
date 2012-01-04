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
 * Molajo Module Class
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 * @link
 */
class MolajoTableModule extends MolajoTable
{
    /**
     * Contructor.
     *
     * @param database A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__modules', 'id', $db);

        $this->access = (int)MolajoController::getApplication()->get('access');
    }

    /**
     * Overloaded check function.
     *
     * @return  boolean  True if the object is ok
     */
    public function check()
    {
        // check for valid name
        if (trim($this->title) == '') {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_MODULE'));
            return false;
        }

        // Check the publish down date is not earlier than publish up.
        if (intval($this->stop_publishing_datetime) > 0 && $this->stop_publishing_datetime < $this->start_publishing_datetime) {
            // Swap the dates.
            $temp = $this->start_publishing_datetime;
            $this->start_publishing_datetime = $this->stop_publishing_datetime;
            $this->stop_publishing_datetime = $temp;
        }

        return true;
    }

    /**
     * Overloaded bind function.
     *
     * @param   array  named array
     *
     * @return  null|string    null is operation was satisfactory, otherwise returns an error
     *
     * @see        MolajoTable:bind
     * @since   1.0
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['parameters']) && is_array($array['parameters'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['parameters']);
            $array['parameters'] = (string)$registry;
        }

        return parent::bind($array, $ignore);
    }
}
