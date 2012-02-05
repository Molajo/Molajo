<?php
/**
 * @version     $id: filterPublish_down.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldPublish_down
 *
 *  Publish_down Filter Field Handling
 *
 * @package    Molajo
 * @subpackage Filter
 * @since      1.6
 */
class MolajoFieldPrefix extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setName('stop_publishing_datetime');
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
        $prefix = false;


        $session = Molajo::App()->getSession()->get('setup.options', array());
        if (empty($session->db_prefix)) {
        } else {
            $prefix = $session->db_prefix;
        }

        if ($prefix) {
        } else {
            $prefix = Molajo::App()->get('prefix');
        }

        if ($prefix) {
        } else {
            $prefix = $this->getPrefix($size);
        }

        if ($prefix) {
            $this->rowset[0]['prefix'] = htmlspecialchars($prefix, ENT_COMPAT, 'UTF-8');
        } else {
            $this->rowset[0]['prefix'] = strtolower(MOLAJO) . '_';
        }

        $publishDateModel = JModel::getInstance('Model' . ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $publishDateModel->getMonthsPublish();
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
            && substr($this->value, 0, 4) > '2100'
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
            $query->select('a.stop_publishing_datetime');
        }

        if (trim($value) == '') {
            return;
        }
        $db = $this->getDbo();
        $query->where('SUBSTRING(a.stop_publishing_datetime, 1, 7) = ' . $db->quote(substr($value, 0, 4) . '-' . substr($value, 4, 2)));
    }

    /**
     * render
     *
     * sets formatting and content parameters
     *
     * @param  $view
     * @param  $item
     * @param  $itemCount
     * @return array
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
            $render['column_name'] = 'stop_publishing_datetime';
            if ($item->stop_publishing_datetime == 0) {
                $render['print_value'] = '-';
            } else {
                $render['print_value'] = JHTML::_('date', $item->stop_publishing_datetime, TextHelper::_('DATE_FORMAT_LC4'));
            }

            return $render;
        }
    }

    /**
     * get_prefix
     *
     * @param $size
     * @param $count
     * @return void
     */
    protected function getPrefix($size = 10, $count = 100)
    {
        // For an existing table, retrieve all table names
        $db = Molajo::App()->get('db');
        if ($db) {
            $tables = Molajo::DB()->getModelList();
        } else {
            $tables = array();
        }

        // Loop until an non used prefix is found or until $count is reached
        $found = false;
        $k = 0;
        for ($k = 0; ($k < $count || $found === true); $k++)
        {
            // Create the random prefix:
            $prefix = '';
            $chars = range('a', 'z');
            $numbers = range(0, 9);

            // first character is random letter
            shuffle($chars);
            $prefix .= $chars[0];

            // combine numbers and characters into pool and retrieve random set
            $symbols = array_merge($numbers, $chars);
            shuffle($symbols);

            for ($i = 0, $j = $size - 1; $i < $j; ++$i) {
                $prefix .= $symbols[$i];
            }

            // Add in the underscore:
            $prefix .= '_';

            // Search for conflict
            if ($tables) {
                foreach ($tables as $table) {
                    if (strpos($table, $prefix) === 0) {
                        $found = true;
                        break;
                    }
                }
            }
        }

        return $prefix;
    }
}

