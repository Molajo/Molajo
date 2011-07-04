<?php
/**
 * @version     $id: include.php
 * @package     Molajo
 * @subpackage  Include
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR.'/models/forms');
JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR.'/models/fields');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/acl.php';
require_once JPATH_COMPONENT.'/helpers/route.php';