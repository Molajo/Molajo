<?php
/**
 * @version     $id: filterAccess.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldAccess
 *
 *  Access Filter Field Handling
 *
 * @package    Molajo
 * @subpackage Filter
 * @since      1.6
 */
class MolajoFieldAccess extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setName('access');
        parent::setFilter('integer');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions()
    {
        return MolajoHTML::_('access.assetgroups');
    }

    /**
     *  getValue
     *
     *  Returns Selected Value
     */
    public function getValue()
    {
        /** retrieve and filter selected value **/
        parent::getValue();

        if ($this->value == null) {
            return false;
        }

        /** validate to list **/
        $this->validateRequestValue();

        /** return filtered and validated value **/
        return $this->value;
    }

    /**
     *  validateRequestValue
     *
     *  Returns Selected Value
     */
    public function validateRequestValue()
    {
        $validItems = $this->getOptions();

        /** loop thru **/
        $found = false;
        foreach ($validItems as $count => $validItem) {

            if ($this->value == $validItem->value) {
                $found = true;
                break;
            }

        }
        return $found;
    }

    /**
     *  getQueryInformation
     *
     *  Appends to query object
     */
    public function getQueryInformation($query, $value, $selectedState, $onlyWhereClause = false)
    {
        if ((int)$value == 0) {
            return;
        }
        $aclClass = 'MolajoACL' . ucfirst(strtolower(JRequest::getVar('DefaultView')));
        $aclClass::getQueryInformation(JRequest::getVar('option'), $query, 'filter', $value);
    }

    /**
     *  render
     *
     *  sets formatting and content parameters
     */
    public function render($view, $item, $itemCount)
    {
        if ($view == 'admin') {
            $render = array();
            $render['link_value'] = false;
            $render['class'] = 'nowrap';
            $render['valign'] = 'top';
            $render['align'] = 'left';
            $render['sortable'] = true;
            $render['checkbox'] = false;
            $render['data_type'] = 'string';
            $render['column_name'] = 'access';
            $render['print_value'] = $item->access_level;

            return $render;
        }
    }
}