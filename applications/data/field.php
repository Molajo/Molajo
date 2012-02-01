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
            $this->value = MolajoController::getUser()->getUserState('filter.' . $this->name);
        }
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

    /**
     * getClass
     *
     * Loads Field Class file
     *
     * @param $name
     * @param bool $reportError
     *
     * @return bool
     */
    public function getClass($name, $reportError = true)
    {
        if (class_exists('MolajoField' . ucfirst($name))) {
        } else {

            $nameClassFile = MOLAJO_APPLICATIONS_CORE_DATA . '/fields/' . $name . '.php';
            if (JFile::exists($nameClassFile)) {
                require_once $nameClassFile;

            } else {
                if ($reportError === true) {
                    MolajoController::getApplication()->setMessage(MolajoTextHelper::_('MOLAJO_INVALID_FIELD_FILENAME') . ' ' . 'MolajoField' . ucfirst($name) . ' ' . $nameClassFile, 'error');
                    return false;
                }
            }
        }
    }
}
