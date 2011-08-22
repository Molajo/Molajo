<?php
/**
 * @version     $id: filterStickied.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldStickied
 *
 *  Stickied Filter Field Handling
 *
 *  @package    Molajo
 *  @subpackage Filter
 *  @since      1.6
 */
class MolajoFieldStickied extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct() {
        parent::__construct();
        parent::setFieldname ('stickied');
        parent::setRequestFilter ('string');

        parent::setTableColumnSortable (true);
        parent::setTableColumnCheckbox (true);
        parent::setDisplayDataType ('integer');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions ()
    {
        $options	= array();
        $options[]	= JHtml::_('select.option', '0', MolajoText::_('MOLAJO_OPTION_UNSTICKIED'));
        $options[]	= JHtml::_('select.option', '1', MolajoText::_('MOLAJO_OPTION_STICKIED'));

        return $options;
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
    *  getQueryInformation
    *
    *  Returns Formatted Where clause for Query
    */
    public function getQueryInformation ($query, $value, $selectedState, $onlyWhereClause=false)
    {
        if ($onlyWhereClause) {
        } else {
            $query->select('a.stickied');
        }

        if ($value == null) {
        } else {
            $query->where('a.stickied = '.trim($value));
        }
    }
}