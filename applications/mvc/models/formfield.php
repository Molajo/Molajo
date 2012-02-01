<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Formfield
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class MolajoModelFormfield
{
    /**
     * $_name
     *
     * @var string
     */
    protected $_name = null;

    /**
     * $_input
     *
     * From Form XML
     *
     * @var array
     */
    protected $_input = null;

    /**
     * $_rowset
     *
     * Results returned to the View
     *
     * @var string
     */
    protected $_rowset = null;

    /**
     * __construct
     *
     * Method to instantiate the attribute object.
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
    }

    /**
     * __get
     *
     * Retrieve Class Parameter
     *
     * @param $property
     * @return null
     */
    public function __get($property)
    {
        return (isset($this->{'_' . $property}) ? $this->{'_' . $property} : null);
    }

    /**
     * __set
     *
     * Set Class Parameter
     *
     * @param $property
     * @param $_value
     * @return void
     */
    public function __set($property, $value)
    {
        if (isset($this->{'_' . $property})) {
            $this->{'_' . $property} = $value;
        }
    }

    /**
     * __isset
     *
     * Determine if Parameter is set
     *
     * @param $property
     * @return bool
     */
    public function __isset($property)
    {
        return isset($this->{'_' . $property});
    }

    /**
     *  Global attributes
     */
    protected function _accesskey ()
    {

    }

    protected function _class ()
    {

    }

    protected function _contenteditable ()
    {

    }

    protected function _contextmenu ()
    {

    }

    protected function _dir ()
    {

    }

    protected function _draggable ()
    {

    }

    protected function _dropzone ()
    {

    }

    protected function _hidden ()
    {

    }

    protected function _id ()
    {

    }

    protected function _itemid ()
    {

    }

    protected function _itemprop ()
    {

    }

    protected function _itemref ()
    {

    }
    protected function _itemscope ()
    {

    }

    protected function _itemtype ()
    {

    }
    protected function _lang ()
    {

    }

    protected function _spellcheck ()
    {

    }
    protected function _style ()
    {

    }

    protected function _tabindex ()
    {

    }
    protected function _title ()
    {

    }



}
