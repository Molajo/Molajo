<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Header
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoPageheaderModuleModel extends MolajoDisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $config
     * @since  1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);
        $this->table = '';
        $this->primary_key = '';

        return parent::__construct($id);
    }

    /**
     * getData
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getData()
    {
        $this->items = array();

        $tempObject = new JObject();
        $tempObject->set('title', Services::Configuration()->get('site_title', 'Molajo'));
        $this->items[] = $tempObject;

        return $this->items;
    }
}
