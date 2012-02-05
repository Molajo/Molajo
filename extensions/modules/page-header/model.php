<?php
/**
 * @package     Molajo
 * @subpackage  Header
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
class PageheaderModuleModel extends MolajoModel
{
    /**
     * __construct
     *
     * Constructor.
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
     * getItems
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getItems()
    {
        $this->items = array();

        $tempObject = new JObject();
        $tempObject->set('title', Molajo::Application()->get('site_title', 'Molajo'));
        $this->items[] = $tempObject;

        return $this->items;
    }
}
