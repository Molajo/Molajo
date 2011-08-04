<?php
/**
 * @package     Molajo
 * @subpackage  Load Component Files
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** @var $filehelper */
$filehelper = new MolajoFileHelper();

/** Controller */
$filehelper->requireClassFile($request['component_path'].'/controller.php', ucfirst($request['no_com_option']).'Controller');
if ($request['controller'] == 'display') {
} else {
    $filehelper->requireClassFile($request['component_path'].'/controllers/'.$request['controller'].'.php', ucfirst($request['no_com_option']).'Controller'.ucfirst($request['controller']));
}
/** Models */
$filehelper->requireClassFile($request['component_path'].'/models/'.$request['model'].'.php', ucfirst($request['no_com_option']).'Controller'.ucfirst($request['model']));
/** Views */
$filehelper->requireClassFile($request['component_path'].'/views/'.$request['view'].'/'.'view'.$request['format'].'.php', ucfirst($request['no_com_option']).'View'.ucfirst($request['view']));
/** ACL */
$filehelper->requireClassFile($request['component_path'].'/helpers/acl.php', 'MolajoACLArticles');
/** Router */
$filehelper->requireClassFile($request['component_path'].'/helpers/router.php', 'ArticlesRouteHelper');

