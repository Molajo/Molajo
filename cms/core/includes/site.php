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
$fileHelper = new MolajoFileHelper();

/**
 *  Site
 */
$fileHelper->requireClassFile(MOLAJO_SITE_CORE . '/site.php', 'MolajoSite');

$files = JFolder::files(MOLAJO_SITE_CORE, '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'site.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_SITE_CORE . '/' . $file, 'MolajoSite' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

MolajoSiteHelper::loadSiteClasses();
