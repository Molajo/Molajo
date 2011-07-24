<?php
/**
 * @package     Molajo
 * @subpackage  Attributes
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Utility class for managing Attributes
 *
 * @package	    Molajo
 * @subpackage	Attributes
 * @since	    1.0
 */
class MolajoAttribute
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
     * Results returned to the Layout
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
	public function __construct($input = array(), $rowset = array()) {}

    /**
     * __get
     * 
     * Retrieve Class Parameter
     * 
     * @param $property
     * @return null
     */
    public function __get($property) {
        return (isset($this->{'_'. $property}) ? $this->{'_'. $property} : null);
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
    public function __set($property, $value) {
        if (isset($this->{'_'. $property})) {
            $this->{'_'. $property} = $value;
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
    public function __isset($property) {
        return isset($this->{'_'. $property});
    }    

    /**
     * requireClassFile
     *
     * Requires the Attribute Class File
     *
     * @param bool $reportError
     *
     * @return bool
     */
    public function requireClassFile ($reportError=true)
    {
        $class = 'MolajoAttribute'.ucfirst($this->_name);

        if (class_exists($class)) {
            return true;
        }

        /** component override */
        $classFile = MOLAJO_COMPONENT_ATTRIBUTES.$this->_name.'.php';
        if (JFile::exists($classFile)) {
            require_once $classFile;
        }

        if (class_exists($class)) {
            return true;
        }

        /** library class */
        $classFile = MOLAJO_LIBRARY_ATTRIBUTES.$this->_name.'.php';
        if (JFile::exists($classFile)) {
            require_once $classFile;
        }

        if ($reportError === true) {
            JFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_ATTRIBUTE_FILENAME').' '.$class.' '.$classFile, 'error');
            return false;
        }

        return true;
    }
}