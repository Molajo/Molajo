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
class HeaderModelModule extends MolajoModel
{

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
        $tempObject->set('title', MolajoController::getApplication()->get('site_title', 'Molajo'));
        $this->items[] = $tempObject;

        return $this->items;
    }
}