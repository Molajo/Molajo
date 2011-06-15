<?php
/**
 * @version     $id: include.php
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/** file helper */
if (class_exists('MolajoFileHelper')) {
} else {
    if (file_exists(MOLAJO_LIBRARY.'/helpers/file.php')) {
        JLoader::register('MolajoFileHelper', MOLAJO_LIBRARY.'/helpers/file.php');
    } else {
        JError::raiseNotice(500, JText::_('MOLAJO_OVERRIDE_CREATE_MISSING_CLASS_FILE'.' '.'MolajoFileHelper'));
        return;
    }
}
$filehelper = new MolajoFileHelper();

/** ACL */
$files = JFolder::files(MOLAJO_LIBRARY.'/acl', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'acl.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/acl/acl.php', 'ACL');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/acl/'.$file, ucfirst(substr($file, 0, strpos($file, '.'))).'ACL');
    }
}

/** Controller */
$files = JFolder::files(MOLAJO_LIBRARY.'/controllers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'controller.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/controllers/controller.php', 'MolajoController');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/controllers/'.$file, 'MolajoController'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Fields */
$files = JFolder::files(MOLAJO_LIBRARY.'/fields', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'field.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/fields/field.php', 'MolajoField');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/fields/'.$file, 'MolajoField'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
/** Form */
$files = JFolder::files(MOLAJO_LIBRARY.'/form/fields', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/form/fields/', 'JFormField'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Helpers - loaded in Joomla */

/** Include Application */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/'.MOLAJO_APPLICATION.'.php', 'Molajo'.ucfirst(MOLAJO_APPLICATION));
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/menu.php', 'MolajoMenu');

/** HTML fields */
$files = JFolder::files(MOLAJO_LIBRARY.'/html/html', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/html/html/', 'JHtml'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Models */
$files = JFolder::files(MOLAJO_LIBRARY.'/models', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/models/'.$file, 'MolajoModel'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
/** Model-Elements */
$files = JFolder::files(MOLAJO_LIBRARY.'/models/elements', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/models/elements/'.$file, 'MolajoElement'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Router */
$files = JFolder::files(MOLAJO_LIBRARY.'/router', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'router.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/router/router.php', 'MolajoRouter');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/router/'.$file, 'MolajoRouter'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Tables */
$files = JFolder::files(MOLAJO_LIBRARY.'/table', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'table.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/table/table.php', 'MolajoTable');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/table/'.$file, 'MolajoTable'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Views */
$files = JFolder::files(MOLAJO_LIBRARY.'/views', '\.php$', false, false);
$format = JRequest::getCmd('format', 'html');
if ($format == 'html' || $format == 'feed' || $format == 'raw') {
} else {
    $format == 'raw';
}
foreach ($files as $file) {

    if ($file == 'view.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/views/view.php', 'MolajoView');
    } else {
        if (strpos($file, $format)) {
            $filehelper->requireClassFile(MOLAJO_LIBRARY.'/views/'.$file, 'MolajoView'.ucfirst(substr($file, 0, strpos($file, '.'))));
        }
    }
}
