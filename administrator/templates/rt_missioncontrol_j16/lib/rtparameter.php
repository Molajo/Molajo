<?php
/**
 * @version		$Id: parameter.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla.Framework
 * @subpackage	Parameter
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport( 'joomla.registry.registry' );
jimport( 'joomla.html.parameter' );

//Register the element class with the loader
JLoader::register('JElement', JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'parameter'.DS.'element.php');

/**
 * Parameter handler
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class RTParameter extends JParameter
{

    protected $_defaults = array();
    protected $_xml = null;

	/**
	 * modified
	 */
	public function __construct($data = '', $path = '')
	{
		parent::__construct($data);
	
		jimport('joomla.form.form');
		JForm::addFormPath($path);
		
		$form =& JForm::getInstance('templateDetails', JPATH_ADMINISTRATOR .'/templates/rt_missioncontrol_j16/templateDetails.xml', array(), true, '//config');
		$this->_form = $form;
	}

	function get($key, $default=false)
	{
		
		$value = parent::get($key);

        if (empty($value) && ($value !== 0) && ($value !== '0')) {
			if (!$default) {
	            if (key_exists($key,$this->_defaults)) {
	                $default = $this->_defaults[$key];
	            } else {
	                $default = $this->_form->getFieldAttribute($key,'default','','params');//works!!!!
	                $this->_defaults[$key] = $default; 
	            }
            }
            return $default;
        } else {
            return $value;
        }
	}
	
	
}