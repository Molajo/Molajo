<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;
namespace Molajo\Extension\Module;

defined('MOLAJO') or die;

/**
 * GridFilters
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class ModuleGridFiltersModel extends Model
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
        /**
         *  Retrieve Filters from Parameters for Component
         */
        $filters =
            Molajo::Request()
                ->parameters
                ->get('filters');

            $filterArray = explode(',', $filters);

        /**
         *  Model Helper: MolajoExtensionModelHelper extends ModelHelper
         */
        $extensionName = ExtensionHelper::formatNameForClass(
            $this->get('extension_instance_name')
        );
        $helperClass = 'Molajo' . $extensionName . 'ModelHelper';
        if (class_exists($helperClass)) {
        } else {
            $helperClass = 'ModelHelper';
        }
        $h = new $helperClass();

        /**
         *  Get list and return results
         */
        $this->data = array();
        foreach ($filterArray as $filter) {

            $row = new \stdClass();

            $row->name = $filter;
            $row->list = $h->getList($filter);
            $row->selected = ''; //get from user state

            $this->data[] = $row;
        }

        return $this->data;
    }
}
