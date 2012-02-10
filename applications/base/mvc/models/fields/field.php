<?php
/**
 * @package     Molajo
 * @subpackage  Field
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Utility class for managing fields
 *
 * @package     Molajo
 * @subpackage  Field
 * @since       1.0
 */
class MolajoField
{
    /**
     * $name
     *
     * @var string
     */
    public $name = null;

    /**
     * $filter
     *
     * @var string
     */
    public $filter = null;

    /**
     * $value
     *
     * @var string
     */
    public $value = null;

    /**
     * $sortable
     *
     * @var string
     */
    public $sortable = false;

    /**
     * $checkbox
     *
     * @var string
     */
    public $checkbox = false;

    /**
     * $displayType
     *
     * @var string
     */
    public $displayType = 'string';

    /**
     * __construct
     */
    public function __construct()
    {
        // find xml file (extension, then core)
        // read xml file, place values into variable
        parent::setName('access');
        parent::setFilter('integer');
    }

    /**
     * setName
     *
     * Sets the Field Name
     *
     * @param $name
     * @return void
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * setFilter
     *
     * Sets the selected field filter
     *
     * @param string $filter
     * @return void
     */
    protected function setFilter($filter = 'integer')
    {
        $this->filter = $filter;
    }

    /**
     * getValue
     *
     * Retrieves the field value given the selected filter
     */
    public function getValue()
    {
        /** float: digits and periods **/
        if ($this->filter == 'float') {
            $this->value = JRequest::getFloat('filter_' . $this->name, null);

            /** base64: URL **/
        } else if ($this->filter == 'base64') {
            $this->value = JRequest::getVar($this->name, null, 'default', 'base64');

            /** boolean: true or false **/
        } else if ($this->filter == 'boolean') {
            $this->value = JRequest::getBool('filter_' . $this->name, null);

            /** command: [A-Za-z0-9.-_] **/
        } else if ($this->filter == 'command') {
            $this->value = JRequest::getCmd('filter_' . $this->name, null);

            /** word: [A-Za-z_] **/
        } else if ($this->filter == 'word') {
            $this->value = JRequest::getWord('filter_' . $this->name, null);

            /** string: only filters 'bad' HTML code **/
        } else if ($this->filter == 'string') {
            $this->value = JRequest::getString('filter_' . $this->name, null);

            /** integer **/
        } else {
            $this->value = JRequest::getInt('filter_' . $this->name, null);
        }

        /** retain value from previous page load if current request is null **/
        if ($this->value == null) {
            $this->value = Molajo::Application()->get('User', '', 'services')->getUserState('filter.' . $this->name);
        }
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

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions()
    {
        return MolajoHTML::_('access.assetgroups');

        $aliasModel = JModel::getInstance('Model' . ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $aliasModel->getOptionList('alias', 'alias', $showKey = false, $showKeyFirst = false, $table = '');
    }

    /**
     * setSortable
     *
     * Set sortable property for field
     *
     * @param bool $value
     * @return void
     */
    protected function setSortable($value = false)
    {
        $this->sortable = $value;
    }

    /**
     * setCheckbox
     *
     * Set checkbox property for field
     *
     * @param bool $value
     * @return void
     */
    protected function setCheckbox($value = false)
    {
        $this->checkbox = $value;
    }

    /**
     * setDisplayType
     *
     * Set displaytype property for field
     *
     * @param bool $value
     * @return void
     */
    protected function setDisplayType($value = false)
    {
        $this->displayType = $value;
    }
}
