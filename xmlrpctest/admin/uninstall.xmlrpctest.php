<?php
/**
 * Joomla! 1.5 component Xmlrpctest
 *
 * @version $Id: uninstall.xmlrpctest.php 2009-04-17 03:54:05 svn $
 * @author Antonio Durán Terrés
 * @package Joomla
 * @subpackage Xmlrpctest
 * @license GNU/GPL
 *
 * Shows information about Moodle courses
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Set a simple message
$application = JFactory::getApplication();
$application->enqueueMessage( JText::_( 'NOTE: Database tables were NOT removed to allow for upgrades' ), 'notice' ) ;
?>