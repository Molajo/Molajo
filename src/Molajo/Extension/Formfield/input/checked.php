<?php
/**
 * @package   Molajo
 * @subpackage  Attributes
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoAttributeChecked
 *
 * Populate Checked Attribute
 *
 * @package   Molajo
 * @subpackage  Attributes
 * @since       1.0
 */
class CheckedControllerFormfield extends InputControllerFormfield
{
    /**
     * __construct
     *
     * Method to instantiate the Checked object.
     *
     * @param array $input
     * @param array $resultset
     *
     * @return  void
     *
     * @since   1.0
     */
    public function __construct($input = array(), $resultset = array())
    {
        parent::__construct();
        parent::__set('name', 'Checked');
        parent::__set('input', $input);
        parent::__set('resultset', $resultset);
    }

    /**
     * setValue
     *
     * Method to set the Attribute Value
     *
     * @return  array   $resultset
     *
     * @since   1.1
     */
    protected function setValue()
    {
        $checked = $this->element['checked'];
        $value = $this->verifyValue($checked);

        parent::__set('value', $value);

        /** $this->resultset */
        $this->resultset[0]['checked'] = $this->value;

        /** return array of attributes */
        return $this->resultset;
    }

    /**
     * verifyValue
     *
     * Method to determine whether or not the Checked exists
     *
     * @return  array   $resultset
     *
     * @since   1.1
     */
    protected function verifyValue($checked)
    {
        if ((boolean)$checked === true) {
            $value = 'checked="checked"';
        } else {
            $value = '';
        }
        return $value;
    }
}
