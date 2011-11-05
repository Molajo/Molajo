<?php
/**
 * @version     $id: field.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Utility class for managing the Access Filter Field
 *
 * @package    Molajo
 * @subpackage    HTML
 * @since    1.0
 */
class MolajoField
{
    /**
     * $fieldName
     *
     * @var string
     */
    public $fieldName = null;

    /**
     * $requestFilter
     *
     * @var string
     */
    public $requestFilter = null;

    /**
     * $requestValue
     *
     * @var string
     */
    public $requestValue = null;

    /**
     * $tableColumnSortable
     *
     * @var string
     */
    public $tableColumnSortable = false;

    /**
     * $tableColumnCheckbox
     *
     * @var string
     */
    public $tableColumnCheckbox = false;

    /**
     * $fieldDataType
     *
     * @var string
     */
    public $displayDataType = 'string';

    /**
     * __construct
     */
    public function __construct()
    {
    }

    /**
     * setFieldName
     *
     * Returns list of Option Values
     */
    protected function setFieldName($field)
    {
        $this->fieldName = $field;
    }

    /**
     * setRequestFilter
     *
     * Returns Selected Value
     */
    protected function setRequestFilter($filter = 'integer')
    {
        $this->requestFilter = $filter;
    }

    /**
     * getSelectedValue
     *
     * Returns Selected Value
     */
    public function getSelectedValue()
    {

        /** float: digits and periods **/
        if ($this->requestFilter == 'float') {
            $this->requestValue = JRequest::getFloat('filter_' . $this->fieldName, null);

            /** base64: URL **/
        } else if ($this->requestFilter == 'base64') {
            $this->requestValue = JRequest::getVar($this->fieldName, null, 'default', 'base64');

            /** boolean: true or false **/
        } else if ($this->requestFilter == 'boolean') {
            $this->requestValue = JRequest::getBool('filter_' . $this->fieldName, null);

            /** command: [A-Za-z0-9.-_] **/
        } else if ($this->requestFilter == 'command') {
            $this->requestValue = JRequest::getCmd('filter_' . $this->fieldName, null);

            /** word: [A-Za-z_] **/
        } else if ($this->requestFilter == 'word') {
            $this->requestValue = JRequest::getWord('filter_' . $this->fieldName, null);

            /** string: only filters 'bad' HTML code **/
        } else if ($this->requestFilter == 'string') {
            $this->requestValue = JRequest::getString('filter_' . $this->fieldName, null);

            /** integer **/
        } else {
            $this->requestValue = JRequest::getInt('filter_' . $this->fieldName, null);
        }

        /** retain value from previous page load if current request is null **/
        if ($this->requestValue == null) {
            $this->requestValue = MolajoFactory::getApplication()->getUserState('filter.' . $this->fieldName);
        }
    }

    /**
     * setTableColumnSortable
     *
     * Display property
     */
    protected function setTableColumnSortable($option = true)
    {
        $this->tableColumnSortable = $option;
    }

    /**
     * setTableColumnCheckbox
     *
     * Display property
     */
    protected function setTableColumnCheckbox($option = false)
    {
        $this->tableColumnCheckbox = $option;
    }

    /**
     * setTableColumnCheckbox
     *
     * Display property
     */
    protected function setDisplayDataType($option = false)
    {
        $this->displayDataType = $option;
    }

    /**
     * requireFieldClassFile
     *
     * Returns Selected Value
     */
    public function requireFieldClassFile($fieldName, $reportError = true)
    {
        if (class_exists('MolajoField' . ucfirst($fieldName))) {
        } else {
            $fieldClassFile = MOLAJO_LIBRARY . '/fields/fields/' . $fieldName . '.php';
            if (JFile::exists($fieldClassFile)) {
                require_once $fieldClassFile;
            } else {
                if ($reportError === true) {
                    MolajoFactory::getApplication()->enqueueMessage(MolajoText::_('MOLAJO_INVALID_FIELD_FILENAME') . ' ' . 'MolajoField' . ucfirst($fieldName) . ' ' . $fieldClassFile, 'error');
                    return false;
                }
            }
        }
    }
}