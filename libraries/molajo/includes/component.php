<?php
/**
 * @package     Molajo
 * @subpackage  Component
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** defines and includes  **/
require_once MOLAJO_PATH_COMPONENT.'/includes/include.php';

/** validate option **/
if (JRequest::getCmd('option') == $current_folder) {
} else {
    JError::raiseError(500, JText::_('MOLAJO_INVALID_OPTION'));
    return false;
}

/** validate request parameters **/
$validate = new MolajoValidateHelper ();
$results = $validate->checkRequest();

/** establish controller **/
$defaultController = substr(JRequest::getCmd('option'), (strpos(JRequest::getCmd('option'), '_') + 1), strlen(JRequest::getCmd('option')) - strpos(JRequest::getCmd('option'), '_'));
$controller = JController::getInstance(ucfirst($defaultController));

/** initialise **/
$results = $controller->execute('initialise');

/** task **/
$controller->execute(JRequest::getCmd('task'));