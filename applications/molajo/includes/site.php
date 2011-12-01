<?php
/**
 * @package     Molajo
 * @subpackage  Load Site Files
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  File Helper
 */
$filehelper = new MolajoFileHelper();

/**
 *  Site
 */
$filehelper->requireClassFile(MOLAJO_SITES . '/molajo/site.php', 'MolajoSite');
$files = JFolder::files(MOLAJO_SITES . '/molajo', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'site') {
    } else {
        $filehelper->requireClassFile(MOLAJO_SITES . '/molajo/' . $file, 'MolajoSite' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}


