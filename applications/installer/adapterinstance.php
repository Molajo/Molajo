<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Adapter Instance Class
 *
 * @package     Joomla.Platform
 * @subpackage  Base
 * @since       11.1
 */
class MolajoAdapterInstance extends JObject
{
    /**
     * Parent
     *
     * @var   object
     * @since  11.1
     */
    protected $parent = null;

    /**
     * Database
     *
     * @var    object
     * @since  11.1
     */
    protected $db = null;

    /**
     * Constructor
     *
     * @param   object  &$parent  Parent object [MolajoAdapter instance]
     * @param   object  &$db      Database object [JDatabase instance]
     * @param   array   $options  Configuration Options
     *
     * @return  MolajoAdapterInstance
     *
     * @since   1.0
     */
    public function __construct(&$parent, &$db, $options = array())
    {
        // Set the properties from the options array that is passed in
        $this->setProperties($options);

        // Set the parent and db in case $options for some reason overrides it.
        $this->parent = &$parent;
        // Pull in the global dbo in case something happened to it.
        $this->db = $db ? $db : MolajoController::getDbo();
    }

    /**
     * Retrieves the parent object
     *
     * @return  object parent
     *
     * @since   1.0
     */
    public function getParent()
    {
        return $this->parent;
    }
}
