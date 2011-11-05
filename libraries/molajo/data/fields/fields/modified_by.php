<?php
/**
 * @version     $id: filterModified_by.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldModified_by
 *
 *  Modified_by Filter Field Handling
 *
 * @package    Molajo
 * @subpackage Filter
 * @since      1.6
 */
class MolajoFieldModified_by extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setFieldname('modified_by');
        parent::setRequestFilter('integer');

        parent::setTableColumnSortable(true);
        parent::setTableColumnCheckbox(false);
        parent::setDisplayDataType('string');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions()
    {
        $authorModel = JModel::getInstance('Model' . ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $authorModel->getAuthors();
    }

    /**
     *  getSelectedValue
     *
     *  Returns Selected Value
     */
    public function getSelectedValue()
    {
        /** retrieve and filter selected value **/
        parent::getSelectedValue();

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
    public function validateRequestValue()
    {
        return MolajoModelDisplay::validateValue('modified_by', $this->requestValue, 'integer', $table = null);
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
            $query->select('a.modified_by');
            $query->select('modified_by.name as modified_by_name');
            $query->select('modified_by.id as modified_by_id');
            $query->select('a.modified_by as modified_by');
            $query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
        }

        if ((int)$value == 0) {
            return;
        }
        $query->where('a.modified_by = ' . (int)$value);
    }
}