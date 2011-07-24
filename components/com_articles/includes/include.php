<?php
/**
 * @package     Molajo
 * @subpackage  Administrator Component Include
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** File Helper */
$filehelper = new MolajoFileHelper();
/** ACL Overrides */
$filehelper->requireClassFile(MOLAJO_PATH_COMPONENT.'/helpers/acl.php', 'MolajoACLArticles');
/** Component Helper */
$filehelper->requireClassFile(MOLAJO_PATH_COMPONENT.'/helpers/router.php', 'ArticlesRouteHelper');
/** Override Folders */
define('MOLAJO_COMPONENT_ATTRIBUTES', MOLAJO_PATH_COMPONENT.'/fields/attributes');
define('MOLAJO_COMPONENT_FIELDS', MOLAJO_PATH_COMPONENT.'/fields/fields');
define('MOLAJO_COMPONENT_FIELDTYPES', MOLAJO_PATH_COMPONENT.'/fields/fieldtypes');