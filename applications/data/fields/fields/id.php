<?php
/**
 * @version     $id: filterId.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldId
 *
 *  Id Filter Field Handling
 *
 * @package    Molajo
 * @subpackage Filter
 * @since      1.6
 */
class MolajoFieldId extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setName('Id');
        parent::setFilter('integer');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions()
    {

    }

    /**
     *  getValue
     *
     *  Returns Selected Value
     */
    public function getValue()
    {
        parent::getValue();

        if ($this->value == null) {
            return false;
        }

        /** return filtered and validated value **/
        return $this->value;
    }

    public function validateRequestValue()
    {

    }

    /**
     *  getQueryInformation
     *
     *  Returns Formatted Where clause for Query
     *
     * @param $query
     * @param $value
     * @param $selectedState
     * @param bool $onlyWhereClause
     * @return
     */
    public function getQueryInformation($query, $value, $selectedState, $onlyWhereClause = false)
    {
        if ($onlyWhereClause === true) {
        } else {
            $query->select('a.id');
        }
        if ((int)$value == 0) {
            return;
        }

        if (is_numeric($value) && $value > 0) {
            $query->where('a.id = ' . $value);
        }
    }

    /**
     *  render
     *
     *  sets formatting and content parameters
     */
    public function render($layout, $item, $itemCount)
    {
        if ($layout == 'admin') {
            $render = array();
            $render['link_value'] = false;
            $render['class'] = 'nowrap';
            $render['valign'] = 'top';
            $render['align'] = 'left';
            $render['sortable'] = true;
            $render['checkbox'] = false;
            $render['data_type'] = 'string';
            $render['column_name'] = 'id';
            $render['print_value'] = $item->id;

            return $render;
        }
    }
}