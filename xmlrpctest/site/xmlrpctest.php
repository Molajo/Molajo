<?php

/**
 * @author Antonio Duran
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package xmlrpctest
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Initialize the controller
$controller = JController::getInstance('Xmlrpctest');
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
?>
