<?php
/**
 * @version     $id: molajosample.php
 * @package     Molajo
 * @subpackage  Molajosample Plugin  
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajosample ACL Plugin
 *
 * @package		Molajo
 * @subpackage	ACL Plugin
 * @since		1.6
 */
class plgACLMolajosample extends JPlugin
{
    /**
    * ACL Events - Events in order of occurrence
    */ 
    
	/**
	 * 1. onACLPopulateState
     *
     * passes in full filter set, can add or modify
     *
	 * @param	object	$state               Array of request variables, filters, list objects
	 * @param	object	$params              Array of parameters
     *
     *  echo $state->get('request.layout');
     *
     *  foreach ($request_variables as $name => $value) {
     *      echo $value.'<br />';
     *  }
     *
     *  $state->set('request.layout', 'manager');
     *
	 * @since	1.0
     *
	 */
	public function onACLPopulateState (&$state, &$params)
	{
        return true;
    }

	/**
	 * 6. onACLComplete
     *
     * passes in full filter set, can add or modify 
	 *
	 * Method is called by the model
     * 
	 * @param	string	$state      Array of request variables, filters, list objects
	 * @param	object	$resultset  Full ACL resultset
	 * @param	object	$params     The content params
	 * 
	 * @since	1.6
	 */    
    public function onACLComplete (&$state, &$resultset, &$params)
    {
        return true;
    }      
}
