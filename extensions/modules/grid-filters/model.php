<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * AdminToolbar
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoGridFiltersModuleModel extends MolajoModel
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

        return parent::__construct($id);
    }

    /**
     * getData
     *
     * @return    array
     * @since    1.0
     */
    public function getData()
    {
        $filters =
            Molajo::Request()->
            parameters->
            get('filters');

            $filterArray = explode(',', $filters);

        $this->data = array();
        foreach ($filterArray as $filter) {
            $this->data[] = $filter;
        }

        return $this->data;
    }
}
