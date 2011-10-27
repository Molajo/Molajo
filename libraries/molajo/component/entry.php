<?php
/**
 * @package     Molajo
 * @subpackage  Component
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
//echo '<pre>';var_dump($request);'</pre>';

/** component includes **/
require_once dirname(__FILE__).'/import.php';

/** validate option **/
if ($request['option'] == $current_folder) {
} else {
    JError::raiseError(500, MolajoText::_('MOLAJO_INVALID_OPTION'));
    return false;
}

/** controller **/
$defaultController = ucfirst($request['no_com_option']);
$controller = JController::getInstance($defaultController);
$controller->initialise($request);
$controller->$request['task']();