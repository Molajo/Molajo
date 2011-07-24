<?php
/**
 * @version     $id: include.php
 * @package     Molajo
 * @subpackage  Administrator Component Include
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** File Helper */
$filehelper = new MolajoFileHelper();
/** ACL Overrides */
$filehelper->requireClassFile(JPATH_COMPONENT.'/helpers/acl.php', 'MolajoACLArticles');
/** Component Helper */
$filehelper->requireClassFile(JPATH_COMPONENT.'/helpers/articles.php', 'ArticlesHelper');
/** Article Form Fields */
JForm::addFormPath(JPATH_COMPONENT.'/models/forms');
/** Override Folders */
define('MOLAJO_COMPONENT_ATTRIBUTES', JPATH_COMPONENT.'/fields/attributes');
define('MOLAJO_COMPONENT_FIELDS', JPATH_COMPONENT.'/fields/fields');
define('MOLAJO_COMPONENT_FIELDTYPES', JPATH_COMPONENT.'/fields/fieldtypes');