<?php
/**
 * @package     Molajo
 * @subpackage  Extension
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  File Helper
 */
$fileHelper = new MolajoFileHelper();

/**
 *  Primary Extensions Classes
 */
$fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/request.php', 'MolajoRequest');
$fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/document.php', 'MolajoDocument');
$fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/renderer.php', 'MolajoRenderer');

/**
 *  Helpers
 */
$files = JFolder::files(MOLAJO_EXTENSIONS_CORE . '/core/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/helpers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Helper');
}

/**
 *  Installer
 */
$files = JFolder::files(MOLAJO_EXTENSIONS_CORE . '/core/installer/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/installer/adapters/' . $file, 'MolajoInstallerAdapter' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Renderers
 */
$files = JFolder::files(MOLAJO_EXTENSIONS_CORE . '/core/renderers', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/renderers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Renderer');
}


/*                                              */
/*  Modernizr                                   */
/*                                              */
//$fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE.'/core/modernizr-server.php', 'Modernizr');
/*
 print 'The server knows:';
foreach($modernizr as $feature=>$value) {
     echo '<pre>'; echo 'Feature: '.$feature ; echo ':<br />';
     var_dump($value); echo '</pre>';
}
*/

