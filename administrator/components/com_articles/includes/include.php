<?php
/**
 * @version     $id: defines.php
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2011 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

JForm::addFormPath(JPATH_COMPONENT.'/models/forms');
JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
require_once JPATH_COMPONENT.'/helpers/acl.php';