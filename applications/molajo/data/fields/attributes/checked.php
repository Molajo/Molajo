<?php
/**
 * @package     Molajo
 * @subpackage  Attributes
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoAttributeChecked
 *
 * Populate Checked Attribute
 *
 * @package     Molajo
 * @subpackage  Attributes
 * @since       1.0
 */
class MolajoAttributeChecked extends MolajoAttribute
{
    /**
     * __construct
     *
     * Method to instantiate the Checked object.
     *
     * @param array $input
     * @param array $rowset
     *
     * @return  void
     *
     * @since   1.0
     */
    public function __construct($input = array(), $rowset = array())
    {
        parent::__construct();
        parent::__set('name', 'Checked');
        parent::__set('input', $input);
        parent::__set('rowset', $rowset);
    }

    /**
     * setValue
     *
     * Method to set the Attribute Value
     *
     * @return  array   $rowset
     *
     * @since   1.1
     */
    protected function setValue()
    {
        $checked = $this->element['checked'];
        $value = $this->verifyValue($checked);

        parent::__set('value', $value);

        /** $this->rowset */
        $this->rowset[0]['checked'] = $this->value;

        /** return array of attributes */
        return $this->rowset;
    }

    /**
     * verifyValue
     *
     * Method to determine whether or not the Checked exists
     *
     * @return  array   $rowset
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