<?php
/**
 * @version     $id: filterCreated.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldCreated
 *
 *  Created Filter Field Handling
 *
 * @package    Molajo
 * @subpackage Filter
 * @since      1.6
 */
class MolajoFieldCreated extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setName('created');
        parent::setFilter('integer');

        parent::setSortable(true);
        parent::setCheckbox(false);
        parent::setDisplayType('date');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions()
    {
        $dateModel = JModel::getInstance('Model' . ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $dateModel->getMonthsCreate();
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
        if (substr($this->value, 0, 4) > '1900'
            && substr($this->value, 0, 4) < '2100'
            && inarray(substr($this->value, 5, 2), array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'))
        ) {
            return $this->value;
        } else {
            return false;
        }
    }

    /**
     *  getQueryInformation
     *
     *  Returns Formatted Where clause for Query
     */
    public function getQueryInformation($query, $value, $selectedState, $onlyWhereClause = false)
    {
        if ($onlyWhereClause) {
        } else {
            $query->select('a.created');
        }

        if (trim($value) == '') {
            return;
        }
        $db = $this->getJdbo();
        $query->where('SUBSTRING(a.created, 1, 7) = ' . $db->quote(substr($value, 0, 4) . '-' . substr($value, 4, 2)));
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
            $render['data_type'] = 'date';
            $render['column_name'] = 'created';
            if ($item->created == 0) {
                $render['print_value'] = '';
            } else {
                $render['print_value'] = JHTML::_('date', $item->created, TextServices::_('DATE_FORMAT_LC4'));
            }

            return $render;
        }
    }
}
