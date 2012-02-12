<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Event
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoEventModel extends MolajoModel
{
    /**
     * __construct
     *
     * @param  $config
     * @since  1.0
     */
    public function __construct(JConfig $config = null)
    {
        $this->_name = get_class($this);
        parent::__construct($config);
    }

    /**
     * getEvents
     *
     * @return    array
     *
     * @since    1.0
     */
    public function getEvents()
    {
        /** input for events is stored in the task request object */
        $this->items = $this->get('items');

        //call out to processing here

        Molajo::Renderer()->items = $this->items;
        return $this->items;
        /** custom */
    }
}
