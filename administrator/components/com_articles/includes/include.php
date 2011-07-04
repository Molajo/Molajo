<?php
/**
 * @version     $id: include.php
 * @package     Molajo
 * @subpackage  Administrator Component Include
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$filehelper = new MolajoFileHelper();
$filehelper->requireClassFile(JPATH_COMPONENT.'/helpers/acl.php', 'MolajoACLArticles');
$filehelper->requireClassFile(JPATH_COMPONENT.'/helpers/articles.php', 'ArticlesHelper');

JForm::addFormPath(JPATH_COMPONENT.'/models/forms');
JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');