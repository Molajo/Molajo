<?php
/**
 * @author Antonio Duran
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package xmlrpctest
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/*
 * Define constants for all pages
 */
define( 'COM_XMLRPCTEST_DIR', 'images'.DS.'xmlrpctest'.DS );
define( 'COM_XMLRPCTEST_BASE', JPATH_ROOT.DS.COM_XMLRPCTEST_DIR );
define( 'COM_XMLRPCTEST_BASEURL', JURI::root().str_replace( DS, '/', COM_XMLRPCTEST_DIR ));

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Require the base controller
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

// Initialize the controller
$controller = new XmlrpctestController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
?>
