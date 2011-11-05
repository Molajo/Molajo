<?php
/**
 * @version     $id: filterSearch.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldSearch
 *
 *  Search Filter Field Handling
 *
 * @package    Molajo
 * @subpackage Filter
 * @since      1.6
 */
class MolajoFieldSearch extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setFieldname('search');
        parent::setRequestFilter('string');

        parent::setTableColumnSortable(false);
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
        $this->requestValueDateModel = JModel::getInstance('Model' . ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $this->requestValueDateModel->getMonthsPublish();
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
    }

    /**
     *  getQueryInformation
     *
     *  Returns Formatted Where clause for Query
     */
    public function getQueryInformation($query, $value, $selectedState, $onlyWhereClause = false)
    {
        $db = MolajoFactory::getDbo();
        if ($value == null || trim($value) == '') {
            return;
        }

        if (stripos($value, 'id:')) {
            $where = 'a.id = ' . (int)substr(trim($value), 3);

        } else if (stripos(trim($value), 'author:')) {
            $authorname = $db->Quote('%' . $db->getEscaped(substr($value, 7), true) . '%');
            $where = 'author.name LIKE ' . $db->quote(trim($authorname)) . ' OR author.username LIKE ' . $db->quote(trim($authorname));

        } else {
            $title = $db->Quote('%' . $db->getEscaped(trim($value)) . '%');
            $where = 'a.title LIKE ' . $title . ' OR a.alias LIKE ' . $db->quote(trim($title));
        }
        $query->where($where);
    }
}