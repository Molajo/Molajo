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
if ($data['option'] == $current_folder) {
} else {
    JError::raiseError(500, JText::_('MOLAJO_INVALID_OPTION'));
    return false;
}

/** establish controller **/
$defaultController = substr($data['option'], (strpos($data['option'], '_') + 1), strlen($data['option']) - strpos($data['option'], '_'));
$controller = JController::getInstance(ucfirst($defaultController));
$controller->data = $data;
$controller->params = MolajoComponentHelper::getParams($data['option']);

/** initialise **/
$results = $controller->execute('initialise');


/** task **/
$controller->execute($data['task']);