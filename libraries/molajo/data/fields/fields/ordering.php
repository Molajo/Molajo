<?php
/**
 * @version     $id: filterOrdering.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldOrdering
 *
 *  Ordering Filter Field Handling
 *
 *  @package    Molajo
 *  @subpackage Filter
 *  @since      1.6
 */
class MolajoFieldOrdering extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct() {
        parent::__construct();
        parent::setFieldname ('ordering');
        parent::setRequestFilter ('integer');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions ()
    {

    }

    /**
     *  getSelectedValue
     *
     *  Returns Selected Value
     */
    public function getSelectedValue ()
    {
        /** retrieve and filter selected value **/
        parent::getSelectedValue ();

        if ($this->requestValue == null) {
            return false;
        }

        /** validate to list **/
        $this->validateRequestValue();

        /** return filtered and validated value **/
        return $this->requestValue;
    }

    /**
     *  validateRequestValue
     *
     *  Returns Selected Value
     */
    public function validateRequestValue ()
    {
        $validItems = $this->getOptions();

        /** loop thru **/
        $found = false;
        foreach ($validItems as $count => $validItem) {

            if ($this->requestValue == $validItem->value) {
                $found = true;
                break;
            }

        }
        return $found;
    }

    /**
     * getQueryInformation
     * @param sstring $value
     * @return array
     */
    public function getQueryInformation ($query, $value, $selectedState, $onlyWhereClause=false)
    {
        $query->select('a.ordering');
        if (is_numeric($value)) {
            $query->where('a.ordering = '.(int) $value);
        } else if ($value == '*') {
            
        } else  {

            $query->where('a.ordering > -1');
        }
    }

    /**
     *  render
     *
     *  sets formatting and content parameters
     */
    public function render ($layout, $item, $itemCount)
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
            $render['column_name'] = 'ordering';
            $render['print_value'] = $item->ordering; //MolajoHTML::_('Mgrid.ordering', $item->ordering, $itemCount, $item->canEditordering);

            return $render;
        }
    }
}